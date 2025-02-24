<?php

namespace App\Domains\Role\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\Role\Model\Role as Model;
use App\Domains\Role\Service\Create as CreateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str; // Thêm use này để sử dụng Str::slug

class Create extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('role-create.meta-title'));

        return $this->page('role.create', $this->data());
    }

    public function store(): Response|JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $this->request->all();
            // Tạo alias từ name, chuyển thành slug và đảm bảo duy nhất
            $alias = Str::slug($data['name']);
            $originalAlias = $alias;
            $counter = 1;

            // Kiểm tra tính duy nhất của alias
            while (Model::where('alias', $alias)->exists()) {
                $alias = $originalAlias . '-' . $counter;
                $counter++;
            }

            $data['alias'] = $alias; // Thêm alias vào dữ liệu

            $role = CreateService::make($data)
                ->validate()
                ->create();

            DB::commit();

            $response = [
                'status' => true,
                'message' => __('role-create.success'),
                'role' => $this->formatRole($role)
            ];

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('role.index')->with('success', __('role-create.success'));
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

    protected function data(): array
    {
        return [
            'errors' => session('errors') ?? new \Illuminate\Support\MessageBag(),
        ];
    }

    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->data()
        ]);
    }

    public function edit($id): Response|JsonResponse
    {
        $role = Model::findOrFail($id);

        if ($this->request->wantsJson()) {
            return $this->json([
                'data' => array_merge($this->data(), ['role' => $this->formatRole($role)])
            ]);
        }

        $this->meta('title', __('role-edit.meta-title'));

        return $this->page('role.edit', array_merge($this->data(), ['role' => $role]));
    }

    public function update($id): Response|JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $role = Model::findOrFail($id);

            $validator = Validator::make($this->request->all(), [
                'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
                'description' => 'nullable|string|max:255',

            ]);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Tạo alias mới từ name nếu name thay đổi
            $newAlias = Str::slug($this->request->get('name'));
            if ($role->name !== $this->request->get('name')) {
                $originalAlias = $newAlias;
                $counter = 1;

                while (Model::where('alias', $newAlias)->where('id', '!=', $role->id)->exists()) {
                    $newAlias = $originalAlias . '-' . $counter;
                    $counter++;
                }
            } else {
                $newAlias = $role->alias; // Giữ nguyên alias nếu name không thay đổi
            }

            $role->update([
                'name' => $this->request->get('name'),
                'description' => $this->request->get('description'),
                'alias' => $newAlias, // Cập nhật alias
            ]);

            DB::commit();

            $response = [
                'status' => true,
                'message' => __('role-update.success'),
                'role' => $this->formatRole($role)
            ];

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('role.index')->with('success', __('role-update.success'));
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

    public function destroy($id): Response|JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $role = Model::findOrFail($id);
            $role->delete();

            DB::commit();

            $response = [
                'status' => true,
                'message' => __('role-delete.success'),
            ];

            if ($this->request->wantsJson()) {
                return $this->json($response);
            }

            return redirect()->route('role.index')->with('success', __('role-delete.success'));
        } catch (\Exception $e) {
            DB::rollBack();

            $errorResponse = [
                'status' => false,
                'message' => $e->getMessage()
            ];

            if ($this->request->wantsJson()) {
                return $this->json($errorResponse, 422);
            }

            return redirect()->route('role.index')->withErrors($e->getMessage());
        }
    }

    protected function formatRole(Model $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'alias' => $role->alias, // Thêm alias vào response JSON
            'created_at' => $role->created_at ? \Carbon\Carbon::parse($role->created_at)->toDateTimeString() : null,
        ];
    }
}
