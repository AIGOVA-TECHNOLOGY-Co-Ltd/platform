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

            Log::info('User authenticated:', [
                'user_id' => $userId,
                'email' => $user->email,
                'enterprise_id' => $enterpriseId
            ]);

            // Lấy danh sách role_id và role_name của user
            $roles = DB::table('user_roles')
                ->where('user_id', $userId)
                ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->select('user_roles.role_id', 'roles.name as role_name')
                ->get();

            $roleIds = $roles->pluck('role_id')->toArray();
            $roleNames = $roles->pluck('role_name')->toArray();

            Log::info('User roles:', ['role_ids' => $roleIds, 'role_names' => $roleNames]);

            // Nếu user có role_name là 'Admin System' và enterpriseId là null thì cho phép bỏ qua kiểm tra quyền
            if (in_array('Admin System', $roleNames, true) && is_null($enterpriseId)) {
                // Thêm thông tin vào request
                $request->merge([
                    'user_id' => $userId,
                    'user_enterprise_id' => $enterpriseId,
                    'user_role' => $roleIds,
                    'user_role_names' => $roleNames,

                    'allowed_actions' => ["create,read,update,delete"]
                ]);

                return $next($request);
            }

            // Lấy danh sách permission_id từ bảng permissions
            $permissionIds = DB::table('permissions')
                ->whereIn('role_id', $roleIds)
                ->where('enterprise_id', $enterpriseId)
                ->pluck('id')
                ->toArray();

            Log::info('Permission IDs:', ['permission_ids' => $permissionIds]);

            // Lấy danh sách action_id từ bảng permissions
            $actionIds = DB::table('permissions')
                ->whereIn('role_id', $roleIds)
                ->where('enterprise_id', $enterpriseId)
                ->pluck('action_id')
                ->toArray();

            Log::info('Action IDs:', ['action_ids' => $actionIds]);

            // Lấy danh sách action name từ bảng actions
            $actionNames = DB::table('actions')
                ->whereIn('id', $actionIds)
                ->pluck('name')
                ->toArray();

            Log::info('Allowed Actions:', ['actions' => $actionNames]);

            // Xác định action cần kiểm tra dựa vào method của request
            $requestMethod = $request->method();
            $requiredAction = $this->methodToAction[$requestMethod] ?? null;

            // Kiểm tra quyền truy cập
            if ($requiredAction && !in_array($requiredAction, $actionNames)) {
                return response()->json(['message' => 'Forbidden: You do not have permission to perform this action'], 403);
            }

            // Thêm thông tin vào request
            $request->merge([
                'user_id' => $userId,
                'user_enterprise_id' => $enterpriseId,
                'user_role' => $roleIds,
                'user_role_names' => $roleNames,
                'permission_id' => $permissionIds,
                'allowed_actions' => $actionNames
            ]);

            Log::info('Final Request Data:', [
                'user_id' => $userId,
                'user_enterprise_id' => $enterpriseId,
                'user_role' => $roleIds,
                'user_role_names' => $roleNames,
                'permission_id' => $permissionIds,
                'allowed_actions' => $actionNames
            ]);
        } catch (AuthFailed $e) {
            return response()->json(['message' => 'Unauthorized: Invalid API Key'], 401);
        }

        return $next($request);
    }
}
