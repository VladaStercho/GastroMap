<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Особистий кабінет | ГастроМапа</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-main: #f9fafb;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #111827;
            --text-muted: #4b5563;
        }

        html.dark {
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --border-color: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
        }

        body { background-color: var(--bg-main) !important; color: var(--text-main) !important; }
        nav, .sidebar, .card { background-color: var(--bg-card) !important; border-color: var(--border-color) !important; }
    </style>

    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') document.documentElement.classList.add('dark');
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

        function switchTab(tabId, element) {

            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));


            const target = document.getElementById('tab-' + tabId);
            if (target) target.classList.remove('hidden');


            document.querySelectorAll('.sidebar-nav-btn').forEach(btn => {
                btn.classList.remove('bg-orange-500', 'text-white', 'font-bold', 'shadow-sm');
                btn.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-900', 'font-semibold');
            });


            element.classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-900', 'font-semibold');
            element.classList.add('bg-orange-500', 'text-white', 'font-bold', 'shadow-sm');
        }

        document.addEventListener("DOMContentLoaded", function() {
            const favorites = JSON.parse(localStorage.getItem('user_favorites')) || [];
            document.getElementById('fav-count-badge').innerText = favorites.length;

            const container = document.getElementById('favorites-list-container');
            if (favorites.length > 0) {
                container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>`;
                const grid = container.querySelector('div');
                favorites.forEach(item => {
                    grid.innerHTML += `
                        <div class="p-4 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-xs font-bold text-orange-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-utensils text-[10px]"></i> ${item.name}
                                </h4>
                                <p class="text-[11px] text-gray-400 mt-0.5"><i class="fa-solid fa-location-dot"></i> ${item.address}</p>
                                <span class="inline-block mt-2 text-[10px] bg-orange-500/10 text-orange-600 dark:text-orange-400 font-bold px-2 py-0.5 rounded-md">Сер. чек: ${item.price}</span>
                            </div>
                            <button onclick="removeFavorite(${item.id})" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center cursor-pointer text-xs">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    `;
                });
            }
        });

        function removeFavorite(id) {
            let favorites = JSON.parse(localStorage.getItem('user_favorites')) || [];
            favorites = favorites.filter(item => item.id !== id);
            localStorage.setItem('user_favorites', JSON.stringify(favorites));
            window.location.reload();
        }
    </script>
