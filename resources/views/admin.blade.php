<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ГастроМапа Адмін</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --border-color: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --table-hover: rgba(31, 41, 55, 0.4);
        }

        html.light {
            color-scheme: light;
            --bg-main: #f3f4f6;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #111827;
            --text-muted: #6b7280;
            --table-hover: #f9fafb;
        }

        body {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
        }

        nav, .universal-card, .admin-box {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        h2, h3, td, th, font, span, div {
            color: var(--text-main);
        }

        .text-gray-400, .text-gray-500, .text-gray-600, p {
            color: var(--text-muted) !important;
        }

        tr {
            border-color: var(--border-color) !important;
        }

        tr:hover {
            background-color: var(--table-hover) !important;
        }

        select, input {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }

        html:not(.light) .theme-sun { display: block !important; }
        html:not(.light) .theme-moon { display: none !important; }
        html.light .theme-sun { display: none !important; }
        html.light .theme-moon { display: block !important; }

        .nav-btn {
            background-color: var(--bg-main) !important;
            color: var(--text-muted) !important;
            border-color: var(--border-color) !important;
        }
        .nav-btn:hover {
            color: var(--text-main) !important;
            border-color: var(--text-muted) !important;
        }
    </style>

    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.remove('light');
            }
        })();

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('light')) {
                html.classList.remove('light');
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.add('light');
                localStorage.setItem('theme', 'light');
            }
        }
    </script>
