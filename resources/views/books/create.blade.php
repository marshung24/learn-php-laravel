{{-- 新增書籍表單頁面 --}}
@extends('layouts.app')

@section('title', '新增書籍')

@section('content')
<h1>新增書籍</h1>

<div class="card">
    <form action="{{ route('books.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">書名</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="author">作者</label>
            <input type="text" id="author" name="author" required>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>

        <div class="form-group">
            <label for="publisher">出版社</label>
            <input type="text" id="publisher" name="publisher">
        </div>

        <div class="form-group">
            <label for="stock">庫存</label>
            <input type="number" id="stock" name="stock" value="0" min="0">
        </div>

        <div class="form-group">
            <button type="submit" class="btn">新增</button>
            <a href="{{ route('books.index') }}" class="btn btn-secondary">取消</a>
        </div>
    </form>
</div>
@endsection
