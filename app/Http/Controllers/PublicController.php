<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function __invoke($slug): View
    {
        $user = User::where('slug', $slug)->firstOrFail();
        $myPage = Auth::id() == $user->id;
        $count = $user->books()
            ->whereDoesntHave('tags', function ($query) {
                $query->where('tag', config('books.non_public_tag'));
            })
            ->count();

        return view('public', [
            'name' => $user->name,
            'userId' => $user->id,
            'userSlug' => $user->slug,
            'myPage' => $myPage,
            'title' => ($user->name ?: $user->slug) . ' ('.$count.') · ' . __('profile.site_name'),
        ]);
    }
}
