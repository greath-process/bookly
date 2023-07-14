@if(Route::is('public') && !Auth::id())
<a href="{{ route('main.page') }}" class="sticky bottom-10 max-w-max mx-auto z-10 bg-blue-500 text-white no-underline text-sm font-bold rounded-xl p-3 px-4 shadow-[0_0_50px_50px_rgba(255,255,255,0.9)] dark:shadow-[0_0_50px_50px_rgba(0,0,0,0.7)]">Create your own bookly</a>
@endif
<footer>
    <div class="w-full p-8 text-center text-xs text-zinc-400 z-20 relative">
        <div class="inline"></div>
    </div>
</footer>
