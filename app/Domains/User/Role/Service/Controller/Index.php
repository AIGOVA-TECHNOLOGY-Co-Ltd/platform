<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Service\Controller;

use Illuminate\Http\Request;
use App\Domains\User\Role\Model\Role as Model;
use Illuminate\Support\Collection;

class Index extends IndexMapAbstract
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
     * Get data for the index page.
     *
     * @return array
     */
    public function data(): array
    {
        return [
            'roles' => $this->listPaginated(), // Sử dụng phân trang cho view
            'search' => $this->request->get('search'),
        ];
    }

    /**
     * Get the paginated list of roles for the view.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function listPaginated()
    {
        $query = Model::query()
            ->select([
                'id',
                'name',
                'description',
                'alias', // Bao gồm alias
                'created_at',
            ]);

        if ($this->request->filled('search')) {
            $search = $this->request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('alias', 'LIKE', "%{$search}%"); // Tìm kiếm theo alias
            });
        }

        return $query->paginate($this->request->get('per_page', 10));
    }

    /**
     * Get the list of roles as a Collection (for JSON or other uses).
     *
     * @return \Illuminate\Support\Collection
     */
    protected function list(): Collection
    {
        $query = Model::query()
            ->select([
                'id',
                'name',
                'description',
                'alias', // Bao gồm alias
                'created_at',
            ])
            ->enabled();

        if ($this->request->filled('search')) {
            $search = $this->request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('alias', 'LIKE', "%{$search}%"); // Tìm kiếm theo alias
            });
        }

        return $query->get(); // Trả về Collection thay vì paginate
    }

    /**
     * Get the list of roles for JSON response.
     *
     * @return \Illuminate\Support\Collection
     */
    public function responseJsonList(): Collection
    {
        return $this->list(); // Sử dụng list() để trả về Collection
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
            'alias' => $role->alias, // Bao gồm alias
            'created_at' => $role->created_at->toDateTimeString(),
        ];
    }
}
