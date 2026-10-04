<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Ticket::STATUSES)],
            'assignee_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => '状態',
            'assignee_name' => '担当者',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '状態を選択してください。',
            'status.in' => '状態は未対応・対応中・解決済みのいずれかを選択してください。',
            'assignee_name.string' => '担当者は文字列で入力してください。',
            'assignee_name.max' => '担当者は:max文字以内で入力してください。',
        ];
    }
}
