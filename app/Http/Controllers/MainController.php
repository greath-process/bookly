<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Services\ImageGenerate;
use Illuminate\Contracts\View\View;

class MainController extends Controller
{
    public function __invoke(): View
    {
        $users = User::select(['slug'])
                    ->join('books_users', 'users.id', '=', 'books_users.user_id')
                    ->groupBy('users.id')
                    ->havingRaw('COUNT(DISTINCT books_users.book_id) >= 20')
                    ->inRandomOrder()
                    ->limit(15)
                    ->get()
                    ->map(function ($user) {
                        return $user->slug;
                    })
                    ->toArray();

        $usersBooks = [];
        foreach ($users as $user) {
            $userBooks = User::where('slug', $user)->first()->books()->limit(20)->get();
            $userBooks = $userBooks->map(function ($bk) {
                $big_image = $bk->big_image ? (file_exists($bk->big_image) ? $bk->big_image : null) : null;
                return [
                    'title' => $bk->title,
                    'author' => $bk->author,
                    'image' => $big_image ? ImageGenerate::covers.basename($big_image) : $bk->image
                ];
            })->toArray();
            $usersBooks[$user] = $userBooks;
        }

        return view('index', ['users' => $users, 'usersBooks' => $usersBooks]);
    }
}
