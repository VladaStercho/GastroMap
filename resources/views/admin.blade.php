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
        <a href="/" class="text-xl font-bold text-orange-500 tracking-tight flex items-center gap-2">
            ГастроМапа Адмін
        </a>
        <div class="flex items-center gap-4">
            <button onclick="toggleTheme()" type="button" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-800 text-gray-300 hover:text-orange-500 transition cursor-pointer border border-gray-700">
                <i class="fa-solid fa-sun text-sm theme-sun"></i>
                <i class="fa-solid fa-moon text-sm theme-moon"></i>
            </button>

            <a href="/" class="text-sm text-gray-400 hover:text-white transition">На мапу</a>
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
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b text-gray-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Користувач</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Поточна роль</th>
                            <th class="py-3 px-4">Змінити роль</th>
                            <th class="py-3 px-4 text-right">Керування</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @if(isset($users) && $users->count() > 0)
                            @foreach($users as $user)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-white">
                                        @if(strtolower($user->role) === 'admin') 👤 @elseif(strtolower($user->role) === 'owner') 🟢 @else 🔥 @endif {{ $user->name }}
                                    </td>
                                    <td class="py-3 px-4">{{ $user->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-mono bg-slate-800 text-gray-300">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form id="role-form-{{ $user->id }}" action="{{ route('admin.user.role', $user->id) }}" method="POST" class="inline-flex gap-1.5 items-center">
                                            @csrf
                                            <select name="role" onchange="document.getElementById('role-form-{{ $user->id }}').submit();" class="border rounded px-2 py-1 bg-slate-900 text-white outline-none text-[11px] cursor-pointer">
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
                                                <button type="submit" class="text-red-500 font-bold hover:underline cursor-pointer">❌ Забанити</button>
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
                <table class="w-full text-left border-collapse text-xs">
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
            <h2 class="text-lg font-bold text-rose-400">💬 Стрічка останніх відгуків (Модерація спаму)</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
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
                <table class="w-full text-left border-collapse text-xs">
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
                                        <span class="bg-slate-800 text-[10px] px-1.5 py-0.5 rounded text-gray-400 ml-2 uppercase font-mono">{{ $est->type ?? 'заклад' }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-300">{{ $est->address }}</td>
                                    <td class="py-4 px-4 text-amber-400 font-bold">{{ $est->average_check ?? $est->price ?? '—' }} ₴</td>
                                    <td class="py-4 px-4 text-gray-400">
                                        {{ !empty($est->menu_pdf) ? '🟢 Наявне' : '⚫ Відсутнє' }}
                                    </td>
                                    <td class="py-4 px-4 text-right flex justify-end gap-2">
                                        <a href="/establishment/{{ $est->id }}" target="_blank" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs cursor-pointer text-center flex items-center">📄 Дивитись</a>
                                        <form action="{{ route('admin.establishment.delete', $est->id) }}" method="POST" onsubmit="return confirm('Видалити цей заклад?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 font-bold hover:underline text-xs cursor-pointer">Видалити</button>
                                        </form>
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
