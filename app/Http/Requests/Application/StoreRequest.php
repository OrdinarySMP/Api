<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('application.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'is_active' => 'required|boolean',
            'log_channel' => 'required|string',
            'accept_message' => 'required|string',
            'deny_message' => 'required|string',
            'confirmation_message' => 'required|string',
            'completion_message' => 'required|string',
            'restricted_role_ids' => 'array',
            'restricted_role_ids.*' => 'string',
            'accepted_role_ids' => 'array',
            'accepted_role_ids.*' => 'string',
            'denied_role_ids' => 'array',
            'denied_role_ids.*' => 'string',
            'ping_role_ids' => 'array',
            'ping_role_ids.*' => 'string',
            'accept_removal_role_ids' => 'array',
            'accept_removal_role_ids.*' => 'string',
            'deny_removal_role_ids' => 'array',
            'deny_removal_role_ids.*' => 'string',
            'pending_role_ids' => 'array',
            'pending_role_ids.*' => 'string',
        ];
    }
}
