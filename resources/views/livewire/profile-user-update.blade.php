<div>
    <form class="flex flex-wrap sm:flex-nowrap gap-2" wire:submit.prevent="saveUserData()" onkeydown="return event.key != 'Enter';">
        <div class="relative w-full clickable md:w-1/3" wire:click="usernameEdit()">
            @if($usernameEdit)
            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none pt-6 pb-1">
                bookly.com/
            </div>
            <input
                    type="text"
                    id="username"
                    class="@if($taken) border-red-500 focus:outline-red-500 @endif rounded-xl border-2 dark:bg-black border-zinc-400 dark:border-zinc-600 w-full focus:ring-blue-500 focus:border-blue-500 block pl-[7.2rem] pt-6 pb-1 pr-2.5 outline-none"
                    placeholder="yourname"
                    required="true"
                    autocomplete="off"
                    autofocus="autofocus"
                    pattern = "^[a-zA-Z0-9._-]+$"
                    wire:focusout="saveUserName()"
                    wire:model.debounce.500ms="username"
                    wire:keydown.enter="saveUserName()"
            >
            @if($taken)
                <div class="absolute ml-3 text-sm text-red-500 font-bold">{{ __('profile.taken') }}</div>
            @endif
            @endif
            <label for="username" class="absolute text-xs top-0 pt-2 pl-3">Username</label>
            @if(!$usernameEdit)
            <div id="username" class="rounded-xl border-2 border-zinc-200 dark:border-zinc-700 w-full focus:ring-blue-500 focus:border-blue-500 block pl-2.5 pt-6 pb-1 pr-2.5" placeholder="yourname" required="true" autocomplete="off">
                <a href="/{{ $username }}" class="text-blue-500 underline">bookly.com/{{ $username }}</a>
            </div>
            @endif
        </div>
        <div class="relative w-full md:w-2/3 md:pr-4">
            <input
                    type="text"
                    id="name"
                    class="rounded-xl border-2 dark:bg-black border-zinc-200 dark:border-zinc-700 w-full focus:ring-blue-500 focus:border-blue-500 outline-none block pt-6 pb-1 px-2.5 dark:placeholder:text-zinc-600"
                    placeholder="{{$username}}'s bookly"
                    autocomplete="off"
                    wire:model="name"
                    wire:focusout="saveName()"
                    wire:keydown.enter="saveName()"
            >
            <label for="name" class="absolute text-xs top-0 pt-2 pl-3">Name</label>
        </div>
    </form>
</div>
