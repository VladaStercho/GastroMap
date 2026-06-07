<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class EstablishmentController extends Controller
{
    /**
     * Головна сторінка з картою, пошуком та фільтрами
     */
    public function index(Request $request)
    {
        $query = Establishment::query();
        $tableName = (new Establishment())->getTable();

        if (Schema::hasColumn($tableName, 'is_approved')) {
            // $query->where('is_approved', true); // розкоментуйте якщо потрібна модерація
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm, $tableName) {
                if (config('database.default') === 'pgsql') {
                    $q->where('name', 'ilike', $searchTerm);
                    if (Schema::hasColumn($tableName, 'search_keywords')) {
                        $q->orWhere('search_keywords', 'ilike', $searchTerm);
                    }
                } else {
                    $q->where('name', 'LIKE', $searchTerm);
                    if (Schema::hasColumn($tableName, 'search_keywords')) {
                        $q->orWhere('search_keywords', 'LIKE', $searchTerm);
                    }
                }
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('check_range')) {
            if ($request->check_range === 'low') {
                $query->where('average_check', '<', 200);
            } elseif ($request->check_range === 'medium') {
                $query->whereBetween('average_check', [200, 500]);
            } elseif ($request->check_range === 'high') {
                $query->where('average_check', '>', 500);
            }
        }

        if ($request->has('has_wifi'))        $query->where('has_wifi', true);
        if ($request->has('has_terrace'))     $query->where('has_terrace', true);
        if ($request->has('is_pet_friendly')) $query->where('is_pet_friendly', true);

        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $query->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$lat, $lng, $lat]
            )->orderBy('distance', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $establishments = $query->get();

        if ($request->ajax()) {
            return response()->json($establishments);
        }

        return view('welcome', compact('establishments'));
    }

    /**
     * Детальна сторінка закладу
     */
    public function show($id)
    {
        $establishment = Establishment::with('reviews.user')->findOrFail($id);

        $currentTime = now()->format('H:i');
        $isOpen = false;

        $openingTime = $establishment->opening_time ?? '09:00';
        $closingTime = $establishment->closing_time ?? '22:00';

        $open  = date('H:i', strtotime($openingTime));
        $close = date('H:i', strtotime($closingTime));

        if ($close < $open) {
            if ($currentTime >= $open || $currentTime <= $close) $isOpen = true;
        } else {
            if ($currentTime >= $open && $currentTime <= $close) $isOpen = true;
        }

        $hasSchedulesTable = Schema::hasTable('schedules');
        $fallbackDays = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', "П'ятниця", 'Субота', 'Неділя'];

        return view('show', compact('establishment', 'isOpen', 'hasSchedulesTable', 'fallbackDays', 'openingTime', 'closingTime'));
    }

    /**
     * Форма додавання нового закладу (тільки для owner)
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->isOwner()) {
            abort(403, 'Доступ тільки для власників закладів.');
        }

        $establishment = null;
        return view('owner.establishment-form', compact('establishment'));
    }

    /**
     * Збереження нового закладу (тільки для owner)
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isOwner()) {
            abort(403);
        }

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:cafe,restaurant,pub',
            'address'         => 'required|string|max:500',
            'city'            => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:20',
            'description'     => 'nullable|string|max:2000',
            'average_check'   => 'nullable|integer|min:0',
            'opening_time'    => 'nullable|string',
            'closing_time'    => 'nullable|string',
            'has_wifi'        => 'nullable|boolean',
            'has_terrace'     => 'nullable|boolean',
            'is_pet_friendly' => 'nullable|boolean',
            'laptop_friendly' => 'nullable|boolean',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
        ]);

        // Чекбокси — якщо не відмічені, значення не передається, встановлюємо false
        $data['has_wifi']        = $request->boolean('has_wifi');
        $data['has_terrace']     = $request->boolean('has_terrace');
        $data['is_pet_friendly'] = $request->boolean('is_pet_friendly');
        $data['laptop_friendly'] = $request->boolean('laptop_friendly');

        $data['user_id']    = Auth::id();
        $data['is_approved'] = false; // Чекає схвалення адміна

        Establishment::create($data);

        return redirect()->route('dashboard')->with('success', 'Заклад "' . $data['name'] . '" подано на модерацію! Адмін скоро перевірить.');
    }

    /**
     * Форма редагування закладу (тільки власник цього закладу)
     */
    public function edit($id)
    {
        $establishment = Establishment::findOrFail($id);

        // Перевірка: або сам власник, або адмін
        if (Auth::user()->isOwner() && $establishment->user_id !== Auth::id()) {
            abort(403, 'Ви можете редагувати тільки свої заклади.');
        }

        if (!Auth::user()->isOwner() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('owner.establishment-form', compact('establishment'));
    }

    /**
     * Оновлення закладу (тільки власник цього закладу або адмін)
     */
    public function update(Request $request, $id)
    {
        $establishment = Establishment::findOrFail($id);

        if (Auth::user()->isOwner() && $establishment->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Auth::user()->isOwner() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:cafe,restaurant,pub',
            'address'         => 'required|string|max:500',
            'city'            => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:20',
            'description'     => 'nullable|string|max:2000',
            'average_check'   => 'nullable|integer|min:0',
            'opening_time'    => 'nullable|string',
            'closing_time'    => 'nullable|string',
            'has_wifi'        => 'nullable|boolean',
            'has_terrace'     => 'nullable|boolean',
            'is_pet_friendly' => 'nullable|boolean',
            'laptop_friendly' => 'nullable|boolean',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
        ]);

        $data['has_wifi']        = $request->boolean('has_wifi');
        $data['has_terrace']     = $request->boolean('has_terrace');
        $data['is_pet_friendly'] = $request->boolean('is_pet_friendly');
        $data['laptop_friendly'] = $request->boolean('laptop_friendly');

        $establishment->update($data);

        return redirect()->route('dashboard')->with('success', 'Заклад "' . $establishment->name . '" успішно оновлено!');
    }

    /**
     * Оновлення PDF-меню
     */
    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'menu_pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $establishment = Establishment::findOrFail($id);

        // Тільки власник або адмін
        if (Auth::user()->isOwner() && $establishment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->hasFile('menu_pdf')) {
            if ($establishment->menu_pdf) {
                Storage::disk('public')->delete($establishment->menu_pdf);
            }
            $path = $request->file('menu_pdf')->store('menus', 'public');
            $establishment->update(['menu_pdf' => $path]);
        }

        return redirect()->back()->with('success', 'Цифрове меню успішно оновлено!');
    }
}
