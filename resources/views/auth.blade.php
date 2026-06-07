<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизація - ГастроМапа</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">

        <div class="p-8 justify-center flex flex-col">
            <h2 class="text-2xl font-bold text-gray-950 mb-2">Вхід у профіль</h2>
            <p class="text-sm text-gray-500 mb-6">Введіть свої дані для входу в систему каталогів.</p>

            @if ($errors->has('email') && !old('name'))
                <div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm mb-4 border border-red-200">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Пароль</label>
                    <input type="password" name="password" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl transition shadow-xs">
                    Увійти
                </button>
            </form>
        </div>

        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-950 mb-2">Створити акаунт</h2>
            <p class="text-sm text-gray-500 mb-6">Зареєструйтесь, щоб залишати відгуки та зберігати заклади.</p>

            @if ($errors->any() && old('name'))
                <div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm mb-4 border border-red-200">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase">Ваше Ім'я</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Пароль (мін. 6 знаків)</label>
                    <input type="password" name="password" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase">Підтвердження паролю</label>
                    <input type="password" name="password_confirmation" required class="mt-1 block w-full p-2.5 border border-gray-300 rounded-xl bg-white text-sm">
                </div>
                <button type="submit" class="w-full bg-gray-950 hover:bg-gray-900 text-white font-bold py-2.5 rounded-xl transition shadow-xs">
                    Зареєструватися
                </button>
            </form>
        </div>

    </div>

</body>
</html>
