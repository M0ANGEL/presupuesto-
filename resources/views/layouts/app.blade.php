<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestor Financiero') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        /* Mejoras para móvil */
        .mobile-tap-area {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Scroll suave */
        .smooth-scroll {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation mejorada para móvil -->
        <nav class="bg-white shadow-lg fixed bottom-0 left-0 right-0 z-50 md:static md:shadow-none">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">
                            <i class="fas fa-wallet mr-2"></i>Finanzas
                        </a>
                    </div>
                    <!-- Menú desktop -->
                    <div class="hidden md:flex items-center space-x-4">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-nav-link>
                        <!-- User Menu -->
                    </div>
                    <!-- Menú móvil -->
                    <div class="md:hidden">
                        <!-- Aquí iría el menú hamburguesa si es necesario -->
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenido principal con padding para el bottom navigation -->
        <main class="pb-20 md:pb-0 pt-4">
            <div class="container mx-auto px-4">
                {{ $slot }}
            </div>
        </main>

        <!-- Bottom Navigation para móvil -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-2 px-4 z-40">
            <div class="flex justify-around">
                <a href="{{ route('dashboard') }}" 
                   class="flex flex-col items-center text-center px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-500' }}">
                    <i class="fas fa-home text-xl mb-1"></i>
                    <span class="text-xs font-medium">Inicio</span>
                </a>
                
                <a href="{{ route('expenses.create') }}"
                   class="flex flex-col items-center text-center px-3 py-2 rounded-lg transition-colors text-gray-500">
                    <i class="fas fa-minus-circle text-xl mb-1"></i>
                    <span class="text-xs font-medium">Gastos</span>
                </a>
                
                <a href="{{ route('incomes.create') }}"
                   class="flex flex-col items-center text-center px-3 py-2 rounded-lg transition-colors text-gray-500">
                    <i class="fas fa-plus-circle text-xl mb-1"></i>
                    <span class="text-xs font-medium">Ingresos</span>
                </a>
                
                <button onclick="openQuickActions()"
                   class="flex flex-col items-center text-center px-3 py-2 rounded-lg transition-colors text-gray-500">
                    <i class="fas fa-bolt text-xl mb-1"></i>
                    <span class="text-xs font-medium">Rápidas</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions Modal -->
    <div id="quickActionsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-end">
        <div class="bg-white rounded-t-3xl w-full max-w-md mx-auto transform transition-transform duration-300 translate-y-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Acciones rápidas</h3>
                    <button onclick="closeQuickActions()" class="text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <form method="POST" action="{{ route('wallet.saving') }}" 
                          class="bg-blue-50 rounded-2xl p-4 flex flex-col items-center">
                        @csrf
                        <input type="number" name="amount" placeholder="Monto" 
                               class="w-full mb-3 rounded-xl border-gray-300 text-center font-bold">
                        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-xl font-semibold">
                            <i class="fas fa-piggy-bank mr-2"></i>Ahorrar
                        </button>
                    </form>
                    
                    <a href="{{ route('incomes.create') }}"
                       class="bg-green-50 rounded-2xl p-4 flex flex-col items-center justify-center">
                        <i class="fas fa-money-bill-wave text-2xl text-green-600 mb-2"></i>
                        <span class="font-semibold">Nuevo Ingreso</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openQuickActions() {
            const modal = document.getElementById('quickActionsModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('translate-y-full');
            }, 10);
        }

        function closeQuickActions() {
            const modal = document.getElementById('quickActionsModal');
            modal.querySelector('.transform').classList.add('translate-y-full');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('quickActionsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickActions();
            }
        });
    </script>
</body>
</html>