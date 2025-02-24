<?php declare(strict_types=1);

namespace App\Domains\Permissions\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Permissions\Model\Collection\Permission as Collection;
use App\Domains\Permissions\Model\Permission as Model;

class Index extends ControllerAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        // Không gọi data() trong constructor
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $data = $this->dataCore();

        // Nếu chưa có 'permissions', thêm vào
        if (!array_key_exists('permissions', $data)) {
            $data['permissions'] = $this->list();
        }

        return $data;
    }

    /**
     * @return \App\Domains\Permissions\Model\Collection\Permission
     */
    public function list(): Collection
    {
        return new Collection(
            Model::query()
                ->with(['role', 'action']) // Load các quan hệ cần thiết
                ->get()
        );
    }
}