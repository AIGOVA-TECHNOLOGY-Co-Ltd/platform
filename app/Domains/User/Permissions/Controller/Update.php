<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Controller;

use Illuminate\Http\RedirectResponse;
use App\Domains\User\Permissions\Model\Permission as Model;
use App\Domains\User\Permissions\Service\Controller\Update as UpdateService;
use Illuminate\Http\Response;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use Illuminate\Support\MessageBag;

class Update extends ControllerWebAbstract
{

    public function edit(int $role_id): Response
    {
        $row = Model::with('role')->where('role_id', $role_id)->firstOrFail();
        $permissions = Model::where('role_id', $role_id)->get();

        $actions = \App\Domains\User\Permissions\Model\Action::all(['id', 'name'])->toArray();

        $this->meta('title', __('permissions-update.meta-title'));
        return $this->page('user.permissions.edit', [
            'row' => $row,
            'permissions' => $permissions,
            'actions' => $actions,
            'errors' => session('errors') ?? new MessageBag(),
            'selected_actions' => $permissions->pluck('action_id')->unique()->toArray(),
        ]);
    }

    public function update(int $role_id): RedirectResponse
    {
        $row = Model::where('role_id', $role_id)->firstOrFail();
        $service = UpdateService::new($this->request, $this->auth, $row);
        $service->update();
        $this->sessionMessage('success', __('permissions-update.success'));
        return redirect()->route('user.permissions.index');
    }
}