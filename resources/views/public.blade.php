@extends('layouts/layout')
@section('title', $title)

@section('content')
    <main class="w-full p-4 sm:p-20 mx-auto flex-auto">
        @livewire('public-books', ['userId' => $userId])
    </main>
@endsection


