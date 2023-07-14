<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\BookStoreRequest;
use App\Http\Requests\Book\BookUpdateRequest;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Auth::user()->books;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BookStoreRequest $request
     * @return Book
     */
    public function store(BookStoreRequest $request): Book
    {
        return Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'image' => $request->image,
            'year' => $request->year,
            'volume_id' => $request->volume_id,
            'isbn' => $request->isbn,
        ]);
    }

    /**
     * Update the specified order in storage.
     *
     * @param  int  $id
     * @param BookUpdateRequest $request
     * @return void
     */
    public function updateOrder(BookUpdateRequest $request, $id): void
    {
        Auth::user()->books()->where('id', $id)->updateExistingPivot($id, ['order' => $request->order]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        Book::find($id)->delete();
    }
}
