<div>
    <div class="flex justify-between">
        @if($this->isTags)
            <div>
                <a href="/{{ $user->slug }}">{{ $name != "" ? $name : $user->slug }}</a> <span class="scale-x-75 inline-block relative bottom-[0.05rem]">▸</span>
                @if(count($getTags) > 1)
                    {!! $htmlTags !!}
                @else
                    {{ implode(', ', $getTags) }}
                @endif
                {{ count($books) >= 1 ? "(".count($books).")" : ""}}
            </div>
        @else
            <div>{{ $name != "" ? $name : $user->slug }} {{ count($books) >= 1 ? "(".count($books).")" : ""}}</div>
        @endif
        <a href="{{ $hashtag }}" id="showAsList" class="text-blue-500 underline text-xs p-2 pr-0" wire:click="showAsList()">{{ $showAsListText }}</a>
    </div>
    <hr class="dark:border-zinc-700">
    <div class="mt-4">
        @if($showAsList)
        <ul class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8 3xl:grid-cols-9 4xl:grid-cols-10 gap-4">
            @foreach($books as $book)
            <li class="relative after:rounded-sm after:content-[''] after:left-0 after:right-0 after:top-0 after:bottom-0 after:absolute after:shadow-[inset_0_0_0_1px_white] dark:after:shadow-[inset_0_0_0_1px_black]">
                <img src="{{ $book['image'] }}" alt="{{ head(explode(' : ', $book['title'])) }} by {{ $book['author'] }}" class="aspect-[2/3] bg-zinc-100 w-full object-cover rounded-sm">
            </li>
            @endforeach
        </ul>
        @else
        <ul class="mx-auto max-w-2xl">
            @foreach(collect($books)->sortBy('title') as $book)
            <li>
                <p><strong>{{ head(explode(' : ', $book['title'])) }}</strong> by {{ $book['author'] }}</p>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
    @if($showMore)
        <button class="text-blue-500 underline text-xs p-2" wire:click="showAll()">Show all books</button>
    @endif
</div>
@push('scripts')
<script>
    if (window.location.hash === '#list') {
        document.addEventListener('DOMContentLoaded', function(){
            @this.call('showAsList');
        });
    }
</script>
@endpush
