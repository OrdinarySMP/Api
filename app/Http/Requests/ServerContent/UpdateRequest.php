<?php

namespace App\Http\Requests\ServerContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('serverContent.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:128',
            'url' => 'required|url:http,https|max:256',
            'description' => 'required|max:512',
            'is_recommended' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }
}
