<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $establishment ? 'Редагування: ' . $establishment->name : 'Додати заклад' }} | ГастроМапа</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root { --bg-main: #f9fafb; --bg-card: #ffffff; --border-color: #e5e7eb; --text-main: #111827; }
        html.dark { --bg-main: #0b0f19; --bg-card: #111827; --border-color: #1f2937; --text-main: #f3f4f6; }
        body { background-color: var(--bg-main) !important; color: var(--text-main) !important; }
        nav, .card { background-color: var(--bg-card) !important; border-color: var(--border-color) !important; }
        .form-input {
            display: block; width: 100%; padding: 0.625rem 0.875rem;
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 0.75rem; font-size: 0.875rem; outline: none;
            transition: border-color 0.15s;
        }
        .form-input:focus { border-color: #f97316 !important; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
    </style>

    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

    <nav class="border-b h-16 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="/" class="text-xl font-black text-orange-600 dark:text-orange-500 flex items-center gap-2">
            <i class="fa-solid fa-utensils text-lg"></i>ГастроМапа
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-sm font-bold bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-xl flex items-center gap-2 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Назад до кабінету
            </a>
        </div>
    </nav>

    <div class="flex-1 max-w-3xl w-full mx-auto p-4 md:p-8">

        <div class="mb-6">
            <h1 class="text-3xl font-black tracking-tight">
                {{ $establishment ? '✏️ Редагування закладу' : '🏪 Додати новий заклад' }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $establishment ? 'Оновіть інформацію про ваш заклад.' : 'Заповніть форму — після перевірки адміном заклад з\'явиться на мапі.' }}
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-xl text-sm mb-6">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ $establishment ? route('owner.establishment.update', $establishment->id) : route('owner.establishment.store') }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @if($establishment)
                @method('PUT')
            @endif

            {{-- Основна інформація --}}
            <div class="card border rounded-2xl p-6 space-y-4 shadow-xs">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-circle-info mr-2 text-orange-500"></i>Основна інформація
                </h2>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Назва закладу *</label>
                    <input type="text" name="name" value="{{ old('name', $establishment->name ?? '') }}"
                           class="form-input" placeholder="Наприклад: Кав'ярня «Сонячна»" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Тип закладу *</label>
                    <select name="type" class="form-input" required>
                        <option value="">— Оберіть тип —</option>
                        <option value="cafe" {{ old('type', $establishment->type ?? '') === 'cafe' ? 'selected' : '' }}>☕ Кав'ярня</option>
                        <option value="restaurant" {{ old('type', $establishment->type ?? '') === 'restaurant' ? 'selected' : '' }}>🍽 Ресторан</option>
                        <option value="pub" {{ old('type', $establishment->type ?? '') === 'pub' ? 'selected' : '' }}>🍺 Паб / Бар</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Опис</label>
                    <textarea name="description" rows="4" class="form-input" placeholder="Розкажіть про ваш заклад: кухня, атмосфера, особливості...">{{ old('description', $establishment->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Телефон</label>
                    <input type="text" name="phone" value="{{ old('phone', $establishment->phone ?? '') }}"
                           class="form-input" placeholder="+380501234567">
                </div>
            </div>

            {{-- Адреса та місцезнаходження --}}
            <div class="card border rounded-2xl p-6 space-y-4 shadow-xs">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-location-dot mr-2 text-orange-500"></i>Адреса
                </h2>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Адреса *</label>
                    <input type="text" name="address" value="{{ old('address', $establishment->address ?? '') }}"
                           class="form-input" placeholder="вул. Незалежності, 1, Ужгород" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Місто</label>
                    <input type="text" name="city" value="{{ old('city', $establishment->city ?? '') }}"
                           class="form-input" placeholder="Ужгород">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Широта (latitude)</label>
                        <input type="number" name="latitude" step="any"
                               value="{{ old('latitude', $establishment->latitude ?? '') }}"
                               class="form-input" placeholder="48.6208">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Довгота (longitude)</label>
                        <input type="number" name="longitude" step="any"
                               value="{{ old('longitude', $establishment->longitude ?? '') }}"
                               class="form-input" placeholder="22.2879">
                    </div>
                </div>
                <p class="text-[11px] text-gray-400">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Координати можна знайти на Google Maps: правою кнопкою → «Що тут?»
                </p>
            </div>

            {{-- Деталі --}}
            <div class="card border rounded-2xl p-6 space-y-4 shadow-xs">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-sliders mr-2 text-orange-500"></i>Деталі та графік
                </h2>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Середній чек (₴)</label>
                    <input type="number" name="average_check" min="0"
                           value="{{ old('average_check', $establishment->average_check ?? 0) }}"
                           class="form-input" placeholder="350">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Час відкриття</label>
                        <input type="time" name="opening_time"
                               value="{{ old('opening_time', $establishment->opening_time ?? '09:00') }}"
                               class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Час закриття</label>
                        <input type="time" name="closing_time"
                               value="{{ old('closing_time', $establishment->closing_time ?? '22:00') }}"
                               class="form-input">
                    </div>
                </div>
            </div>

            {{-- Зручності --}}
            <div class="card border rounded-2xl p-6 space-y-3 shadow-xs">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-star mr-2 text-orange-500"></i>Зручності
                </h2>

                @php
                    $amenities = [
                        'has_wifi'        => ['icon' => 'fa-wifi',         'label' => 'Wi-Fi'],
                        'has_terrace'     => ['icon' => 'fa-umbrella-beach','label' => 'Тераса'],
                        'is_pet_friendly' => ['icon' => 'fa-paw',           'label' => 'Можна з тваринами'],
                        'laptop_friendly' => ['icon' => 'fa-laptop',        'label' => 'Зручно з ноутбуком'],
                    ];
                @endphp

                <div class="grid grid-cols-2 gap-3">
                    @foreach($amenities as $field => $info)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">
                            <input type="checkbox" name="{{ $field }}" value="1"
                                   class="w-4 h-4 accent-orange-500 cursor-pointer"
                                   {{ old($field, $establishment->{$field} ?? false) ? 'checked' : '' }}>
                            <i class="fa-solid {{ $info['icon'] }} text-orange-500 text-sm"></i>
                            <span class="text-sm font-semibold">{{ $info['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="flex items-center justify-between gap-4 pb-4">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Скасувати
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-black px-8 py-3 rounded-xl transition shadow-lg shadow-orange-500/20 flex items-center gap-2 text-sm">
                    @if($establishment)
                        <i class="fa-solid fa-floppy-disk"></i> Зберегти зміни
                    @else
                        <i class="fa-solid fa-paper-plane"></i> Подати на модерацію
                    @endif
                </button>
            </div>

        </form>
    </div>

</body>
</html>
