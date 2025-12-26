<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use function PHPUnit\Framework\assertFalse;

trait CrudPermissionTrait
{
    protected function assertReadPermissions(string $route, string $permission)
    {
        $user = User::factory()->create();
        $this->assertCannotRead($user, $route, $permission);
        $this->assertCanRead($user, $route, $permission);
    }

    private function assertCanRead(User $user, string $route, string $permission)
    {
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->get(route($route))
            ->assertOk();
    }

    private function assertCannotRead(User $user, string $route, string $permission)
    {
        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->get(route($route))
            ->assertForbidden();
    }

    protected function assertCreatePermissions(string $route, string $permission, array $data, string $table, ?array $assertData = null)
    {
        $user = User::factory()->create();
        $this->assertCannotCreate($user, $route, $permission, $data, $table, $assertData);
        $this->assertCanCreate($user, $route, $permission, $data, $table, $assertData);
    }

    private function assertCanCreate(
        User $user,
        string $route,
        string $permission,
        array $data,
        string $table,
        ?array $assertData
    ) {
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->postJson(route($route), $data)
            ->assertCreated();

        $this->assertDatabaseHas($table, $assertData ?? $data);
    }

    private function assertCannotCreate(
        User $user,
        string $route,
        string $permission,
        array $data,
        string $table,
        ?array $assertData
    ) {
        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->postJson(route($route), $data)
            ->assertForbidden();

        $this->assertDatabaseMissing($table, $assertData ?? $data);
    }

    protected function assertUpdatePermissions(string $route, string $permission, Model $model, array $data, string $table)
    {
        $user = User::factory()->create();
        $this->assertCannotUpdate($user, $route, $permission, $model, $data, $table);
        $this->assertCanUpdate($user, $route, $permission, $model, $data, $table);
    }

    private function assertCanUpdate(
        User $user,
        string $route,
        string $permission,
        Model $model,
        array $data,
        string $table
    ) {
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->patchJson(route($route, $model), $data)
            ->assertOk();

        $this->assertDatabaseHas($table, $data);
    }

    private function assertCannotUpdate(
        User $user,
        string $route,
        string $permission,
        Model $model,
        array $data,
        string $table
    ) {
        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->patchJson(route($route, $model), $data)
            ->assertForbidden();

        $this->assertDatabaseMissing($table, $data);
    }

    protected function assertDeletePermissions(string $route, string $permission, Model $model, string $table, bool $softDelete = false)
    {
        $user = User::factory()->create();
        $this->assertCannotDelete($user, $route, $permission, $model, $table);
        $this->assertCanDelete($user, $route, $permission, $model, $table, $softDelete);
    }

    private function assertCanDelete(
        User $user,
        string $route,
        string $permission,
        Model $model,
        string $table,
        bool $softDelete
    ) {
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->deleteJson(route($route, $model))
            ->assertOk();

        if ($softDelete) {
            $this->assertSoftDeleted($table, ['id' => $model->id]);
        } else {
            $this->assertDatabaseMissing($table, ['id' => $model->id]);
        }
    }

    private function assertCannotDelete(
        User $user,
        string $route,
        string $permission,
        Model $model,
        string $table
    ) {
        assertFalse($user->can($permission));

        $this->actingAs($user)
            ->deleteJson(route($route, $model))
            ->assertForbidden();

        $this->assertDatabaseHas($table, ['id' => $model->id]);
    }
}
