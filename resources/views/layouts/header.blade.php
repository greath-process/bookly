<header class="flex justify-between">
    <div>
    {{-- @if(!Route::is('profile')) --}}
        <a href="/" class="inline-block p-2 leading-none h-[1rem] pt-0.5">
            <svg width="375" height="89" viewBox="0 0 175 42" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-black dark:fill-white h-[1rem] w-auto pt-0.5" style="overflow: visible;">
                <text x="20" y="45" style="font-size:54px">Bookly</text>
            </svg>
        </a>
    {{-- @endif --}}
    </div>
    <div>
    @if(Route::is('main.page'))
        @guest
        <a href="{{ route('login') }}" class="inline-block p-2 leading-none">Log In</a>
        @endguest

        @auth
        <a href="{{ route('profile') }}" class="inline-block p-2 leading-none">Profile</a>
        @endauth

    @endif
    @if(Route::is('profile'))
        <a href="{{ route('logout') }}" class="inline-block p-2 leading-none">Log out</a>
    @endif
    @if((Route::is('public') || Route::is('public.tags')) && Auth::id())
        <a href="{{ route('profile') }}" class="inline-block p-2 leading-none">Profile</a>
    @endif
    </div>
</header>
