<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $establishment->name ?? 'Перегляд закладу' }} - ГастроМапа</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style type="text/tailwindcss">
        @theme {
            --animate-pulse: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @custom-variant dark (&:where(.dark, .dark *));
    </style>

    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const id = "{{ $establishment->id ?? 1 }}";
            let favorites = JSON.parse(localStorage.getItem('user_favorites')) || [];

            if (favorites.some(item => item.id == id)) {
                const icon = document.getElementById('fav-icon');
                const btn = document.getElementById('fav-btn');
                if(icon && btn) {
                    icon.classList.remove('fa-regular', 'text-gray-400', 'dark:text-gray-500');
                    icon.classList.add('fa-solid', 'text-red-500');
                    btn.classList.add('bg-red-50', 'dark:bg-red-950/20', 'border-red-200', 'dark:border-red-900/50');
                }
            }
        });

        function toggleFavorite() {
            const id = "{{ $establishment->id ?? 1 }}";
            const name = "{{ $establishment->name ?? 'Заклад без назви' }}";
            const address = "{{ $establishment->address ?? 'Адреса відсутня' }}";
            const price = "{{ $establishment->average_check ?? '0' }} грн";

            let favorites = JSON.parse(localStorage.getItem('user_favorites')) || [];
            const index = favorites.findIndex(item => item.id == id);
            const icon = document.getElementById('fav-icon');
            const btn = document.getElementById('fav-btn');

            if (index > -1) {
                favorites.splice(index, 1);
                icon.classList.remove('fa-solid', 'text-red-500');
                icon.classList.add('fa-regular', 'text-gray-400', 'dark:text-gray-500');
                btn.classList.remove('bg-red-50', 'dark:bg-red-950/20', 'border-red-200', 'dark:border-red-900/50');
            } else {
                favorites.push({ id, name, address, price });
                icon.classList.remove('fa-regular', 'text-gray-400', 'dark:text-gray-500');
                icon.classList.add('fa-solid', 'text-red-500');
                btn.classList.add('bg-red-50', 'dark:bg-red-950/20', 'border-red-200', 'dark:border-red-900/50');
            }

            localStorage.setItem('user_favorites', JSON.stringify(favorites));
        }
    </script>

    <style>
        .slides-container {
            transition: transform 0.5s ease-in-out;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .theme-sun, .theme-moon { display: none; }
        html:not(.dark) .theme-sun  { display: block; }
        html.dark .theme-moon       { display: block; }
    </style>
</head>
<body class="bg-gray-50/50 dark:bg-gray-950 font-sans antialiased min-h-screen text-gray-800 dark:text-gray-200 transition-colors duration-200">

    <nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-xs h-16 flex items-center justify-between px-6 sticky top-0 z-40 border-b border-gray-100 dark:border-gray-800">
        <a href="/" class="text-xl font-black text-orange-600 dark:text-orange-500 tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-utensils text-lg"></i>ГастроМапа
        </a>
        <div class="flex items-center gap-4">
            <button onclick="toggleTheme()" type="button" aria-label="Перемкнути тему" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-orange-500 dark:hover:text-orange-400 border border-gray-200 dark:border-gray-700 transition cursor-pointer" title="Змінити тему">
                <i class="fa-solid fa-sun text-sm theme-sun"></i>
                <i class="fa-solid fa-moon text-sm theme-moon"></i>
            </button>

            <a href="/" aria-label="До карти" title="До карти" class="h-10 w-10 sm:w-auto sm:px-2 inline-flex items-center justify-center gap-1.5 rounded-xl text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i><span class="hidden sm:inline">До карти</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" aria-label="Мій Кабінет" title="Мій Кабінет" class="h-10 w-10 sm:w-auto sm:px-4 inline-flex items-center justify-center gap-2 rounded-xl text-sm font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:text-orange-600 border border-gray-200 dark:border-gray-700 transition"><i class="fa-solid fa-circle-user text-base"></i><span class="hidden sm:inline">Мій Кабінет</span></a>
            @else
                @if(Route::has('auth'))
                    <a href="{{ route('auth') }}" class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-black transition shadow-sm hover:shadow">Увійти</a>
                @else
                    <a href="/login" class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-black transition shadow-sm hover:shadow">Увійти</a>
                @endif
            @endauth
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8 space-y-8">

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-4 rounded-xl font-medium text-sm flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-900 text-rose-800 dark:text-rose-300 p-4 rounded-xl font-medium text-sm flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i> {{ session('error') }}
            </div>
        @endif

        {{-- ПІДГОТОВКА ФОТОГРАФІЙ --}}
        @php
            $displayPhotos = [];
            if (!empty($establishment->photos) && is_array($establishment->photos)) {
                foreach ($establishment->photos as $p) {
                    $displayPhotos[] = asset('storage/' . $p);
                }
            }

            $fallbacks = [
                "https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=1200&q=80",
                "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80",
                "https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=1200&q=80"
            ];

            for ($i = count($displayPhotos); $i < 3; $i++) {
                $displayPhotos[] = $fallbacks[$i] ?? $fallbacks[0];
            }
        @endphp

        <div class="bg-white dark:bg-gray-900 p-3 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 space-y-3">
            <div class="flex items-center justify-between px-2 pt-1">
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                    <i class="fa-regular fa-images"></i> Інтер'єр та страви
                </span>
                <span class="text-xs bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 px-3 py-1 rounded-full font-black uppercase tracking-wide">
                    @php $estType = $establishment->type ?? 'cafe'; @endphp
                    @if($estType == 'cafe') <i class="fa-solid fa-mug-saucer"></i> Кав'ярня @elseif($estType == 'restaurant') <i class="fa-solid fa-utensils"></i> Ресторан @else <i class="fa-solid fa-beer-mug-empty"></i> Паб @endif
                </span>
            </div>

            {{-- Десктопна галерея --}}
            <div class="hidden md:grid grid-cols-3 gap-3 min-h-[380px] max-h-[420px] rounded-2xl overflow-hidden">
                <div class="col-span-2 bg-gray-900 flex items-center justify-center relative group overflow-hidden cursor-pointer" onclick="openLightbox(0)">
                    <img src="{{ $displayPhotos[0] }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500" alt="Основне photo">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/20 transition duration-300 flex items-center justify-center">
                        <i class="fa-solid fa-magnifying-glass-plus text-white text-3xl opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition duration-300"></i>
                    </div>
                </div>

                <div class="grid grid-rows-2 gap-3 h-full">
                    <div class="bg-gray-900 flex items-center justify-center overflow-hidden relative group cursor-pointer" onclick="openLightbox(1)">
                        <img src="{{ $displayPhotos[1] }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500" alt="Зал">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-300 flex items-center justify-center">
                            <i class="fa-solid fa-magnifying-glass-plus text-white text-2xl opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition duration-300"></i>
                        </div>
                    </div>
                    <div class="bg-gray-900 flex items-center justify-center overflow-hidden relative group cursor-pointer" onclick="openLightbox(2)">
                        <img src="{{ $displayPhotos[2] }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500" alt="Деталі">
                        <div class="absolute inset-0 bg-black/30 group-hover:bg-black/50 transition duration-300 flex flex-col items-center justify-center text-white gap-1.5">
                            <i class="fa-solid fa-expand text-xl"></i>
                            <span class="font-bold text-xs uppercase tracking-wider">Дивитись усі ({{ count($displayPhotos) }})</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Мобільний слайдер --}}
            <div class="block md:hidden relative w-full h-64 overflow-hidden rounded-2xl bg-gray-900 mobile-detail-slider" data-current="0">
                <div class="flex h-full slides-container" style="width: {{ count($displayPhotos) * 100 }}%;">
                    @foreach($displayPhotos as $index => $photoUrl)
                        <div class="h-full flex items-center justify-center bg-gray-900 cursor-pointer" style="width: {{ 100 / count($displayPhotos) }}%;" onclick="openLightbox({{ $index }})">
                            <img src="{{ $photoUrl }}" class="w-full h-full object-cover" alt="Слайд {{ $index + 1 }}">
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="changeMobileSlide(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/60 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm backdrop-blur-xs shadow-xs"><i class="fa-solid fa-chevron-left"></i></button>
                <button type="button" onclick="changeMobileSlide(1)" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/60 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm backdrop-blur-xs shadow-xs"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                                    {{ $establishment->name ?? 'Заклад без назви' }}
                                </h1>

                                @auth
                                    <button type="button" onclick="toggleFavorite()" id="fav-btn" class="w-10 h-10 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40 text-gray-400 dark:text-gray-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition flex items-center justify-center cursor-pointer text-base shadow-2xs" title="Додати до обраного">
                                        <i class="fa-regular fa-heart" id="fav-icon"></i>
                                    </button>
                                @endauth
                            </div>

                            {{-- Адреса закладу + Секретне текстове посилання для адміністратора та власника --}}
                            <div class="flex flex-col sm:flex-row sm:items-center gap-x-3 gap-y-1">
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-orange-500 text-base"></i> {{ $establishment->address ?? 'Адреса відсутня' }}
                                </p>

                                {{-- ВИПРАВЛЕНО: Адмін бачить кнопку абсолютно скрізь, а власник — тільки на своїх закладах --}}
                                @if(Auth::check() && (strtolower(Auth::user()->role) === 'admin' || (strtolower(Auth::user()->role) === 'owner' && $establishment->user_id === Auth::id())))
                                    <span class="hidden sm:inline text-gray-300 dark:text-gray-700">|</span>
                                    @if(strtolower(Auth::user()->role) === 'admin')
                                        <a href="{{ route('admin.establishment.edit', $establishment->id) }}"
                                           class="text-[11px] font-medium text-blue-500/80 hover:text-blue-500 hover:underline transition duration-150 tracking-wide">
                                            редагувати
                                        </a>
                                    @else
                                        <a href="{{ route('owner.establishment.edit', $establishment->id) }}"
                                           class="text-[11px] font-medium text-blue-500/80 hover:text-blue-500 hover:underline transition duration-150 tracking-wide">
                                            редагувати
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0">
                            {{-- СТАТУС ВІДЧИНЕНО/ЗАЧИНЕНО --}}
                            @if($isOpen)
                                <span class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-100 dark:border-emerald-900 text-emerald-700 dark:text-emerald-400 px-4 py-2 rounded-full text-xs font-black flex items-center gap-2 shadow-xs w-fit">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> ВІДЧИНЕНО
                                </span>
                            @else
                                <span class="bg-rose-50 dark:bg-rose-950/50 border border-rose-100 dark:border-rose-900 text-rose-600 dark:text-rose-400 px-4 py-2 rounded-full text-xs font-black flex items-center gap-2 shadow-xs w-fit">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span> ЗАЧИНЕНО
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-gray-50/70 dark:bg-gray-800/40 p-4 rounded-2xl border border-gray-100/50 dark:border-gray-800 text-center sm:text-left sm:flex sm:gap-8 text-sm">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5">Середній чек</span>
                            <span class="font-black text-gray-900 dark:text-white text-base">{{ $establishment->average_check ?? '0' }} грн</span>
                        </div>
                        <div class="hidden sm:block border-r border-gray-200 dark:border-gray-700 my-1"></div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5">Рейтинг закладу</span>
                            <span class="font-black text-orange-600 dark:text-orange-500 text-base flex items-center justify-center sm:justify-start gap-1">
                                <i class="fa-solid fa-star text-xs text-orange-500"></i>
                                {{ isset($establishment->reviews) && $establishment->reviews->count() > 0 ? '4.8' : '0.0' }}
                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">({{ isset($establishment->reviews) ? $establishment->reviews->count() : 0 }} відгуків)</span>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-xs font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-align-left"></i> Про заклад
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed bg-amber-50/30 dark:bg-amber-950/20 p-4 rounded-2xl border border-amber-100/30 dark:border-amber-900/30 italic">
                            {{ $establishment->description ?: 'Для цього закладу власник ще не додав розлогий текстовий опис. Завітайте за вказаною адресою, щоб особисто оцінити кухню та атмосферу!' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 space-y-4">
                    <h3 class="text-xs font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1.5 pb-2 border-b border-gray-100 dark:border-gray-800">
                        <i class="fa-solid fa-layer-group"></i> Характеристики та сервіси
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl transition duration-200 border {{ ($establishment->has_wifi ?? false) ? 'bg-emerald-50/30 dark:bg-emerald-950/20 border-emerald-100/50 dark:border-emerald-900/50 text-gray-800 dark:text-gray-200' : 'bg-gray-50/50 dark:bg-gray-800/20 border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 line-through select-none' }}">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ ($establishment->has_wifi ?? false) ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-200 dark:bg-gray-800 text-gray-400' }}">
                                <i class="fa-solid fa-wifi"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wide">Бездротовий Wi-Fi</span>
                        </div>
                        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl transition duration-200 border {{ ($establishment->has_terrace ?? false) ? 'bg-emerald-50/30 dark:bg-emerald-950/20 border-emerald-100/50 dark:border-emerald-900/50 text-gray-800 dark:text-gray-200' : 'bg-gray-50/50 dark:bg-gray-800/20 border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 line-through select-none' }}">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ ($establishment->has_terrace ?? false) ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-200 dark:bg-gray-800 text-gray-400' }}">
                                <i class="fa-solid fa-umbrella-beach"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wide">Літня тераса</span>
                        </div>
                        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl transition duration-200 border {{ ($establishment->is_pet_friendly ?? false) ? 'bg-emerald-50/30 dark:bg-emerald-950/20 border-emerald-100/50 dark:border-emerald-900/50 text-gray-800 dark:text-gray-200' : 'bg-gray-50/50 dark:bg-gray-800/20 border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 line-through select-none' }}">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ ($establishment->is_pet_friendly ?? false) ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-200 dark:bg-gray-800 text-gray-400' }}">
                                <i class="fa-solid fa-dog"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wide">Дозволено з тваринами</span>
                        </div>
                        <div class="flex items-center gap-3.5 p-3.5 rounded-2xl transition duration-200 border {{ ($establishment->laptop_friendly ?? false) ? 'bg-emerald-50/30 dark:bg-emerald-950/20 border-emerald-100/50 dark:border-emerald-900/50 text-gray-800 dark:text-gray-200' : 'bg-gray-50/50 dark:bg-gray-800/20 border-gray-100 dark:border-gray-800 text-gray-400 dark:text-gray-600 line-through select-none' }}">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm {{ ($establishment->laptop_friendly ?? false) ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-200 dark:bg-gray-800 text-gray-400' }}">
                                <i class="fa-solid fa-laptop"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wide">Робота за ноутбуком</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- РОЗКЛАД РОБОТИ --}}
                <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 space-y-4">
                    <h3 class="text-xs font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-2 pb-1 border-b border-gray-50 dark:border-gray-800">
                        <i class="fa-regular fa-clock text-orange-500 text-sm"></i> Розклад годин роботи
                    </h3>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-gray-50/70 dark:bg-gray-800/20 border border-gray-100/50 dark:border-gray-800/50 {{ !$isWeekend ? 'border-l-4 border-orange-500 pl-2' : '' }}">
                            <span class="font-bold text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-days text-[11px] text-gray-400"></i> Будні (Пн — Пт)
                            </span>
                            <span class="font-black text-gray-900 dark:text-gray-200 text-sm">
                                {{ $weekdayOpen }} – {{ $weekdayClose }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-gray-50/70 dark:bg-gray-800/20 border border-gray-100/50 dark:border-gray-800/50 {{ $isWeekend ? 'border-l-4 border-orange-500 pl-2' : '' }}">
                            <span class="font-bold text-gray-600 dark:text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-umbrella-beach text-[11px] text-gray-400"></i> Вихідні (Сб — Нд)
                            </span>
                            <span class="font-black text-gray-900 dark:text-gray-200 text-sm">
                                {{ $weekendOpen }} – {{ $weekendClose }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ЦИФРОВЕ МЕНЮ (ПЕРЕГЛЯД ТА ЗАВАНТАЖЕННЯ) --}}
                <div class="bg-white dark:bg-gray-900 p-5 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 text-center space-y-4">
                    <div class="w-12 h-12 bg-orange-50 dark:bg-orange-950/30 text-orange-500 rounded-2xl flex items-center justify-center mx-auto text-xl shadow-xs">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-black text-gray-900 dark:text-white">Цифрове меню закладу</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 max-w-[220px] mx-auto leading-normal">Переглядайте актуальні страви та ціни в повноекранному режимі</p>
                    </div>

                    @if(!empty($establishment->menu_pdf))
                        <button type="button" onclick="openMenuModal()" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 rounded-2xl text-xs transition shadow-sm hover:shadow active:scale-98 flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                            <i class="fa-solid fa-file-pdf text-sm"></i> Відкрити онлайн-меню
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 font-bold py-3 rounded-2xl text-xs flex items-center justify-center gap-2 cursor-not-allowed uppercase tracking-wider">
                            <i class="fa-solid fa-ban text-sm"></i> Меню відсутнє
                        </button>
                    @endif

                    {{-- Секретна форма завантаження меню для Адміна або Власника (Owner) --}}
                    @if(Auth::check() && (strtolower(Auth::user()->role) === 'admin' || (strtolower(Auth::user()->role) === 'owner' && $establishment->user_id === Auth::id())))
                        <div class="mt-2 pt-4 border-t border-gray-100 dark:border-gray-800 text-left">
                            <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Управління меню (Адмін/Власник)</span>

                            @if(Route::has('establishment.menu.update'))
                                <form action="{{ route('establishment.menu.update', $establishment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                            @else
                                <form action="/establishments/{{ $establishment->id }}/update-menu" method="POST" enctype="multipart/form-data" class="space-y-2">
                            @endif
                                @csrf
                                <label class="block w-full cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700/60 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-center transition">
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Оберіть PDF файл...</span>
                                    <input type="file" name="menu_pdf" accept="application/pdf" required class="hidden" onchange="this.form.submit()">
                                </label>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-amber-500 p-5 rounded-3xl text-center space-y-2 text-white shadow-sm">
                    <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center mx-auto text-sm backdrop-blur-md">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <p class="text-[11px] text-white/80 font-medium uppercase tracking-wider">Гаряча лінія / Резерв столів</p>
                    <a href="tel:{{ $establishment->phone ?? '+380501112233' }}" class="block text-lg font-black hover:underline tracking-wide">{{ $establishment->phone ?? '+38 (050) 111-2233' }}</a>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-3xl shadow-xs border border-gray-100 dark:border-gray-800 space-y-6">
            <h2 class="text-xl font-black text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-4 flex items-center gap-2">
                <i class="fa-regular fa-comments text-orange-500"></i> Відгуки користувачів
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <div class="lg:col-span-2 bg-gray-50/70 dark:bg-gray-800/40 p-5 md:p-6 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-5 h-fit">
                    <h3 class="font-black text-sm text-gray-800 dark:text-white uppercase tracking-wide">Залишити свій відгук</h3>
                    @auth
                        @if(Route::has('review.store'))
                            <form action="{{ route('review.store', $establishment->id) }}" method="POST" class="space-y-4">
                        @else
                            <form action="/establishments/{{ $establishment->id }}/reviews" method="POST" class="space-y-4">
                        @endif
                            @csrf
                            <div class="space-y-3">
                                <div class="flex justify-between items-center bg-white dark:bg-gray-900 p-2 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <label class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-wider pl-1"><i class="fa-solid fa-burger"></i> Кухня</label>
                                    <select name="rating_food" class="bg-gray-50 dark:bg-gray-800 border-0 rounded-lg p-1 text-xs font-black text-gray-700 dark:text-gray-300 focus:ring-0">
                                        <option value="5">★★★★★ 5</option>
                                        <option value="4">★★★★ 4</option>
                                        <option value="3">★★★ 3</option>
                                        <option value="2">★★ 2</option>
                                        <option value="1">★ 1</option>
                                    </select>
                                </div>
                                <div class="flex justify-between items-center bg-white dark:bg-gray-900 p-2 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <label class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-wider pl-1"><i class="fa-solid fa-bell-concierge"></i> Сервіс</label>
                                    <select name="rating_service" class="bg-gray-50 dark:bg-gray-800 border-0 rounded-lg p-1 text-xs font-black text-gray-700 dark:text-gray-300 focus:ring-0">
                                        <option value="5">★★★★★ 5</option>
                                        <option value="4">★★★★ 4</option>
                                        <option value="3">★★★ 3</option>
                                        <option value="2">★★ 2</option>
                                        <option value="1">★ 1</option>
                                    </select>
                                </div>
                                <div class="flex justify-between items-center bg-white dark:bg-gray-900 p-2 rounded-xl border border-gray-100 dark:border-gray-800">
                                    <label class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-wider pl-1"><i class="fa-solid fa-wand-magic-sparkles"></i> Атмосфера</label>
                                    <select name="rating_ambience" class="bg-gray-50 dark:bg-gray-800 border-0 rounded-lg p-1 text-xs font-black text-gray-700 dark:text-gray-300 focus:ring-0">
                                        <option value="5">★★★★★ 5</option>
                                        <option value="4">★★★★ 4</option>
                                        <option value="3">★★★ 3</option>
                                        <option value="2">★★ 2</option>
                                        <option value="1">★ 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <textarea name="text" rows="4" required class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-3 text-xs focus:outline-none focus:border-orange-500 transition placeholder:text-gray-400 dark:text-gray-200 resize-none" placeholder="Розкажіть детальніше про ваші враження..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 rounded-2xl text-xs transition uppercase tracking-wider shadow-xs hover:shadow cursor-pointer">Надіслати</button>
                        </form>
                    @else
                        <div class="text-center py-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 space-y-2.5 px-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium leading-normal">Тільки зареєстровані гості можуть ставити оцінки закладам.</p>
                            @if(Route::has('auth'))
                                <a href="{{ route('auth') }}" class="inline-block text-xs font-black text-orange-500 hover:text-orange-600 transition uppercase tracking-wider">Авторизуватися <i class="fa-solid fa-arrow-right-long"></i></a>
                            @else
                                <a href="/login" class="inline-block text-xs font-black text-orange-500 hover:text-orange-600 transition uppercase tracking-wider">Авторизуватися <i class="fa-solid fa-arrow-right-long"></i></a>
                            @endif
                        </div>
                    @endauth
                </div>

                <div class="lg:col-span-3 space-y-4 max-h-[460px] overflow-y-auto pr-2 custom-scrollbar">
                    @if(isset($establishment->reviews) && $establishment->reviews->count() > 0)
                        @foreach($establishment->reviews as $rev)
                            <div class="p-4 bg-gray-50/60 dark:bg-gray-800/30 border border-gray-100 dark:border-gray-800 rounded-2xl space-y-3 text-xs transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center font-black text-orange-700 dark:text-orange-400 text-[11px] border border-orange-100 dark:border-orange-900">
                                            {{ mb_substr($rev->user->name ?? 'Г', 0, 1) }}
                                        </div>
                                        <span class="font-black text-gray-800 dark:text-gray-200 text-sm">{{ $rev->user->name ?? 'Гість платформи' }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-mono font-bold">{{ $rev->created_at ? $rev->created_at->format('d.m.Y H:i') : now()->format('d.m.Y') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2.5 text-[10px] font-bold text-gray-500 dark:text-gray-400">
                                    <span class="bg-white dark:bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-100 dark:border-gray-800"><i class="fa-solid fa-burger"></i> Їжа: <b class="text-orange-600 dark:text-orange-500 ml-0.5">{{ $rev->rating_food ?? 5 }}/5</b></span>
                                    <span class="bg-white dark:bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-100 dark:border-gray-800"><i class="fa-solid fa-bell-concierge"></i> Сервіс: <b class="text-orange-600 dark:text-orange-500 ml-0.5">{{ $rev->rating_service ?? 5 }}/5</b></span>
                                    <span class="bg-white dark:bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-100 dark:border-gray-800"><i class="fa-solid fa-wand-magic-sparkles"></i> Зал: <b class="text-orange-600 dark:text-orange-500 ml-0.5">{{ $rev->rating_ambience ?? 5 }}/5</b></span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed italic bg-white dark:bg-gray-900 p-3 rounded-xl border border-gray-50 dark:border-gray-800/80">"{{ $rev->text }}"</p>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-20 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-2xl text-gray-400 dark:text-gray-600 space-y-2">
                            <div class="text-3xl"><i class="fa-regular fa-comment-dots text-gray-300 dark:text-gray-700"></i></div>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Поки що немає відгуків</p>
                            <p class="text-xs max-w-[240px] mx-auto">Будьте першим, хто поділися своєю чесною думкою про цей заклад!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="menu-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300">
        <div class="bg-white dark:bg-gray-900 rounded-3xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden shadow-2xl border border-white/20 animate-in fade-in zoom-in-95 duration-200">
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 text-gray-800 dark:text-gray-200">
                    <i class="fa-solid fa-file-pdf text-orange-500 text-lg"></i>
                    <span class="text-sm font-black uppercase tracking-wide font-mono">Цифрове меню закладу.pdf</span>
                </div>
                <button type="button" onclick="closeMenuModal()" class="w-8 h-8 rounded-full bg-gray-200/70 dark:bg-gray-700 hover:bg-rose-500 hover:text-white transition flex items-center justify-center text-xl font-bold cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="flex-1 bg-gray-100 dark:bg-gray-950 relative">
                @if(!empty($establishment->menu_pdf))
                    <iframe src="{{ asset('storage/' . $establishment->menu_pdf) }}#toolbar=0" class="w-full h-full border-0" allow="autoplay"></iframe>
                @endif
            </div>
        </div>
    </div>

    <div id="lightbox" class="hidden fixed inset-0 bg-black/95 z-50 flex items-center justify-center select-none backdrop-blur-md">
        <button onclick="closeLightbox()" class="absolute top-4 right-6 text-white/70 hover:text-white text-4xl font-light p-2 transition cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        <button onclick="navigateLightbox(-1)" class="absolute left-4 text-white/50 hover:text-white text-4xl p-4 transition cursor-pointer"><i class="fa-solid fa-chevron-left"></i></button>
        <div class="max-w-[90vw] max-h-[85vh] flex items-center justify-center">
            <img id="lightbox-img" src="" class="max-w-full max-h-[85vh] object-contain rounded-md shadow-2xl transition duration-200" alt="Повноекранне фото">
        </div>
        <button onclick="navigateLightbox(1)" class="absolute right-4 text-white/50 hover:text-white text-4xl p-4 transition cursor-pointer"><i class="fa-solid fa-chevron-right"></i></button>
        <div id="lightbox-counter" class="absolute bottom-6 text-white/60 text-xs font-mono tracking-widest bg-white/10 px-3 py-1 rounded-full backdrop-blur-xs"></div>
    </div>

    <script>
        function openMenuModal() {
            document.getElementById('menu-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeMenuModal() {
            document.getElementById('menu-modal').classList.add('hidden');
            if (document.getElementById('lightbox').classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
        }

        const imagesArray = @json($displayPhotos);
        let currentImageIndex = 0;

        function openLightbox(index) {
            currentImageIndex = index;
            document.getElementById('lightbox').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updateLightboxContent();
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.add('hidden');
            if(!document.getElementById('menu-modal').classList.contains('hidden')) return;
            document.body.style.overflow = '';
        }
        function navigateLightbox(direction) {
            currentImageIndex += direction;
            if (currentImageIndex >= imagesArray.length) currentImageIndex = 0;
            if (currentImageIndex < 0) currentImageIndex = imagesArray.length - 1;
            updateLightboxContent();
        }
        function updateLightboxContent() {
            const imgElement = document.getElementById('lightbox-img');
            const counterElement = document.getElementById('lightbox-counter');
            imgElement.src = imagesArray[currentImageIndex];
            counterElement.innerText = `${currentImageIndex + 1} / ${imagesArray.length}`;
        }

        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('lightbox').classList.contains('hidden')) {
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') navigateLightbox(-1);
                if (e.key === 'ArrowRight') navigateLightbox(1);
            } else if (!document.getElementById('menu-modal').classList.contains('hidden')) {
                if (e.key === 'Escape') closeMenuModal();
            }
        });

        function changeMobileSlide(direction) {
            const sliderRoot = document.querySelector('.mobile-detail-slider');
            if (!sliderRoot) return;
            const container = sliderRoot.querySelector('.slides-container');
            let current = parseInt(sliderRoot.getAttribute('data-current')) || 0;
            current += direction;

            const totalSlides = imagesArray.length;
            if (current >= totalSlides) current = 0;
            if (current < 0) current = totalSlides - 1;

            sliderRoot.setAttribute('data-current', current);
            container.style.transform = `translateX(-${current * (100 / totalSlides)}%)`;
        }
    </script>
</body>
</html>
