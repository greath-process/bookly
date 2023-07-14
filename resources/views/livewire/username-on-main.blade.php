<div>
    <form action="{{ route('login') }}" class="mx-auto mt-10">
        <div class="flex flex-wrap sm:flex-nowrap gap-2 justify-center items-center">
            <label for="username" class="sr-only">Username</label>
            <div class="relative w-full sm:w-auto">
                <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none select-none">
                    bookly.com/
                </div>
                <input
                    type="text"
                    id="username"
                    class="@if($notAllow) taken @endif rounded-xl border-2 border-zinc-400 dark:border-zinc-600 w-full sm:w-auto focus:ring-blue-500 focus:border-blue-500 outline-none block pl-[7.3rem] p-2.5 dark:bg-zinc-900 dark:placeholder:text-zinc-600"
                    placeholder="yourname"
                    required="true"
                    autocomplete="off"
                    name="userName"
                    pattern = "^[a-zA-Z0-9._-]+$"
                    wire:model.debounce.200ms="userName"
                >
                @if($notAllow)
                    <div class="absolute ml-3 text-sm text-red-500 font-bold">{{ __('profile.taken') }}</div>
                @endif
            </div>
            <button type="submit"
                    @if($notAllow) disabled="disabled" @endif
                    class="disabled:opacity-50 disabled:pointer-events-none select-none whitespace-nowrap pb-2.5 pt-2 px-4 text-white dark:text-black bg-black dark:bg-white rounded-xl border-2 border-black dark:border-white w-full sm:w-auto focus:ring-2 focus:outline-none focus:ring-blue-700 focus:ring-offset-2 dark:focus:ring-offset-black outline-none active:translate-y-0.5 active:focus:ring-0">
                Claim your Bookly
            </button>
        </div>
    </form>
</div>
