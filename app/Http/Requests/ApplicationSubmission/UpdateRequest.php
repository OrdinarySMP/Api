<?php

namespace App\Http\Requests\ApplicationSubmission;

use App\Enums\ApplicationSubmissionState;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('application-submission.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discord_id' => 'nullable|size:18',
            'submitted_at' => 'nullable|date_format:Y-m-d H:i:s',
            'application_response_id' => 'nullable|exists:application_responses,id',
            'state' => ['nullable', new Enum(ApplicationSubmissionState::class)],
            'custom_response' => 'nullable|string',
            'message_link' => 'nullable|string|url:https',
            'handled_by' => 'nullable|string|size:18',
            'application_id' => 'nullable|exists:applications,id',
        ];
    }
}
