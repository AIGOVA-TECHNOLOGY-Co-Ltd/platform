<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Action;

use App\Domains\Role\Feature\Model\Feature as Model;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data): Model
    {
        $this->data = $this->validateData($data); // Validate trước khi tạo
        return $this->createFeature();
    }

    protected function validateData(array $data): array
    {
        return $this->request->validate([
            'alias' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'role_id' => 'nullable|exists:roles,id',
        ]);
    }

    protected function createFeature(): Model
    {
        $this->row = Model::query()->create([
            'alias' => $this->data['alias'],
            'name' => $this->data['name'],
            'description' => $this->data['description'],
        ]);

        if (isset($this->data['role_id'])) {
            $this->row->roles()->create(['role_id' => $this->data['role_id']]);
        }

        return $this->row;
    }
}