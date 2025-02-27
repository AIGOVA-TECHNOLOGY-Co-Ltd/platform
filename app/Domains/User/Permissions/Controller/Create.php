<?php declare(strict_types=1);
namespace App\Domains\User\Permissions\Controller;
use Illuminate\Http\RedirectResponse;
use App\Domains\User\Permissions\Model\Permission as Model;
use App\Domains\User\Permissions\Service\Controller\Create as CreateService;

use Illuminate\Http\Response;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Create extends ControllerWebAbstract
{
    private ?Model $row;

    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('permissions-create.meta-title'));
        return $this->page('user.permissions.create', $this->data());
    }

    public function data(): array
    {
        return CreateService::new($this->request, $this->auth)->data();
    }
    protected function create(): RedirectResponse
    {
        $service = CreateService::new($this->request, $this->auth);
        $this->row = $service->create();
        $this->sessionMessage('success', __('permissions-create.success'));
        return redirect()->route('user.permissions.index', $this->data());
    }
}