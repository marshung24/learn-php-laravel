<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '書籍管理系統')</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        nav {
            background: #4a5568;
            padding: 1rem 2rem;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-right: 1.5rem;
        }
        nav a:hover {
            text-decoration: underline;
        }
        h1, h2 {
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #edf2f7;
            font-weight: 600;
        }
        tr:hover {
            background: #f7fafc;
        }
        a {
            color: #4299e1;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #4299e1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background: #3182ce;
            text-decoration: none;
        }
        .btn-secondary {
            background: #718096;
        }
        .btn-secondary:hover {
            background: #4a5568;
        }
        .alert {
            padding: 12px 16px;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background: #c6f6d5;
            color: #276749;
        }
        .text-success {
            color: #38a169;
        }
        .text-danger {
            color: #e53e3e;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .mb-4 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav>
        <a href="{{ url('/') }}">首頁</a>
        <a href="{{ route('books.index') }}">書籍清單</a>
        <a href="{{ route('books.create') }}">新增書籍</a>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
