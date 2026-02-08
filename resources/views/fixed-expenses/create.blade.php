<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gradient-to-b from-gray-50 to-white p-4 pb-24">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Nuevo Gasto Fijo</h1>
            <div class="w-6"></div> <!-- Spacer para centrar -->
        </div>

        <form method="POST" action="{{ route('fixed-expenses.store') }}" class="space-y-5" id="fixedExpenseForm">
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
                    <input type="text" 
                           name="amount_display" 
                           id="amount_display"
                           placeholder="0" 
                           class="w-full pl-12 pr-4 py-4 text-2xl font-bold bg-white border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                           oninput="formatCurrency(this)"
                           onblur="formatCurrency(this)"
                           required>
                    <!-- Input oculto para el valor real -->
                    <input type="hidden" name="amount" id="amount">
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
            <button type="button" onclick="submitFixedExpenseForm()" 
                    class="w-full mt-8 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 md:fixed md:bottom-6 md:left-4 md:right-4 md:w-auto">
                <i class="fas fa-save mr-2"></i>Guardar Gasto Fijo
            </button>
        </form>
    </div>

    <script>
        // Función para formatear moneda
        function formatCurrency(input) {
            // Guardar la posición del cursor
            let cursorPosition = input.selectionStart;
            let originalLength = input.value.length;
            
            // Obtener valor sin formato
            let rawValue = input.value.replace(/[^\d]/g, '');
            
            // Si está vacío
            if (rawValue === '') {
                input.value = '';
                document.getElementById('amount').value = '';
                return;
            }
            
            // Convertir a número
            let number = parseInt(rawValue, 10);
            
            // Formatear con puntos
            let formatted = new Intl.NumberFormat('es-CO').format(number);
            
            // Actualizar el input visual
            input.value = formatted;
            
            // Actualizar el input oculto con el valor numérico
            document.getElementById('amount').value = number;
            
            // Restaurar posición del cursor
            let newLength = input.value.length;
            let positionChange = newLength - originalLength;
            input.setSelectionRange(cursorPosition + positionChange, cursorPosition + positionChange);
        }
        
        // Función para preparar y enviar el formulario
        function submitFixedExpenseForm() {
            const form = document.getElementById('fixedExpenseForm');
            const amountHidden = document.getElementById('amount');
            const amountDisplay = document.getElementById('amount_display');
            
            // Si no hay valor en el oculto, prepararlo desde el display
            if (!amountHidden.value && amountDisplay.value) {
                let rawValue = amountDisplay.value.replace(/[^\d]/g, '');
                amountHidden.value = rawValue || '0';
            }
            
            // Validar que tenga un monto
            if (!amountHidden.value || amountHidden.value == '0') {
                alert('Por favor ingresa un monto válido');
                amountDisplay.focus();
                return;
            }
            
            // Validar que el día de pago esté seleccionado
            const dueDaySelect = form.querySelector('select[name="due_day"]');
            if (!dueDaySelect.value) {
                alert('Por favor selecciona un día de pago');
                dueDaySelect.focus();
                return;
            }
            
            // Validar que el nombre esté lleno
            const nameInput = form.querySelector('input[name="name"]');
            if (!nameInput.value.trim()) {
                alert('Por favor ingresa un nombre para el gasto fijo');
                nameInput.focus();
                return;
            }
            
            // Enviar el formulario
            form.submit();
        }
        
        // Formatear al cargar si hay valor
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount_display');
            if (amountInput) {
                // Enfocar el campo de nombre primero
                const nameInput = document.querySelector('input[name="name"]');
                if (nameInput) {
                    nameInput.focus();
                }
            }
            
            // Ajustar botón en móvil cuando aparece teclado
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const button = document.querySelector('button[type="button"]');
                    button.classList.add('md:fixed');
                });
                
                input.addEventListener('blur', function() {
                    const button = document.querySelector('button[type="button"]');
                    setTimeout(() => {
                        button.classList.add('md:fixed');
                    }, 300);
                });
            });
        });
    </script>
</x-app-layout>