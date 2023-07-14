<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Services\Helpers;
use App\Services\ImageGenerate;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PublicBooks extends Component
{
    public array $books;
    public object $user;
    public string $name;
    public bool $showMore;
    public int $count = 200;
    public bool $showAsList = false;
    public string $showAsListText;
    public string $hashtag;
    public bool $isTags = false;
    public array $getTags = [];
    public string $htmlTags;


    public function mount($userId) {
        $this->user = User::find($userId);
        $this->name = $this->user->name ?? '';
        $this->showMore = $this->user->books()->count() > $this->count;
        $this->isTags = $this->isTags();
        $this->getTags = $this->getTags();
        $this->htmlTags = $this->getHtmlTags();
        $this->books = $this->showMore
            ? $this->allBooks(false)
            : $this->allBooks();

        $this->showAsList();
    }

    public function showAll() {
        $this->books = $this->allBooks();
        $this->showMore = false;
    }

    public function getTags(): array
    {
        return isset(Route::current()->parameters['tags'])
            ? explode('/', Helpers::clearTheTag(Route::current()->parameters['tags']))
            : [];
    }

    public function isTags(): bool
    {
        return isset(Route::current()->parameters['tags']);
    }

    public function isOneTag(): bool
    {
        return isset(Route::current()->parameters['tags']);
    }

    public function getHtmlTags(): string
    {
        $linkTags = '';
        foreach ($this->getTags as $tag) {
            $linkTags .= '<a href="/'.Route::current()->parameters['slug'].'/'. $tag .'">'. $tag .'</a>, ';
        }

        $linkTags = rtrim($linkTags, ', ');

        return $linkTags;
    }

    public function allBooks($all = true) {
        $booksQuery = $this->isTags
            ? $this->user->books()
                ->where(function ($query) {
                    $query->whereHas('tags', function ($query) {
                        $query->whereIn('tag', $this->getTags);

                    });
                    if(!in_array(config('books.non_public_tag'),$this->getTags)) {
                        $query->whereDoesntHave('tags', function ($query) {
                            $query->where('tag', config('books.non_public_tag'));
                        });
                    }
                })
            : $this->user->books()
                ->whereDoesntHave('tags', function ($query) {
                    $query->where('tag', config('books.non_public_tag'));
                });

        $books = $all
            ? $booksQuery->select(['image', 'big_image', 'title', 'author'])->get()
            : $booksQuery->limit($this->count)->select(['image', 'big_image', 'title', 'author'])->get();

        return $books->map(function ($bk) {
            $big_image = $bk->big_image ? (file_exists($bk->big_image) ? $bk->big_image : null) : null;
            return [
                'image' => $big_image ? ImageGenerate::covers.basename($big_image) : $bk->image,
                'title' => $bk->title,
                'author' => $bk->author,
            ];
        })->toArray();
    }

    public function showAsList() {
        $this->showAsList = !$this->showAsList;
        $this->showAsListText = $this->showAsList ? 'Show as list' : 'Show as covers';
        $this->hashtag = $this->showAsList ? '#list' : '#covers';
    }

    public function render()
    {
        return view('livewire.public-books');
    }
}
