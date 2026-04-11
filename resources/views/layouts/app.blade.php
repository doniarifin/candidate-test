<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- icon -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <div 
                x-data="{ show: false, message: '', type: 'success' }"
                x-init="
                    @if(session('success'))
                        show = true;
                        message = '{{ session('success') }}';
                        type = 'success';
                        setTimeout(() => show = false, 3000);
                    @endif
                "

                x-on:toast.window="
                    show = true;
                    message = $event.detail.message;
                    type = $event.detail.type;

                    setTimeout(() => show = false, 3000);
                "

                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak
                class="fixed top-6 right-6 z-50"
            >
                <div 
                    class="min-w-[300px] max-w-sm px-5 py-4 rounded-xl shadow-xl flex items-start gap-3"
                    :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
                >
                    <!-- icon -->
                    <div class="mt-1">
                        <i x-show="type === 'success'" class="fa-solid fa-circle-check"></i>
                        <i x-show="type === 'error'" class="fa-solid fa-circle-exclamation"></i>
                    </div>

                    <!-- text -->
                    <div class="flex-1">
                        <p class="font-semibold text-lg">
                            <span x-text="type === 'success' ? 'Success' : 'Error'"></span>
                        </p>
                        <p class="text-sm opacity-90" x-text="message"></p>
                    </div>

                    <!-- close -->
                    <button @click="show = false" class="text-white opacity-70 hover:opacity-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

             @if (session('success') || session('error'))
                <div 
                    x-data="{ show: false, message: '', type: 'success' }"
                    x-init="
                        @if(session('success'))
                            show = true;
                            message = '{{ session('success') }}';
                            type = 'success';
                            setTimeout(() => show = false, 3000);
                        @endif
                    "

                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-cloak
                    class="fixed top-6 right-6 z-50"
                >
                    <div 
                        class="min-w-[300px] max-w-sm px-5 py-4 rounded-xl shadow-xl flex items-start gap-3"
                        :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
                    >
                        <!-- icon -->
                        <div class="mt-1">
                            <i x-show="type === 'success'" class="fa-solid fa-circle-check"></i>
                            <i x-show="type === 'error'" class="fa-solid fa-circle-exclamation"></i>
                        </div>

                        <!-- text -->
                        <div class="flex-1">
                            <p class="font-semibold text-lg">
                                <span x-text="type === 'success' ? 'Success' : 'Error'"></span>
                            </p>
                            <p class="text-sm opacity-90" x-text="message"></p>
                        </div>

                        <!-- close -->
                        <button @click="show = false" class="text-white opacity-70 hover:opacity-100">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
