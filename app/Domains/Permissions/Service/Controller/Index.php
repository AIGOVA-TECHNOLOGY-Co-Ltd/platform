<?php declare(strict_types=1);

namespace App\Domains\Permissions\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Permissions\Model\Collection\Permission as Collection;
use App\Domains\Permissions\Model\Permission as Model;

class Index extends ControllerAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        // Không gọi data() trong constructor
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $data = $this->dataCore();

        if (!array_key_exists('permissions', $data)) {
            $data['permissions'] = $this->list();
        }

        return $data;
    }

    /**
     * @return \App\Domains\Permissions\Model\Collection\Permission
     */
    public function list(): Collection
    {
        $permissions = Model::query()
            ->with(['role', 'action']) // Load quan hệ Role & Action
            ->get()
            ->groupBy('role.name'); // Nhóm theo Role Name

        $formattedPermissions = collect();
        $index = 1; // Bắt đầu số thứ tự từ 1

        foreach ($permissions as $roleName => $groupedPermissions) {
            $actions = $groupedPermissions->pluck('action.name')->unique()->implode(', ');
            $firstPermission = $groupedPermissions->first(); // Lấy bản ghi đầu tiên

            $formattedPermissions->push((object) [
                'stt' => $index++, // Gán số thứ tự
                'role_id' => $firstPermission->role_id, // Sử dụng role_id thay vì id
                'role_name' => $roleName,
                'actions' => $actions,
                'created_at' => $firstPermission->created_at, // Lấy created_at đầu tiên
            ]);
        }

        return new Collection($formattedPermissions);
    }

}