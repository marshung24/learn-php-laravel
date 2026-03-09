{{-- 書籍詳情頁面 --}}
@extends('layouts.app')

@section('title', $book['title'])

@section('content')
<h1>{{ $book['title'] }}</h1>

<div class="card">
    <p><strong>作者：</strong>{{ $book['author'] }}</p>
    <p><strong>ISBN：</strong>{{ $book['isbn'] }}</p>
    <p>
        <strong>庫存：</strong>
        @if ($book['stock'] > 0)
            <span class="text-success">{{ $book['stock'] }} 本</span>
        @else
            <span class="text-danger">缺貨</span>
        @endif
    </p>
</div>

<div class="mb-4" style="margin-top: 1rem;">
    <a href="{{ route('books.index') }}" class="btn btn-secondary">返回清單</a>
</div>
@endsection
