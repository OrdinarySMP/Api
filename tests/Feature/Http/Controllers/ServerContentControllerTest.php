<?php

use App\Models\ServerContent;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

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

describe('resend operations', function () {})->todo('add tests');
