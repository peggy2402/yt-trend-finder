<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between bg-slate-800">
            <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <div class="relative group">
                    <div class="absolute inset-0 bg-blue-400 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    <span class="relative w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-lg border border-slate-100 dark:border-slate-700">
                        <i class="fa-solid fa-user-shield text-xl"></i>
                    </span>
                </div>
                <div>
                    <span class="block text-slate-800 dark:text-slate-100">{{ __('Cài đặt tài khoản') }}</span>
                    <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">Quản lý thông tin & bảo mật</span>
                </div>
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-[#0f172a] min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                
                <!-- SIDEBAR NAVIGATION (Desktop) -->
                <div class="hidden lg:block lg:col-span-3">
                    <nav class="sticky top-24 space-y-2">
                        <a href="#profile-info" 
                           class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl transition-all shadow-sm hover:shadow-md border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-3 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/30 transition-colors">
                                <i class="fa-regular fa-id-card"></i>
                            </span>
                            Thông tin cá nhân
                        </a>

                        <a href="#update-password" 
                           class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl transition-all shadow-sm hover:shadow-md border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-3 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/30 transition-colors">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            Đổi mật khẩu
                        </a>

                        <a href="#delete-account" 
                           class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 rounded-xl transition-all shadow-sm hover:shadow-md border border-transparent hover:border-red-100 dark:hover:border-red-900/30">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center mr-3 text-slate-500 dark:text-slate-400 group-hover:text-red-600 group-hover:bg-red-100 dark:group-hover:bg-red-800/30 transition-colors">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </span>
                            Khu vực nguy hiểm
                        </a>
                    </nav>
                </div>

                <!-- MAIN CONTENT -->
                <div class="lg:col-span-9 space-y-8">
                    
                    <!-- 1. Form Thông tin cá nhân -->
                    <div id="profile-info" class="scroll-mt-24 bg-slate-800 dark:bg-slate-800 sm:rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-10 dark:border-slate-700 overflow-hidden">
                        <div class="p-6 sm:p-10 max-w-2xl">
                            <header class="mb-6">
                                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <i class="fa-regular fa-id-card text-blue-500"></i>
                                    {{ __('Thông tin hồ sơ') }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-100 dark:text-slate-400">
                                    {{ __("Cập nhật thông tin hồ sơ và địa chỉ email của tài khoản.") }}
                                </p>
                            </header>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- 2. Form Đổi mật khẩu -->
                    <div id="update-password" class="scroll-mt-24 bg-slate-800 dark:bg-slate-800 sm:rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <div class="p-6 sm:p-10 max-w-2xl">
                            <header class="mb-6">
                                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <i class="fa-solid fa-key text-blue-500"></i>
                                    {{ __('Cập nhật mật khẩu') }}
                                </h2>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                                    {{ __("Đảm bảo tài khoản của bạn đang sử dụng mật khẩu dài, ngẫu nhiên để bảo mật.") }}
                                </p>
                            </header>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- 3. Form Xóa tài khoản -->
                    <div id="delete-account" class="scroll-mt-24 bg-red-50/50 dark:bg-red-900/10 sm:rounded-3xl shadow-sm border border-red-100 dark:border-red-900/30 overflow-hidden">
                        <div class="p-6 sm:p-10 max-w-2xl">
                             <header class="mb-6">
                                <h2 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    {{ __('Xóa tài khoản') }}
                                </h2>
                                <p class="mt-1 text-sm text-red-600/80 dark:text-red-400/80">
                                    {{ __("Sau khi xóa tài khoản, toàn bộ dữ liệu sẽ bị xóa vĩnh viễn. Hãy cân nhắc kỹ.") }}
                                </p>
                            </header>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>