<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Controller;

use App\Domains\User\Permissions\Model\Permission as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\User\Permissions\Model\Permission
     */

    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\User\Permissions\Model\Permission
     */

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->first(); // Hoặc find($id)
    }
}
