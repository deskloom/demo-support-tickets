@extends('layouts.app')

@section('title', $ticket->title)

@section('content')
    <div class="space-y-4">
        <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:underline">&larr; 一覧に戻る</a>
        <div class="rounded-lg border border-slate-200 bg-white p-6">
            <h1 class="text-xl font-bold">{{ $ticket->title }}</h1>
            <p class="mt-2 whitespace-pre-line text-slate-700">{{ $ticket->description }}</p>
            <dl class="mt-4 grid grid-cols-2 gap-y-2 text-sm">
                <dt class="text-slate-500">カテゴリ</dt>
                <dd>{{ $ticket->category->name }}</dd>
                <dt class="text-slate-500">状態</dt>
                <dd>{{ ['open' => '未対応', 'in_progress' => '対応中', 'resolved' => '解決済み'][$ticket->status] }}</dd>
                <dt class="text-slate-500">優先度</dt>
                <dd>{{ ['low' => '低', 'normal' => '中', 'high' => '高'][$ticket->priority] }}</dd>
                <dt class="text-slate-500">担当</dt>
                <dd>{{ $ticket->assignee_name ?? '未割当' }}</dd>
                <dt class="text-slate-500">起票日時</dt>
                <dd>{{ $ticket->created_at->format('Y/m/d H:i') }}</dd>
            </dl>
        </div>
    </div>
@endsection
