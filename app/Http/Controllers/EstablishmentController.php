<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EstablishmentController extends Controller
{

    public function index(Request $request)
    {
        $query = Establishment::query();
        $tableName = (new Establishment())->getTable();

        if (Schema::hasColumn($tableName, 'is_approved')) {
            // Тут за потреби можна відфільтрувати лише схвалені
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


    public function show($id)
    {
        $establishment = Establishment::with('reviews.user')->findOrFail($id);

        // Поточний час в Україні
        $now = Carbon::now('Europe/Kyiv');
        $isWeekend = $now->isWeekend();
        $currentTime = $now->format('H:i');
        $isOpen = false;

        // Дефолтні значення годин роботи
        $weekdayOpen = '09:00'; $weekdayClose = '22:00';
        $weekendOpen = '10:00'; $weekendClose = '23:00';

        // Розклеюємо години для будніх днів (opening_time)
        if (!empty($establishment->opening_time) && str_contains($establishment->opening_time, '-')) {
            $parts = explode('-', $establishment->opening_time);
            $weekdayOpen = date('H:i', strtotime($parts[0]));
            $weekdayClose = date('H:i', strtotime($parts[1]));
        }

        // Розклеюємо години для вихідних днів (closing_time)
        if (!empty($establishment->closing_time) && str_contains($establishment->closing_time, '-')) {
            $parts = explode('-', $establishment->closing_time);
            $weekendOpen = date('H:i', strtotime($parts[0]));
            $weekendClose = date('H:i', strtotime($parts[1]));
        }

        // Визначаємо графік на сьогодні залежно від дня тижня
        $openToday = $isWeekend ? $weekendOpen : $weekdayOpen;
        $closeToday = $isWeekend ? $weekendClose : $weekdayClose;

        // Перевіряємо статус "Відчинено"
        if ($closeToday < $openToday) {
            // Якщо заклад закривається після опівночі (наприклад, з 18:00 до 02:00)
            if ($currentTime >= $openToday || $currentTime <= $closeToday) {
                $isOpen = true;
            }
        } else {
            // Класичний денний графік (наприклад, з 09:00 до 22:00)
            if ($currentTime >= $openToday && $currentTime <= $closeToday) {
                $isOpen = true;
            }
        }

        $hasSchedulesTable = Schema::hasTable('schedules');
        $fallbackDays = ['Понеділок', 'Вівторок', 'Середа', 'Четвер', "П'ятниця", 'Субота', 'Неділя'];

        return view('show', compact(
            'establishment',
            'isOpen',
            'hasSchedulesTable',
            'fallbackDays',
            'weekdayOpen',
            'weekdayClose',
            'weekendOpen',
            'weekendClose',
            'isWeekend'
        ));
    }


    public function create()
    {
        if (!Auth::check() || (!Auth::user()->isOwner() && !Auth::user()->isAdmin())) {
            abort(403, 'Доступ тільки для власників закладів або адміністраторів.');
        }

        $establishment = null;
        return view('owner.establishment-form', compact('establishment'));
    }


    public function store(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isOwner() && !Auth::user()->isAdmin())) {
            abort(403, 'Дія заборонена.');
        }

        // Склеюємо години з окремих інпутів перед валідацією та збереженням
        $request->merge([
            'opening_time' => $request->weekday_open . '-' . $request->weekday_close,
            'closing_time' => $request->weekend_open . '-' . $request->weekend_close,
        ]);

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
            'photos'          => 'required|array|min:3',
            'photos.*'        => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'photos.required' => 'Будь ласка, завантажте фотографії вашого закладу.',
            'photos.min'      => 'Необхідно завантажити щонайменше 3 фотографії закладу.',
            'photos.*.image'  => 'Кожен завантажений файл має бути зображенням.',
        ]);

        $data['has_wifi']        = $request->boolean('has_wifi');
        $data['has_terrace']     = $request->boolean('has_terrace');
        $data['is_pet_friendly'] = $request->boolean('is_pet_friendly');
        $data['laptop_friendly'] = $request->boolean('laptop_friendly');

        $data['user_id']     = Auth::id();
        $data['is_approved'] = Auth::user()->isAdmin() ? true : false;

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('establishments_photos', 'public');
                $photoPaths[] = $path;
            }
        }

        $data['photos'] = $photoPaths;

        Establishment::create($data);

        return redirect()->route('dashboard')->with('success', 'Заклад успішно додано!');
    }


    public function edit($id)
    {
        $establishment = Establishment::findOrFail($id);

        if (!Auth::user()->isAdmin() && $establishment->user_id !== Auth::id()) {
            abort(403, 'Ви можете редагувати тільки свої заклади або ви не є адміністратором.');
        }

        return view('owner.establishment-form', compact('establishment'));
    }


    public function update(Request $request, $id)
    {
        $establishment = Establishment::findOrFail($id);

        if (!Auth::user()->isAdmin() && $establishment->user_id !== Auth::id()) {
            abort(403, 'Дія дозволена тільки власнику закладу або адміністратору.');
        }

        // Склеюємо години з окремих інпутів перед валідацією та збереженням
        $request->merge([
            'opening_time' => $request->weekday_open . '-' . $request->weekday_close,
            'closing_time' => $request->weekend_open . '-' . $request->weekend_close,
        ]);

        $rules = [
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
        ];

        if ($request->hasFile('photos')) {
            $rules['photos']   = 'required|array|min:3';
            $rules['photos.*'] = 'image|mimes:jpeg,png,jpg,webp|max:5120';
        }

        $data = $request->validate($rules, [
            'photos.min'     => 'Якщо ви оновлюєте фотографії, необхідно завантажити щонайменше 3 штуки.',
            'photos.*.image' => 'Кожен завантажений файл має бути зображенням.',
        ]);

        $data['has_wifi']        = $request->boolean('has_wifi');
        $data['has_terrace']     = $request->boolean('has_terrace');
        $data['is_pet_friendly'] = $request->boolean('is_pet_friendly');
        $data['laptop_friendly'] = $request->boolean('laptop_friendly');

        if ($request->hasFile('photos')) {
            if ($establishment->photos && is_array($establishment->photos)) {
                foreach ($establishment->photos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('establishments_photos', 'public');
                $photoPaths[] = $path;
            }

            $data['photos'] = $photoPaths;
        }

        $establishment->update($data);

        return redirect()->route('dashboard')->with('success', 'Заклад "' . $establishment->name . '" успешно оновлено!');
    }


    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'menu_pdf' => 'required|file|mimes:pdf|max:10240', // Обмеження до 10 МБ
        ], [
            'menu_pdf.required' => 'Будь ласка, виберіть файл.',
            'menu_pdf.mimes'    => 'Меню має бути виключно у форматі PDF.',
            'menu_pdf.max'      => 'Розмір файлу PDF не повинен перевищувати 10 МБ.',
        ]);

        $establishment = Establishment::findOrFail($id);

        // Перевірка прав: дію дозволено тільки адміну або фактичному власнику цього закладу
        if (!Auth::user()->isAdmin() && $establishment->user_id !== Auth::id()) {
            abort(403, 'Недостатньо прав для оновлення цифрового меню цього закладу.');
        }

        if ($request->hasFile('menu_pdf')) {
            // Якщо старий PDF-файл існує в сховищі, видаляємо його для економії місця
            if ($establishment->menu_pdf && Storage::disk('public')->exists($establishment->menu_pdf)) {
                Storage::disk('public')->delete($establishment->menu_pdf);
            }

            // Зберігаємо новий файл у папку storage/app/public/menus
            $path = $request->file('menu_pdf')->store('menus', 'public');

            // Оновлюємо шлях до файлу в базі даних
            $establishment->update(['menu_pdf' => $path]);
        }

        return redirect()->back()->with('success', 'Цифрове меню закладу успішно завантажено!');
    }
}
