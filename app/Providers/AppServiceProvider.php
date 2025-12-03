<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::macro('getOrPaginate', function (int $maxResults = 500, int $defaultSize = 25) {
            $query = $this;
            if (request()->has('full')) {
                return $query->get();
            } else {
                $size = (int) request()->input('page_size', $defaultSize);
                $size = $size > $maxResults || $size < 1 ? $maxResults : $size;

                return $query->paginate($size);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole(['Owner', 'Bot']) ? true : null;
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
        });

        Http::macro('discord', function (?string $token = null) {
            $user = auth()->user();

            $bearerToken = $token ?? $user->discord_token ?? '';

            $client = Http::withHeaders([
                'Authorization' => 'Bearer '.$bearerToken,
            ])->baseUrl(config('services.discord.api_url'));

            return $client->withMiddleware(function ($handler) use ($user) {
                return function ($request, array $options) use ($handler, $user) {
                    return $handler($request, $options)->then(function ($response) use ($request, $handler, $options, $user) {
                        if ($response->getStatusCode() === 401 && $user && $user->discord_refresh_token) {
                            $newTokens = Http::asForm()->post('https://discord.com/api/oauth2/token', [
                                'client_id' => config('services.discord.client_id'),
                                'client_secret' => config('services.discord.client_secret'),
                                'grant_type' => 'refresh_token',
                                'refresh_token' => $user->discord_refresh_token,
                            ])->json();

                            if (isset($newTokens['access_token'])) {
                                $user->update([
                                    'discord_token' => $newTokens['access_token'],
                                    'discord_refresh_token' => $newTokens['refresh_token'] ?? $user->discord_refresh_token,
                                ]);

                                $request = $request->withHeader('Authorization', 'Bearer '.$newTokens['access_token']);

                                return $handler($request, $options);
                            } else {
                                Log::warning('Discord token refresh failed', ['user_id' => $user->id]);
                            }
                        }

                        return $response;
                    });
                };
            });
        });

        Http::macro('discordBot', function () {
            return Http::withHeaders([
                'Authorization' => 'Bot '.config('services.discord.bot_token'),
            ])->baseUrl(config('services.discord.api_url'));
        });
    }
}
