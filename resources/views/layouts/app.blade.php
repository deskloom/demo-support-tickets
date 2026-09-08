<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'サポートチケット管理')（デモ）</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-4xl px-4 py-3">
            <a href="{{ route('tickets.index') }}" class="text-lg font-semibold">サポートチケット管理</a>
            <span class="ml-2 text-xs text-slate-400">（デモ・架空データ）</span>
        </div>
    </header>
    <main class="mx-auto max-w-4xl px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
