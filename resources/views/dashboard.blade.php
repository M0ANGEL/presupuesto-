<x-app-layout>
    <div class="max-w-md mx-auto space-y-6 pb-28">
        {{-- Saludos y fecha --}}
        <div class="pt-6">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Hola, {{ auth()->user()->name }}!</h1>
                    <p class="text-gray-600">{{ now()->translatedFormat('l, d \d\e F') }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700" title="Historial">
                        <i class="fas fa-history text-xl"></i>
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-gray-500 hover:text-gray-700" title="Configuración">
                        <i class="fas fa-cog text-xl"></i>
                    </a>
                    {{-- Botón de Cerrar Sesión --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RESUMEN DE SALDOS --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-green-700 uppercase tracking-wide">Stock</span>
                    <i class="fas fa-box text-green-500"></i>
                </div>
                <p class="text-xl font-bold text-gray-800">
                    ${{ number_format($wallet->stock, 0, ',', '.') }}
                </p>
                <p class="text-xs text-green-600 mt-1">
                    @if($wallet->stock > 0)
                    Disponible
                    @else
                    <span class="text-red-600">Sin fondos</span>
                    @endif
                </p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Ahorro</span>
                    <i class="fas fa-piggy-bank text-blue-500"></i>
                </div>
                <p class="text-xl font-bold text-gray-800">
                    ${{ number_format($wallet->saving, 0, ',', '.') }}
                </p>
                <p class="text-xs text-blue-600 mt-1">Seguro</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-purple-700 uppercase tracking-wide">Personal</span>
                    <i class="fas fa-user text-purple-500"></i>
                </div>
                <p class="text-xl font-bold text-gray-800">
                    ${{ number_format($wallet->personal, 0, ',', '.') }}
                </p>
                <p class="text-xs text-purple-600 mt-1">Gastos personales</p>
            </div>
        </div>

        {{-- ACCIONES RÁPIDAS --}}
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                Acciones Rápidas
            </h2>
            <div class="grid grid-cols-3 gap-3">
                <a href="{{ route('fixed-expenses.create') }}" 
                   class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl p-4 text-center transition-colors">
                    <i class="fas fa-calendar-plus text-2xl text-blue-500 mb-2"></i>
                    <p class="font-medium text-sm text-blue-700">Gasto Fijo</p>
                </a>
                
                <a href="{{ route('incomes.create') }}" 
                   class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-xl p-4 text-center transition-colors">
                    <i class="fas fa-money-bill-wave text-2xl text-green-500 mb-2"></i>
                    <p class="font-medium text-sm text-green-700">Ingreso</p>
                </a>
                
                <a href="{{ route('expenses.create') }}" 
                   class="bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl p-4 text-center transition-colors">
                    <i class="fas fa-minus-circle text-2xl text-red-500 mb-2"></i>
                    <p class="font-medium text-sm text-red-700">Gasto</p>
                </a>
            </div>
        </div>

        {{-- GASTOS FIJOS DEL MES --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-lg text-gray-800">
                            <i class="fas fa-calendar-alt mr-2 text-red-500"></i>
                            Gastos Fijos
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded-full font-medium">
                            {{ $fixedExpenses->where('paid_at', null)->count() }} pendientes
                        </span>
                        <p class="text-xs text-gray-500 mt-1">
                            Total: ${{ number_format($fixedExpenses->sum('amount'), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($fixedExpenses as $expense)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            {{-- Checkbox para marcar como pagado --}}
                            <form method="POST" action="{{ route('fixed-expenses.pay', $expense) }}" 
                                  class="flex items-center" id="pay-form-{{ $expense->id }}">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('¿Marcar {{ addslashes($expense->name) }} como pagado?\nSe descontarán ${{ number_format($expense->amount, 0, ',', '.') }} del stock.')"
                                        class="focus:outline-none">
                                    @if($expense->paid_at)
                                    <div class="w-10 h-10 flex items-center justify-center bg-green-100 rounded-full border-2 border-green-300">
                                        <i class="fas fa-check text-green-600 text-lg"></i>
                                    </div>
                                    @else
                                    <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full border-2 border-gray-300 hover:border-blue-400 hover:bg-blue-50 transition-colors cursor-pointer"
                                         title="Marcar como pagado">
                                        <i class="fas fa-check text-transparent"></i>
                                    </div>
                                    @endif
                                </button>
                            </form>

                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $expense->name }}</p>
                                        <div class="flex items-center space-x-3 text-sm text-gray-500 mt-1">
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                Día {{ $expense->due_day }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                ${{ number_format($expense->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        @if($expense->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $expense->description }}</p>
                                        @endif
                                    </div>
                                    
                                    {{-- Estado --}}
                                    @if(!$expense->paid_at)
                                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-red-100 text-red-700">
                                        Pendiente
                                    </span>
                                    @else
                                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-700">
                                        Pagado
                                    </span>
                                    @endif
                                </div>
                                
                                {{-- Advertencia si no hay suficiente stock --}}
                                @if(!$expense->paid_at && $wallet->stock < $expense->amount)
                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-xs text-yellow-700 flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Stock insuficiente para pagar
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Botón para desmarcar si ya está pagado --}}
                    @if($expense->paid_at)
                    <div class="mt-3 pl-14">
                        <form method="POST" action="{{ route('fixed-expenses.unpay', $expense) }}">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('¿Desmarcar {{ addslashes($expense->name) }} como pagado?\nSe devolverán ${{ number_format($expense->amount, 0, ',', '.') }} al stock.')"
                                    class="text-xs text-red-600 hover:text-red-700">
                                <i class="fas fa-undo mr-1"></i> Desmarcar pago
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-8 text-center">
                    <i class="fas fa-receipt text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No hay gastos fijos este mes</p>
                    <a href="{{ route('fixed-expenses.create') }}" 
                       class="inline-block mt-3 text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Agregar gasto fijo
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- INGRESOS FIJOS --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-lg text-gray-800">
                            <i class="fas fa-money-check-alt mr-2 text-green-600"></i>
                            Ingresos por Confirmar
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Ingresos fijos y variables</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">
                            {{ $incomes->where('paid_at', null)->count() }} pendientes
                        </span>
                        <p class="text-xs text-gray-500 mt-1">
                            Total: ${{ number_format($incomes->where('paid_at', null)->sum('amount'), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($incomes->where('paid_at', null) as $income)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            {{-- Checkbox para marcar como recibido --}}
                            <form method="POST" action="{{ route('incomes.pay', $income) }}" 
                                  class="flex items-center">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('¿Confirmar recepción de {{ addslashes($income->name) }}?\nSe agregarán ${{ number_format($income->amount, 0, ',', '.') }} al stock.')"
                                        class="focus:outline-none">
                                    <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full border-2 border-gray-300 hover:border-green-400 hover:bg-green-50 transition-colors cursor-pointer"
                                         title="Confirmar recepción">
                                        <i class="fas fa-check text-transparent"></i>
                                    </div>
                                </button>
                            </form>

                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $income->name }}</p>
                                        <div class="flex items-center space-x-3 text-sm text-gray-500 mt-1">
                                            <span class="flex items-center">
                                                @if($income->type === 'fixed')
                                                <i class="fas fa-calendar-check mr-1 text-green-500"></i>
                                                Fijo
                                                @else
                                                <i class="fas fa-chart-line mr-1 text-blue-500"></i>
                                                Variable
                                                @endif
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                ${{ number_format($income->amount, 0, ',', '.') }}
                                            </span>
                                            @if($income->expected_date)
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $income->expected_date->format('d/m') }}
                                            </span>
                                            @endif
                                        </div>
                                        @if($income->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $income->description }}</p>
                                        @endif
                                    </div>
                                    
                                    {{-- Tipo de pago --}}
                                    @if($income->payment_method)
                                    <span class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                                        @if($income->payment_method === 'nequi')
                                        <i class="fas fa-mobile-alt mr-1"></i> Nequi
                                        @elseif($income->payment_method === 'efectivo')
                                        <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                                        @else
                                        <i class="fas fa-university mr-1"></i> Banco
                                        @endif
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <i class="fas fa-money-bill-wave text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No hay ingresos pendientes</p>
                    <a href="{{ route('incomes.create') }}" 
                       class="inline-block mt-3 text-green-600 hover:text-green-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Agregar ingreso
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- TRANSFERENCIAS RÁPIDAS --}}
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-exchange-alt mr-2 text-purple-500"></i>
                Transferir Dinero
            </h2>
            <form method="POST" action="{{ route('wallet.transfer') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">De:</label>
                        <select name="from" class="w-full rounded-xl border-gray-300" required>
                            <option value="">Seleccionar...</option>
                            <option value="stock">Stock</option>
                            <option value="saving">Ahorro</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">A:</label>
                        <select name="to" class="w-full rounded-xl border-gray-300" required>
                            <option value="">Seleccionar...</option>
                            <option value="saving">Ahorro</option>
                            <option value="personal">Personal</option>
                            <option value="stock">Stock</option>
                        </select>
                    </div>
                </div>
                
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold">$</span>
                    <input type="number" name="amount" placeholder="Monto a transferir" 
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none"
                           required min="1">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-3 rounded-xl transition-all">
                    <i class="fas fa-exchange-alt mr-2"></i>Transferir
                </button>
            </form>
        </div>
    </div>

    {{-- Floating Action Button para móvil --}}
    <div class="md:hidden bg-black fixed bottom-24 rounded-full right-4 z-30">
        <div id="fabMenu" class="absolute  bottom-16 right-0 space-y-2 hidden flex-col items-end">
            {{-- Botón de cerrar (X) en color negro --}}
            <button onclick="closeFABMenu()"
                    class="w-14 h-14 bg-black rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transition-all transform hover:scale-110 mb-2">
                <i class="fas fa-times text-lg"></i>
            </button>
            
            <a href="{{ route('incomes.create') }}"
               class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transition-all transform hover:scale-110"
               title="Agregar Ingreso">
                <i class="fas fa-plus-circle text-lg"></i>
            </a>
            <a href="{{ route('expenses.create') }}"
               class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transition-all transform hover:scale-110"
               title="Agregar Gasto">
                <i class="fas fa-minus-circle text-lg"></i>
            </a>
            <a href="{{ route('fixed-expenses.create') }}"
               class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-xl transition-all transform hover:scale-110"
               title="Agregar Gasto Fijo">
                <i class="fas fa-calendar-plus text-lg"></i>
            </a>
        </div>
        
        <button onclick="openFABMenu()" 
                class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-full shadow-lg flex items-center justify-center text-white hover:shadow-xl transition-all duration-300">
            <i class="fas fa-plus text-xl"></i>
        </button>
    </div>

    <script>
        function openFABMenu() {
            const menu = document.getElementById('fabMenu');
            menu.classList.remove('hidden');
            menu.classList.add('flex');
        }
        
        function closeFABMenu() {
            const menu = document.getElementById('fabMenu');
            menu.classList.add('hidden');
            menu.classList.remove('flex');
        }

        // Cerrar FAB al hacer clic fuera
        document.addEventListener('click', function(e) {
            const fabMenu = document.getElementById('fabMenu');
            const fabButton = document.querySelector('button[onclick="openFABMenu()"]');
            const closeButton = document.querySelector('button[onclick="closeFABMenu()"]');
            
            if (!fabMenu.contains(e.target) && e.target !== fabButton && !fabButton.contains(e.target) && e.target !== closeButton && !closeButton.contains(e.target)) {
                closeFABMenu();
            }
        });
    </script>
</x-app-layout>