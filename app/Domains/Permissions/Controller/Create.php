<?php

namespace App\Domains\Permissions\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Domains\Permissions\Model\Permission as Model;
use App\Domains\Permissions\Service\Create as CreateService;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
use App\Domains\Enterprise\Model\Enterprise;
use App\Domains\Permissions\Model\Action;


class Create extends ControllerAbstract
{
    /**
     * Hiển thị form tạo permission mới
     */
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('permissions-create.title'));

        return $this->page('permissions.create', $this->data());
    }

    /**
     * Xử lý lưu permission mới vào database
     */
    public function store(): Response|JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $permission = CreateService::make($this->request->all())
                ->validate()
                ->create();

            DB::commit();

            $response = [
                'status' => true,
                'message' => __('permissions-create.success'),
                'permission' => $this->formatPermission($permission)
            ];

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('permissions.index')->with('success', __('permissions-create.success'));
        } catch (\Exception $e) {
            DB::rollBack();

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
     * Lấy dữ liệu cho form tạo permission
     */
    protected function data(): array
    {
        return [
            'roles' => $this->getRoles(),
            'actions' => $this->getActions(),
            'enterprises' => $this->getEnterprises(),
            'errors' => session('errors') ?? new \Illuminate\Support\MessageBag(),
        ];
    }

    /**
     * Lấy danh sách roles
     */
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

    /**
     * Xử lý response JSON
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->data()
        ]);
    }

    /**
     * Format permission để trả về JSON
     */
    protected function formatPermission(Model $permission): array
    {
        // $enterprises = $this->getEnterprises();
        // dd($enterprises);
        return [
            'id' => $permission->id,
            'role_id' => $permission->role_id,
            'action_id' => $permission->action_id,
            'enterprise_id' => $permission->enterprise_id,
            'created_at' => $permission->created_at ? \Carbon\Carbon::parse($permission->created_at)->toDateTimeString() : null,
        ];
    }

    /**
     * Lấy danh sách enterprises
     */
    protected function getEnterprises(): array
    {
        return Enterprise::select('id', 'name')->get()->toArray();
    }

}
