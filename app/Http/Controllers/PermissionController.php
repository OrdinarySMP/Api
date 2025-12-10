<?php

namespace App\Http\Controllers;

use App\Data\Requests\CreatePermissionRequest;
use App\Data\Requests\ReadPermissionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function template(ReadPermissionRequest $request): JsonResponse
    {
        if (! request()->user()?->hasRole('Owner')) {
            abort(403);
        }

        $permissions = [];
        foreach (CreatePermissionRequest::$models as $model) {
            $permissions[$model] = [];
            foreach (CreatePermissionRequest::$operations as $operation) {
                $permissions[$model][] = $operation;
            }
        }

        foreach (CreatePermissionRequest::$specialPermissions as $key => $specialPermission) {
            $permissions[$key] = [
                ...$permissions[$key] ?? [],
                ...$specialPermission,
            ];
        }

        return response()->json($permissions);
    }

    public function index(ReadPermissionRequest $request): JsonResponse
    {
        if (! request()->user()?->hasRole('Owner')) {
            abort(403);
        }

        $roles = Role::whereNotIn('name', ['Owner', 'Bot'])->with('permissions')->get()->map(function (Role $role) {
            return [
                'role' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ];
        });

        return response()->json($roles);
    }

    public function store(CreatePermissionRequest $request): JsonResponse
    {
        /**
         * @var Collection<int, array{role: string, permissions: array<string, array<string,bool>>}>
         */
        $validatedRoles = collect($request->permissions);
        $roles = $validatedRoles->pluck('role')
            ->push('Owner', 'Bot');
        Role::whereNotIn('name', $roles)->delete();

        /**
         * @param  array{role: string, permissions: array<string, array<string,bool>>}  $validatedRole
         */
        $validatedRoles->each(function ($validatedRole) {
            $role = Role::firstOrCreate(['guard_name' => 'web', 'name' => $validatedRole['role']]);

            /**
             * @var array<string, array<string,bool>> $permissions
             */
            $permissions = (array) $validatedRole['permissions'];
            $mappedPermissions = collect($permissions)
                /**
                 * @param  array<string,bool>  $flags
                 * @param  string  $model
                 * @return array<int,string>
                 */
                ->flatMap(fn (array $flags, string $model) => collect($flags)
                    ->filter(fn ($value) => $value === true)
                    ->keys()
                    ->map(fn ($operation) => "$model.$operation"))
                ->values();

            $role->syncPermissions($mappedPermissions);
        });

        return response()->json();
    }
}
