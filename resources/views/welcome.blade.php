<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ГастроМапа - Каталог Закладів</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* НЕВБИВАНІ СИСТЕМНІ ЗМІННІ ДЛЯ ВСІХ БРАУЗЕРІВ */
        :root {
            --bg-main: #f9fafb;
            --bg-card: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #111827;
            --text-muted: #4b5563;
            --form-bg: #ffffff;
            --form-border: #d1d5db;
        }

        html.dark {
            --bg-main: #0b0f19;
            --bg-card: #111827;
            --border-color: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --form-bg: #1f2937;
            --form-border: #374151;
        }

        /* ГЛОБАЛЬНЕ ЗАСТОСУВАННЯ ЗМІННИХ (Перебиває будь-які заскоки Tailwind) */
        body {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
        }

        nav {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        .sidebar-container {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        #filterForm {
            border-color: var(--border-color) !important;
        }

        #filterForm input[type="text"],
        #filterForm select {
            background-color: var(--form-bg) !important;
            border-color: var(--form-border) !important;
            color: var(--text-main) !important;
        }

        .establishment-card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        .establishment-card h3 {
            color: var(--text-main) !important;
        }

        .establishment-card p {
            color: var(--text-muted) !important;
        }

        .establishment-card .border-t {
            border-color: var(--border-color) !important;
        }

        /* Стилізація скроллбару списку */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }
        html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        /* Попапи Leaflet */
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            padding: 6px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e2e8f0;
            background: #ffffff !important;
            color: #111827 !important;
        }
        .leaflet-popup-tip {
            background: #ffffff !important;
        }

        /* Інверсія карти в темній темі */
        html.dark .leaflet-tile-container {
            filter: invert(100%) hue-rotate(180deg) brightness(85%) contrast(90%);
        }
        html.dark .leaflet-container {
            background: #0b0f19 !important;
        }
        html.dark .leaflet-popup-content-wrapper {
            background: #111827 !important;
            color: #f3f4f6 !important;
            border-color: #1f2937;
        }
        html.dark .leaflet-popup-tip {
            background: #111827 !important;
        }

        /* Пульсуючий маркер користувача */
        .user-location-pulse {
            background-color: #3b82f6;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.4);
            position: relative;
        }
        html.dark .user-location-pulse {
            border-color: #111827;
        }
        .slides-container {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
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
    </script>
</head>
<body class="font-sans antialiased transition-colors duration-300">

    <nav class="border-b h-16 flex items-center justify-between px-6 sticky top-0 z-[1000] transition-colors duration-300">
        <a href="/" class="text-xl font-black text-orange-600 dark:text-orange-500 flex items-center gap-2 tracking-tight">
            <i class="fa-solid fa-utensils text-lg"></i>ГастроМапа
        </a>
        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleTheme()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition cursor-pointer">
                <i class="fa-solid fa-sun block dark:hidden text-sm"></i>
                <i class="fa-solid fa-moon hidden dark:block text-sm"></i>
            </button>

            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-bold transition bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-xl">
                    Мій Кабінет
                </a>
            @else
                <a href="{{ route('auth') }}" class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-bold transition shadow-md shadow-orange-500/10">
                    Увійти
                </a>
            @endauth
        </div>
    </nav>

    <div class="flex flex-col lg:flex-row h-[calc(100vh-4rem)]">

        <div class="sidebar-container w-full lg:w-96 border-r flex flex-col h-full shadow-xl z-10 transition-colors duration-300">

            <form action="{{ route('home') }}" method="GET" id="filterForm" class="p-4 border-b space-y-3 bg-gray-50/70 dark:bg-gray-950/20 transition-colors duration-300">
                <input type="hidden" name="lat" id="userLat" value="{{ request('lat') }}">
                <input type="hidden" name="lng" id="userLng" value="{{ request('lng') }}">

                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Що ви шукаєте?</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="w-full rounded-xl py-2 pl-9 pr-3 text-sm focus:outline-none focus:border-orange-500 dark:focus:border-orange-500 transition placeholder:text-gray-400 dark:placeholder:text-gray-600 font-medium"
                               placeholder="Введіть назву закладу...">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Місто</label>
                        <select name="city" class="w-full rounded-xl p-2 text-xs font-semibold focus:outline-none focus:border-orange-500">
                            <option value="">Всі міста</option>
                            <option value="Ужгород" {{ request('city') == 'Ужгород' ? 'selected' : '' }}>Ужгород</option>
                            <option value="Мукачево" {{ request('city') == 'Мукачево' ? 'selected' : '' }}>Мукачево</option>
                            <option value="Хуст" {{ request('city') == 'Хуст' ? 'selected' : '' }}>Хуст</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Тип закладу</label>
                        <select name="type" class="w-full rounded-xl p-2 text-xs font-semibold focus:outline-none focus:border-orange-500">
                            <option value="">Всі типи</option>
                            <option value="cafe" {{ request('type') == 'cafe' ? 'selected' : '' }}>Кав'ярня</option>
                            <option value="restaurant" {{ request('type') == 'restaurant' ? 'selected' : '' }}>Ресторан</option>
                            <option value="pub" {{ request('type') == 'pub' ? 'selected' : '' }}>Паб</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Середній чек</label>
                    <select name="check_range" class="w-full rounded-xl p-2 text-xs font-semibold focus:outline-none focus:border-orange-500">
                        <option value="">Будь-який чек</option>
                        <option value="low" {{ request('check_range') == 'low' ? 'selected' : '' }}>До 200 грн</option>
                        <option value="medium" {{ request('check_range') == 'medium' ? 'selected' : '' }}>200 - 500 грн</option>
                        <option value="high" {{ request('check_range') == 'high' ? 'selected' : '' }}>Більше 500 грн</option>
                    </select>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Зручності</label>
                    <label class="flex items-center gap-2.5 text-xs font-medium cursor-pointer select-none">
                        <input type="checkbox" name="has_wifi" value="1" {{ request('has_wifi') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-orange-500 focus:ring-0 w-4 h-4 bg-white dark:bg-gray-900"> Wi-Fi інтернет
                    </label>
                    <label class="flex items-center gap-2.5 text-xs font-medium cursor-pointer select-none">
                        <input type="checkbox" name="has_terrace" value="1" {{ request('has_terrace') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-orange-500 focus:ring-0 w-4 h-4 bg-white dark:bg-gray-900"> Літня тераса
                    </label>
                    <label class="flex items-center gap-2.5 text-xs font-medium cursor-pointer select-none">
                        <input type="checkbox" name="is_pet_friendly" value="1" {{ request('is_pet_friendly') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-orange-500 focus:ring-0 w-4 h-4 bg-white dark:bg-gray-900"> Дружні до тварин
                    </label>
                </div>

                <div class="grid grid-cols-5 gap-2 pt-1">
                    <button type="submit" class="col-span-3 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                         Фільтрувати
                    </button>
                    <button type="button" onclick="getLocation()" id="geoBtn" class="col-span-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-location-crosshairs"></i> <span id="geoBtnText">Локація</span>
                    </button>
                </div>
            </form>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar" id="establishmentsList">
                <h3 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest px-1">Знайдено закладів: {{ $establishments->count() }}</h3>

                @forelse($establishments as $index => $est)
                    @php
                        $cardLat = $est->latitude ?? $est->shirota ?? $est->широта ?? 0;
                        $cardLng = $est->longitude ?? $est->dovgota ?? $est->довгота ?? 0;
                        $estType = $est->type ?? $est->тип ?? 'cafe';
                        $estAddress = $est->address ?? $est->adresa ?? $est->адреса ?? 'Адреса відсутня';
                        $estCheck = $est->average_check ?? $est->середній_чек ?? 0;
                        $reviewsCount = $est->reviews ? $est->reviews->count() : 0;
                        $routeExist = Route::has('establishments.show') ? 'establishments.show' : (Route::has('establishment.show') ? 'establishment.show' : null);

                        // ПІДГОТОВКА ЗОБРАЖЕНЬ: Перевіряємо фото з бази, якщо немає — добираємо красиві Unsplash-заглушки
                        $cardPhotos = [];
                        if (!empty($est->photos) && is_array($est->photos)) {
                            foreach ($est->photos as $p) {
                                $cardPhotos[] = asset('storage/' . $p);
                            }
                        }

                        $fallbacks = [
                            "https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&q=80",
                            "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&q=80",
                            "https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=400&q=80"
                        ];

                        for ($i = count($cardPhotos); $i < 3; $i++) {
                            $cardPhotos[] = $fallbacks[$i];
                        }
                    @endphp

                    <div class="establishment-card border p-4 rounded-2xl hover:border-orange-400 dark:hover:border-orange-500/50 transition duration-200 cursor-pointer shadow-xs relative group" onclick="focusOnMap({{ $cardLat }}, {{ $cardLng }})">
                        <span class="absolute top-3 right-3 text-[10px] font-bold bg-orange-50 dark:bg-orange-950/50 text-orange-700 dark:text-orange-400 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            @if($estType == 'cafe' || $estType == 'кафе') Кафе @elseif($estType == 'restaurant' || $estType == 'ресторан') Ресторан @else Паб @endif
                        </span>

                        <h3 class="font-bold pr-14 text-base group-hover:text-orange-600 dark:group-hover:text-orange-500 transition-colors">{{ $est->name }}</h3>
                        <p class="text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-location-dot"></i> {{ $estAddress }}</p>

                        {{-- Оновлений слайдер картинок закладу --}}
                        <div class="relative w-full h-32 mt-3 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 interaction-slider" data-current="0">
                            <div class="flex h-full w-[300%] slides-container">
                                @foreach($cardPhotos as $pUrl)
                                    <div class="w-1/3 h-full">
                                        <img src="{{ $pUrl }}" class="w-full h-full object-cover" alt="Фото закладу">
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="event.stopPropagation(); changeCardSlide(this, -1)" class="absolute left-1.5 top-1/2 -translate-y-1/2 bg-black/30 dark:bg-black/50 hover:bg-black/70 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs select-none backdrop-blur-xs transition">&#10094;</button>
                            <button type="button" onclick="event.stopPropagation(); changeCardSlide(this, 1)" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-black/30 dark:bg-black/50 hover:bg-black/70 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs select-none backdrop-blur-xs transition">&#10095;</button>
                        </div>

                        <p class="text-xs font-semibold mt-2.5">Середній чек: <span class="text-orange-600 dark:text-orange-400 font-bold">{{ $estCheck }} грн</span></p>

                        @if(isset($est->distance))
                            <p class="text-[11px] font-bold text-green-600 dark:text-green-400 mt-1 flex items-center gap-1"><i class="fa-solid fa-route text-xs"></i> Відстань: {{ round($est->distance, 2) }} км</p>
                        @endif

                        <div class="mt-3 pt-2.5 border-t flex justify-between items-center">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $reviewsCount }} відгуків</span>
                            @if($routeExist)
                                <a href="{{ route($routeExist, $est->id) }}" onclick="event.stopPropagation();" class="text-xs font-bold text-orange-500 hover:text-orange-600 dark:hover:text-orange-400 underline flex items-center gap-0.5">
                                    Детальніше <i class="fa-solid fa-angle-right text-[10px]"></i>
                                </a>
                            @else
                                <a href="/establishment/{{ $est->id }}" onclick="event.stopPropagation();" class="text-xs font-bold text-orange-500 hover:text-orange-600 dark:hover:text-orange-400 underline flex items-center gap-0.5">
                                    Детальніше <i class="fa-solid fa-angle-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-orange-50/60 dark:bg-orange-950/10 border border-orange-100 dark:border-orange-900/30 p-5 rounded-2xl text-center space-y-2 mt-4">
                        <h4 class="font-bold text-sm">Закладів не знайдено</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                            У місті <span class="font-bold text-orange-600">{{ request('city') ?: 'обраному регіоні' }}</span> немає об'єктів з обраними фільтрами.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="map" class="flex-1 h-full z-0"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const searchLat = "{{ request('lat') }}";
        const searchLng = "{{ request('lng') }}";
        const currentCity = "{{ request('city') }}";

        let defaultLat = 48.6212; let defaultLng = 22.2978; let defaultZoom = 13;

        if (currentCity === 'Мукачево') {
            defaultLat = 48.4415; defaultLng = 22.7212; defaultZoom = 14;
        } else if (currentCity === 'Хуст') {
            defaultLat = 48.1794; defaultLng = 23.2982; defaultZoom = 14;
        }

        if (searchLat && searchLng) {
            defaultLat = parseFloat(searchLat); defaultLng = parseFloat(searchLng); defaultZoom = 14;
        }

        // Модифікуємо дані для JS, додаючи повні URL-адреси зображень сховища в JSON
        const establishments = @json($establishments).map(est => {
            let photos = [];
            if (est.photos && Array.isArray(est.photos)) {
                photos = est.photos.map(p => `{{ asset('storage') }}/${p}`);
            }
            if (photos.length === 0) {
                photos.push("https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&q=80");
            }
            est.compiled_first_photo = photos[0];
            return est;
        });

        if (establishments.length > 0 && !(searchLat && searchLng)) {
            const firstLat = establishments[0].latitude ?? establishments[0].shirota ?? establishments[0].широта;
            const firstLng = establishments[0].longitude ?? establishments[0].dovgota ?? establishments[0].довгота;
            if (firstLat && firstLng) {
                defaultLat = parseFloat(firstLat); defaultLng = parseFloat(firstLng); defaultZoom = 14;
            }
        }

        const map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let userLiveMarker = null;
        const blueUserIcon = L.divIcon({
            className: 'custom-user-marker',
            html: '<div class="user-location-pulse"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        if (searchLat && searchLng) {
            userLiveMarker = L.marker([parseFloat(searchLat), parseFloat(searchLng)], { icon: blueUserIcon }).addTo(map);
            L.circle([parseFloat(searchLat), parseFloat(searchLng)], { color: '#3b82f6', fillOpacity: 0.12, radius: 400, weight: 2 }).addTo(map);
        }

        const markers = {};
        establishments.forEach((est, index) => {
            const estLat = est.latitude ?? est.shirota ?? est.широта;
            const estLng = est.longitude ?? est.dovgota ?? est.довгота;
            const estCheck = est.average_check ?? est.середній_чек ?? 0;
            const estAddress = est.address ?? est.adresa ?? est.адреса ?? 'Адреса відсутня';

            if (!estLat || !estLng) return;

            const marker = L.marker([parseFloat(estLat), parseFloat(estLng)]).addTo(map);
            marker.bindPopup(`
                <div style="font-family: inherit; padding: 2px; max-width: 200px;">
                    <img src="${est.compiled_first_photo}" style="width: 100%; h-auto; max-height: 80px; object-cover: cover; border-radius: 8px; margin-bottom: 6px;" alt="">
                    <strong style="font-size: 14px; color: #ea580c; display: block; margin-bottom: 2px;">${est.name}</strong>
                    <span style="font-size: 11px; display: block; margin-bottom: 6px; color: #4b5563;">${estAddress}</span>
                    <span style="font-size: 12px; font-weight: 700; display: block;">Середній чек: <span style="color:#ea580c;">${estCheck} грн</span></span>
                    <a href="/establishment/${est.id}" style="display: inline-block; margin-top: 10px; font-size: 11px; font-weight: 800; color: #f97316;">Детальніше →</a>
                </div>
            `);
            markers[`${estLat}_${estLng}`] = marker;
            if (index === 0 && !(searchLat && searchLng)) marker.openPopup();
        });

        function focusOnMap(lat, lng) {
            if (!lat || !lng) return;
            map.setView([lat, lng], 16);
            if (markers[`${lat}_${lng}`]) markers[`${lat}_${lng}`].openPopup();
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    document.getElementById('userLat').value = position.coords.latitude;
                    document.getElementById('userLng').value = position.coords.longitude;
                    document.getElementById('filterForm').submit();
                });
            }
        }

        function changeCardSlide(button, direction) {
            const sliderRoot = button.closest('.interaction-slider');
            const container = sliderRoot.querySelector('.slides-container');
            let current = parseInt(sliderRoot.getAttribute('data-current')) || 0;
            current = (current + direction + 3) % 3;
            sliderRoot.setAttribute('data-current', current);
            container.style.transform = `translateX(-${current * 33.333}%)`;
        }
    </script>
</body>
</html>
