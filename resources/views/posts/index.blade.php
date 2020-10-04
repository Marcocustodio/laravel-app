@extends('layout')
@section('content')
    @foreach ($posts as $post)
        <p>
            <h3><a href="{{ route('posts.show',['post' => $post->id]) }}">{{ $post->title }}</a></h3>
        </p>

        <a href="{{ route('posts.edit', ['post' => $post->id]) }}" class="btn btn-primary">Edit</a>

        <form action="{{ route('posts.destroy', ['post' => $post->id]) }}" method="post" class="d-inline">
            @csrf
            @method('DELETE')
            <input type="submit" value="Delete!" class="btn btn-danger">

        </form>
    @endforeach
@endsection
