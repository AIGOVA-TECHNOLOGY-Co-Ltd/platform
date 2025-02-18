<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Action\AuthApi;
use App\Domains\User\Exception\AuthFailed;

class CheckPermission
{
    private $methodToAction = [
        'GET' => 'read',
        'POST' => 'create',
        'PUT' => 'update',
        'PATCH' => 'update',
        'DELETE' => 'delete'
    ];

    public function handle(Request $request, Closure $next, $entity = null)
    {
        try {
            $authApi = new AuthApi($request);
            $user = $authApi->handle();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized: Authentication failed'], 401);
            }

            $userId = $user->id;

            $enterpriseId = DB::table('user_enterprises')
                ->where('user_id', $userId)
                ->value('enterprise_id');

            $roles = DB::table('user_roles')
                ->where('user_id', $userId)
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->select('user_roles.role_id', 'roles.name as role_name', 'roles.highest_privilege_role as highest_privilege_role')
                ->get()
                ->map(fn($item) => (array) $item)
                ->values()
                ->toArray();

            $roleNames = collect($roles)->pluck('role_name')->toArray();
            $roleIds = collect($roles)->pluck('role_id')->toArray();


            if (in_array('Admin System', $roleNames, true) && is_null($enterpriseId)) {
                $request->merge([
                    'user_id' => $userId,
                    'user_enterprise_id' => $enterpriseId,
                    'user_role' => $roles,
                    'allowed_actions' => ["create,read,update,delete"]
                ]);

                return $next($request);
            }



            $permissions = DB::table('permissions')
                ->whereIn('role_id', $roleIds)
                ->where('enterprise_id', $enterpriseId)
                ->get()
                ->map(fn($item) => (array) $item)
                ->values()
                ->toArray();

            $actionIds = collect($permissions)->pluck('action_id')->toArray();

            $actionNames = DB::table('actions')
                ->whereIn('id', $actionIds)
                ->pluck('name', 'id')
                ->toArray();

            $requestMethod = $request->method();
            $requiredAction = $this->methodToAction[$requestMethod] ?? null;

            if ($requiredAction && !in_array($requiredAction, $actionNames)) {
                return response()->json(['message' => 'Forbidden: You do not have permission to perform this action'], 403);
            }

            // Suy luận ngược action_id từ actionNames
            $matchedActionIds = array_keys(array_filter($actionNames, fn($name) => in_array($name, $actionNames)));

            // Suy luận ngược permission từ action_id
            $matchedPermissions = array_values(array_filter($permissions, fn($perm) => in_array($perm['action_id'], $matchedActionIds)));

            // Log::info('Suy luận ngược:', [
            //     'matched_action_ids' => $matchedActionIds,
            //     'matched_permissions' => $matchedPermissions
            // ]);

            $request->merge([
                'user_id' => $userId,
                'user_enterprise_id' => $enterpriseId,
                'user_role' => $roles,
                'permissions' => $matchedPermissions,
                'allowed_actions' => array_values($actionNames)
            ]);
        } catch (AuthFailed $e) {
            return response()->json(['message' => 'Unauthorized: Invalid API Key'], 401);
        }

