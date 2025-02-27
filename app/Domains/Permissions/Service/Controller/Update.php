<?php declare(strict_types=1);

namespace App\Domains\Permissions\Service\Controller;

use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\Permissions\Model\Action;
use App\Domains\Permissions\Model\Permission;
use App\Domains\User\Role\Model\Role;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class Update
{
    protected $request;
    protected $auth;
    protected $permission;

    public function __construct(Request $request, Authenticatable $auth, Permission $permission)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->permission = $permission;
    }

    public static function new(Request $request, Authenticatable $auth, Permission $permission): self
    {
        return new self($request, $auth, $permission);
    }

    // public function update(): Permission
    // {
    //     $data = $this->request->validate([
    //         'actions' => 'required|array',
    //         'actions.*' => 'exists:actions,id',
    //     ]);

    //     Permission::where('role_id', $this->permission->role_id)->delete();

    //     if (!empty($data['actions'])) {
    //         foreach ($data['actions'] as $action_id) {
    //             Permission::create([
    //                 'role_id' => $this->permission->role_id,
    //                 'action_id' => $action_id,
    //             ]);
    //         }
    //     }

    //     return $this->permission;
    // }
    public function update(): Permission
    {
        $data = $this->request->validate([
            'actions' => 'array', // Cho phép mảng rỗng
            'actions.*' => 'exists:actions,id',
        ]);

        // Xóa tất cả permissions cũ của role_id
        Permission::where('role_id', $this->permission->role_id)->delete();

        // Tạo mới chỉ nếu có actions được chọn
        if (!empty($data['actions'])) {
            $actionIds = array_map('intval', $data['actions']); // Chuyển đổi sang integer để đảm bảo đúng kiểu
            foreach ($actionIds as $action_id) {
                Permission::firstOrCreate([
                    'role_id' => $this->permission->role_id,
                    'action_id' => $action_id,
                ], [
                    'created_at' => now(), // Đảm bảo created_at được thiết lập
                ]);
            }
        }

        return $this->permission;
    }

    public function data(array $extraData = []): array
    {
        $permissions = $extraData['permissions'] ?? [];
        $selected_actions = $permissions->pluck('action_id')->unique()->toArray();

        return [
            'row' => $this->permission,
            'roles' => $this->getRoles(),
            'actions' => $this->getActions(),
            'selected_actions' => $selected_actions,
            'errors' => session('errors') ?? new \Illuminate\Support\MessageBag(),
        ];
    }

    protected function getRoles(): array
    {
        return Role::all(['id', 'name'])->toArray();
    }

    protected function getActions(): array
    {
        return Action::all(['id', 'name'])->toArray();
    }

    protected function getEnterprises(): array
    {
        return Enterprise::select('id', 'name')->get()->toArray();
    }


}