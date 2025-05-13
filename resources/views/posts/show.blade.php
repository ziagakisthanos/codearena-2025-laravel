@extends('layouts.app')

@section('content')
<div class="bg-white px-6 py-32 lg:px-8">
    <div class="mx-auto max-w-3xl text-base/7 text-gray-700">
      <h1 class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">{{ $post->title }}</h1>
      <p class="mt-6 text-xl/8">{{ $post->description }}</p>
      <img class="aspect-video rounded-xl bg-gray-50 object-cover mt-10" src="{{ $post->image }}" alt="{{ $post->title }}">
      <div class="mt-16 max-w-2xl">
        <p class="mt-6">{{ $post->body }}</p>
      </div>
      <div class="mt-16 font-bold">
        <a href="">{{ $post->author->name }}</a>
      </div>
    </div>
  </div>
@endsection
