<?php

namespace App\Domains\Enterprise\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    protected function data(): array
    {
        return [];
    }

    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        // todo: Bổ sung thêm phần này
        $this->meta('title', __('enterprise-index.meta-title'));

        return $this->page('enterprise.index', $this->data());
    }

    /**
     * @return JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $this->responseJsonList()));
    }

    /**
     * @return \App\Domains\Enterprise\Model\Collection\Enterprise
     */
    protected function responseJsonList(): Collection
    {
        // todo: Bổ sung thêm phần này
        return [];
    }
}
