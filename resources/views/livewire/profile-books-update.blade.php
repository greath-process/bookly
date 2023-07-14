<div class="flex flex-wrap lg:flex-nowrap gap-x-10">
    <div class="mt-6 w-full lg:w-3/5">
        <div class="flex justify-between items-center">
            <div for="search" class="pl-3 mb-1 inline-block">
                <h1 class="font-bold inline">
                Bookly {{ $books->count() >= 1 ? "(".$books->count().")" : ""}}
                </h1>

                @if($bookTags)
                <div class="inline-block">
                    <ul class="inline whitespace-nowrap">
                        @foreach($filterTags as $tag)
                            <li class="pl-1.5 pt-0.5 pb-1 rounded border border-zinc-200 dark:border-zinc-800 inline-block leading-none whitespace-nowrap select-none">
                            <a href="/{{ Auth::user()->slug }}/{{ $tag }}" class="no-underline">{{ $tag }}</a><button class="ml-0.5 px-1 hover:text-red-500 select-none" wire:click="removeFilterTag('{{ $tag }}')">×</button>
                        </li>
                        @endforeach
                    </ul>
                    <div class="relative inline-block">
                        <input
                            type="text"
                            class="ring-0 outline-none bg-transparent"
                            spellcheck="false"
                            placeholder="+&thinsp;filter"
                            wire:model="bookTag.0"
                            wire:focus="openBookSuggest(0, true)"
                            wire:blur.debounce.75ms="openBookSuggest(0, false, true)"
                            wire:keydown.enter="addFilterTag()"
                            wire:keydown.arrow-up.prevent="highlight(true)"
                            wire:keydown.arrow-down.prevent="highlight(false)"
                        >
                        @if($bookSuggest[0] && count($tags) > 0)
                        <ul class="absolute bg-white border border-black dark:bg-zinc-900 rounded shadow-lg px-4 pt-2 pb-3 z-10 -left-4 max-h-[50vh] overflow-y-auto">
                            @foreach($tags as $k => $tag)
                                <li>
                                    <button
                                        class="w-full text-left hover:text-blue-600
                                        @if($this->isHighlighted($k)) text-blue-600 before:content-['•'] before:absolute before:-translate-x-full @endif"
                                        wire:click="addFilterTag('{{ $tag }}')">
                                        {{ $tag }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            @endif
            </div>

        </div>
        <hr class="dark:border-zinc-700">
        <div class="text-right -mr-1.5">
            <button wire:click="sortBy('{{ $sortAZ }}')" @disabled($books->count() < 2) class="text-blue-500 disabled:text-zinc-200 underline text-xs p-1.5">sort A&thinsp;→&thinsp;Z</button>
            <button wire:click="sortBy('{{ $sortYear }}')" @disabled($books->count() < 2) class="text-blue-500 disabled:text-zinc-200 underline text-xs p-1.5">sort new&thinsp;→&thinsp;old</button>
        </div>
        <div class="py-2" wire:sortable="updateTaskOrder">
            @foreach($books as $key => $book)
            <div class="flex items-center bg-white/90 dark:bg-black/90 border border-transparent rounded-xl transition-[border] hover:border-zinc-200 dark:hover:border-zinc-700 group draggable-element" wire:sortable.item="{{ $book->id }}" wire:key="task-{{ $book->id }}">
                <div class="py-1 w-full flex items-center">
                    <svg viewBox="0 0 75 124" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0 h-10 pl-1 sm:pl-2.5 p-2.5 fill-zinc-100 dark:fill-zinc-700 group-hover:fill-zinc-300 transition cursor-grab" wire:sortable.handle>
                        <circle cx="13" cy="13" r="13"/>
                        <circle cx="62" cy="13" r="13"/>
                        <circle cx="13" cy="62" r="13"/>
                        <circle cx="62" cy="62" r="13"/>
                        <circle cx="13" cy="111" r="13"/>
                        <circle cx="62" cy="111" r="13"/>
                    </svg>
                    <img src="{{ $book->image }}" class="aspect-[2/3] h-16 object-cover cursor-grab select-none" alt="{{ head(explode(' : ', $book['title'])) }} by {{ $book['author'] }}" wire:sortable.handle>
                    <div class="px-4 w-full">
                        <div class="leading-tight line-clamp-2">
                            <h2 class="font-bold inline">{{ head(explode(' : ', $book['title'])) }}</h2>
                            by {{ $book->author }}
                        </div>
                        <span>
                            <ul class="inline select-none">
                            @foreach($book->tags(Auth::id())->get() as $k => $tag)
                                <li
                                    class="pl-1.5 pb-0.5 rounded-full border border-black dark:border-zinc-700 inline-block leading-none text-xs select-none
                                    @if($this->isLastTag($book->id, $k))
                                        border-red-500 text-red-500
                                    @endif
                                    "
                                >
                                    <a href="/{{ Auth::user()->slug }}/{{ $tag->tag }}" class="no-underline select-none">{{ $tag->tag }}</a><button class="ml-0.5 px-1 hover:text-red-500 select-none" wire:click="removeTag({{ $book->id }},'{{ $tag->tag }}')">×</button>
                                </li>
                                @endforeach
                            </ul>
                            <div class="inline-block relative">
                                <input
                                    type="text"
                                    class="inline text-xs ring-0 outline-none bg-transparent w-20 select-none"
                                    spellcheck="false"
                                    placeholder="+&thinsp;tags"
                                    wire:model="bookTag.{{ $book->id }}"
                                    wire:keydown.enter="addTag({{$book->id}})"
                                    wire:keydown.backspace="lastTag({{$book->id}})"
                                    wire:focus="openBookSuggest({{ $book->id }}, true)"
                                    wire:blur.debounce.75ms="openBookSuggest({{ $book->id }}, false, true)"
                                    wire:keydown.arrow-up.prevent="highlight(true)"
                                    wire:keydown.arrow-down.prevent="highlight(false)"
                                    wire:keydown.escape.prevent="openBookSuggest({{ $book->id }}, true)"
                                >
                                @if(($bookSuggest[$book->id] && count($tags) > 0) || Str::length($bookTag[$book->id]) > 0)
                                <ul class="absolute bg-white border dark:bg-zinc-900 border-black dark:border-zinc-700 rounded shadow-lg p-4 pt-2 pb-3 z-10 text-xs -left-4 max-h-[50vh] overflow-y-auto">
                                    @foreach($tags as $k => $tag)
                                    <li>
                                        <button
                                            wire:click="addTag({{ $book->id }},'{{ $tag }}')"
                                            class="w-full text-left hover:text-blue-600 whitespace-nowrap
                                            @if($this->isHighlighted($k)) text-blue-600 before:content-['•'] before:absolute before:-translate-x-full @endif
                                            "
                                        >
                                            {{ $tag }}
                                        </button>
                                    </li>
                                    @endforeach
                                    @if(!$tags)
                                        <li>
                                        <button
                                            wire:click="addTag({{ $book->id }},'{{ $bookTag[$book->id] }}')"
                                            class="w-full text-left text-zinc-400 hover:text-blue-600 whitespace-nowrap"
                                        >
                                            Create "{{ $bookTag[$book->id] }}" tag
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                                @endif
                            </div>
                        </span>

                    </div>
                </div>
                <div class="mr-2 leading-none">
                    <button wire:click="delete({{ $book->id }})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-zinc-300 dark:stroke-zinc-600 hover:stroke-red-500 transition h-6" viewBox="0 0 512 512">
                            <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke-miterlimit="10" stroke-width="32"/>
                            <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M336 256H176"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="mt-6 w-full lg:w-2/5">
        <div id="search-widget" class="sticky top-4">
            <div class="z-10 relative">

                @if(!$importBooks)
                <div class="mx-auto">
                    <div class="flex justify-between items-center mr-2 md:mr-6">
                        <label for="search" class="mb-1 pl-3 inline-block font-bold">Add a book</label>
                        <button wire:click="toggleImportBooks" class="text-blue-500 underline text-xs p-1.5">import multiple</button>
                    </div>
                    <div class="relative w-full lg:pr-4">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            @if($loading)
                            <svg width="100" height="101" viewBox="0 0 100 101" fill="none" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M50.0005 16.1274C30.9669 16.1274 15.5371 31.5572 15.5371 50.5908C15.5371 69.6244 30.9669 85.0542 50.0005 85.0542C69.0341 85.0542 84.4639 69.6244 84.4639 50.5908C84.4639 31.5572 69.0341 16.1274 50.0005 16.1274ZM0.537109 50.5908C0.537109 23.2729 22.6826 1.12744 50.0005 1.12744C77.3184 1.12744 99.4639 23.2729 99.4639 50.5908C99.4639 77.9087 77.3184 100.054 50.0005 100.054C22.6826 100.054 0.537109 77.9087 0.537109 50.5908Z" class="fill-zinc-200 dark:fill-zinc-700"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M42.5 8.62744C42.5 4.48531 45.8579 1.12744 50 1.12744C72.1449 1.12744 90.8726 15.6746 97.1854 35.7154C98.4298 39.6661 96.236 43.8777 92.2852 45.1222C88.3344 46.3667 84.1229 44.1728 82.8784 40.222C78.4763 26.247 65.4101 16.1274 50 16.1274C45.8579 16.1274 42.5 12.7696 42.5 8.62744Z" class="fill-zinc-400 dark:fill-zinc-500"/>
                            </svg>
                            @else
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                            @endif
                        </div>
                        <input
                            wire:model="search"
                            wire:keyup="findBook"
                            type="search"
                            id="search"
                            class="rounded-xl border-2 border-zinc-400 dark:border-zinc-700 w-full focus:ring-blue-500 focus:border-blue-600 outline-none block pl-[2.2rem] p-2.5 pt-2 dark:bg-black dark:placeholder:text-zinc-600"
                            placeholder="Type to search"
                            required="true"
                            autocomplete="off">

                    </div>
                    <p class="text-xs pt-1 pl-3">Search by title, author, or ISBN</p>
                </div>
                @endif


                @if($importBooks)
                <div class="mx-auto">
                    <div class="flex justify-between items-center mr-2 md:mr-6">
                        <label for="search" class="mb-1 pl-3 inline-block font-bold">Add ISBN codes</label>
                        <button wire:click="toggleImportBooks" class="text-blue-500 underline text-xs p-1.5">search books</button>
                    </div>
                    <div class="relative w-full lg:pr-4">

                        <div id="dropzone_wrapper" class="rounded-xl border-2 border-zinc-400 dark:border-zinc-700 w-full focus-within:ring-blue-600 focus-within:border-blue-600 outline-none block overflow-hidden whitespace-nowrap leading-none">
                            <div class="relative">
                                <textarea wire:model="importText" id="import_text" class="dark:bg-black w-full min-h-[60vh] p-2.5 pt-2 border-none focus:border-transparent focus:ring-0 focus:outline-0 leading-tight dark:placeholder:text-zinc-600" placeholder="Paste numbers or drag and drop file"></textarea>
                                <div id="dropzone" class="absolute bg-zinc-100 dark:bg-zinc-800 h-full w-full top-0 flex justify-around items-center text-zinc-600 opacity-0 pointer-events-none">
                                    <div><strong>drop here</strong></div>
                                </div>
                            </div>
                            <label class="relative text-xs w-full block bg-zinc-100 dark:bg-zinc-800 text-zinc-500 p-2 pb-2.5 flex items-center gap-1.5">
                                <input id="import_file" accept=".csv,.txt,.xls,.xlsx" type="file" multiple="" class="absolute opacity-0 hover:cursor-pointer left-0 top-0 right-0 bottom-0">
                                <svg width="20" height="18" viewBox="0 0 20 18" xmlns="http://www.w3.org/2000/svg" class="fill-zinc-400 dark:fill-zinc-600 inline-block h-4 pt-0.5">
                                    <path d="M9.53426 1.08259C9.82801 0.791116 10.3019 0.791116 10.5956 1.08259L14.3627 4.82047C14.658 5.11355 14.6599 5.59058 14.3668 5.88595C14.0737 6.18132 13.5967 6.18318 13.3013 5.8901L10.8183 3.42635V12.9525C10.8183 13.3686 10.481 13.7059 10.0649 13.7059C9.64883 13.7059 9.31151 13.3686 9.31151 12.9525V3.42635L6.82852 5.8901C6.53315 6.18318 6.05612 6.18132 5.76304 5.88595C5.46996 5.59058 5.47181 5.11355 5.76718 4.82047L9.53426 1.08259Z"/>
                                    <path d="M1.02392 11.2713C1.44002 11.2713 1.77734 11.6086 1.77734 12.0247V15.6869H18.3525V12.0247C18.3525 11.6086 18.6898 11.2713 19.1059 11.2713C19.522 11.2713 19.8593 11.6086 19.8593 12.0247V16.4403C19.8593 16.8564 19.522 17.1937 19.1059 17.1937H1.02392C0.607824 17.1937 0.270508 16.8564 0.270508 16.4403V12.0247C0.270508 11.6086 0.607824 11.2713 1.02392 11.2713Z"/>
                                </svg>
                                <span>Attach csv/txt by dragging & dropping or <span class="underline">selecting</span></span>
                            </label>
                        </div>

                        <div class="flex justify-between items-center mt-2 gap-5">
                            <p class="text-xs">Put in any text and we'll add every valid ISBN to your bookly.</p>
                            <button @if(!$this->import) disabled @endif class="disabled:opacity-50 dark:disabled:opacity-20 disabled:pointer-events-none leading-none select-none whitespace-nowrap mt-1 h-10 px-3 bg-black dark:bg-white text-white dark:text-black rounded-xl border-2 border-black w-full sm:w-auto focus:ring-2 focus:outline-none focus:ring-blue-700 focus:ring-offset-2 active:translate-y-0.5 active:focus:ring-0 outline-none" wire:click="sendImportText">Import</button>
                        </div>
                    </div>
                </div>
                <script>
                    // Allow user to drag and drop text files onto #import_text or #import_file or select file in #import_file so the textarea is filled with the file text content
                    const import_text = document.getElementById('import_text');
                    const import_file = document.getElementById('import_file');
                    const dropzone = document.getElementById('dropzone');
                    const dropzone_wrapper = document.getElementById('dropzone_wrapper');

                    const importData = (file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (import_text.value === "") {
                        import_text.value = e.target.result;
                        } else {
                        import_text.value += "\n---\n" + e.target.result;
                        }
                    };
                    reader.readAsText(file);
                    };

                    const handleDropOrChange = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.dataTransfer && e.dataTransfer.files.length) {
                        importData(e.dataTransfer.files[0]);
                    } else if (e.target && e.target.files.length) {
                        importData(e.target.files[0]);
                    }
                        import_text.dispatchEvent(new Event('input', { bubbles: true }));
                    };

                    import_text.addEventListener('drop', handleDropOrChange);
                    import_file.addEventListener('change', handleDropOrChange);

                    document.addEventListener('dragover', (e) => {
                        dropzone.classList.add('opacity-80');
                        dropzone_wrapper.classList.add('border-blue-600');
                    });
                    document.addEventListener('dragleave', (e) => {
                        dropzone.classList.remove('opacity-80');
                        dropzone_wrapper.classList.remove('border-blue-600');
                    });
                    import_text.addEventListener('drop', (e) => {
                        dropzone.classList.remove('opacity-80');
                        dropzone_wrapper.classList.remove('border-blue-600');
                        import_text.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                    import_file.addEventListener('drop', (e) => {
                        dropzone.classList.remove('opacity-80');
                        dropzone_wrapper.classList.remove('border-blue-600');
                    });
                </script>
                @endif
            </div>
            @if($searchedBooks)
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-xl -m-2 lg:-m-5 p-5 pt-[7rem] -mt-[7rem] lg:-mt-[7rem] lg:w-[calc(100%+1.25rem)] /*shadow-[0_0_0_9999px_rgba(0,0,0,0.3)]*/">
                @foreach($searchedBooks as $key => $book)
                <div class="py-3 last:pb-0.5 border-t border-t-zinc-200 dark:border-t-zinc-700 first:border-t-0 first:mt-4 @if($book['added'])added @endif">
                    <button class="search-element w-full text-left flex gap-2 sm:gap-2 items-center rounded-md focus:ring-2 focus:outline-none focus:ring-black dark:focus:ring-white focus:ring-offset-[5px] dark:focus:ring-offset-zinc-900 disabled:opacity-30 group disabled:pointer-events-none" @if($book['added']) disabled @endif wire:click="addBook({{ $key }})">
                        <img src="{{ $book['image'] }}" class="aspect-[2/3] h-full object-cover ml-1 h-14" alt="{{ head(explode(' : ', $book['title'])) }} by {{ $book['author'] }}">
                        <div class="px-2">
                            <div class="leading-tight line-clamp-2">
                                <h2 class="font-bold inline">{{ head(explode(' : ', $book['title'])) }}</h2> by
                                {{ $book['author'] }}</div>
                            <p class="text-xs">
                                {{ Str::limit($book['published'], 4, '') }} · {{ $book['isbn'] }}
                            </p>
                        </div>
                        <div class="ml-auto sm:mr-1">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 stroke-black dark:stroke-white group-hover:stroke-blue-600 group-focus:stroke-blue-600" viewBox="0 0 512 512">
                                    <path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke-miterlimit="10" stroke-width="32"/>
                                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 176v160M336 256H176"/>
                                </svg>
                            </div>
                        </div>
                    </button>
                </div>
                @endforeach
            </div>
            @endif
            <div class="mt-6 lg:mr-4 px-3 pt-[0.875rem] pb-4 border-2 border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 dark:border-zinc-700 rounded-xl leading-none text-zinc-400 dark:text-zinc-500">
                    Book not listed? <a href="https://x9xdjkeu8dy.typeform.com/to/yJ4rOeKY" target="_blank">Let us know</a>
                </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- <link rel="stylesheet" href="{{ asset('css/simple-tag-input.css') }}"> -->
<script>
function isInvalidKey(keyCode) {
    const invalidKeys = [
        9, 16, 17, 18, 20, 35, 36,
        37, 38, 39, 40, 16,
    ];
    return invalidKeys.includes(keyCode);
}

function debounce(func, delay) {
  let timeoutId;
  return function() {
    const context = this;
    const args = arguments;
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func.apply(context, args), delay);
  };
}

document.getElementById('search').addEventListener("keydown", function(event) {
    if (!isInvalidKey(event.keyCode)) {
        @this.call('showLoading');
    }
});

document.getElementById('search').addEventListener('keyup', debounce(function(event) {
    if (!isInvalidKey(event.keyCode)) {
        @this.call('findBook');
    }
}, 300));

// Close search suggestions when input is empty
document.getElementById('search').addEventListener('input', (e) => {
    if (e.currentTarget.value === '') {
        @this.call('closeSearch');
    }
})

const searchWidget = document.getElementById('search-widget');
const input = searchWidget.querySelector('input');
document.addEventListener('keydown', (event) => {
    // focus to input if user press '/' key or ctrl + k or cmd + k
    if ((event.key === '/' && document.activeElement.tagName !== 'INPUT') || (event.key === 'k' && (event.ctrlKey || event.metaKey))) {
        event.preventDefault();
        input.focus();
        setTimeout(() => {
            input.select();
        }, 1);
    }

    // navigate with arrows inside search results
    if(document.activeElement.classList.contains('search-element') || document.activeElement.id === 'search'){
        const buttons = searchWidget.querySelectorAll('.search-element:not([disabled])');
        const index = Array.prototype.indexOf.call(buttons, document.activeElement);
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (index === buttons.length - 1) return;
            buttons[index+1].focus();
        }
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (document.activeElement.id === 'search') return;

            if (index === 0){
                input.focus();
                setTimeout(() => {
                    input.select();
                }, 1);
            } else {
                buttons[index-1].focus();
            }
        }
    }

    if (document.activeElement.classList.contains('search-element') && event.key === 'Escape') {
            event.preventDefault();
            input.focus();
            setTimeout(() => {
                input.select();
            }, 1);
        }
});
</script>
@endpush
