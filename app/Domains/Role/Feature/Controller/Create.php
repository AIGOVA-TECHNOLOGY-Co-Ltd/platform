<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\Role\Feature\Service\Controller\Create as CreateService;
use App\Domains\Role\Feature\Model\Feature as Model;

class Create extends ControllerAbstract
{
    protected ?Model $row;

    public function __invoke(): Response|RedirectResponse
    {
        if ($this->request->isMethod('post')) {
            return $this->create();
        }

        $this->meta('title', __('role-feature-create.meta-title'));
        return $this->page('role.feature.create', $this->data());
    }

    protected function data(): array
    {
        return CreateService::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        $service = CreateService::new($this->request, $this->auth);
        $this->row = $service->create();
        $this->sessionMessage('success', __('role-feature-create.success'));
        return redirect()->route('role.feature.index', $this->data());
    }
}