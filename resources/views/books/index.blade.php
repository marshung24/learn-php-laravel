{{-- 書籍清單頁面 --}}
@extends('layouts.app')

@section('title', '書籍清單')

@section('content')
<h1>書籍清單</h1>

<div class="mb-4">
    <a href="{{ route('books.create') }}" class="btn">新增書籍</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>書名</th>
            <th>作者</th>
            <th>ISBN</th>
            <th>庫存</th>
            <th>狀態</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($books as $book)
        <tr>
            <td>{{ $book['id'] }}</td>
            <td>
                <a href="{{ route('books.show', $book['id']) }}">
                    {{ $book['title'] }}
                </a>
            </td>
            <td>{{ $book['author'] }}</td>
            <td>{{ $book['isbn'] }}</td>
            <td>{{ $book['stock'] }}</td>
            <td>
                @if ($book['stock'] > 0)
                    <span class="text-success">有庫存</span>
                @else
                    <span class="text-danger">缺貨</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
