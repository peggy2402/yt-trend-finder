<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- LOGO ZENTRA -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <!-- Icon Logo -->
                        <div class="w-9 h-9 bg-slate-900 rounded-xl flex items-center justify-center text-white text-lg group-hover:bg-red-600 transition-colors shadow-lg shadow-slate-900/20">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <!-- Text Logo -->
                        <div class="flex flex-col">
                            <span class="font-extrabold text-xl tracking-tight leading-none text-slate-900">ZENTRA</span>
                            <span class="text-[10px] font-bold text-red-600 tracking-widest leading-none">GROUP</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-sm font-bold">
                        <i class="fa-solid fa-store mr-2"></i> {{ __('Cửa hàng') }}
                    </x-nav-link>
                    
                    <!-- Bro có thể thêm các link khác ở đây -->
                </div>
            </div>

            <!-- Settings Dropdown (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-xl text-slate-600 bg-slate-50 hover:bg-slate-100 hover:text-slate-800 focus:outline-none transition ease-in-out duration-150 gap-2 border border-slate-200">
                            <!-- Avatar nhỏ -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ef4444&color=fff&size=28" class="rounded-full w-6 h-6 border border-white shadow-sm" alt="Avatar">
                            
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Header Dropdown -->
                        <div class="block px-4 py-3 text-xs text-slate-400 border-b border-slate-100 mb-1">
                            <span class="block font-bold text-slate-700">Tài khoản</span>
                            <span class="truncate">{{ Auth::user()->email }}</span>
                        </div>

                        <!-- Profile Link -->
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fa-solid fa-user-gear mr-2 text-slate-400"></i> {{ __('Cài đặt tài khoản') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" 
                                    class="text-red-600 hover:bg-red-50 hover:text-red-700 flex items-center">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> {{ __('Đăng xuất') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-slate-200 shadow-xl">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold">
                <i class="fa-solid fa-store mr-2"></i> {{ __('Cửa hàng') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-200 bg-slate-50">
            <div class="px-4 flex items-center gap-3 mb-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ef4444&color=fff&size=40" class="rounded-full w-10 h-10 border-2 border-white shadow" alt="Avatar">
                <div>
                    <div class="font-bold text-base text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fa-solid fa-user-gear mr-2 text-slate-400"></i> {{ __('Cài đặt tài khoản') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-red-600">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> {{ __('Đăng xuất') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>