<?php

use App\Models\ServerContentMessage;
use App\Models\User;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;

beforeEach(function () {
    $this->serverId = '100000000000000000';
    config(['services.discord.server_id' => $this->serverId]);
});

describe('read operations', function () {
    test('read permission', function () {
        $user = User::factory()->create();
        $route = 'server-content-message.index';
        $permission = 'serverContentMessage.read';

        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->get(route($route))
            ->assertForbidden();

        $user->givePermissionTo($permission);
        $this->actingAs($user)
            ->get(route($route))
            ->assertOk();
    });

    test('can read empty message', function () {
        $user = User::factory()->owner()->create();

        $response = $this->actingAs($user)
            ->get(route('server-content-message.index'))
            ->assertOk();
        assertEquals('', $response->getContent());
    });

    test('can read message', function () {
        $user = User::factory()->owner()->create();
        new ServerContentMessage([
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
            'server_id' => $this->serverId,
        ])->save();

        $this->actingAs($user)
            ->get(route('server-content-message.index'))
            ->assertOk()
            ->assertJson([
                'heading' => 'heading',
                'not_recommended' => 'not_recommended',
                'recommended' => 'recommended',
            ]);
    });
});

describe('upsert operations', function () {
    test('upsert permission', function () {
        $user = User::factory()->create();
        $data = [
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
        ];
        $route = 'server-content-message.store';
        $permission = 'serverContentMessage.create';

        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->postJson(route($route), $data)
            ->assertForbidden();

        $user->givePermissionTo($permission);
        $this->actingAs($user)
            ->postJson(route($route), $data)
            ->assertCreated();
    });

    test('can create', function () {
        $user = User::factory()->owner()->create();
        $data = [
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
        ];

        $this->actingAs($user)
            ->postJson(route('server-content-message.store'), $data)
            ->assertCreated()
            ->assertJson($data);
        $this->assertDatabaseHas(ServerContentMessage::class, $data);
    });

    test('can update', function () {
        $user = User::factory()->owner()->create();
        new ServerContentMessage([
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
            'server_id' => $this->serverId,
        ])->save();

        $data = [
            'heading' => 'Update',
            'not_recommended' => 'Update',
            'recommended' => 'Update',
        ];

        $this->actingAs($user)
            ->postJson(route('server-content-message.store'), $data)
            ->assertCreated()
            ->assertJson($data);

        $this->assertDatabaseHas(ServerContentMessage::class, $data);

        $this->assertDatabaseMissing(ServerContentMessage::class, [
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
        ]);
    });
});
