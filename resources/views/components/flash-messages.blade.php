<!-- Flash Messages Component -->
@if (session('success') || session('error') || session('warning') || session('info'))
    <div class="container mx-auto px-4 pt-4">
        
        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm mb-4 fade-in" 
                 role="alert" 
                 id="success-alert">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                        <div>
                            <p class="font-medium text-sm">¡Éxito!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('success-alert')" 
                            class="text-green-400 hover:text-green-600 transition-colors duration-200 ml-4">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm mb-4 fade-in" 
                 role="alert" 
                 id="error-alert">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                        <div>
                            <p class="font-medium text-sm">Error</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('error-alert')" 
                            class="text-red-400 hover:text-red-600 transition-colors duration-200 ml-4">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Warning Message -->
        @if (session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg shadow-sm mb-4 fade-in" 
                 role="alert" 
                 id="warning-alert">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 text-lg"></i>
                        <div>
                            <p class="font-medium text-sm">Advertencia</p>
                            <p class="text-sm">{{ session('warning') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('warning-alert')" 
                            class="text-yellow-400 hover:text-yellow-600 transition-colors duration-200 ml-4">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Info Message -->
        @if (session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg shadow-sm mb-4 fade-in" 
                 role="alert" 
                 id="info-alert">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3 text-lg"></i>
                        <div>
                            <p class="font-medium text-sm">Información</p>
                            <p class="text-sm">{{ session('info') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAlert('info-alert')" 
                            class="text-blue-400 hover:text-blue-600 transition-colors duration-200 ml-4">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = ['success-alert', 'error-alert', 'warning-alert', 'info-alert'];
            
            alerts.forEach(alertId => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    setTimeout(() => {
                        closeAlert(alertId);
                    }, 5000);
                }
            });
        });

        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }
    </script>
@endif