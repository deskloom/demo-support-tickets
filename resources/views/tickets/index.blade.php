@extends('layouts.app')

@section('title', 'チケット一覧')

@section('content')
    <div class="space-y-6">
        <section class="flex items-center justify-between">
            <h1 class="text-xl font-bold">チケット一覧（{{ $tickets->count() }}件）</h1>
            <div class="flex gap-2 text-sm">
                @foreach (['' => 'すべて', 'open' => '未対応', 'in_progress' => '対応中', 'resolved' => '解決済み'] as $value => $label)
                    <a
                        href="{{ route('tickets.index', $value ? ['status' => $value] : []) }}"
                        class="rounded px-2 py-1 {{ $currentStatus === $value || (!$currentStatus && $value === '') ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-300' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>
        </section>

        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600">タイトル</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600">カテゴリ</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600">優先度</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600">状態</th>
                        <th class="px-4 py-2 text-left font-semibold text-slate-600">担当</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline">{{ $ticket->title }}</a>
                            </td>
                            <td class="px-4 py-2 text-slate-600">{{ $ticket->category->name }}</td>
                            <td class="px-4 py-2">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    'bg-slate-100 text-slate-700' => $ticket->priority === 'low',
                                    'bg-amber-100 text-amber-800' => $ticket->priority === 'normal',
                                    'bg-red-100 text-red-800' => $ticket->priority === 'high',
                                ])>{{ ['low' => '低', 'normal' => '中', 'high' => '高'][$ticket->priority] }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    'bg-amber-100 text-amber-800' => $ticket->status === 'open',
                                    'bg-blue-100 text-blue-800' => $ticket->status === 'in_progress',
                                    'bg-emerald-100 text-emerald-800' => $ticket->status === 'resolved',
                                ])>{{ ['open' => '未対応', 'in_progress' => '対応中', 'resolved' => '解決済み'][$ticket->status] }}</span>
                            </td>
                            <td class="px-4 py-2 text-slate-600">{{ $ticket->assignee_name ?? '未割当' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">該当するチケットはありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <section>
            <h2 class="mb-2 text-lg font-semibold">チケットを起票</h2>
            <form method="POST" action="{{ route('tickets.store') }}" class="grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:grid-cols-2">
                @csrf
                <input name="title" placeholder="タイトル" required value="{{ old('title') }}" class="rounded border border-slate-300 px-2 py-1 sm:col-span-2">
                <textarea name="description" placeholder="内容" required rows="3" class="rounded border border-slate-300 px-2 py-1 sm:col-span-2">{{ old('description') }}</textarea>
                <select name="category_id" required class="rounded border border-slate-300 px-2 py-1">
                    <option value="">カテゴリを選択</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="priority" required class="rounded border border-slate-300 px-2 py-1">
                    <option value="low">優先度: 低</option>
                    <option value="normal" selected>優先度: 中</option>
                    <option value="high">優先度: 高</option>
                </select>
                <input name="assignee_name" placeholder="担当者（任意）" value="{{ old('assignee_name') }}" class="rounded border border-slate-300 px-2 py-1 sm:col-span-2">
                <button type="submit" class="rounded bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700 sm:col-span-2">起票する</button>
                @if ($errors->any())
                    <ul class="sm:col-span-2 list-disc pl-5 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </form>
        </section>
    </div>
@endsection
