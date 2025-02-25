<?php

namespace App\Domains\Permissions\Service;

use App\Domains\Permissions\Model\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class Create
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data): self
    {
        return new static($data);
    }

    public function validate(): self
    {
        $validator = Validator::make($this->data, [
            'role_id' => 'required|exists:roles,id',
            'action_id' => 'required|exists:actions,id',
            'enterprise_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this;
    }

    public function create(): Permission
    {
        return Permission::create([
            'role_id' => $this->data['role_id'],
            'action_id' => $this->data['action_id'],
            'enterprise_id' => $this->data['enterprise_id'] ?? null,
        ]);
    }
}
