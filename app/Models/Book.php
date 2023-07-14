<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'image',
        'year',
        'volume_id',
        'isbn',
        'big_image',
    ];

    public $timestamps = false;

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'books_users')->withPivot('order');
    }

    public function tags($user_id = null): BelongsToMany {
        $query = $this->belongsToMany(Tag::class, 'book_tag')->withPivot('user_id');
        if($user_id) $query->where('user_id', $user_id);
        return $query;
    }
}
