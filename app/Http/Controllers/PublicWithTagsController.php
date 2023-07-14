<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Helpers;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PublicWithTagsController extends Controller
{
    public function __invoke($slug, $tags = null): View
    {
        $user = User::where('slug', $slug)->firstOrFail();
        $myPage = Auth::id() == $user->id;
        $tagsArray = explode('/', Helpers::clearTheTag($tags));

        $books = $user->books()
            ->where(function ($query) use ($tagsArray) {
                $query->whereHas('tags', function ($query) use ($tagsArray) {
                    $query->whereIn('tag', $tagsArray);
                });
                if(!in_array(config('books.non_public_tag'),$tagsArray)){
                    $query->whereDoesntHave('tags', function ($query) {
                        $query->where('tag', config('books.non_public_tag'));
                    });
                }
            })->get();

        $count = $books->count();
        $tags = implode(', ', $tagsArray);

        return view('public', [
            'name' => $user->name,
            'userId' => $user->id,
            'userSlug' => $user->slug,
            'myPage' => $myPage,
            'title' => ($user->name ?: $user->slug) . ' ▸ '. $tags .' ('.$count.') ' . __('profile.site_name'),
        ]);
    }
}