        return $next($request);
    }


    /**
     * Kiểm tra quyền GET
     */
    public function validatePermission(
        Request $request,
        ?int $requiredEnterpriseId = null,
        ?int $requiredEntityId = null,
        ?int $requiredRoleId = null,
        ?int $requiredEntityRecordId = null,
        ?string $requiredRoleName = null,
        ?int $requiredHighestPrivilegeRole = null
    ): bool {
        // Log the incoming request for debugging
        // Log::info('validatePermission called', [
        //     'requiredEnterpriseId' => $requiredEnterpriseId,
        //     'requiredEntityId' => $requiredEntityId,
        //     'requiredRoleId' => $requiredRoleId,
        //     'requiredEntityRecordId' => $requiredEntityRecordId,
        //     'user_enterprise_id' => $request['user_enterprise_id'],
        // ]);

        // Kiểm tra các permission trong request
        if (isset($request['permissions']) && is_array($request['permissions'])) {
            foreach ($request['permissions'] as $permission) {
                // Log::info('Checking permission', ['permission' => $permission]);

                $scope = DB::table('scopes')->where('id', $permission['scope_id'])->value('name');
                // Log::info('Scope name for permission', ['scope' => $scope]);

                if ($scope === 'none') {
                    // Log::warning('Permission scope is none', ['permission' => $permission]);
                    return false;
                }

                // Kiểm tra Entity ID
                if ($requiredEntityId !== null) {
                    // Log::info('Checking Entity ID', ['requiredEntityId' => $requiredEntityId, 'permissionEntityId' => $permission['entity_id']]);
                    if ($requiredEntityId !== $permission['entity_id']) {
                        // Log::warning('Entity ID mismatch', ['requiredEntityId' => $requiredEntityId, 'permissionEntityId' => $permission['entity_id']]);
                        return false;
                    }
                }

                // Kiểm tra Enterprise ID
                if ($requiredEnterpriseId !== null) {
                    // Log::info('Checking Enterprise ID', ['requiredEnterpriseId' => $requiredEnterpriseId, 'userEnterpriseId' => $request['user_enterprise_id']]);
                    if ($requiredEnterpriseId !== $request['user_enterprise_id']) {
                        // Log::warning('Enterprise ID mismatch', ['requiredEnterpriseId' => $requiredEnterpriseId, 'userEnterpriseId' => $request['user_enterprise_id']]);
                        return false;
                    }
                }

                // Kiểm tra Entity Record ID
                if ($requiredEntityRecordId !== null) {
                    // Log::info('Checking Entity Record ID', ['requiredEntityRecordId' => $requiredEntityRecordId, 'permissionEntityRecordId' => $permission['entity_record_id']]);
                    if ($requiredEntityRecordId !== $permission['entity_record_id']) {
                        // Log::warning('Entity Record ID mismatch', ['requiredEntityRecordId' => $requiredEntityRecordId, 'permissionEntityRecordId' => $permission['entity_record_id']]);
                        return false;
                    }
                }

                if ($requiredRoleId !== null) {
                    // Sử dụng hasRolePermission để kiểm tra quyền
                    // Log::info('Checking role permission', ['role_id' => $permission['role_id'], 'requiredRoleId' => $requiredRoleId, 'user_enterprise_id' => $request['user_enterprise_id']]);
                    if (!$this->hasRolePermission($permission['role_id'], $requiredRoleId, $request['user_enterprise_id'])) {
                        // Log::warning('Role permission check failed', ['role_id' => $permission['role_id'], 'requiredRoleId' => $requiredRoleId]);
                        return false; // Không có quyền
                    }
                }



                if ($requiredRoleName !== null) {

                    $role_name = null; // Mặc định là null nếu không tìm thấy

                    foreach ($request['user_role'] as $role) {
                        if ($role['role_id'] == $permission['role_id']) {
                            $role_name = $role['role_name'];
                            break; // Dừng lại khi tìm thấy role_id
                        }
                    }

                    // Log::warning('Role Name', ['role name' => $role_name]);

                    if ($requiredRoleName !== $role_name) {
                        return false;
                    }
                }

                if ($requiredHighestPrivilegeRole !== null) {
                    $role_highest_privilege_role = null; // Mặc định là null nếu không tìm thấy

                    foreach ($request['user_role'] as $role) {
                        if ($role['role_id'] == $permission['role_id']) {
                            $role_highest_privilege_role = $role['highest_privilege_role'];
                            break; // Dừng lại khi tìm thấy role_id
                        }
                    }

                    // Log::warning('Role role_highest_privilege_role', ['role_highest_privilege_role' => $role_highest_privilege_role]);

                    if ($requiredHighestPrivilegeRole !== $role_highest_privilege_role) {
                        return false;
                    }
                }

                // Log the request method
                $requestMethod = $request->method();
                // Log::info('Request method', ['method' => $requestMethod]);

                if ($requestMethod === 'GET') {
                    // Log::info('Checking GET permission', ['validateGetPermission' => $this->validateGetPermission()]);
                    if (!$this->validateGetPermission()) {
                        // Log::warning('GET permission denied');
                        return false; // Không có quyền
                    }
                }

                if ($requestMethod === 'POST') {
                    // Log::info('Checking POST permission');
                    if (!$this->validatePostPermission()) {
                        // Log::warning('POST permission denied');
                        return false; // Không có quyền
                    }
                }

                if ($requestMethod === 'PATCH') {
                    // Log::info('Checking PATCH permission');
                    if (!$this->validatePatchPermission()) {
                        // Log::warning('PATCH permission denied');
                        return false; // Không có quyền
                    }
                }

                if ($requestMethod === 'DELETE') {
                    // Log::info('Checking DELETE permission');
                    if (!$this->validateDeletePermission()) {
                        // Log::warning('DELETE permission denied');
                        return false; // Không có quyền
                    }
                }
            }
        }

        // Log::info('Permission validation successful');
        return true;
    }


    private function hasRolePermission($userRole, $requiredRole, $enterpriseId)
    {
        if ($userRole === $requiredRole) {
            return true; // Nếu userRole và requiredRole giống nhau thì có quyền
        }

        // Truy vấn đệ quy để kiểm tra xem requiredRole có nằm trong danh sách quyền hạn của userRole không
        $roles = collect([$userRole]);

        do {
            $childRoles = DB::table('role_hierarchy')
                ->whereIn('parent_role_id', $roles)
                ->where('enterprise_id', $enterpriseId)
                ->pluck('child_role_id');

            if ($childRoles->contains($requiredRole)) {
                return true; // Nếu requiredRole nằm trong danh sách quyền hạn của userRole thì có quyền
            }

            $roles = $childRoles;
        } while ($roles->isNotEmpty());

        return false;
    }

    private function validateGetPermission()
    {
        return true;
    }

    private function validatePostPermission()
    {
        return true;
    }

    private function validatePatchPermission()
    {
        return true;
    }

    private function validateDeletePermission()
    {
        return true;
    }
}

