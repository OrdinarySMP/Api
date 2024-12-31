<?php

namespace App\Http\Requests\ApplicationResponse;

use App\Enums\ApplicationResponseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('applicationResponse.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(ApplicationResponseType::class)],
            'name' => 'required|string',
            'response' => 'required|string',
            'application_id' => 'required|exists:applications,id',
        ];
    }
}
