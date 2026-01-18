<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-3">
            <span class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 shadow-sm border border-slate-200 dark:border-slate-600">
                <i class="fa-solid fa-user-gear"></i>
            </span>
            {{ __('Cài đặt tài khoản') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-[#0f172a] min-h-screen transition-colors duration-300">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Grid Layout cho Desktop: Chia cột nếu cần, hiện tại để stack dọc cho mobile-first -->
            <div class="grid grid-cols-1 gap-8">
                
                <!-- 1. Form Thông tin cá nhân -->
                @include('profile.partials.update-profile-information-form')

                <!-- 2. Form Đổi mật khẩu -->
                @include('profile.partials.update-password-form')

                <!-- 3. Form Xóa tài khoản -->
                @include('profile.partials.delete-user-form')
                
            </div>

        </div>
    </div>
</x-app-layout>