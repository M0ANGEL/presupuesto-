<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gray-50 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Historial de Transacciones</h1>
            <div class="w-6"></div> <!-- Spacer para centrar -->
        </div>

        <!-- Filtros Simples -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-filter mr-2 text-blue-500"></i>
                Filtros por Mes
            </h2>
            
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                        <select name="month" class="w-full rounded-xl border-gray-300" onchange="this.form.submit()">
                            @foreach($months as $month)
                                <option value="{{ $month['value'] }}" {{ $selectedMonth == $month['value'] ? 'selected' : '' }}>
                                    {{ $month['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="type" class="w-full rounded-xl border-gray-300" onchange="this.form.submit()">
                            <option value="">Todos los tipos</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Ingresos</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Gastos</option>
                            <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transferencias</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 rounded-xl transition-colors">
                        <i class="fas fa-search mr-2"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('transactions.index') }}" 
                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 rounded-xl text-center transition-colors">
                        <i class="fas fa-redo mr-2"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Resumen del Mes -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                Resumen del Mes
            </h2>
            
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-3 bg-green-50 rounded-xl">
                    <p class="text-xs text-gray-600">Ingresos</p>
                    <p class="text-lg font-bold text-green-600">
                        ${{ number_format($totalIncome, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-xl">
                    <p class="text-xs text-gray-600">Gastos</p>
                    <p class="text-lg font-bold text-red-600">
                        ${{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-xl">
                    <p class="text-xs text-gray-600">Transferencias</p>
                    <p class="text-lg font-bold text-blue-600">
                        ${{ number_format($totalTransfer, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-600">Balance Neto</p>
                    @php
                        $balance = $totalIncome - $totalExpense;
                    @endphp
                    <p class="text-lg font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format($balance, 0, ',', '.') }}
                    </p>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                </p>
            </div>
        </div>

        <!-- Lista de Transacciones -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <h2 class="font-bold text-lg text-gray-800">
                        <i class="fas fa-list mr-2 text-gray-600"></i>
                        Transacciones
                    </h2>
                    <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                        {{ $transactions->total() }} registros
                    </span>
                </div>
            </div>

            @if($transactions->count() > 0)
            <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                @foreach($transactions as $transaction)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Icono según tipo -->
                            @if($transaction->type == 'income')
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-arrow-down text-green-500"></i>
                            </div>
                            @elseif($transaction->type == 'expense')
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-arrow-up text-red-500"></i>
                            </div>
                            @else
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-blue-500"></i>
                            </div>
                            @endif

                            <div>
                                <p class="font-medium text-gray-800">
                                    @if($transaction->type == 'income')
                                    Ingreso
                                    @elseif($transaction->type == 'expense')
                                    Gasto
                                    @else
                                    Transferencia
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $transaction->created_at->translatedFormat('d M, H:i') }}
                                </p>
                                @if($transaction->description)
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($transaction->description, 30) }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-right">
                            @if($transaction->type == 'income')
                            <p class="font-bold text-green-600">
                                +${{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                            @elseif($transaction->type == 'expense')
                            <p class="font-bold text-red-600">
                                -${{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                            @else
                            <p class="font-bold text-blue-600">
                                ${{ number_format($transaction->amount, 0, ',', '.') }}
                            </p>
                            @endif
                            
                            @if($transaction->type == 'transfer' && $transaction->from && $transaction->to)
                            <p class="text-xs text-gray-500 mt-1">
                                {{ ucfirst($transaction->from) }} → {{ ucfirst($transaction->to) }}
                            </p>
                            @elseif($transaction->type == 'expense' && $transaction->from)
                            <p class="text-xs text-gray-500 mt-1">
                                De: {{ ucfirst($transaction->from) }}
                            </p>
                            @elseif($transaction->type == 'income' && $transaction->to)
                            <p class="text-xs text-gray-500 mt-1">
                                A: {{ ucfirst($transaction->to) }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <i class="fas fa-exchange-alt text-3xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No hay transacciones registradas</p>
                <p class="text-sm text-gray-400 mt-1">
                    {{ Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                </p>
            </div>
            @endif
        </div>

        <!-- Paginación -->
        @if($transactions->hasPages())
        <div class="mt-6 bg-white rounded-xl p-4 shadow">
            {{ $transactions->links() }}
        </div>
        @endif
        
        <!-- Botones de Acción -->
        <div class="mt-6 grid grid-cols-2 gap-3">
            <a href="{{ route('dashboard') }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 rounded-xl text-center transition-all shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i> Dashboard
            </a>
            <button onclick="window.print()" 
                    class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 rounded-xl text-center transition-all shadow-lg">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>
</x-app-layout>