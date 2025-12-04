<?php

use App\Models\Rule;
use App\Models\User;

test('auth user can get rules', function () {
    $rule = Rule::factory()->create();
    $user = User::factory()->owner()->create();

    $this->actingAs($user)
        ->get(route('rule.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $rule->id);
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

test('can delete rule', function () {
    $user = User::factory()->owner()->create();
    $rule = Rule::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('rule.destroy', $rule))
        ->assertOk();

    $this->assertDatabaseMissing('rules', $rule->toArray());
});
