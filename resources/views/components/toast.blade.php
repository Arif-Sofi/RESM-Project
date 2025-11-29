{{-- Toast Notification Container --}}
{{-- This component should be included once in the main layout --}}
<div
    x-data="toastContainer()"
    x-on:toast.window="addToast($event.detail)"
    class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none w-96"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform ease-in duration-200 transition"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="pointer-events-auto w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border"
            :class="{
                'border-green-500': toast.type === 'success',
                'border-red-500': toast.type === 'error',
                'border-blue-500': toast.type === 'info',
                'border-yellow-500': toast.type === 'warning'
            }"
        >
            <div class="p-4">
                <div class="flex items-start">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        {{-- Success Icon --}}
                        <template x-if="toast.type === 'success'">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        {{-- Error Icon --}}
                        <template x-if="toast.type === 'error'">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        {{-- Info Icon --}}
                        <template x-if="toast.type === 'info'">
                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        {{-- Warning Icon --}}
                        <template x-if="toast.type === 'warning'">
                            <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </template>
                    </div>

                    {{-- Message --}}
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="toast.title" x-show="toast.title"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="toast.message"></p>
                    </div>

                    {{-- Close Button --}}
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="removeToast(toast.id)"
                            class="rounded-md inline-flex text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
                        >
                            <span class="sr-only">{{ __('Close') }}</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="h-1 bg-gray-200 dark:bg-gray-700">
                <div
                    class="h-1 transition-all duration-100"
                    :class="{
                        'bg-green-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                        'bg-blue-500': toast.type === 'info',
                        'bg-yellow-500': toast.type === 'warning'
                    }"
                    :style="`width: ${toast.progress}%`"
                ></div>
            </div>
        </div>
    </template>
</div>

<script>
function toastContainer() {
    return {
        toasts: [],
        toastId: 0,

        addToast(detail) {
            const id = this.toastId++;
            const toast = {
                id,
                type: detail.type || 'info',
                title: detail.title || '',
                message: detail.message || '',
                duration: detail.duration || 3000,
                visible: true,
                progress: 100
            };

            this.toasts.push(toast);

            // Start progress animation
            const startTime = Date.now();
            const progressInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const remaining = Math.max(0, 100 - (elapsed / toast.duration) * 100);
                const toastIndex = this.toasts.findIndex(t => t.id === id);
                if (toastIndex !== -1) {
                    this.toasts[toastIndex].progress = remaining;
                }
                if (remaining <= 0) {
                    clearInterval(progressInterval);
                }
            }, 50);

            // Auto-remove after duration
            setTimeout(() => {
                this.removeToast(id);
            }, toast.duration);
        },

        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                // Remove from array after animation
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }
}

// Global helper function to show toasts
window.showToast = function(type, message, title = '', duration = 3000) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { type, message, title, duration }
    }));
};

// Convenience methods
window.showSuccess = function(message, title = '') {
    window.showToast('success', message, title);
};

window.showError = function(message, title = '') {
    window.showToast('error', message, title);
};

window.showInfo = function(message, title = '') {
    window.showToast('info', message, title);
};

window.showWarning = function(message, title = '') {
    window.showToast('warning', message, title);
};
</script>
