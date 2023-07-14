<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Datasetbook extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bestsellers_rank',
        'format_id',
        'book_id',
        'image_path',
        'image_url',
        'isbn10',
        'isbn13',
        'lang',
        'publication_date',
        'rating_avg',
        'rating_count',
        'author_name',
        'title',
    ];

    protected $with = [
        'authors'
    ];

    public $timestamps = false;

    public function authors(): BelongsToMany {
        return $this->belongsToMany(Author::class, 'author_datasetbook');
    }

    protected $searchable = [
        'title' ,
        'isbn10',
        'isbn13',
        'authors.name',
    ];

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'isbn10' => $this->isbn10,
            'isbn13' => $this->isbn13,
            'authors' => [
                'name' => $this->authors()?->first() ? implode(', ', $this->authors()?->pluck('author_name')?->toArray()) : null,
            ],
            'lang' => $this->lang,
        ];
    }

    public function getSearchableOptions(): array
    {
        return [
            'rankingRules' => [
                'sort',
                'words',
                'typo',
                'proximity',
                'attribute',
                'exactness'
            ],
        ];
    }
}
