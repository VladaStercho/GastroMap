<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $establishmentId)
    {
        // Перевіряємо, чи користувач увійшов у систему
        if (!Auth::check()) {
            return back()->withErrors(['auth' => 'Тільки авторизовані користувачі можуть залишати відгуки.']);
        }

        // Валідація даних форми відповідно до ТЗ
        $request->validate([
            'rating_food' => 'required|integer|between:1,5',
            'rating_service' => 'required|integer|between:1,5',
            'rating_ambience' => 'required|integer|between:1,5',
            'text' => 'required|string|min:5|max:1000',
        ]);

        // Зберігаємо відгук у базу даних PostgreSQL
        Review::create([
            'user_id' => Auth::id(),
            'establishment_id' => $establishmentId,
            'rating_food' => $request->rating_food,
            'rating_service' => $request->rating_service,
            'rating_ambience' => $request->rating_ambience,
            'text' => $request->text,
            'is_approved' => true // Автоматично схвалюємо для демонстрації
        ]);

        return back()->with('success', 'Дякуємо! Ваш відгук успішно додано.');
    }
}
