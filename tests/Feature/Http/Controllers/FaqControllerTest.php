<?php

use App\Models\Faq;
use App\Models\User;
use Tests\Traits\CrudPermissionTrait;

pest()->use(CrudPermissionTrait::class);

describe('read operations', function () {
    test('read permission', function () {
        Faq::factory()->create();
        $this->assertReadPermissions('faq.index', 'faq.read');
    });

    test('can read faqs', function () {
        $faq = Faq::factory()->create();
        $user = User::factory()->owner()->create();

        $this->actingAs($user)
            ->get(route('faq.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $faq->id);
    });
});

describe('create operations', function () {
    test('create permission', function () {
        $data = [
            'question' => 'Is this a test?',
            'answer' => 'Yes, this is a test!',
        ];
        $this->assertCreatePermissions('faq.store', 'faq.create', $data, Faq::class);
    });

    test('can create faq', function () {
        $user = User::factory()->owner()->create();
        $data = [
            'question' => 'Is this a test?',
            'answer' => 'Yes, this is a test!',
        ];

        $this->actingAs($user)
            ->postJson(route('faq.store'), $data)
            ->assertCreated()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('faqs', $data);
    });
});

describe('update operations', function () {
    test('update permission', function () {
        $faq = Faq::factory()->create();
        $data = [
            'question' => 'Is this a test?',
        ];
        $this->assertUpdatePermissions('faq.update', 'faq.update', $faq, $data, Faq::class);
    });

    test('can update faq', function () {
        $user = User::factory()->owner()->create();
        $faq = Faq::factory()->create();
        $data = [
            'question' => 'Is this a test?',
            'answer' => 'Yes, this is a test!',
        ];

        $this->actingAs($user)
            ->patchJson(route('faq.update', $faq), $data)
            ->assertOk()
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('faqs', $data);
    });
});

describe('update operations', function () {
    test('delete permission', function () {
        $faq = Faq::factory()->create();
        $this->assertDeletePermissions('faq.destroy', 'faq.delete', $faq, Faq::class);
    });

    test('can delete faq', function () {
        $user = User::factory()->owner()->create();
        $faq = Faq::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('faq.destroy', $faq))
            ->assertOk();

        $this->assertDatabaseMissing('faqs', $faq->toArray());
    });
});