</head>
<body class="font-sans antialiased min-h-screen transition-colors duration-200">

    <nav class="h-16 flex items-center justify-between px-6 border-b transition-colors duration-200">
        <a href="/" class="text-xl font-black text-orange-500 tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-utensils text-lg"></i>ГастроМапа Адмін
        </a>
        <div class="flex items-center gap-4">
            <button onclick="toggleTheme()" type="button" class="nav-btn w-9 h-9 flex items-center justify-center rounded-xl transition cursor-pointer border">
                <i class="fa-solid fa-sun text-sm theme-sun hover:text-orange-500"></i>
                <i class="fa-solid fa-moon text-sm theme-moon hover:text-orange-500"></i>
            </button>

            <a href="/" aria-label="На мапу" class="nav-btn text-sm font-bold w-9 h-9 sm:w-auto sm:px-4 flex items-center justify-center rounded-xl gap-2 transition border cursor-pointer">
                <i class="fa-solid fa-map-location-dot text-sm"></i> <span class="hidden sm:inline">На мапу</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-bold bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg cursor-pointer transition">Вийти</button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="admin-box p-6 rounded-xl border shadow-sm transition-colors duration-200">
                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Користувачі</span>
                <span class="text-3xl font-black text-white">
                    {{ isset($users) ? $users->count() : 0 }}
                </span>
            </div>
            <div class="admin-box p-6 rounded-xl border shadow-sm transition-colors duration-200">
                <span class="block text-xs font-bold text-green-500 uppercase tracking-wider">Заклади на мапі</span>
                <span class="text-3xl font-black text-green-400">
                    {{ isset($establishments) ? $establishments->where('is_approved', true)->count() : 0 }}
                </span>
            </div>
            <div class="admin-box p-6 rounded-xl border shadow-sm transition-colors duration-200">
                <span class="block text-xs font-bold text-amber-500 uppercase tracking-wider">Очікують перевірки</span>
                <span class="text-3xl font-black text-amber-400">
                    {{ isset($establishments) ? $establishments->where('is_approved', false)->count() : 0 }}
                </span>
            </div>
            <div class="admin-box p-6 rounded-xl border shadow-sm transition-colors duration-200">
                <span class="block text-xs font-bold text-blue-500 uppercase tracking-wider">Всього відгуків</span>
                <span class="text-3xl font-black text-blue-400">
                    {{ isset($reviews) ? $reviews->count() : 0 }}
                </span>
            </div>
        </div>

        <div class="admin-box p-6 rounded-xl shadow-sm border space-y-4 transition-colors duration-200">
            <h2 class="text-lg font-bold text-blue-400">Управління правами користувачів</h2>

            @if(session('success'))
                <div class="bg-green-950/50 border border-green-500 text-green-200 p-3 rounded-lg text-xs mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs [&_td]:align-middle">
                    <thead>
                        <tr class="border-b text-gray-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Користувач</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Роль</th>
                            <th class="py-3 px-4 text-right">Керування</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @if(isset($users) && $users->count() > 0)
                            @foreach($users as $user)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-white">
                                        @if(strtolower($user->role) === 'admin') <i class="fa-solid fa-user-shield text-orange-400"></i> @elseif(strtolower($user->role) === 'owner') <i class="fa-solid fa-user-tie text-green-500"></i> @else <i class="fa-solid fa-user text-gray-400"></i> @endif {{ $user->name }}
                                    </td>
                                    <td class="py-3 px-4">{{ $user->email }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $r = strtolower($user->role);
                                            $roleMap = [
                                                'admin' => ['Адмін', 'fa-user-shield', 'bg-rose-500/15 text-rose-600 border-rose-500/30'],
                                                'owner' => ['Власник', 'fa-user-tie', 'bg-emerald-500/15 text-emerald-600 border-emerald-500/30'],
                                            ];
                                            [$roleLabel, $roleIcon, $roleBadge] = $roleMap[$r] ?? ['Гість', 'fa-user', 'bg-slate-500/20 text-slate-500 border-slate-500/40'];
                                        @endphp
                                        <form id="role-form-{{ $user->id }}" action="{{ route('admin.user.role', $user->id) }}" method="POST" class="inline-flex gap-2 items-center">
                                            @csrf
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider {{ $roleBadge }}">
                                                <i class="fa-solid {{ $roleIcon }}"></i> {{ $roleLabel }}
                                            </span>
                                            <select name="role" onchange="document.getElementById('role-form-{{ $user->id }}').submit();" class="border border-slate-600 rounded-lg px-2 py-1 bg-slate-900 text-white outline-none text-[11px] cursor-pointer">
                                                <option value="user" {{ strtolower($user->role) === 'user' ? 'selected' : '' }}>Гість (User)</option>
                                                <option value="owner" {{ strtolower($user->role) === 'owner' ? 'selected' : '' }}>Власник (Owner)</option>
                                                <option value="admin" {{ strtolower($user->role) === 'admin' ? 'selected' : '' }}>Адмін (Admin)</option>
                                            </select>
                                            <button type="submit" class="hidden">Зберегти</button>
                                        </form>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if(Auth::id() !== $user->id)
                                            <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" onsubmit="return confirm('Видалити користувача?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 font-bold hover:underline cursor-pointer"><i class="fa-solid fa-ban"></i> Забанити</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 italic text-[11px]">Це Ви</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-4 text-sm text-gray-500 italic text-center">Користувачів у базі даних не знайдено.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-box p-6 rounded-xl shadow-sm border space-y-4 transition-colors duration-200">
            <h2 class="text-lg font-bold text-amber-500">Заявки на публікацію закладів</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs [&_td]:align-middle">
                    <tbody class="divide-y">
                        @if(isset($establishments) && $establishments->where('is_approved', false)->count() > 0)
                            @foreach($establishments->where('is_approved', false) as $est)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-white">{{ $est->name }}</td>
                                    <td class="py-3 px-4 text-gray-400">{{ $est->address }}</td>
                                    <td class="py-3 px-4 text-right flex justify-end gap-2">
                                        <form action="{{ route('admin.approve', $est->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs cursor-pointer">Схвалити</button>
                                        </form>
                                        <form action="{{ route('admin.establishment.delete', $est->id) }}" method="POST" onsubmit="return confirm('Видалити цей заклад?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 font-bold hover:underline text-xs cursor-pointer">Відхилити</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="py-4 text-sm text-gray-500 italic text-center">Нових заявок на модерацію немає.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-box p-6 rounded-xl shadow-sm border space-y-4 transition-colors duration-200">
            <h2 class="text-lg font-bold text-rose-400"><i class="fa-solid fa-comments"></i> Стрічка останніх відгуків (Модерація спаму)</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs [&_td]:align-middle">
                    <tbody class="divide-y">
                        @if(isset($reviews) && $reviews->count() > 0)
                            @foreach($reviews as $review)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-white">{{ $review->user->name ?? 'Гість' }}</td>
                                    <td class="py-3 px-4 text-orange-400 font-medium">{{ $review->establishment->name ?? 'Заклад' }}</td>
                                    <td class="py-3 px-4 text-gray-300 italic">"{{ $review->text }}"</td>
                                    <td class="py-3 px-4 text-right">
                                        <form action="{{ route('admin.review.delete', $review->id) }}" method="POST" onsubmit="return confirm('Видалити цей відгук?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 font-bold hover:underline cursor-pointer">Вилити</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="py-2 text-sm text-gray-500 italic text-center">Відгуків у базі даних ще немає.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-box p-6 rounded-xl shadow-sm border space-y-4 transition-colors duration-200">
            <h2 class="text-lg font-bold text-green-400">Повний реєстр закладів</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs [&_td]:align-middle">
                    <thead>
                        <tr class="border-b text-gray-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Назва</th>
                            <th class="py-3 px-4">Адреса</th>
                            <th class="py-3 px-4">Чек</th>
                            <th class="py-3 px-4">Статус меню</th>
                            <th class="py-3 px-4 text-right">Дія</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @if(isset($establishments) && $establishments->count() > 0)
                            @foreach($establishments as $est)
                                <tr>
                                    <td class="py-4 px-4 font-bold text-white">
                                        {{ $est->name }}
                                        @php
                                            $tt = strtolower($est->type ?? '');
                                            $tMap = [
                                                'cafe'       => ['Кафе', 'fa-mug-saucer', 'bg-amber-500/15 text-amber-600 border-amber-500/30'],
                                                'restaurant' => ['Ресторан', 'fa-utensils', 'bg-rose-500/15 text-rose-600 border-rose-500/30'],
                                                'pub'        => ['Паб', 'fa-beer-mug-empty', 'bg-violet-500/15 text-violet-600 border-violet-500/30'],
                                            ];
                                            [$tLabel, $tIcon, $tBadge] = $tMap[$tt] ?? [($est->type ?? 'заклад'), 'fa-shop', 'bg-slate-500/20 text-slate-500 border-slate-500/40'];
                                        @endphp
                                        <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-[10px] font-bold uppercase tracking-wider {{ $tBadge }}"><i class="fa-solid {{ $tIcon }} text-[9px]"></i>{{ $tLabel }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-300">{{ $est->address }}</td>
                                    <td class="py-4 px-4 text-amber-400 font-bold">{{ $est->average_check ?? $est->price ?? '—' }} ₴</td>
                                    <td class="py-4 px-4 text-gray-400">
                                        @if(!empty($est->menu_pdf))<i class="fa-solid fa-circle-check text-green-500"></i> Наявне @else<i class="fa-solid fa-circle-xmark text-gray-500"></i> Відсутнє @endif
                                    </td>
                                    <td class="py-4 px-4 align-middle">
                                        <div class="flex justify-end items-center gap-2">
                                            <a href="/establishment/{{ $est->id }}" target="_blank" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs cursor-pointer text-center flex items-center gap-1.5"><i class="fa-solid fa-file-lines"></i> Дивитись</a>
                                            <form action="{{ route('admin.establishment.delete', $est->id) }}" method="POST" onsubmit="return confirm('Видалити цей заклад?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 font-bold hover:underline text-xs cursor-pointer">Видалити</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-4 text-sm text-gray-500 italic text-center">Закладів у вашій базі даних ще немає.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
