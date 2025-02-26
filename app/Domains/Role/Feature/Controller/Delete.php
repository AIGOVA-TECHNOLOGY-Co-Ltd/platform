<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Controller;

use Illuminate\Http\RedirectResponse;
use App\Domains\Role\Feature\Service\Controller\Delete as DeleteService;
use App\Domains\Role\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Delete extends ControllerWebAbstract
{
    protected ?Model $row;

    public function __invoke(int $id): RedirectResponse
    {
        $this->row = Model::findOrFail($id);
        $service = DeleteService::new($this->request, $this->auth);
        $service->delete($this->row);
        $this->sessionMessage('success', __('role-feature-update.delete-success'));
        return redirect()->route('role.feature.index');
    }
}