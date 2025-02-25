<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Action;

use App\Domains\Role\Feature\Model\Feature as Model;
use App\Domains\Core\Action\ActionFactoryAbstract;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(): Model
    {
        // Truyền dữ liệu thô từ request thay vì validate ở đây
        $data = $this->request->only(['alias', 'name', 'description', 'role_id']);
        return $this->actionHandle(Create::class, $data);
    }
}