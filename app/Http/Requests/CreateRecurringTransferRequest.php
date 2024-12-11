<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRecurringTransferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'frequency' => ['required', 'integer', 'min:1'],
            'recipient_email' => ['required', 'email'],
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }
}
