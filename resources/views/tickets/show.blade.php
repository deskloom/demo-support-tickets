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

        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-2">
            @csrf
            @method('PATCH')
            <h2 class="text-lg font-semibold sm:col-span-2">状態・担当者を更新</h2>
            <label class="text-sm text-slate-600">状態
                <select name="status" required class="mt-1 block w-full rounded border border-slate-300 px-2 py-1 text-slate-900">
                    @foreach (array_combine(\App\Models\Ticket::STATUSES, ['未対応', '対応中', '解決済み']) as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $ticket->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm text-slate-600">担当者
                <input name="assignee_name" placeholder="担当者（空欄で未割当）" value="{{ old('assignee_name', $ticket->assignee_name) }}" class="mt-1 block w-full rounded border border-slate-300 px-2 py-1 text-slate-900">
            </label>
            <button type="submit" class="rounded bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700 sm:col-span-2">更新する</button>
            @if ($errors->any())
                <ul class="sm:col-span-2 list-disc pl-5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </form>
    </div>
@endsection
