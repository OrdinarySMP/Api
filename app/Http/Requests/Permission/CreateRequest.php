<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * @var array<string>
     */
    public static array $operations = [
        'create',
        'read',
        'update',
        'delete',
    ];

    /**
     * @var array<string>
     */
    public static array $models = [
        'faq',
        'rule',
        'serverContent',
        'serverContentMessage',
        'reactionRole',
        'application',
        'applicationQuestion',
        'applicationAnswerQuestion',
        'applicationResponse',
        'applicationSubmission',
        'ticketConfig',
        'ticket',
        'ticketPanel',
        'ticketTeam',
        'ticketTranscript',
        'ticketButton',
    ];

    /**
     * @var array<array<string>>
     */
    public static array $specialPermissions = [
        'serverContent' => [
            'resend',
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('Owner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.role' => 'required|string',
            '*.permissions.*.*' => 'boolean',
        ];
    }

    public function withValidator(mixed $validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            foreach ($data as $entry) {
                // Validate the role and permissions
                if (! isset($entry['role']) || ! isset($entry['permissions'])) {
                    $validator->errors()->add('permissions', 'Invalid structure. Each item must contain a role and permissions.');

                    continue;
                }

                $permissions = $entry['permissions'];

                foreach ($permissions as $model => $modelPermissions) {
                    // Validate if the model is valid
                    if (! $this->isValidModel($model)) {
                        $validator->errors()->add('permissions', "Invalid model key: $model");

                        continue;
                    }

                    // Validate each permission key for the model
                    foreach ($modelPermissions as $key => $value) {
                        if (! $this->isValidPermission($model, $key)) {
                            $validator->errors()->add('permissions', "Invalid permission key: $model.$key");
                        }
                    }
                }
            }
        });
    }

    private function isValidModel(string $model): bool
    {
        // Check if the model exists in $models or $specialPermissions
        return in_array($model, self::$models) || isset($this->specialPermissions[$model]);
    }

    private function isValidPermission(string $model, string $key): bool
    {
        // Check if the key is a standard operation or a special permission
        if (in_array($key, self::$operations)) {
            return true;
        }

        return isset(self::$specialPermissions[$model]) && in_array($key, self::$specialPermissions[$model]);
    }
}
