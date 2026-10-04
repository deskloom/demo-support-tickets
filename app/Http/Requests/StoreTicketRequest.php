<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,normal,high'],
            'assignee_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'カテゴリ',
            'title' => 'タイトル',
            'description' => '内容',
            'priority' => '優先度',
            'assignee_name' => '担当者',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'カテゴリを選択してください。',
            'category_id.exists' => '選択されたカテゴリは存在しません。',
            'title.required' => 'タイトルを入力してください。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは:max文字以内で入力してください。',
            'description.required' => '内容を入力してください。',
            'description.string' => '内容は文字列で入力してください。',
            'priority.required' => '優先度を選択してください。',
            'priority.in' => '優先度は低・中・高のいずれかを選択してください。',
            'assignee_name.string' => '担当者は文字列で入力してください。',
            'assignee_name.max' => '担当者は:max文字以内で入力してください。',
        ];
    }
}
