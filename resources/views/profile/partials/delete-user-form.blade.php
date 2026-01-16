<section class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 transition-all hover:shadow-md">
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-xl font-extrabold text-red-600 flex items-center gap-2">
            <span class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            {{ __('Xóa tài khoản') }}
        </h2>

        <p class="mt-2 text-sm text-slate-500 ml-10">
            {{ __('Một khi xóa, mọi dữ liệu sẽ mất vĩnh viễn. Hãy cân nhắc kỹ.') }}
        </p>
    </header>

    <div class="flex items-center gap-4 pt-2">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2"
        >
            <i class="fa-solid fa-trash-can"></i> {{ __('Xóa tài khoản') }}
        </x-danger-button>
    </div>

    <!-- Modal Xác Nhận -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600 text-3xl animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-800">
                    {{ __('Bạn có chắc chắn muốn xóa?') }}
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ __('Hành động này không thể hoàn tác. Toàn bộ dữ liệu, lịch sử mua hàng và số dư ví sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận.') }}
                </p>
            </div>

            <!-- Password Input -->
            <div class="mt-6">
                <label for="password" class="sr-only">{{ __('Mật khẩu') }}</label>
                <div class="relative group">
                    <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-red-500 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder-slate-400"
                        placeholder="{{ __('Nhập mật khẩu của bạn') }}"
                    />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500 text-sm font-medium" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="py-2.5 px-5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-bold transition-all">
                    {{ __('Hủy bỏ') }}
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-red-500/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> {{ __('Xác nhận xóa') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>