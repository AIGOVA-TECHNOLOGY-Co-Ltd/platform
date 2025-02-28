<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Action;

use Illuminate\Http\RedirectResponse;
use App\Domains\User\Permissions\Model\Permission as PermissionModel;

class Delete
{
    /**
     * @param int $role_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $role_id): RedirectResponse
    {
        // Hard delete all permissions for the given role_id
        // PermissionModel::where('role_id', $role_id)->delete();

        // Soft delete all permissions for the given role_id
        $deletedCount = PermissionModel::where('role_id', $role_id)
            ->whereNull('delete_at') // Chỉ xóa các bản ghi chưa bị soft delete
            ->delete();
        // Kiểm tra nếu có bản ghi nào được soft delete
        if ($deletedCount === 0) {
            return redirect()->route('user.permissions.index')->with('error', __('permissions-delete.no-records'));
        }
        // Trả về redirect response
        return redirect()->route('user.permissions.index')->with('success', __('permissions-delete.success'));
    }
}


/*  // retrieve soft-deleted records
    // Include soft-deleted records:
    PermissionModel::withTrashed()->where('role_id', $role_id)->get();
    // Only soft-deleted records:
    PermissionModel::onlyTrashed()->where('role_id', $role_id)->get();
    // Restore a soft-deleted record:
    PermissionModel::withTrashed()->where('role_id', $role_id)->restore();

*/