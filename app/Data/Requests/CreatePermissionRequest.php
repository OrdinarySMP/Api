<?php

namespace App\Data\Requests;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Validation\Validator;
use Spatie\LaravelData\Data;

class CreatePermissionRequest extends Data
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

    public function __construct(
        /**
         * @var array{ role: string, permissions: array<string, bool>}
         */
        public readonly array $permissions,
    ) {}

    public static function authorize(
        #[CurrentUser] User $user,
    ): bool {
        return $user->can('permission.read');
    }

    /**
     * @return array<string, array<string>>
     */
    public static function rules(): array
    {
        return [
            'permissions' => ['required', 'array'],
            'permissions.*.role' => ['required', 'string'],
            'permissions.*.permissions' => ['required', 'array'],
            'permissions.*.permissions.*' => ['array'],
            'permissions.*.permissions.*.*' => ['boolean'],
        ];
    }

    public static function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $baseData = $validator->getData();
            if (! isset($baseData['permissions'])) {
                $validator->errors()->add('permissions', 'Invalid structure.');

                return;
            }
            foreach ($baseData['permissions'] as $entry) {
                // Validate the role and permissions
                if (! isset($entry['role']) || ! isset($entry['permissions'])) {
                    $validator->errors()->add('permissions', 'Invalid structure. Each item must contain a role and permissions.');

                    continue;
                }

                $permissions = $entry['permissions'];

                foreach ($permissions as $model => $modelPermissions) {
                    // Validate if the model is valid
                    if (! self::isValidModel($model)) {
                        $validator->errors()->add('permissions', "Invalid model key: $model");

                        continue;
                    }

                    // Validate each permission key for the model
                    foreach ($modelPermissions as $key => $value) {
                        if (! self::isValidPermission($model, $key)) {
                            $validator->errors()->add('permissions', "Invalid permission key: $model.$key");
                        }
                    }
                }
            }
        });
    }

    private static function isValidModel(string $model): bool
    {
        // Check if the model exists in $models or $specialPermissions
        return in_array($model, self::$models) || isset(self::$specialPermissions[$model]);
    }

    private static function isValidPermission(string $model, string $key): bool
    {
        // Check if the key is a standard operation or a special permission
        if (in_array($key, self::$operations)) {
            return true;
        }

        return isset(self::$specialPermissions[$model]) && in_array($key, self::$specialPermissions[$model]);
    }
}
