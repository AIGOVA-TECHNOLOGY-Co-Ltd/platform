<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\Role\Feature\Service\Controller\Update as UpdateService;
use App\Domains\Role\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Update extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): Response|RedirectResponse
    {
        $this->row = Model::findOrFail($id);

        if ($this->request->isMethod('patch')) {
            return $this->update();
        }

        $this->meta('title', __('role-feature-update.meta-title'));
        return $this->page('role.feature.update', $this->data());
    }

    protected function data(): array
    {
        return array_merge(
            ['row' => $this->row],
            UpdateService::new($this->request, $this->auth)->data()
        );
    }

    protected function update(): RedirectResponse
    {
        $service = UpdateService::new($this->request, $this->auth);
        $this->row = $service->update($this->row);
        $this->sessionMessage('success', __('role-feature-update.success'));
        return redirect()->route('role.feature.index');
    }
}