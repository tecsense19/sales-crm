<div
    x-data="toastComponent()"
    x-show="show"
    x-cloak
    class="fixed bottom-5 right-5 z-[9999999] min-w-[300px] max-w-md"
    @toast.window="showToast($event.detail.message, $event.detail.type)"
>
    <div :class="{
        'bg-green-500': type === 'success',
        'bg-red-500': type === 'error',
        'bg-blue-500': type === 'info',
        'bg-yellow-500': type === 'warning'
    }" class="rounded-lg shadow-2xl p-4 text-white flex items-center justify-between border border-white/10">
        <div class="flex items-center">
            <template x-if="type === 'success'">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </template>
            <span x-text="message" class="text-sm font-bold"></span>
        </div>
        <button @click="show = false" class="ml-4 text-white/80 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>

<script>
    function toastComponent() {
        return {
            show: false,
            message: '',
            type: 'success',
            init() {
                @if(session('success'))
                    this.showToast({!! json_encode(session('success')) !!}, 'success');
                @elseif(session('error'))
                    this.showToast({!! json_encode(session('error')) !!}, 'error');
                @endif
            },
            showToast(message, type) {
                if (!message) return;
                this.message = message;
                this.type = type;
                this.show = true;
                setTimeout(() => { this.show = false }, 5000);
            }
        }
    }
</script>
