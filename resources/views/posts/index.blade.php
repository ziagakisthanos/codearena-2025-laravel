@extends('layouts.app')

@section('content')
<div class="bg-white py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-4xl font-semibold tracking-tight text-balance text-gray-900 sm:text-5xl">Blog page</h2>
      </div>

      @if($posts->total() === 0)
        <p>No posts found.</p>
      @else
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
          @foreach($posts as $post)
            <x-blog.post :post="$post" />
          @endforeach
        </div>

        <div class="mt-16">
          {{ $posts->links() }}
        </div>
      @endif  
    </div>
  </div>

  <section id="authors">
    @foreach($authors as $author)
      <div>{{ $author->name }}</div>
    @endforeach
</section>

@endsection
