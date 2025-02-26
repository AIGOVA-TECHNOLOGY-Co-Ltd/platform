<?php declare(strict_types=1);

namespace App\Domains\Permissions\Action;

use Illuminate\Http\RedirectResponse;
use App\Domains\Permissions\Model\Permission as PermissionModel;

class Delete
{
    /**
     * @param int $role_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $role_id): RedirectResponse
    {
        // Xóa tất cả permissions liên quan đến role_id
        PermissionModel::where('role_id', $role_id)->delete();

        // Trả về redirect response
        return redirect()->route('permissions.index')->with('success', __('permissions-delete.success'));
    }
}