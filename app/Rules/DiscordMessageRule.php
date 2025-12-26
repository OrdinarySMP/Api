<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class DiscordMessageRule implements ValidationRule
{
    public static string $discordChannelLinkBase = 'https://discord.com/channels/';

    public static string $discordCanaryChannelLinkBase = 'https://canary.discord.com/channels/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            ! str_starts_with($value, self::$discordChannelLinkBase) &&
            ! str_starts_with($value, self::$discordCanaryChannelLinkBase)
        ) {
            $fail('The provided :attribute is not a discord message link.');

            return;
        }

        [$guildId, $channelId, $messageId] = $this->splitMessageLink($value);

        if ($guildId !== config('services.discord.server_id')) {
            $fail('The provided :attribute is not from this server.');

            return;
        }

        $response = Http::discordBot()->get('/channels/'.$channelId.'/messages/'.$messageId);
        if (! $response->successful()) {
            $fail('Could not load the message of the provided :attribute.');

            return;
        }
    }

    /**
     * @return string[]
     */
    public static function splitMessageLink(string $messageLink): array
    {
        $linkString = str_replace(self::$discordChannelLinkBase, '', $messageLink);
        $linkString = str_replace(self::$discordCanaryChannelLinkBase, '', $linkString);

        return explode('/', $linkString);
    }
}
