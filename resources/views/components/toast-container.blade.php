<div 
    x-data="{
        toasts: [],
        addToast(toast) {
            this.toasts.push({
                id: Date.now(),
                message: toast.message,
                type: toast.type || 'success',
                ...toast
            });
            setTimeout(() => this.removeToast(this.toasts[this.toasts.length - 1].id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(toast => toast.id !== id);
        }
    }"
    @toast.window="addToast($event.detail)"
    class="fixed top-4 right-4 z-50 space-y-2"
    style="max-width: 400px;"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            :class="{
                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200': toast.type === 'success',
                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200': toast.type === 'error',
                'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200': toast.type === 'info',
                'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200': toast.type === 'warning'
            }"
            class="p-4 rounded-lg border shadow-lg flex items-start gap-3"
        >
            <div class="flex-1">
                <p class="font-medium" x-text="toast.message"></p>
            </div>
            <button
                @click="removeToast(toast.id)"
                class="text-current opacity-70 hover:opacity-100"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </template>
</div>

