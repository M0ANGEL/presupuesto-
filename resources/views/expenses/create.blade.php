<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gradient-to-b from-gray-50 to-white p-4 pb-24">
        <!-- Header con navegación -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Nuevo Gasto</h1>
            <div class="w-6"></div> <!-- Spacer para centrar -->
        </div>

        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-5" id="expenseForm">
            @csrf

            <!-- Tarjetas de selección de fuente -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Fuente del gasto</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" name="source" value="personal" checked 
                               class="hidden peer">
                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                            <i class="fas fa-user text-2xl text-purple-500 mb-2"></i>
                            <p class="font-medium">Personal</p>
                            <p class="text-xs text-gray-500 mt-1">
                                ${{ number_format(auth()->user()->wallet->personal, 0, ',', '.') }}
                            </p>
                        </div>
                    </label>
                    
                    <label class="relative">
                        <input type="radio" name="source" value="stock" 
                               class="hidden peer">
                        <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                            <i class="fas fa-box text-2xl text-green-500 mb-2"></i>
                            <p class="font-medium">Stock</p>
                            <p class="text-xs text-gray-500 mt-1">
                                ${{ number_format(auth()->user()->wallet->stock, 0, ',', '.') }}
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Categorías -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Categoría</label>
                <select name="category_id" 
                        class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none appearance-none"
                        required>
                    <option value="">Selecciona una categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Monto con formato -->
            <div class="relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-xl font-bold">$</span>
                <input type="text" 
                       name="amount_display" 
                       id="amount_display"
                       placeholder="0" 
                       class="w-full pl-12 pr-4 py-4 text-2xl font-bold bg-white border-2 border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
                       required
                       oninput="formatCurrency(this)"
                       onblur="formatCurrency(this)">
                <!-- Input oculto para el valor real -->
                <input type="hidden" name="amount" id="amount">
            </div>

            <!-- Descripción -->
            <div class="relative">
                <textarea name="description" placeholder="Descripción (opcional)" rows="3"
                          class="w-full p-4 bg-white border-2 border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-none"></textarea>
                <span class="absolute right-3 bottom-3 text-gray-400">
                    <i class="fas fa-pen"></i>
                </span>
            </div>

            <!-- Botón de envío -->
            <button type="button" onclick="submitExpenseForm()" 
                    class="w-full mt-8 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 md:fixed md:bottom-6 md:left-4 md:right-4 md:w-auto">
                <i class="fas fa-check-circle mr-2"></i>Registrar Gasto
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
        function submitExpenseForm() {
            const form = document.getElementById('expenseForm');
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
            
            // Validar que la categoría esté seleccionada
            const categorySelect = form.querySelector('select[name="category_id"]');
            if (!categorySelect.value) {
                alert('Por favor selecciona una categoría');
                categorySelect.focus();
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