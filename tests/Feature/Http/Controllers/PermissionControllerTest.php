<?php

use App\Data\Requests\CreatePermissionRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\assertFalse;

describe('read template operations', function () {
    test('can read templates', function () {
        $user = User::factory()->owner()->create();

        $response = $this->actingAs($user)
            ->get(route('permission.template'))
            ->assertOk()
            ->assertJsonStructure([
                '*' => [],
            ]);

        $data = $response->json();
        expect($data)->toHaveKeys(CreatePermissionRequest::$models);
        foreach (CreatePermissionRequest::$models as $model) {
            expect($data[$model])->toContain(...CreatePermissionRequest::$operations);
        }

        foreach (CreatePermissionRequest::$specialPermissions as $model => $specialPermissions) {
            expect($data[$model])->toContain(...$specialPermissions);
        }
    });

    test('can not read without permission', function () {
        $user = User::factory()->create();
        assertFalse($user->can('permission.read'));

        $this->actingAs($user)
            ->get(route('permission.template'))
            ->assertForbidden();
    });
});

describe('read permission operations', function () {
    test('owner can get permissions', function () {
        $user = User::factory()->owner()->create();
        $role = Role::create(['name' => 'TestRole']);
        $permission = Permission::where(['name' => 'faq.create'])->first();
        $role->givePermissionTo($permission);

        $this->actingAs($user)
            ->get(route('permission.index'))
            ->assertOk()
            ->assertJsonFragment([
                'role' => 'TestRole',
                'permissions' => ['faq.create'],
            ]);
    });

    test('can not get permissions', function () {
        $user = User::factory()->create();
        assertFalse($user->can('permission.read'));

        $role = Role::create(['name' => 'TestRole']);
        $permission = Permission::where(['name' => 'faq.create'])->first();
        $role->givePermissionTo($permission);

        $this->actingAs($user)
            ->get(route('permission.index'))
            ->assertForbidden();
    });
});

describe('create permission operations', function () {
    test('owner can create permissions', function () {
        $user = User::factory()->owner()->create();
        $data = [
            [
                'role' => 'Tester',
                'permissions' => [
                    'faq' => ['create' => true, 'read' => false],
                    'rule' => ['read' => true],
                    'serverContent' => ['resend' => true],
                ],
            ],
            [
                'role' => 'Testing',
                'permissions' => [
                    'faq' => ['create' => true],
                ],
            ],
        ];

        $this->actingAs($user)
            ->post(route('permission.store'), ['permissions' => $data])
            ->assertOk();

        $this->assertDatabaseHas('roles', ['name' => 'Tester']);
        $this->assertDatabaseHas('roles', ['name' => 'Testing']);

        $managerRole = Role::where(['name' => 'Tester'])->first();
        expect($managerRole->hasPermissionTo('faq.create'))->toBeTrue();
        expect($managerRole->hasPermissionTo('rule.read'))->toBeTrue();
        expect($managerRole->hasPermissionTo('faq.read'))->toBeFalse();

        $managerRole = Role::where(['name' => 'Testing'])->first();
        expect($managerRole->hasPermissionTo('faq.create'))->toBeTrue();
        expect($managerRole->hasPermissionTo('rule.read'))->toBeFalse();
        expect($managerRole->hasPermissionTo('faq.read'))->toBeFalse();
    });

    test('none owner can not create permissions', function () {
        $user = User::factory()->create();
        assertFalse($user->can('permission.create'));
        $data = [
            [
                'role' => 'Tester',
                'permissions' => [
                    'faq' => ['create' => true, 'read' => false],
                    'rule' => ['read' => true],
                ],
            ],
            [
                'role' => 'Testing',
                'permissions' => [
                    'faq' => ['create' => true],
                ],
            ],
        ];

        $this->actingAs($user)
            ->post(route('permission.store'), ['permissions' => $data])
            ->assertForbidden();
    });
});
