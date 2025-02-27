<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Action;

use App\Domains\User\Permissions\Model\Permission as Model;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $data;
        return $this->createFeature();
    }

    protected function createFeature(): Model
    {
        $this->row = Model::query()->create([
            'role_id' => $this->data['role_id'],
            'action_id' => $this->data['action_id'],
        ]);


        return $this->row;
    }
}