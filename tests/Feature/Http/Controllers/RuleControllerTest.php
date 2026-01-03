<?php

use App\Models\Rule;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        Rule::factory()->create();
        $this->assertReadPermissions('rule.index', 'rule.read');
    });

    test('can read rules', function () {
        $rule = Rule::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('rule.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $rule->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'number' => 1,
            'name' => 'Testing',
            'rule' => 'This has to be tested!',
        ];
        $this->assertCreatePermissions('rule.store', 'rule.create', $data, Rule::class);
    });

    test('can create rule', function () {
        $user = User::factory()->owner()->create();
        $data = [
            'number' => 1,
            'name' => 'Testing',
            'rule' => 'This has to be tested!',
        ];

        $this->actingAs($user)
            ->postJson(route('rule.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('rules', $data);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $rule = Rule::factory()->create();
        $data = [
            'number' => 1,
        ];
        $this->assertUpdatePermissions('rule.update', 'rule.update', $rule, $data, Rule::class);
    });

    test('can update rule', function () {
        $user = User::factory()->owner()->create();
        $rule = Rule::factory()->create();
        $data = [
            'number' => 1,
            'name' => 'Testing',
            'rule' => 'This has to be tested!',
        ];

        $this->actingAs($user)
            ->patchJson(route('rule.update', $rule), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('rules', $data);
    });
});

describe('delete operations', function () {
    test('delete permission', function () {
        $rule = Rule::factory()->create();
        $this->assertDeletePermissions('rule.destroy', 'rule.delete', $rule, Rule::class);
    });

    test('can delete rule', function () {
        $user = User::factory()->owner()->create();
        $rule = Rule::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('rule.destroy', $rule))
            ->assertOk();

        $this->assertDatabaseMissing('rules', $rule->toArray());
    });
});
