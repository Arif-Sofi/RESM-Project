{{-- Confirmation Modal Container --}}
{{-- This component should be included once in the main layout --}}
{{-- Usage: window.showConfirm({ title, message, confirmText, cancelText, type }).then(confirmed => { ... }) --}}
<div
    x-data="confirmationModal()"
    x-on:show-confirm.window="showModal($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[110] overflow-y-auto"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="cancel()"
    ></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @keydown.escape.window="cancel()"
            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
        >
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    {{-- Icon --}}
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                        :class="{
                            'bg-red-100 dark:bg-red-900/30': modalType === 'danger',
                            'bg-yellow-100 dark:bg-yellow-900/30': modalType === 'warning',
                            'bg-blue-100 dark:bg-blue-900/30': modalType === 'info'
                        }"
                    >
                        {{-- Danger Icon --}}
                        <template x-if="modalType === 'danger'">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </template>
                        {{-- Warning Icon --}}
                        <template x-if="modalType === 'warning'">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </template>
                        {{-- Info Icon --}}
                        <template x-if="modalType === 'info'">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                        </template>
                    </div>

                    {{-- Content --}}
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100" x-text="title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button
                    type="button"
                    @click="confirm()"
                    class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto"
                    :class="{
                        'bg-red-600 hover:bg-red-700': modalType === 'danger',
                        'bg-yellow-600 hover:bg-yellow-700': modalType === 'warning',
                        'bg-blue-600 hover:bg-blue-700': modalType === 'info'
                    }"
                    x-text="confirmText"
                ></button>
                <button
                    type="button"
                    @click="cancel()"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto"
                    x-text="cancelText"
                ></button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmationModal() {
    return {
        show: false,
        title: '',
        message: '',
        confirmText: '{{ __("Confirm") }}',
        cancelText: '{{ __("Cancel") }}',
        modalType: 'danger',
        resolvePromise: null,

        showModal(detail) {
            this.title = detail.title || '{{ __("Are you sure?") }}';
            this.message = detail.message || '';
            this.confirmText = detail.confirmText || '{{ __("Confirm") }}';
            this.cancelText = detail.cancelText || '{{ __("Cancel") }}';
            this.modalType = detail.type || 'danger';
            this.resolvePromise = detail.resolve;
            this.show = true;
        },

        confirm() {
            this.show = false;
            if (this.resolvePromise) {
                this.resolvePromise(true);
            }
        },

        cancel() {
            this.show = false;
            if (this.resolvePromise) {
                this.resolvePromise(false);
            }
        }
    }
}

// Global helper function to show confirmation modals
// Returns a Promise that resolves to true (confirmed) or false (cancelled)
window.showConfirm = function(options = {}) {
    return new Promise((resolve) => {
        window.dispatchEvent(new CustomEvent('show-confirm', {
            detail: {
                title: options.title || '{{ __("Are you sure?") }}',
                message: options.message || '',
                confirmText: options.confirmText || '{{ __("Confirm") }}',
                cancelText: options.cancelText || '{{ __("Cancel") }}',
                type: options.type || 'danger',
                resolve: resolve
            }
        }));
    });
};

// Convenience methods for common confirmations
window.confirmDelete = function(itemName = '{{ __("this item") }}') {
    return window.showConfirm({
        title: '{{ __("Delete") }} ' + itemName + '?',
        message: '{{ __("This action cannot be undone.") }}',
        confirmText: '{{ __("Delete") }}',
        type: 'danger'
    });
};

window.confirmCancel = function(itemName = '{{ __("this item") }}') {
    return window.showConfirm({
        title: '{{ __("Cancel") }} ' + itemName + '?',
        message: '{{ __("This action cannot be undone.") }}',
        confirmText: '{{ __("Yes, cancel it") }}',
        type: 'warning'
    });
};
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
