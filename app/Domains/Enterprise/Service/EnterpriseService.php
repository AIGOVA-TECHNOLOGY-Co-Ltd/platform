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

    public function getAll(): array
    {
        return Enterprise::with('ownerRole')
            ->get()
            ->map(function ($enterprise) {
                return [
                    'id' => $enterprise->id,
                    'user_name' => $enterprise->owner->name,
                    'name' => $enterprise->name,
                    'email' => $enterprise->email,
                    'roleName' => $enterprise->ownerRole->name ?? null,
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

    public function delete($id): void
    {
        // Tìm enterprise theo ID
        $enterprise = Enterprise::find($id);

        // Xóa enterprise nếu tồn tại
        $enterprise?->delete();
    }
}
