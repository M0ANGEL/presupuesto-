<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gray-50 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('settings.index') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Gestión de Categorías</h1>
            <div class="w-6"></div>
        </div>

        <!-- Agregar Nueva Categoría -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                Nueva Categoría
            </h2>
            
            <form method="POST" action="{{ route('settings.categories.store') }}" class="space-y-4">
                @csrf
                
                <div class="flex space-x-3">
                    <input type="text" name="name" placeholder="Nombre de la categoría" 
                           class="flex-1 p-3 bg-white border border-gray-300 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none"
                           required>
                    
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-xl font-medium transition-colors">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Categorías -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="font-bold text-lg text-gray-800">
                    <i class="fas fa-list mr-2 text-blue-500"></i>
                    Tus Categorías ({{ $categories->count() }})
                </h2>
            </div>
            
            @if($categories->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($categories as $category)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <form method="POST" action="{{ route('settings.categories.update', $category) }}" 
                              class="flex items-center justify-between">
                            @csrf
                            @method('PUT')
                            
                            <div class="flex-1 mr-4">
                                <input type="text" name="name" value="{{ $category->name }}" 
                                       class="w-full p-2 bg-transparent border-b border-gray-300 focus:border-blue-500 focus:outline-none">
                            </div>
                            
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="text-blue-600 hover:text-blue-700"
                                        title="Guardar cambios">
                                    <i class="fas fa-save"></i>
                                </button>
                                
                                <button type="button" 
                                        onclick="confirmDelete('{{ route('settings.categories.destroy', $category) }}', '{{ $category->name }}')"
                                        class="text-red-600 hover:text-red-700"
                                        title="Eliminar categoría">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Contador de gastos -->
                        <div class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-receipt mr-1"></i>
                            {{ $category->expenses()->count() }} gastos registrados
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center">
                    <i class="fas fa-tags text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No tienes categorías creadas</p>
                    <p class="text-sm text-gray-400 mt-2">Crea tu primera categoría arriba</p>
                </div>
            @endif
        </div>
        
        <!-- Información -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                <div>
                    <p class="text-sm text-blue-800">
                        <strong>Importante:</strong> Solo puedes eliminar categorías que no tengan gastos asociados. 
                        Para reorganizar, edita el nombre de la categoría.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(url, categoryName) {
            if (confirm(`¿Estás seguro de eliminar la categoría "${categoryName}"?\n\nNota: Solo puedes eliminar categorías sin gastos asociados.`)) {
                // Crear formulario para eliminar
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';
                
                // Agregar método DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                // Agregar CSRF token
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);
                
                // Agregar al documento y enviar
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>