@extends('layouts.layout')
@section('title', 'Bookly · Profile')

@section('content')
    <main class="p-2 sm:p-4 w-full max-w-7xl mx-auto flex-auto">

        @livewire('profile-user-update')

        @livewire('profile-books-update')

    </main>
@endsection


