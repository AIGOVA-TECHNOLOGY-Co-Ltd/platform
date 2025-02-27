<?php declare(strict_types=1);

namespace App\Domains\Permissions\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\Permissions\Service\Controller\Index as ControllerService;

class Index extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        $service = new ControllerService($this->request, $this->auth);

        if ($this->request->wantsJson()) {
            return $this->responseJson($service);
        }

        $this->meta('title', __('permissions-index.meta-title'));

        return $this->page('permissions.index', $service->data());
    }

    protected function responseJson(ControllerService $service): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $service->list()));
    }
}
