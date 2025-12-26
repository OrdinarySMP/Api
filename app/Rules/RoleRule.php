<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class RoleRule implements ValidationRule
{
    public string $discordChannelLinkBase = 'https://discord.com/channels/';

    public string $discordCanaryChannelLinkBase = 'https://canary.discord.com/channels/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('services.discord.server_id') === $value) {
            $fail(':attribute can not be the @everyone role.');

            return;
        }

        $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/roles/'.$value);
        if (! $response->successful()) {
            $fail('The provided :attribute was not found.');

            return;
        }
    }
}
