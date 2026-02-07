<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gradient-to-b from-gray-50 to-white p-4">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Nuevo Gasto Fijo</h1>
            <div class="w-6"></div> <!-- Spacer para centrar -->
        </div>

        <form method="POST" action="{{ route('fixed-expenses.store') }}" class="space-y-5">
            @csrf

            <!-- Nombre del gasto fijo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2 text-gray-500"></i>
                    Nombre del gasto
                </label>
                <input type="text" name="name" placeholder="Ej: Internet, Arriendo, Servicios..." 
                       class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                       required>
            </div>

            <!-- Monto -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>
                    Monto mensual
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl font-bold">$</span>
                    <input type="number" name="amount" placeholder="0" 
                           class="w-full pl-12 pr-4 py-4 text-2xl font-bold bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                           step="100" min="1" required>
                </div>
            </div>

            <!-- Día de pago -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-day mr-2 text-gray-500"></i>
                    Día de pago cada mes
                </label>
                <div class="relative">
                    <select name="due_day" 
                            class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none appearance-none"
                            required>
                        <option value="">Selecciona un día</option>
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}">Día {{ $i }} de cada mes</option>
                        @endfor
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Mes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar mr-2 text-gray-500"></i>
                    Mes a aplicar
                </label>
                <input type="month" name="month" 
                       value="{{ now()->format('Y-m') }}"
                       class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                       required>
                <p class="text-xs text-gray-500 mt-2">
                    Selecciona el mes en que aplicará este gasto fijo
                </p>
            </div>

            <!-- Notas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2 text-gray-500"></i>
                    Notas (opcional)
                </label>
                <textarea name="description" placeholder="Agrega cualquier detalle importante sobre este gasto fijo..." 
                          rows="3"
                          class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"></textarea>
            </div>

            <!-- Botón de envío -->
            <button type="submit" 
                    class="fixed bottom-6 left-4 right-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                <i class="fas fa-save mr-2"></i>Guardar Gasto Fijo
            </button>
        </form>
    </div>
</x-app-layout>