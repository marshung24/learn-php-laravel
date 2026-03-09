{{-- 編輯書籍表單頁面 --}}
@extends('layouts.app')

@section('title', '編輯書籍')

@section('content')
<h1>編輯書籍</h1>

<div class="card">
    <form action="{{ route('books.update', $book->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">書名</label>
            <input type="text" id="title" name="title" value="{{ $book->title }}" required>
        </div>

        <div class="form-group">
            <label for="author">作者</label>
            <input type="text" id="author" name="author" value="{{ $book->author }}" required>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" value="{{ $book->isbn }}" required>
        </div>

        <div class="form-group">
            <label for="publisher">出版社</label>
            <input type="text" id="publisher" name="publisher" value="{{ $book->publisher }}">
        </div>

        <div class="form-group">
            <label for="stock">庫存</label>
            <input type="number" id="stock" name="stock" value="{{ $book->stock }}" min="0">
        </div>

        <div class="form-group">
            <button type="submit" class="btn">更新</button>
            <a href="{{ route('books.show', $book->id) }}" class="btn btn-secondary">取消</a>
        </div>
    </form>
</div>
@endsection
