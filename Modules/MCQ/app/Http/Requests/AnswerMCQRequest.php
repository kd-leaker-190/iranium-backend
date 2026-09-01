<?php

namespace Modules\MCQ\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerMCQRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'answer' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
