<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Domains\User\Role\Service\Controller\Create as ControllerService;

class Create extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('role-create.meta-title'));

        return $this->page('user.role.create', $this->getService()->data());
    }

    /**
     * Store a new role.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->store();

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('user.role.index')->with('success', __('role-create.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage()
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function edit($id): Response|JsonResponse
    {
        $role = \App\Domains\User\Role\Model\Role::findOrFail($id);

        if ($this->request->wantsJson()) {
            return $this->json([
                'data' => array_merge($this->getService()->data(), ['role' => $this->getService()->formatRole($role)])
            ]);
        }

        $this->meta('title', __('role-edit.meta-title'));

        return $this->page('user.role.edit', array_merge($this->getService()->data(), ['role' => $role]));
    }

    /**
     * Update the specified role.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update($id): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->update($id);

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('user.role.index')->with('success', __('role-update.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage()
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified role.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id): Response|JsonResponse|RedirectResponse
    {
        try {
            $response = $this->getService()->destroy($id);

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('user.role.index')->with('success', __('role-delete.success'));
        } catch (\Exception $e) {
            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage()
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->route('user.role.index')->withErrors($e->getMessage());
        }
    }

    /**
     * Get JSON response for create/edit.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->getService()->data()
        ]);
    }

    /**
     * Get the service instance.
     *
     * @return \App\Domains\User\Role\Service\Controller\Create
     */
    protected function getService()
    {
        return ControllerService::new($this->request, $this->auth);
    }
}
