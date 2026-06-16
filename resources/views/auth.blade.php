<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизація - ГастроМапа</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @custom-variant dark (&:where(.dark, .dark *));
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        input:focus { outline: none; }
        /* Ховаємо скролбар для красивішого вигляду на мобільному */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-950 font-sans min-h-screen flex flex-col md:flex-row items-center justify-center md:p-4 transition-colors duration-300">

    <!-- Головний контейнер: на весь екран для мобільних, фіксований розмір для десктопу -->
    <div class="w-full min-h-screen md:min-h-[600px] md:h-auto md:max-w-4xl bg-white dark:bg-gray-900 md:rounded-2xl shadow-none md:shadow-2xl overflow-hidden flex flex-col md:flex-row transition-colors duration-300">
        
        <!-- Ліва частина (Зображення) - Зверху на моб (28vh), зліва на десктопі (50%) -->
        <div class="w-full h-[28vh] md:h-auto md:w-1/2 bg-gray-900 relative flex flex-col items-center justify-center p-6 md:p-8 shrink-0">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&q=80" alt="Restaurant Background" class="absolute inset-0 w-full h-full object-cover opacity-50 md:opacity-40">
            
            <!-- Хрестик для мобільних (на фоні картинки) -->
            <a href="/" class="md:hidden absolute top-4 right-4 text-white/80 hover:text-white transition w-8 h-8 flex items-center justify-center bg-black/30 rounded-full backdrop-blur-md z-20">
                <i class="fa-solid fa-xmark"></i>
            </a>

            <div class="relative z-10 text-center text-white flex flex-row md:flex-col items-center gap-4 md:gap-0 mt-4 md:mt-0">
                <div class="w-14 h-14 md:w-20 md:h-20 bg-orange-500 rounded-2xl flex items-center justify-center md:mx-auto md:mb-4 shadow-lg shadow-orange-500/30">
                    <i class="fa-solid fa-utensils text-2xl md:text-4xl"></i>
                </div>
                <div class="text-left md:text-center">
                    <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-0.5 md:mb-2 text-white drop-shadow-md">ГастроМапа</h1>
                    <p class="text-gray-200 md:text-gray-300 font-medium text-xs md:text-base drop-shadow-md">Ваш путівник світом смаків</p>
                </div>
            </div>
        </div>

        <!-- Права частина (Форми) - "Стягується" на картинку на мобільному -->
        <div class="flex-1 w-full md:w-1/2 bg-white dark:bg-gray-900 flex flex-col relative rounded-t-3xl md:rounded-none -mt-6 md:mt-0 z-10 shadow-[0_-8px_30px_-15px_rgba(0,0,0,0.3)] md:shadow-none overflow-y-auto hide-scrollbar transition-colors duration-300">
            
            <!-- Хрестик для десктопу -->
            <a href="/" class="hidden md:flex absolute top-6 right-6 text-gray-400 dark:text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 transition w-8 h-8 items-center justify-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full">
                <i class="fa-solid fa-xmark text-sm"></i>
            </a>

            <div class="m-auto w-full max-w-md p-6 py-8 md:p-12">
                
                <!-- Перемикач -->
                <div class="bg-gray-100 dark:bg-gray-800 p-1.5 rounded-xl flex items-center mb-6 md:mb-8 transition-colors duration-300">
                    <button type="button" onclick="switchForm('login')" id="btn-login" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all cursor-pointer">Вхід</button>
                    <button type="button" onclick="switchForm('register')" id="btn-register" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all cursor-pointer">Реєстрація</button>
                </div>

                <!-- Форма Входу -->
                <div id="form-login" class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Вхід у профіль</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Введіть свої дані для входу в систему каталогів.</p>
                    </div>

                    @if ($errors->has('email') && !old('name'))
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 p-3 rounded-lg text-sm border border-red-200 dark:border-red-800/50 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="email" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-orange-500 dark:focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Пароль</label>
                            <input type="password" name="password" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-orange-500 dark:focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                        </div>
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition shadow-md shadow-orange-500/20 mt-2 cursor-pointer">
                            Увійти
                        </button>
                    </form>
                </div>

                <!-- Форма Реєстрації -->
                <div id="form-register" class="space-y-6 hidden">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Створити акаунт</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Зареєструйтесь, щоб залишати відгуки та зберігати заклади.</p>
                    </div>

                    @if ($errors->any() && old('name'))
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 p-3 rounded-lg text-sm border border-red-200 dark:border-red-800/50 flex items-start gap-2">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ваше Ім'я</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-gray-900 dark:focus:border-gray-600 focus:ring-4 focus:ring-gray-900/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-gray-900 dark:focus:border-gray-600 focus:ring-4 focus:ring-gray-900/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Пароль (мін. 8)</label>
                                <input type="password" name="password" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-gray-900 dark:focus:border-gray-600 focus:ring-4 focus:ring-gray-900/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Підтвердження</label>
                                <input type="password" name="password_confirmation" required class="block w-full p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-900 focus:border-gray-900 dark:focus:border-gray-600 focus:ring-4 focus:ring-gray-900/10 text-gray-900 dark:text-white text-sm transition-colors duration-300">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-gray-900 dark:bg-white hover:bg-black dark:hover:bg-gray-200 text-white dark:text-gray-900 font-bold py-3.5 rounded-xl transition shadow-md shadow-gray-900/20 mt-2 cursor-pointer">
                            Зареєструватися
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function switchForm(type) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const btnLogin = document.getElementById('btn-login');
            const btnRegister = document.getElementById('btn-register');

            const activeClasses = ['bg-white', 'dark:bg-gray-700', 'shadow-sm', 'text-gray-900', 'dark:text-white'];
            const inactiveClasses = ['text-gray-500', 'dark:text-gray-400', 'hover:text-gray-900', 'dark:hover:text-gray-200'];

            if (type === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                
                btnLogin.classList.add(...activeClasses);
                btnLogin.classList.remove(...inactiveClasses);
                
                btnRegister.classList.remove(...activeClasses);
                btnRegister.classList.add(...inactiveClasses);
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                
                btnRegister.classList.add(...activeClasses);
                btnRegister.classList.remove(...inactiveClasses);
                
                btnLogin.classList.remove(...activeClasses);
                btnLogin.classList.add(...inactiveClasses);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            @if ($errors->any() && old('name'))
                switchForm('register');
            @else
                switchForm('login');
            @endif
        });
    </script>
</body>
</html>