</head>
<body class="font-sans antialiased transition-colors duration-300 min-h-screen flex flex-col">

    {{-- НАВБАР --}}
    <nav class="border-b h-16 flex items-center justify-between px-6 sticky top-0 z-50 transition-colors duration-300">
        <a href="/" class="text-xl font-black text-orange-600 dark:text-orange-500 flex items-center gap-2 tracking-tight">
            <i class="fa-solid fa-utensils text-lg"></i>ГастроМапа
        </a>
        <div class="flex items-center gap-3">
            {{-- Бейдж ролі --}}
            @if(auth()->user()->isAdmin())
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-red-500/10 text-red-500 border border-red-500/20">
                    <i class="fa-solid fa-shield-halved mr-1"></i>Адмін
                </span>
            @elseif(auth()->user()->isOwner())
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/20">
                    <i class="fa-solid fa-store mr-1"></i>Власник
                </span>
            @else
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                    <i class="fa-solid fa-user mr-1"></i>Користувач
                </span>
            @endif

            <button type="button" onclick="toggleTheme()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition cursor-pointer">
                <i class="fa-solid fa-sun block dark:hidden text-sm"></i>
                <i class="fa-solid fa-moon hidden dark:block text-sm"></i>
            </button>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">
                    <i class="fa-solid fa-shield-halved text-xs"></i> Адмін-панель
                </a>
            @endif

            <a href="/" class="text-sm font-bold bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-xl flex items-center gap-2 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <i class="fa-solid fa-house text-xs"></i> На головну
            </a>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl font-bold transition shadow-md shadow-red-500/10 cursor-pointer">
                    Вийти
                </button>
            </form>
        </div>
    </nav>

    <div class="flex-1 max-w-7xl w-full mx-auto p-4 md:p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- САЙДБАР --}}
        <div class="sidebar border rounded-2xl p-5 flex flex-col gap-2 h-fit transition-colors duration-300 shadow-xs">
            <div class="flex flex-col items-center text-center pb-4 mb-2 border-b border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 rounded-full bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 flex items-center justify-center text-2xl font-black shadow-inner mb-3">
                    @php
                        $words = explode(' ', auth()->user()->name ?? 'Користувач');
                        $initials = mb_substr($words[0] ?? '', 0, 1);
                        if(isset($words[1])) $initials .= mb_substr($words[1], 0, 1);
                    @endphp
                    {{ mb_strtoupper($initials) }}
                </div>
                <h2 class="font-black text-lg leading-tight">{{ auth()->user()->name ?? 'Користувач' }}</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ auth()->user()->email ?? '' }}</p>

                {{-- Бейдж ролі в сайдбарі --}}
                @if(auth()->user()->isOwner())
                    <span class="mt-2 text-[10px] font-black px-2.5 py-1 rounded-full bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/20 uppercase tracking-wider">
                        <i class="fa-solid fa-store mr-1"></i>Власник закладу
                    </span>
                @elseif(auth()->user()->isAdmin())
                    <span class="mt-2 text-[10px] font-black px-2.5 py-1 rounded-full bg-red-500/10 text-red-500 border border-red-500/20 uppercase tracking-wider">
                        <i class="fa-solid fa-shield-halved mr-1"></i>Адміністратор
                    </span>
                @else
                    <span class="mt-2 text-[10px] font-black px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 uppercase tracking-wider">
                        <i class="fa-solid fa-user mr-1"></i>Користувач
                    </span>
                @endif
            </div>

            <button type="button" onclick="switchTab('profile', this)" class="sidebar-nav-btn w-full flex items-center gap-3 px-4 py-2.5 rounded-xl bg-orange-500 text-white font-bold text-sm transition shadow-sm cursor-pointer">
                <i class="fa-solid fa-user w-4 text-center"></i> Мій профіль
            </button>
            <button type="button" onclick="switchTab('reviews', this)" class="sidebar-nav-btn w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 font-semibold text-sm transition cursor-pointer">
                <i class="fa-solid fa-comment-dots w-4 text-center"></i> Мої відгуки
            </button>
            <button type="button" onclick="switchTab('favorites', this)" class="sidebar-nav-btn w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 font-semibold text-sm transition cursor-pointer">
                <i class="fa-solid fa-heart w-4 text-center"></i> Обрані заклади
            </button>

            {{-- Кнопка "Мої заклади" — тільки для власника --}}
            @if(auth()->user()->isOwner())
                <div class="mt-1 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="switchTab('establishments', this)" class="sidebar-nav-btn w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 font-semibold text-sm transition cursor-pointer">
                        <i class="fa-solid fa-store w-4 text-center"></i> Мої заклади
                    </button>
                    <a href="{{ route('owner.establishment.create') }}" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl bg-green-500/10 text-green-600 dark:text-green-400 hover:bg-green-500/20 font-bold text-sm transition cursor-pointer mt-1">
                        <i class="fa-solid fa-plus w-4 text-center"></i> Додати заклад
                    </a>
                </div>
            @endif
        </div>

        {{-- ОСНОВНИЙ КОНТЕНТ --}}
        <div class="lg:col-span-3">

            {{-- Flash-повідомлення --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-4 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            {{-- ВКЛАДКА: Профіль --}}
            <div id="tab-profile" class="tab-content space-y-6">
                <div class="card border rounded-2xl p-6 relative overflow-hidden transition-colors duration-300 shadow-xs">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-orange-500/10 rounded-full blur-xl"></div>
                    <h1 class="text-2xl font-black tracking-tight mb-2">Вітаємо, {{ auth()->user()->name ?? 'користувачу' }}! 👋</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xl leading-relaxed">
                        @if(auth()->user()->isOwner())
                            Ви маєте права <strong class="text-green-600 dark:text-green-400">Власника закладу</strong>. Ви можете додавати та редагувати свої заклади у ГастроМапі.
                        @elseif(auth()->user()->isAdmin())
                            Ви увійшли як <strong class="text-red-500">Адміністратор</strong>. Вам доступна повна адмін-панель.
                        @else
                            Ви успішно увійшли до свого особистого кабінету «ГастроМапа». Тут ви можете переглядати обрані заклади та залишати відгуки.
                        @endif
                    </p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="/" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition shadow-md shadow-orange-500/10 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot"></i> Переглянути карту закладів
                        </a>
                        @if(auth()->user()->isOwner())
                            <a href="{{ route('owner.establishment.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition shadow-md shadow-green-500/10 flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> Додати свій заклад
                            </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition shadow-md shadow-red-500/10 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i> Адмін-панель
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="card border rounded-2xl p-5 transition-colors duration-300 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-lg"><i class="fa-solid fa-star"></i></div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Залишено відгуків</span>
                            <span class="text-xl font-black">{{ auth()->user()->reviews ? auth()->user()->reviews->count() : 0 }}</span>
                        </div>
                    </div>
                    <div class="card border rounded-2xl p-5 transition-colors duration-300 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center text-lg"><i class="fa-solid fa-heart"></i></div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Улюблених місць</span>
                            <span class="text-xl font-black" id="fav-count-badge">0</span>
                        </div>
                    </div>
                    @if(auth()->user()->isOwner())
                        <div class="card border rounded-2xl p-5 transition-colors duration-300 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-green-500/10 text-green-500 flex items-center justify-center text-lg"><i class="fa-solid fa-store"></i></div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Моїх закладів</span>
                                <span class="text-xl font-black">{{ $myEstablishments->count() }}</span>
                            </div>
                        </div>
                    @else
                        <div class="card border rounded-2xl p-5 transition-colors duration-300 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center text-lg"><i class="fa-solid fa-user-tag"></i></div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Ваша роль</span>
                                <span class="text-sm font-black capitalize">{{ auth()->user()->role }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card border rounded-2xl p-6 transition-colors duration-300 space-y-4">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Основна інформація</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
                        <div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 rounded-xl">
                            <span class="text-gray-400 block mb-1">Ім'я користувача</span>
                            <span class="text-sm">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 rounded-xl">
                            <span class="text-gray-400 block mb-1">Електронна пошта</span>
                            <span class="text-sm">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 rounded-xl">
                            <span class="text-gray-400 block mb-1">Роль в системі</span>
                            <span class="text-sm font-bold
                                @if(auth()->user()->isAdmin()) text-red-500
                                @elseif(auth()->user()->isOwner()) text-green-600 dark:text-green-400
                                @else text-blue-600 dark:text-blue-400 @endif">
                                @if(auth()->user()->isAdmin()) <i class="fa-solid fa-shield-halved mr-1"></i>Адміністратор
                                @elseif(auth()->user()->isOwner()) <i class="fa-solid fa-store mr-1"></i>Власник закладу
                                @else <i class="fa-solid fa-user mr-1"></i>Користувач @endif
                            </span>
                        </div>
                        <div class="p-3 bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 rounded-xl">
                            <span class="text-gray-400 block mb-1">Дата реєстрації</span>
                            <span class="text-sm">{{ auth()->user()->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ВКЛАДКА: Відгуки --}}
            <div id="tab-reviews" class="tab-content space-y-4 hidden">
                <div class="card border rounded-2xl p-6 transition-colors duration-300 space-y-4">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Ваші відгуки</h3>

                    @if(auth()->user()->reviews && auth()->user()->reviews->count() > 0)
                        <div class="space-y-3">
                            @foreach(auth()->user()->reviews as $review)
                                <div class="p-4 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                    <div>
                                        <span class="text-xs font-bold text-orange-500">{{ $review->establishment->name ?? 'Заклад' }}</span>
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-0.5">"{{ $review->text ?? '' }}"</p>
                                        <div class="flex gap-2 mt-1 text-[10px] text-gray-400">
                                            <span>🍽 Їжа: {{ $review->rating_food }}/5</span>
                                            <span>👨‍💼 Сервіс: {{ $review->rating_service }}/5</span>
                                            <span>🎨 Атмосфера: {{ $review->rating_ambience }}/5</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500 text-xs shrink-0">
                                        @php $avg = round(($review->rating_food + $review->rating_service + $review->rating_ambience) / 3); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $avg ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-xl space-y-2">
                            <div class="text-gray-300 dark:text-gray-700 text-3xl"><i class="fa-regular fa-comment-dots"></i></div>
                            <h4 class="text-sm font-bold text-gray-500">У вас ще немає залишених відгуків</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-600 max-w-xs mx-auto">Напишіть свою думку про заклади на мапі.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ВКЛАДКА: Обрані --}}
            <div id="tab-favorites" class="tab-content space-y-4 hidden">
                <div class="card border rounded-2xl p-6 transition-colors duration-300 space-y-4">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Список обраного</h3>
                    <div id="favorites-list-container">
                        <div class="p-8 text-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-xl space-y-2">
                            <div class="text-gray-300 dark:text-gray-700 text-3xl"><i class="fa-regular fa-heart"></i></div>
                            <h4 class="text-sm font-bold text-gray-500">Список обраного порожній</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-600 max-w-xs mx-auto">Додавайте заклади в улюблені на мапі.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ВКЛАДКА: Мої заклади (тільки для owner) --}}
            @if(auth()->user()->isOwner())
            <div id="tab-establishments" class="tab-content space-y-4 hidden">
                <div class="card border rounded-2xl p-6 transition-colors duration-300 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Мої заклади</h3>
                        <a href="{{ route('owner.establishment.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold text-xs px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-md shadow-green-500/10">
                            <i class="fa-solid fa-plus"></i> Додати заклад
                        </a>
                    </div>

                    @forelse($myEstablishments as $est)
                        <div class="p-4 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold">{{ $est->name }}</h4>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                        @if($est->type === 'cafe') bg-amber-500/10 text-amber-600
                                        @elseif($est->type === 'restaurant') bg-orange-500/10 text-orange-600
                                        @else bg-purple-500/10 text-purple-600 @endif">
                                        {{ $est->type === 'cafe' ? 'Кав\'ярня' : ($est->type === 'restaurant' ? 'Ресторан' : 'Паб') }}
                                    </span>
                                    @if($est->is_approved)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-500/10 text-green-600 dark:text-green-400">
                                            <i class="fa-solid fa-circle-check mr-1"></i>Опубліковано
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-600">
                                            <i class="fa-solid fa-clock mr-1"></i>На модерації
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot text-[10px]"></i> {{ $est->address }}
                                </p>
                                @if($est->phone)
                                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                        <i class="fa-solid fa-phone text-[10px]"></i> {{ $est->phone }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span class="font-semibold text-orange-500">{{ $est->average_check }} ₴</span> середній чек
                                    <span class="mx-2">·</span>
                                    {{ $est->opening_time }} – {{ $est->closing_time }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('establishments.show', $est->id) }}" target="_blank"
                                   class="text-xs font-bold px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition flex items-center gap-1">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Переглянути
                                </a>
                                <a href="{{ route('owner.establishment.edit', $est->id) }}"
                                   class="text-xs font-bold px-3 py-2 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[10px]"></i> Редагувати
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-xl space-y-3">
                            <div class="text-gray-300 dark:text-gray-700 text-4xl"><i class="fa-solid fa-store"></i></div>
                            <h4 class="text-sm font-bold text-gray-500">У вас ще немає доданих закладів</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-600 max-w-xs mx-auto">Додайте свій заклад на мапу, щоб його знайшли тисячі гостей.</p>
                            <a href="{{ route('owner.establishment.create') }}" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-green-500/10">
                                <i class="fa-solid fa-plus"></i> Додати перший заклад
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

        </div>
    </div>

</body>
</html>
