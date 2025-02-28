<?php

namespace App\Domains\Enterprise\Service;

use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\User\Model\User;

class EnterpriseService
{
    private Enterprise $enterprise;

    // constructor
    public function __construct(Enterprise $enterprise)
    {
        $this->enterprise = $enterprise;
    }

    /**
     * Get all enterprises including soft deleted
     * @return array
     */
    public function getAll(): array
    {
        return Enterprise::with(['ownerRole', 'owner'])
            ->withTrashed()
            ->get()
            ->map(function ($enterprise) {
                return [
                    'id' => $enterprise->id,
                    'user_name' => $enterprise->owner->name ?? null,
                    'name' => $enterprise->name,
                    'email' => $enterprise->email,
                    'roleName' => $enterprise->ownerRole->name ?? null,
                    'deleted_at' => $enterprise->deleted_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get users without enterprise
     *
     * @return array
     */
    public function getUsersWithoutEnterprise(): array
    {
        return User::whereNotIn('id', Enterprise::pluck('owner_id'))->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        })->toArray();
    }

    /**
     * Store new Enterprise
     *
     * @param $validated
     *
     * @return Enterprise
     */
    public function store($validated)
    {
        return Enterprise::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'owner_id' => $validated['owner_id'],
        ]);
    }

    public function update($validated, $id)
    {
        // Tìm enterprise theo ID
        $enterprise = Enterprise::find($id);

        // Nếu không tìm thấy, báo lỗi
        if (!$enterprise) {
            throw new \Exception('Enterprise not found');
        }

        // Cập nhật enterprise
        $enterprise->update($validated);

        return $enterprise;
    }

    public function getEnterpriseById($id): ?Enterprise
    {
        $enterprise = Enterprise::find($id);

        return $enterprise;
    }

    /**
     * Soft delete enterprise
     * @param $id
     * @return void
     */
    public function softDelete($id): void
    {
        // Tìm enterprise hoặc báo lỗi nếu không tìm thấy
        $enterprise = Enterprise::findOrFail($id);

        // Thực hiện soft delete (chỉ cập nhật `deleted_at`)
        $enterprise->delete();
    }

    /**
     * Restore enterprise
     * @param $id
     * @return bool
     */
    public function restore($id): bool
    {
        // Tìm enterprise hoặc báo lỗi nếu không tìm thấy
        $enterprise = Enterprise::withTrashed()->findOrFail($id);

        // Thực hiện khôi phục enterprise
        return $enterprise->restore();
    }

    /**
     * Delete enterprise permanently
     * @param $id
     * @return void
     */
    public function forceDelete($id): void
    {
        // Tìm enterprise hoặc báo lỗi nếu không tìm thấy
        $enterprise = Enterprise::withTrashed()->findOrFail($id);

        // Thực hiện force delete (xóa vĩnh viễn)
        $enterprise->forceDelete();
    }
}
//Mặc định, các bản ghi đã xóa sẽ không xuất hiện khi bạn gọi:
//Enterprise::all();
//Nếu muốn lấy cả những bản ghi đã bị xóa:
//Enterprise::withTrashed()->get();
//Nếu chỉ muốn lấy những bản ghi đã bị xóa:
//Enterprise::onlyTrashed()->get();
