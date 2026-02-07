<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gradient-to-b from-gray-50 to-white p-4">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Nuevo Ingreso</h1>
            <div class="w-6"></div> <!-- Spacer para centrar -->
        </div>

        <form method="POST" action="{{ route('incomes.store') }}" class="space-y-5" id="incomeForm">
            @csrf

            <!-- Tipo de ingreso -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-filter mr-2 text-gray-500"></i>
                    Tipo de ingreso
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" name="type" value="fixed" checked 
                               class="hidden peer">
                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                            <i class="fas fa-calendar-check text-2xl text-green-500 mb-2"></i>
                            <p class="font-medium">Fijo</p>
                            <p class="text-xs text-gray-500 mt-1">Recurrente cada mes</p>
                        </div>
                    </label>
                    
                    <label class="relative">
                        <input type="radio" name="type" value="variable" 
                               class="hidden peer">
                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                            <i class="fas fa-chart-line text-2xl text-blue-500 mb-2"></i>
                            <p class="font-medium">Variable</p>
                            <p class="text-xs text-gray-500 mt-1">Ocasional o extra</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nombre del ingreso -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-signature mr-2 text-gray-500"></i>
                    Concepto
                </label>
                <input type="text" name="name" placeholder="Ej: Salario, Freelance, Venta..." 
                       class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none"
                       required>
            </div>

            <!-- Monto con formato -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>
                    Monto
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl font-bold">$</span>
                    <input type="text" 
                           name="amount_display" 
                           id="amount_display"
                           placeholder="0" 
                           class="w-full pl-12 pr-4 py-4 text-2xl font-bold bg-white border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none"
                           required
                           oninput="formatCurrency(this)"
                           onblur="formatCurrency(this)">
                    <!-- Input oculto para el valor real -->
                    <input type="hidden" name="amount" id="amount">
                </div>
            </div>

            <!-- Método de pago -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-credit-card mr-2 text-gray-500"></i>
                    Método de pago
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="relative">
                        <input type="radio" name="payment_method" value="nequi" checked 
                               class="hidden peer">
                        <div class="border border-gray-300 rounded-xl p-3 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:bg-gray-50 transition-all">
                            <i class="fas fa-mobile-alt text-lg text-purple-500 mb-1"></i>
                            <p class="text-xs font-medium mt-1">Nequi</p>
                        </div>
                    </label>
                    
                    <label class="relative">
                        <input type="radio" name="payment_method" value="efectivo" 
                               class="hidden peer">
                        <div class="border border-gray-300 rounded-xl p-3 text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition-all">
                            <i class="fas fa-money-bill-wave text-lg text-green-500 mb-1"></i>
                            <p class="text-xs font-medium mt-1">Efectivo</p>
                        </div>
                    </label>
                    
                    <label class="relative">
                        <input type="radio" name="payment_method" value="banco" 
                               class="hidden peer">
                        <div class="border border-gray-300 rounded-xl p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-all">
                            <i class="fas fa-university text-lg text-blue-500 mb-1"></i>
                            <p class="text-xs font-medium mt-1">Banco</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Fecha esperada -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calendar-check mr-2 text-gray-500"></i>
                    Fecha esperada de pago
                </label>
                <input type="date" name="expected_date" 
                       value="{{ now()->format('Y-m-d') }}"
                       class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none">
                <p class="text-xs text-gray-500 mt-2">
                    Selecciona cuándo esperas recibir este ingreso
                </p>
            </div>

            <!-- Notas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2 text-gray-500"></i>
                    Notas (opcional)
                </label>
                <textarea name="description" placeholder="Agrega cualquier detalle importante sobre este ingreso..." 
                          rows="3"
                          class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none resize-none"></textarea>
            </div>

            <!-- Botón de envío -->
            <button type="button" onclick="submitIncomeForm()" 
                    class="fixed bottom-6 left-4 right-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0">
                <i class="fas fa-save mr-2"></i>Guardar Ingreso
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
        function submitIncomeForm() {
            const form = document.getElementById('incomeForm');
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
            
            // Enviar el formulario
            form.submit();
        }
        
        // Formatear al cargar si hay valor
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount_display');
            if (amountInput) {
                // Si hay un valor previo (por ejemplo, en caso de error)
                if (amountInput.value) {
                    formatCurrency(amountInput);
                }
                
                // Enfocar el campo de monto
                setTimeout(() => {
                    amountInput.focus();
                }, 300);
            }
        });
    </script>
</x-app-layout>