<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Controller;

use App\Domains\Role\Feature\Model\Feature as Model;
use App\Domains\Role\Model\Role;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOrFail();
    }
}