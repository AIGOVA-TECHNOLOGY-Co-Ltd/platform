<?php declare(strict_types=1);

namespace App\Domains\Permissions\Action;

use App\Domains\Permissions\Model\Permission as Model;

class Update extends ActionAbstract
{
    protected array $data;
    protected Model $permission;

    public function __construct(Model $permission)
    {
        $this->permission = $permission;
    }

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->updatePermission();
    }

    protected function updatePermission(): Model
    {
        $this->permission->update([
            'role_id' => $this->data['role_id'],
            'action_id' => $this->data['action_id'],
        ]);

        return $this->permission;
    }
}