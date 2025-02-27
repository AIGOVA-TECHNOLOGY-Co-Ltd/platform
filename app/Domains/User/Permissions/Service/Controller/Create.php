<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Service\Controller;

use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\User\Permissions\Model\Action;
use App\Domains\User\Permissions\Model\Permission;
use App\Domains\User\Role\Model\Role;
use Illuminate\Validation\ValidationException;
use App\Domains\User\Permissions\Action\ActionFactory;

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

        try {
            return $this->factory->create($data);
        } catch (ValidationException $e) {
            throw $e; // Re-throw the exception to be caught by the controller
        }
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
        return \App\Domains\User\Role\Model\Role::all(['id', 'name'])->toArray();
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