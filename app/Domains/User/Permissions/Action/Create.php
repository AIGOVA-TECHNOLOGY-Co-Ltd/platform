<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Action;

use App\Domains\User\Permissions\Model\Permission as Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;
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
        // Check if the permission already exists for this role and action
        $existingPermission = Model::where('role_id', $this->data['role_id'])
            ->where('action_id', $this->data['action_id'])
            ->first();

        if ($existingPermission) {
            $errors = new MessageBag(['message' => 'This action is already assigned to the specified role.']);
            throw ValidationException::withMessages($errors->getMessages());
        }

        $this->row = Model::query()->create([
            'role_id' => $this->data['role_id'],
            'action_id' => $this->data['action_id'],
        ]);


        return $this->row;
    }
}