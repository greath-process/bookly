@extends('layouts/layout')
@section('title', 'Bookly · Digital Bookly')

@section('content')
    <main class="w-full mt-20 p-4 mx-auto flex-auto">
        <h1 class="text-center text-lg italic">Every book you've read. In one simple link.</h1>

        @livewire('username-on-main')

        <div class="mt-20 max-w-5xl mx-auto border border-zinc-200 dark:border-zinc-700 rounded-t-2xl book-block relative">
            <div class="bg-zinc-200 dark:bg-zinc-800 p-1 rounded-t-2xl">
                <div id="example_usernames" class="w-[85%] sm:w-1/2 mx-auto text-center bg-zinc-100 dark:bg-zinc-900 p-0.5 rounded-md overflow-hidden"><span class="pointer-events-none select-none">&nbsp;</span>
                    <div class="inline relative">
                        @foreach($users as $index => $user)
                        <a href="/{{$user}}" class="{{ $index != 0 ? 'opacity-0 pointer-events-none' : '' }} text-zinc-400/50 no-underline transition duration-200 absolute left-1/2 -translate-x-1/2">bookly.com/{{$user}}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="example_userbooks" class="p-4 relative min-h-[80vh]">
                @foreach($usersBooks as $userBooks)
                <div class="{{ !$loop->first ? 'opacity-0 pointer-events-none' : '' }} transition duration-200 absolute w-[calc(100%-2rem)]">
                    <ul class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                        @foreach($userBooks as $book)
                            <li class="relative after:rounded-sm after:content-[''] after:left-0 after:right-0 after:top-0 after:bottom-0 after:absolute after:shadow-[inset_0_0_0_1px_white] dark:after:shadow-[inset_0_0_0_1px_black]"><img src="{{ $book['image'] }}" alt="{{ head(explode(' : ', $book['title'])) }} by {{ $book['author'] }}" class="aspect-[2/3] w-full object-cover rounded-sm"></li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
                <div>
                    <ul class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                        @foreach(range(1, 20) as $i)
                            <li><div class="aspect-[2/3] bg-zinc-100 dark:bg-zinc-900 rounded-sm"></div></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="absolute -bottom-0.5 -left-0.5 -right-0.5 h-3/4 bg-gradient-to-t from-white dark:from-black to-white/0 dark:to-black/0 pointer-events-none"></div>

        </div>

        <script>
            const textContainer = document.getElementById('example_usernames');
            const users = textContainer.getElementsByTagName('a');
            const imageContainer = document.getElementById('example_userbooks');
            const lists = imageContainer.getElementsByTagName('div');
            let index = 0;

            setInterval(() => {
                users[index].classList.remove('opacity-0', 'pointer-events-none');
                lists[index].classList.remove('opacity-0', 'pointer-events-none');

                setTimeout(() => {
                    users[index].classList.add('opacity-0', 'pointer-events-none');
                    lists[index].classList.add('opacity-0', 'pointer-events-none');
                    index = (index + 1) % users.length;
                }, 995000);
            }, 995200);
        </script>


    </main>
@endsection

