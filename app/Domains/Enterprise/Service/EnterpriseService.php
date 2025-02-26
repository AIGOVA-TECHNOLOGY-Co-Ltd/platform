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
     * @param $validated
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
}
