<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class EmojiRule implements ValidationRule
{
    private string $discordEmojiRegex = '/^<.+:([0-9]+)>$/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (\Emoji\is_single_emoji($value) !== false) {
            return;
        }

        if (preg_match($this->discordEmojiRegex, $value, $matches)) {
            $emojiId = $matches[1];

            $response = Http::discordBot()->get('/guilds/'.config('services.discord.server_id').'/emojis');
            $guildEmojis = $response->json();
            if (! is_array($guildEmojis)) {
                $fail('could not validate :attribute');

                return;
            }

            foreach ($guildEmojis as $guildEmoji) {
                if (isset($guildEmoji['id']) && $guildEmoji['id'] === $emojiId) {
                    return;
                }
            }
        }

        $fail(':attribute must be a valid emoji');
    }
}
