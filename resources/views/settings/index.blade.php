<x-app-layout>
    <div class="max-w-md mx-auto min-h-screen bg-gray-50 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 pt-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Configuración</h1>
            <div class="w-6"></div>
        </div>

        <!-- Perfil de Usuario -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                Perfil de Usuario
            </h2>
            
            <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                           required>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="font-medium text-gray-700 mb-3">Cambiar Contraseña</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Contraseña Actual</label>
                            <input type="password" name="current_password" 
                                   class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Nueva Contraseña</label>
                            <input type="password" name="new_password" 
                                   class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Confirmar Nueva Contraseña</label>
                            <input type="password" name="new_password_confirmation" 
                                   class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 rounded-xl transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar Cambios
                </button>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                Estadísticas de tu Cuenta
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Categorías creadas:</span>
                    <span class="font-medium">{{ $categories->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Gastos registrados:</span>
                    <span class="font-medium">{{ $user->expenses()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Ingresos registrados:</span>
                    <span class="font-medium">{{ $user->incomes()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Gastos fijos:</span>
                    <span class="font-medium">{{ $user->fixedExpenses()->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Cuenta creada:</span>
                    <span class="font-medium">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white rounded-2xl shadow-lg p-5 mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-cog mr-2 text-purple-500"></i>
                Configuración Rápida
            </h2>
            
            <div class="space-y-3">
                <a href="{{ route('settings.categories') }}" 
                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-tags text-gray-500 mr-3"></i>
                        <span>Gestionar Categorías</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                
                <a href="{{ route('settings.notifications') }}" 
                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-bell text-gray-500 mr-3"></i>
                        <span>Notificaciones</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                
                <a href="{{ route('settings.backup') }}" 
                   class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-database text-gray-500 mr-3"></i>
                        <span>Copias de Seguridad</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>

        <!-- Información de la App -->
        <div class="bg-white rounded-2xl shadow-lg p-5">
            <h2 class="font-bold text-lg text-gray-800 mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Información de la Aplicación
            </h2>
            
            <div class="space-y-3 text-sm text-gray-600">
                <p><i class="fas fa-code mr-2"></i> Versión: 1.0.0</p>
                <p><i class="fas fa-calendar mr-2"></i> Última actualización: {{ now()->translatedFormat('d M Y') }}</p>
                <p><i class="fas fa-shield-alt mr-2"></i> Tu información está segura y encriptada</p>
                <p><i class="fas fa-mobile-alt mr-2"></i> Optimizada para dispositivos móviles</p>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} Finanzas Personales. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>