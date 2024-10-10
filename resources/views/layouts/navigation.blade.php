<header class="bg-[#2E2A27] p-2.5 flex justify-between items-center sticky top-0 z-50 w-full">
    <div class="flex items-center w-full">
        <!-- Menu Icon -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                class="flex flex-col justify-between mr-4 cursor-pointer"
                aria-label="Toggle Mobile Menu" 
                :aria-expanded="mobileMenuOpen">
            <span :class="mobileMenuOpen ? 'rotate-45 translate-y-1.5' : ''" class="block w-7 h-[3px] bg-white mb-1 transition-all duration-300"></span>
            <span :class="mobileMenuOpen ? 'opacity-0' : ''" class="block w-7 h-[3px] bg-white mb-1 transition-all duration-300"></span>
            <span :class="mobileMenuOpen ? '-rotate-45 -translate-y-1.5' : ''" class="block w-7 h-[3px] bg-white transition-all duration-300"></span>
        </button>

        <!-- Logo and Title -->
        <div class="flex items-center">
            <img src="{{ asset('storage/images/Icon.png') }}" alt="Waribiki Watcher Logo" class="h-10 mr-3">
            <h1 class="text-3xl text-white font-mono m-0">Waribiki Watcher</h1>
        </div>
    </div>
</header>

<div x-data="{ mobileMenuOpen: false }">
    <!-- Mobile Menu -->
    <div
        class="block fixed z-10 top-0 bottom-0 height h-full w-[220px] transition-all bg-slate-900 md:hidden"
        :class="mobileMenuOpen ? 'left-0' : '-left-[220px]'"
    >
        <ul>
            <li>
                <a href="{{ route('cart.index') }}" class="relative flex items-center justify-between py-2 px-3 transition-colors hover:bg-slate-800">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cart
                    </div>
                </a>
            </li>
            @if (!Auth::guest())
                <!-- My Account and Logout links -->
                <li x-data="{ open: false }" class="relative">
                    <a @click="open = !open" class="cursor-pointer flex justify-between items-center py-2 px-3 hover:bg-slate-800">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            My Account
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <ul x-show="open" x-transition class="z-10 right-0 bg-slate-800 py-2">
                        <li>
                            <a href="{{ route('profile') }}" class="flex px-3 py-2 hover:bg-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" class="flex items-center px-3 py-2 hover:bg-slate-900"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            @else
                <!-- Login and Register links for guests -->
                <li>
                    <a href="{{ route('login') }}" class="flex items-center py-2 px-3 transition-colors hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Login
                    </a>
                </li>
                <li class="px-3 py-3">
                    <a href="{{ route('register') }}" class="block text-center text-white bg-emerald-600 py-2 px-3 rounded shadow-md hover:bg-emerald-700 active:bg-emerald-800 transition-colors w-full">
                        Register now
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
