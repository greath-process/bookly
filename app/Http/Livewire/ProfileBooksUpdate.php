<?php

namespace App\Http\Livewire;

use App\Contracts\BooksSearchInterface;
use App\Http\Controllers\BookController;
use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Jobs\GetLargeCoverJob;
use App\Jobs\MakeUserImageJob;
use App\Models\Book;
use App\Models\Tag;
use App\Services\CustomerIO;
use App\Services\Helpers;
use App\Services\ImageGenerate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ProfileBooksUpdate extends Component
{
    public $books;
    public string $search = '';
    public array $searchedBooks = [];
    public string $sortAZ = 'title';
    public string $sortYear = 'year';
    public bool $loading = false;
    public bool $importBooks = false;
    public string $importText = '';
    public array $bookTags = [];
    public array $defaultTags = [];
    public array $bookTag = [];
    public array $filterTags = [];
    public array $bookSuggest = [];
    public int $lastTag = 0;
    public array $selectTag = [];


    public function mount(): void
    {
        $this->defaultTags = [];
        $this->setBooks();
    }

    public function setBooks(): void
    {
        $this->books = (new BookController())->index();
        $this->getBookTags();
    }

    public function findBook(BooksSearchInterface $booksSearch): void
    {
        if ($this->search) {
            $response = $booksSearch::getBooks($this->search);

            $this->searchedBooks = count($response) > 0 ? $this->checkAdded($response) : [];
        } else {
            $this->searchedBooks = [];
        }

        $this->showLoading(false);
    }

    public function addBook($key): void
    {
        $this->emit('set-focus');
        $this->searchedBooks[$key]['added'] = true;
        $book = $this->searchedBooks[$key];
        if ($book) {
            $addedBook = Book::where('volume_id', $book['id'])->first();
            if (!$addedBook) {
                $request = new BookStoreRequest;
                $request->title = $book['title'];
                $request->year = $book['published'];
                $request->volume_id = $book['id'];
                $request->author = $book['author'];
                $request->image = $this->getCover($book);
                $request->isbn = $book['isbn'];

                $addedBook = (new BookController())->store($request);
            }

            Auth::user()->books()->attach($addedBook->id);
        }

        $this->setBooks();
        $this->getLargeCover($book);
        $this->updateCover();
        (new CustomerIO(Auth::user()))->update();
    }

    public function delete($id): void
    {
        $this->books = $this->books->except($id);
        Auth::user()->books()->detach($id);
        $this->setBooks();
        $this->updateCover();
        (new CustomerIO(Auth::user()))->update();
    }

    public function sortBy($type): void
    {
        $this->books = $type == $this->sortAZ
            ? $this->books->sortBy($this->sortAZ, SORT_NATURAL)
            : $this->books->SortByDesc($this->sortYear);

        $this->saveSort();
    }

    public function updateTaskOrder($list, $duplicate = []): void
    {
        foreach($list as $item) {
            if (in_array($item['value'], $duplicate)) continue;
            else $duplicate[] = $item['value'];

            $request = new BookUpdateRequest;
            $request->order = $item['order'];
            (new BookController())->updateOrder($request, $item['value']);
        }
        $this->setBooks();
        $this->updateCover();
    }

    public function updateCover(): void
    {
        MakeUserImageJob::dispatch(Auth::user());
    }

    public function closeSearch() {
        $this->searchedBooks = [];
        $this->search = '';
    }

    public function getCover($book): string
    {
        return $book['image'] ?: ImageGenerate::generateCoverStub($book['title']);
    }

    public function getLargeCover($book): void
    {
        if (config('books.download_covers_from_ol')) {
            GetLargeCoverJob::dispatch($book);
        }
    }

    public function checkAdded($searchedBooks): array
    {
        $alreadyHave = $this->books->map(function ($book){
            return $book->volume_id;
        })->toArray();

        foreach($searchedBooks as $key => $book) {
            $searchedBooks[$key]['added'] = in_array($book['id'], $alreadyHave);
        }

        return $searchedBooks;
    }

    public function showLoading($loading = true): void
    {
        if ($this->loading != $loading) {
            $this->loading = $loading;
        }
    }

    public function saveSort($list = [], $order = 1): void
    {
        foreach($this->books as $item) {
            $list[] = [
                "order" => $order,
                "value" => $item->id
            ];
            $order++;
        }

        $this->updateTaskOrder($list);
    }

    public function toggleImportBooks(): void
    {
        $this->importBooks = !$this->importBooks;
        $this->closeSearch();
    }

    public function getImportProperty(): bool
    {
        return strlen($this->importText) > 7;
    }

    public function sendImportText(BooksSearchInterface $booksSearch): void
    {
        $this->showSaveToast(__('profile.import_start'));
        $this->addBooksByIbsn(Helpers::getIbsnFromText($this->importText), $booksSearch);
    }

    public function addBooksByIbsn($ibsnArray, $booksSearch): void
    {
        $update = Auth::user()->books()->count();
        foreach ($ibsnArray as $ibsn) {
            $response = $booksSearch::getBooks($ibsn);
            foreach ($response as $book) {
                $book['added'] = Auth::user()->books()->where('volume_id', $book['id'])->exists();
                if (!$book['added'] && $book['isbn'] == $ibsn || $book['isbn13'] == $ibsn) {
                    $addedBook = Book::where('volume_id', $book['id'])->first();
                    if (!$addedBook) {
                        $request = new BookStoreRequest;
                        $request->title = $book['title'];
                        $request->year = $book['published'];
                        $request->volume_id = $book['id'];
                        $request->author = $book['author'];
                        $request->image = $this->getCover($book);
                        $request->isbn = $book['isbn'];

                        $addedBook = (new BookController())->store($request);
                        $this->getLargeCover($book);
                    }
                    Auth::user()->books()->syncWithoutDetaching($addedBook->id);
                }
            }
        }
        $update = Auth::user()->books()->count() - $update;

        if($update > 0) {
            $this->setBooks();
            $this->updateCover();
            (new CustomerIO(Auth::user()))->update();
        }
        $this->showSaveToast($update .__('profile.import_end'));
        $this->importText = '';
    }

    public function showSaveToast($text): void
    {
        $this->dispatchBrowserEvent('alert',[
            'type' => 'success',
            'message' => $text
        ]);
    }

    public function addTag(int $bookId, string $tag = ''): void
    {
        $enter = $tag == '';
        $tag = ($tag == '') ? (current($this->selectTag) ?: $this->bookTag[$bookId]) : $tag;

        if($tag != '') {
            $tag = Tag::where('tag', $tag)->first() ? : Tag::create(['tag' => $tag]);
            if (!$this->books->find($bookId)->tags(Auth::id())->where('tags.id', $tag->id)->exists())
                $this->books->find($bookId)->tags(Auth::id())->attach($tag->id, ['user_id' => Auth::id()]);
            $this->filterByTags();
        }
        $this->bookTag[$bookId] = '';
        if(!$enter) {
            $this->openBookSuggest($bookId, false);
        } else {
            $this->bookSuggest[$bookId] = true;
        }
    }

    public function removeTag(int $bookId, string $tag): void
    {
        $tag = Tag::where('tag', $tag)->first();
        $this->books->find($bookId)->tags(Auth::id())->wherePivot('user_id', '=', Auth::id())->detach($tag->id);
        $this->filterByTags();
    }

    public function getBookTags(): void
    {
        $this->bookTag[0] = '';
        $this->bookSuggest[0] = false;

        $this->books->map(function ($book) {
            $this->bookTag[$book->id] = '';
            $this->bookSuggest[$book->id] = false;
        })->toArray();

        $this->bookTags = Auth::user()->books()
            ->with(['tags' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->get()
            ->pluck('tags.*.tag')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    public function getTagsProperty(): array
    {
        $tags = array_unique($this->bookTags + $this->defaultTags);
        foreach ($this->bookTag as $k => $value) {
            if (!empty($value)) {
                $tag = $value;
            }
            if ($this->bookSuggest[$k]) {
                $alreadyTags = $k == 0
                    ? $this->filterTags
                    : $this->books->find($k)->tags(Auth::id())->pluck('tag')->toArray();
                if ($alreadyTags) {
                    $tags = array_filter($tags, function($item) use ($alreadyTags) {
                        return !in_array($item, $alreadyTags);
                    });
                }
            }
        }

        $tags = !isset($tag) ? $tags : array_filter($tags, function($item) use ($tag) {
            return str_contains($item, $tag);
        });

        if (current($this->selectTag)) {
            if (!in_array(current($this->selectTag), $tags))
                $this->selectTag = [];
        }

        natsort($tags);
        return array_values($tags);
    }

    public function filterByTags(): void
    {
        $this->setBooks();
        if($this->filterTags) {
            $this->books = $this->books->filter(function ($book) {
                $tagsExist = true;
                foreach ($this->filterTags as $tag) {
                    if (!$book->tags(Auth::id())->where('tag', $tag)->exists()) {
                        $tagsExist = false;
                        break;
                    }
                }

                return $tagsExist;
            });
        }
    }

    public function addFilterTag(string $tag = ''): void
    {
        $enter = $tag == '';
        $tag = ($tag == '') ? (current($this->selectTag) ?: $this->bookTag[0]) : $tag;

        if($tag != '') {
            $this->filterTags[] = $tag;
            $this->filterTags = array_unique($this->filterTags);

            $this->filterByTags();
        }
        $this->bookTag[0] = '';
        if(!$enter) {
            $this->bookSuggest[0] = false;
        }
    }

    public function removeFilterTag(string $tag): void
    {
        $this->filterTags = array_filter($this->filterTags, function($item) use ($tag) {
            return $item !== $tag;
        });

        $this->filterByTags();
    }

    public function openBookSuggest(int $bookId, bool $bool, bool $leave = false): void
    {
        $this->bookSuggest[$bookId] = $bool;
        $this->lastTag = 0;
        $this->selectTag = [];
        if($leave) $this->bookTag[$bookId] = '';
    }

    public function lastTag(int $bookId): void
    {
        if(!strlen($this->bookTag[$bookId])) {
            if ($this->lastTag > 0) {
                $tag = $this->books->find($bookId)->tags(Auth::id())?->get()?->last()?->tag;
                if($tag) {
                    $this->removeTag($bookId, $tag);
                }
                $this->lastTag = 0;
            } else {
                $this->lastTag = $bookId;
            }
        }
    }

    public function isLastTag(int $bookId, int $key): bool
    {
        return (($key + 1) == count($this->books->find($bookId)->tags(Auth::id())->get())) && $this->lastTag == $bookId && !strlen($this->bookTag[$bookId]);
    }

    public function isHighlighted(int $key): bool
    {
        $tags = $this->getTagsProperty();
        return $tags && isset($tags[$key]) && $this->selectTag && isset($this->selectTag[$key]) && $tags[$key] == $this->selectTag[$key];
    }

    public function highlight(bool $direction): void
    {
        $tags = $this->getTagsProperty();
        if($tags) {
            if(empty($this->selectTag)) {
                $this->selectTag = $direction
                    ? [key(array_slice($tags, -1, 1, true)) => end($tags) ]
                    : [ 0 => current($tags) ];
            } else {
                $key = key($this->selectTag);
                $key = $direction ? ($key - 1) : ($key + 1);
                if(($key + 1) > count($tags)) $key = 0;
                if(($key + 1) <= 0) $key = count($tags) - 1;
                $this->selectTag = [$key => $tags[$key]];
            }
        }
    }

    public function updatedBookTag($value)
    {
        foreach($this->bookTag as $k => $tag){
            if ($tag) {
                $this->openBookSuggest($k, true);
                break;
            }
        }
    }

    public function render(): View
    {
        return view('livewire.profile-books-update', [
            'tags' => $this->tags,
        ]);
    }
}
