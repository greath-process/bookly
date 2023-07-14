<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'author_name',
    ];

    public $timestamps = false;

    public function datasetbooks(): BelongsToMany {
        return $this->belongsToMany(Datasetbook::class, 'author_datasetbook');
    }
}
