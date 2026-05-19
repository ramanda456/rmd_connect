<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RMD Connect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: #f5f5f5;
            color: #222;
        }

        .navbar{
            background: white !important;
            border-bottom: 1px solid #ddd;
        }

        .navbar-brand{
            color: black !important;
            font-weight: bold;
        }

        .nav-btn{
            border: 1px solid #ccc;
            background: white;
            color: black;
            padding: 5px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .nav-btn:hover{
            background: #eee;
        }

        .logout-btn{
            border: 1px solid #ccc;
            background: white;
            padding: 5px 12px;
            border-radius: 6px;
        }

        .container-box{
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container d-flex justify-content-between align-items-center">

        <a class="navbar-brand">
            RMD Connect
        </a>

        @auth
        <div class="d-flex align-items-center gap-2">

            <a href="{{ route('dashboard') }}" class="nav-btn">
                Chat
            </a>

            <a href="{{ route('group.index') }}" class="nav-btn">
                Group
            </a>

            <span>
                {{ Auth::user()->name }}
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn">
                    Logout
                </button>
            </form>

        </div>
        @endauth

    </div>
</nav>

<div class="container">
    <div class="container-box">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@vite(['resources/js/app.js'])

@stack('scripts')

</body>
</html>