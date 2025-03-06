<?php

namespace App\Http\Requests\TicketButton;

use App\Rules\EmojiRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('ticketButton.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ticket_team_id' => 'required|int|exists:ticket_teams,id',
            'ticket_panel_id' => 'required|int|exists:ticket_panels,id',
            'text' => 'required|string|max:50',
            'color' => 'required|int|max:7',
            'initial_message' => 'required|string|max:1000',
            'emoji' => ['required', 'string', new EmojiRule],
            'naming_scheme' => 'required|string|max:128',
            'disabled' => 'required|boolean',
            'ticket_button_ping_role_ids' => 'required|array',
            'ticket_button_ping_role_ids.*' => 'string',
        ];
    }
}
