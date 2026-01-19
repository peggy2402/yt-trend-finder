<section class="bg-slate-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-10 transition-all hover:shadow-md">
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-xl font-extrabold text-slate-100 flex items-center gap-2">
            <span class="w-8 h-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-sm">
                <i class="fa-solid fa-id-card"></i>
            </span>
            {{ __('Thông tin cá nhân') }}
        </h2>

        <p class="mt-2 text-sm text-slate-100 ml-10">
            {{ __("Cập nhật thông tin hồ sơ và địa chỉ email của tài khoản.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name Input -->
        <div>
            <label for="name" class="block text-sm font-bold text-slate-100 mb-2">{{ __('Họ và Tên') }}</label>
            <div class="relative group">
                <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i class="fa-regular fa-user"></i>
                </span>
                <input id="name" name="name" type="text" class="w-full bg-slate-600 border border-slate-50 rounded-xl py-3 pl-10 pr-4 text-slate-100 font-semibold focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder-slate-100" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Nhập tên hiển thị" />
            </div>
            <x-input-error class="mt-2 text-red-500 text-sm font-medium" :messages="$errors->get('name')" />
        </div>

        <!-- Email Input -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-100 mb-2">{{ __('Email Đăng nhập') }}</label>
            <div class="relative group">
                <span class="absolute left-4 top-3.5 text-slate-400 group-focus-within:text-red-500 transition-colors">
                    <i class="fa-regular fa-envelope"></i>
                </span>
                <input id="email" name="email" type="email" class="w-full bg-slate-600 border border-slate-50 rounded-xl py-3 pl-10 pr-4 text-slate-100 font-semibold focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder-slate-400" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="name@example.com" />
            </div>
            <x-input-error class="mt-2 text-red-500 text-sm font-medium" :messages="$errors->get('email')" />

            <!-- Unverified Warning -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl flex flex-col gap-2">
                    <p class="text-sm text-orange-700 font-medium flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ __('Email của bạn chưa được xác thực.') }}
                    </p>

                    <button form="send-verification" class="text-sm font-bold text-orange-600 underline hover:text-orange-800 w-fit transition-colors">
                        {{ __('Nhấn vào đây để gửi lại email xác thực.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-bold text-green-600 bg-green-50 p-2 rounded-lg border border-green-100 flex items-center gap-2">
                             <i class="fa-solid fa-check-circle"></i>
                            {{ __('Liên kết xác thực mới đã được gửi!') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 pt-2 border-t border-slate-100 mt-2">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-slate-900/20 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> {{ __('Lưu thay đổi') }}
            </button>

            @if (session('status') === 'profile-updated')
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