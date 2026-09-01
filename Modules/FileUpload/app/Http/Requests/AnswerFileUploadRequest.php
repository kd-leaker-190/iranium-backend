<?php

namespace Modules\FileUpload\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerFileUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file'],
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
