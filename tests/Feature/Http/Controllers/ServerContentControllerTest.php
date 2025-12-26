<?php

use App\Models\ServerContent;
use App\Models\ServerContentMessage;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

pest()->use(CrudPermissionTrait::class);

beforeEach(function () {
    $this->serverId = '123';
    config(['services.discord.server_id' => $this->serverId]);
});

describe('read operations', function () {
    test('read permission', function () {
        ServerContent::factory()->create();
        $this->assertReadPermissions('server-content.index', 'serverContent.read');
    });

    test('auth user can get server contents', function () {
        $serverContent = ServerContent::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('server-content.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $serverContent->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'name' => 'Test',
            'url' => 'https://example.com',
            'description' => 'Test Content',
            'is_recommended' => true,
            'is_active' => true,
        ];
        $this->assertCreatePermissions('server-content.store', 'serverContent.create', $data, ServerContent::class);
    });

    test('can create server content', function () {
        $user = User::factory()->owner()->create();
        $data = [
            'name' => 'Test',
            'url' => 'https://example.com',
            'description' => 'Test Content',
            'is_recommended' => true,
            'is_active' => true,
        ];

        $this->actingAs($user)
            ->postJson(route('server-content.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('server_contents', $data);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $serverContent = ServerContent::factory()->create();
        $data = [
            'name' => 'Test',
        ];
        $this->assertUpdatePermissions('server-content.update', 'serverContent.update', $serverContent, $data, ServerContent::class);
    });

    test('can update server content', function () {
        $user = User::factory()->owner()->create();
        $serverContent = ServerContent::factory()->create();
        $data = [
            'name' => 'Test',
            'url' => 'https://example.com',
            'description' => 'Test Content',
            'is_recommended' => true,
            'is_active' => true,
        ];

        $this->actingAs($user)
            ->patchJson(route('server-content.update', $serverContent), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('server_contents', $data);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $serverContent = ServerContent::factory()->create();
        $this->assertDeletePermissions('server-content.destroy', 'serverContent.delete', $serverContent, ServerContent::class);
    });

    test('can delete server content', function () {
        $user = User::factory()->owner()->create();
        $serverContent = ServerContent::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('server-content.destroy', $serverContent))
            ->assertOk();

        $this->assertDatabaseMissing('server_contents', $serverContent->toArray());
    });
});

describe('resend operations', function () {
    test('resend permission', function () {
        Http::fake();
        $user = User::factory()->create();
        ServerContentMessage::create([
            'server_id' => $this->serverId,
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
        ]);

        $route = 'server-content.resend';
        $permission = 'serverContent.resend';
        $data = [
            'channel_id' => '321',
        ];

        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->postJson(route($route, $data))
            ->assertForbidden();

        $user->givePermissionTo($permission);
        $this->actingAs($user)
            ->postJson(route($route, $data))
            ->assertOk();
    });

    test('does not resend server content without messages', function () {
        $user = User::factory()->owner()->create();
        assertEquals(0, ServerContentMessage::where('server_id', $this->serverId)->count());

        $this->actingAs($user)
            ->postJson(route('server-content.resend'), [
                'channel_id' => '321',
            ])
            ->assertStatus(400);
    });

    test('resend server content', function () {
        Http::fake();
        $user = User::factory()->owner()->create();
        $serverContentMessage = ServerContentMessage::create([
            'server_id' => $this->serverId,
            'heading' => 'heading',
            'not_recommended' => 'not_recommended',
            'recommended' => 'recommended',
        ]);
        ServerContent::factory()->active()->recommended()->create();
        ServerContent::factory()->active()->notRecommended()->create();

        $this->actingAs($user)
            ->postJson(route('server-content.resend'), [
                'channel_id' => '321',
            ])
            ->assertOk();

        Http::assertSentCount(3);

        $contents = collect(Http::recorded())->map(fn ($record) => $record[0]['content']);

        assertTrue(str_contains($contents[0], $serverContentMessage->heading));
        assertTrue(str_contains($contents[1], $serverContentMessage->not_recommended));
        assertTrue(str_contains($contents[2], $serverContentMessage->recommended));
    });
});
