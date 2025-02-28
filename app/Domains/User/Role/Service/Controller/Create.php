<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Service\Controller;

use Illuminate\Http\Request;
use App\Domains\User\Role\Model\Role as Model;
use App\Domains\User\Role\Service\Create as CreateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;

class Create extends CreateMapAbstract
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $auth;

    /**
     * @param \Illuminate\Http\Request $request
     * @param mixed $auth
     * @return void
     */
    public function __construct(Request $request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $auth
     * @return static
     */
    public static function new(Request $request, $auth)
    {
        return new static($request, $auth);
    }

    /**
     * Get data for the create/edit page.
     *
     * @return array
     */
    public function data(): array
    {
        return [
            'errors' => session('errors') ?? new MessageBag(),
            'features' => \App\Domains\User\Role\Feature\Model\Feature::all(),
        ];
    }

    /**
     * Store a new role.
     *
     * @return array
     */
    public function store(): array
    {
        try {
            Log::info('Starting store method');

            DB::beginTransaction();

            $data = $this->request->all();

            $alias = Str::slug($data['name']);
            $originalAlias = $alias;
            $counter = 1;

            while (Model::where('alias', $alias)->exists()) {
                $alias = $originalAlias . '-' . $counter;
                $counter++;
            }

            $data['alias'] = $alias;

            $role = CreateService::make($data)
                ->validate()
                ->create();

            if ($this->request->has('feature_ids')) {
                $featureIds = array_map('intval', $this->request->get('feature_ids'));
                $result = $role->features()->sync($featureIds);
                Log::info('Sync result: ', (array) $result);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => __('role-create.success'),
                'role' => $this->formatRole($role)
            ];
        } catch (\Exception $e) {
            Log::error('Error in store: ' . $e->getMessage(), $e->getTrace());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param int $id
     * @return \App\Domains\User\Role\Model\Role
     */
    public function edit($id): Model
    {
        return Model::findOrFail($id);
    }

    /**
     * Update the specified role.
     *
     * @param int $id
     * @return array
     */
    public function update($id): array
    {
        try {
            Log::info('Starting update method');

            DB::beginTransaction();

            $role = Model::findOrFail($id);
            Log::info('Role found: ', $role->toArray());

            $validator = Validator::make($this->request->all(), [
                'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
                'description' => 'nullable|string|max:255',
                'alias' => 'nullable|string|max:100|unique:roles,alias,' . $role->id,
            ]);

            Log::info('Validator data: ', $this->request->all());
            if ($validator->fails()) {
                Log::error('Validation failed: ', $validator->errors()->toArray());
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $newAlias = $role->alias; // Giữ alias cũ làm mặc định

            // Chỉ tạo alias mới nếu tên thay đổi
            if ($role->name !== $this->request->get('name')) {
                $newAlias = Str::slug($this->request->get('name'));
                $originalAlias = $newAlias;
                $counter = 1;

                while (Model::where('alias', $newAlias)->where('id', '!=', $role->id)->exists()) {
                    $newAlias = $originalAlias . '-' . $counter;
                    $counter++;
                    Log::info('Checking alias conflict, new alias: ' . $newAlias);
                }
            }

            $role->update([
                'name' => $this->request->get('name'),
                'description' => $this->request->get('description'),
                'alias' => $newAlias,
            ]);

            Log::info('Role updated: ', $role->toArray());

            if ($this->request->has('feature_ids')) {
                $featureIds = !empty($this->request->get('feature_ids'))
                    ? array_map('intval', $this->request->get('feature_ids'))
                    : [];

                Log::info('Feature IDs before sync: ', $featureIds);
                $result = $role->features()->sync($featureIds);
                Log::info('Sync result: ', (array) $result);
            }

            DB::commit();
            Log::info('Transaction committed');

            return [
                'status' => true,
                'message' => __('role-update.success'),
                'role' => $this->formatRole($role)
            ];
        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage(), $e->getTrace());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified role.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id): array
    {
        try {
            DB::beginTransaction();

            $role = Model::findOrFail($id);

            // Xóa các quan hệ trước nếu cần
            $role->features()->detach();

            $role->delete();

            DB::commit();

            return [
                'status' => true,
                'message' => __('role-delete.success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in destroy: ' . $e->getMessage(), $e->getTrace());
            throw $e;
        }
    }

    /**
     * Get JSON response for create/edit.
     *
     * @return array
     */
    public function responseJson(): array
    {
        return $this->data();
    }

    /**
     * Format role data.
     *
     * @param \App\Domains\User\Role\Model\Role $role
     * @return array
     */
    public function formatRole(Model $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'alias' => $role->alias,
            'created_at' => $role->created_at ? \Carbon\Carbon::parse($role->created_at)->toDateTimeString() : null,
        ];
    }
}
