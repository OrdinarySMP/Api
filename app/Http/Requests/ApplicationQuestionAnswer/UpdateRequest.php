<?php

namespace App\Http\Requests\ApplicationQuestionAnswer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('applicationQuestionAnswer.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'application_question_id' => 'required|exists:application_questions,id',
            'application_submission_id' => 'required|exists:application_submissions,id',
            'answer' => 'required|string',
            'attachments' => 'nullable|string',
        ];
    }
}
