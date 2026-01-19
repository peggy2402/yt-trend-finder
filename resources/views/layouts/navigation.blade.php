<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="{ 'bg-slate/80 backdrop-blur-lg shadow-sm': scrolled, 'bg-slate-800': !scrolled }"
     class="border-b border-slate-700 sticky top-0 z-50 transition-all duration-300">
    
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- LOGO ZENTRA -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group relative">
                        <div class="absolute -inset-2 bg-gradient-to-r from-red-500 to-slate-900 rounded-lg blur opacity-20 group-hover:opacity-40 transition duration-200"></div>
                        <!-- Icon Logo -->
                        <div class="relative w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white text-lg group-hover:scale-105 transition-transform shadow-xl shadow-slate-900/20">
                            <i class="fa-solid fa-bolt text-yellow-400"></i>
                        </div>
                        <!-- Text Logo -->
                        <div class="relative flex flex-col">
                            <span class="font-black text-xl tracking-tighter leading-none text-slate-100 group-hover:text-red-600 transition-colors">ZENTRA</span>
                            <span class="text-[10px] font-bold text-slate-400 tracking-[0.2em] leading-none group-hover:text-slate-100 transition-colors">GROUP</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="group rounded-xl px-4 py-2 text-sm font-bold transition-all duration-200 hover:bg-slate-500 border-0 h-10 inline-flex items-center gap-2
                        {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-red-600' : 'text-slate-600' }}">
                        <i class="fa-solid fa-store {{ request()->routeIs('dashboard') ? 'text-red-600' : 'text-slate-400 group-hover:text-red-500' }} transition-colors"></i> 
                        {{ __('Cửa hàng') }}
                    </x-nav-link>

                    <!-- Link Nạp tiền (New) -->
                    <x-nav-link href="{{ route('deposit') }}" :active="false" 
                        class="group rounded-xl px-4 py-2 text-sm font-bold transition-all duration-200 hover:bg-slate-500 border-0 h-10 inline-flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-wallet text-slate-400 group-hover:text-green-500 transition-colors"></i> 
                        {{ __('Nạp tiền') }}
                    </x-nav-link>

                    <!-- Link Lịch sử (New) -->
                    <x-nav-link href="{{ route('history') }}" :active="false" 
                        class="group rounded-xl px-4 py-2 text-sm font-bold transition-all duration-200 hover:bg-slate-500 border-0 h-10 inline-flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400 group-hover:text-blue-500 transition-colors"></i> 
                        {{ __('Lịch sử') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                
                <!-- Wallet Balance Display -->
                <div class="hidden lg:flex items-center gap-2 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200">
                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xs">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Số dư</span>
                        <span class="text-xs font-black text-slate-100">{{ number_format(Auth::user()->balance ?? 0) }}đ</span>
                    </div>
                    <a href="{{route('deposit')}}" class="ml-1 text-slate-300 hover:text-green-600 transition-colors" title="Nạp ngay">
                        <i class="fa-solid fa-circle-plus"></i>
                    </a>
                </div>

                <div class="h-8 w-[1px] bg-slate-200 mx-1"></div>

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 transition duration-150 ease-in-out group outline-none">
                            <div class="text-right hidden xl:block">
                                <div class="text-xs font-bold text-slate-100 group-hover:text-red-400 transition-colors">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-slate-200 font-medium">Thành viên</div>
                            </div>
                            
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1e293b&color=fff&size=40" 
                                    class="rounded-xl w-9 h-9 border-2 border-slate-500 shadow-md group-hover:shadow-lg transition-all group-hover:scale-105" alt="Avatar">
                                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-slate-500 rounded-full"></div>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Header in Dropdown -->
                        <div class="px-4 py-3 border-b border-slate-800 bg-slate-800">
                            <div class="font-bold text-slate-100">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-slate-100 truncate">{{ Auth::user()->email }}</div>
                            <div class="mt-2 flex items-center justify-between bg-slate-800 p-2 rounded-lg border border-slate-600 shadow-sm">
                                <span class="text-xs font-bold text-slate-100">Ví:</span>
                                <span class="text-sm font-black text-green-200">{{ number_format(Auth::user()->balance ?? 0) }}đ</span>
                            </div>
                        </div>

                        <div class="py-1 bg-slate-800">
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-red-600 group">
                                <span class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center mr-3 text-slate-500 group-hover:bg-red-50 group-hover:text-red-600 transition-colors">
                                    <i class="fa-solid fa-user-gear text-xs"></i>
                                </span>
                                {{ __('Cài đặt tài khoản') }}
                            </x-dropdown-link>
                        </div>

                        <div class="border-t border-slate-100 bg-slate-800">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" 
                                        class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium group">
                                    <span class="w-6 h-6 rounded-lg bg-red-100 flex items-center justify-center mr-3 text-red-500 group-hover:bg-red-200 transition-colors">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                                    </span>
                                    {{ __('Đăng xuất') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden gap-3">
                <!-- Mobile Balance (Compact) -->
                <div class="flex items-center gap-1.5 bg-slate-800 px-2 py-1 rounded-lg border border-slate-10">
                    <i class="fa-solid fa-wallet text-green-500 text-xs"></i>
                    <span class="text-xs font-bold text-slate-100">{{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }} đ</span>
                </div>

                <button @click="open = ! open" class="relative inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out group">
                    <div class="absolute inset-0 bg-slate-200 rounded-xl opacity-0 group-hover:opacity-50 transition-opacity"></div>
                    <!-- Animated Icon -->
                    <div class="relative w-6 h-6 flex flex-col justify-center items-center overflow-hidden">
                        <span :class="{'translate-y-1.5 rotate-45': open, '-translate-y-1': !open}" class="absolute w-5 h-0.5 bg-current transform transition-all duration-300 rounded-full"></span>
                        <span :class="{'opacity-0': open}" class="absolute w-5 h-0.5 bg-current transform transition-all duration-300 rounded-full"></span>
                        <span :class="{'-translate-y-1.5 -rotate-45': open, 'translate-y-1': !open}" class="absolute w-5 h-0.5 bg-current transform transition-all duration-300 rounded-full"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden absolute top-16 left-0 right-0 bg-white border-b border-slate-200 shadow-2xl z-40 max-h-[85vh] overflow-y-auto">
        
        <!-- User Info Card Mobile -->
        <div class="p-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ef4444&color=fff&size=50" class="rounded-xl border-2 border-white/20 shadow-lg" alt="Avatar">
                <div>
                    <div class="font-bold text-lg leading-tight">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-400 font-medium">{{ Auth::user()->email }}</div>
                    <div class="mt-1 inline-flex items-center gap-1.5 bg-white/10 px-2 py-0.5 rounded text-xs font-bold text-green-400 border border-white/10">
                        <i class="fa-solid fa-coins"></i> {{ number_format(Auth::user()->balance ?? 0) }} VNĐ
                    </div>
                </div>
            </div>
        </div>

        <div class="p-2 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-800 {{ request()->routeIs('dashboard') ? 'bg-red-100 text-red-600' : '' }}">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    {{ __('Cửa hàng') }}
                </div>
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('deposit') }}" :active="false" class="rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                    {{ __('Nạp tiền tài khoản') }}
                </div>
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('history') }}" :active="false" class="rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    {{ __('Lịch sử giao dịch') }}
                </div>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tài khoản</div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center p-3 bg-white border border-slate-200 rounded-xl hover:border-slate-300 transition-colors shadow-sm">
                    <i class="fa-solid fa-user-gear text-slate-500 text-xl mb-1"></i>
                    <span class="text-xs font-bold text-slate-700">Cài đặt</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="h-full">
                    @csrf
                    <button type="submit" class="w-full h-full flex flex-col items-center justify-center p-3 bg-white border border-red-100 rounded-xl hover:bg-red-50 transition-colors shadow-sm group">
                        <i class="fa-solid fa-power-off text-red-500 text-xl mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold text-red-600">Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>