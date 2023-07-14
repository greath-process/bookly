
    @if($sended)
        <main class="w-full relative text-center mt-20 p-4 mx-auto flex-auto">
            <h1 class="font-bold">Check your inbox</h1>
            <p>To finish signing in, click the link we sent to {{ $email }}</p>
            <p class="text-xs mt-5">Wrong address? <a wire:click="tryAgain" style="cursor:pointer" class="underline text-blue-600">Try again</a></p>
        </main>
    @else
        <main class="w-full mt-20 p-4 mx-auto flex-auto">
            <div class="relative text-center">
                @if ($error)
                    <div class="">{{ $error }}</div>
                @endif
                @if($isLogin)
                    <h1 class="font-bold">Log into your account</h1>
                    <p>We'll email you a magic link for a password-free sign in.</p>
                @else
                    <h1 class="font-bold">Create your account</h1>
                    <p>Choose your Bookly username. You can always change it later.</p>
                @endif
            </div>
            <form
                class="mx-auto max-w-sm mt-10 flex flex-wrap gap-2 justify-center items-center"
                wire:submit.prevent="sendForm"
            >
                @if(!$isLogin)
                <label for="username" class="sr-only">Username</label>
                <div class="relative w-full">
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        bookly.com/
                    </div>
                    @if($notAllow)
                        <div class="absolute ml-3 text-sm text-red-500 font-bold -translate-y-full pb-0.5">{{ __('profile.taken') }}</div>
                    @endif
                    <input
                            type="text"
                            id="username"
                            class="@if($notAllow || $errors->has('userName')) border-red-500 focus:outline-red-500 @endif dark:bg-zinc-900 rounded-xl border-2 border-zinc-400 dark:border-zinc-700 focus:ring-blue-700 focus:ring-offset-2 outline-none w-full block pl-[7.3rem] p-2.5 pt-2 dark:placeholder:text-zinc-600"
                            placeholder="yourname"
                            required="true"
                            autocomplete="off"
                            pattern = "^[a-zA-Z0-9._-]+$"
                            wire:model.debounce.200ms="userName"
                    >
                </div>
                @endif
                <input
                        type="email"
                        id="email"
                        class="dark:bg-zinc-900 rounded-xl border-2 border-zinc-400 dark:border-zinc-700 w-full focus:ring-blue-500 focus:border-blue-500 outline-none block p-2.5 pt-2 dark:placeholder:text-zinc-600"
                        placeholder="Email"
                        required="true"
                        autocomplete="off"
                        autofocus
                        wire:model.lazy="email"
                >
                @if($errors->has('email'))
                    <span class="text-red-500">{{ $errors->first('email') }}</span>
                @endif
                @if($isLogin)
                    <button type="submit" class="whitespace-nowrap pb-2.5 pt-2 px-4 text-white dark:text-black bg-black dark:bg-white rounded-xl border-2 border-black w-full focus:ring-4 focus:outline-none focus:ring-black focus:ring-offset-2 active:translate-y-0.5 active:focus:ring-0">
                        Send sign in link
                    </button>
                @else
                    <button
                        type="submit"
                        class="@if($notAllow) @endif whitespace-nowrap pb-2.5 pt-2 px-4 text-white dark:text-black bg-black dark:bg-white rounded-xl border-2 border-black w-full focus:ring-2 focus:outline-none focus:ring-blue-700 focus:ring-offset-2 active:translate-y-0.5 active:focus:ring-0"
                        @if($notAllow) disabled="disabled" @endif
                    >
                        Create account
                    </button>
                    <div class="text-xs text-zinc-400">By signing up, you agree to the <a href="https://pokohq.notion.site/Bookly-11204ee0049c40d79419b72fad29fc07#" target="_blank" class="text-inherit">Terms of Service</a>.</div>
                @endif
            </form>
        </main>
    @endif
