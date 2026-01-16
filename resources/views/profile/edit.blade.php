<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500">
                <i class="fa-solid fa-user-gear"></i>
            </span>
            {{ __('Cài đặt tài khoản') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#f3f4f6]">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. Form Thông tin cá nhân -->
            <!-- Đã có khung Card trong file partial, chỉ cần include -->
            @include('profile.partials.update-profile-information-form')

            <!-- 2. Form Đổi mật khẩu -->
            <!-- Đã có khung Card trong file partial, chỉ cần include -->
            @include('profile.partials.update-password-form')

            <!-- 3. Form Xóa tài khoản -->
            <!-- Đã có khung Card trong file partial, chỉ cần include -->
            @include('profile.partials.delete-user-form')

        </div>
    </div>
</x-app-layout>