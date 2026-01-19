<section class="bg-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 transition-all hover:shadow-md">
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-xl font-extrabold text-slate-100 flex items-center gap-2">
            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">
                <i class="fa-solid fa-lock"></i>
            </span>
            {{ __('Đổi mật khẩu') }}
        </h2>

        <p class="mt-2 text-sm text-slate-100 ml-10">
            {{ __('Hãy sử dụng mật khẩu dài và ngẫu nhiên để bảo mật tài khoản.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-slate-100 mb-2">{{ __('Mật khẩu hiện tại') }}</label>
            <div class="relative group">
                <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-slate-600 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-700 font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder-slate-400" autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-500 text-sm font-medium" />
        </div>

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block text-sm font-bold text-slate-100 mb-2">{{ __('Mật khẩu mới') }}</label>
            <div class="relative group">
                <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input id="update_password_password" name="password" type="password" class="w-full bg-slate-600 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-700 font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder-slate-400" autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-500 text-sm font-medium" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-slate-100 mb-2">{{ __('Nhập lại mật khẩu mới') }}</label>
            <div class="relative group">
                <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-slate-600 border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-slate-700 font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder-slate-400" autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-500 text-sm font-medium" />
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 pt-2 border-t border-slate-100 mt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> {{ __('Lưu mật khẩu') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold flex items-center gap-1 bg-green-50 px-3 py-1 rounded-lg border border-green-100"
                >
                    <i class="fa-solid fa-check"></i> {{ __('Đã lưu thành công.') }}
                </p>
            @endif
        </div>
    </form>
</section>