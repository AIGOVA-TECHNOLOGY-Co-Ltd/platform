<?php declare(strict_types=1);

namespace App\Domains\Permissions\Service\Controller;

use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\Permissions\Model\Action;
use App\Domains\Permissions\Model\Permission;
use App\Domains\Role\Model\Role;

use App\Domains\Permissions\Action\ActionFactory;

class Create
{
    protected $request;
    protected $auth;
    protected $factory;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->factory = new ActionFactory($request, $auth);
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function create(): Permission
    {
        $data = $this->request->validate([
            'role_id' => 'required|exists:roles,id',
            'action_id' => 'required|exists:actions,id',
        ]);

        return $this->factory->create($data);
    }

    public function data(): array
    {
        return [
            'roles' => $this->getRoles(),
            'actions' => $this->getActions(),
            'errors' => session('errors') ?? new \Illuminate\Support\MessageBag(),
        ];
    }

    protected function getRoles(): array
    {
        return \App\Domains\Role\Model\Role::all(['id', 'name'])->toArray();
    }

    /**
     * Lấy danh sách actions
     */
    protected function getActions(): array
    {
        return Action::all(['id', 'name'])->toArray();
    }
    protected function getEnterprises(): array
    {
        return Enterprise::select('id', 'name')->get()->toArray();
    }
}