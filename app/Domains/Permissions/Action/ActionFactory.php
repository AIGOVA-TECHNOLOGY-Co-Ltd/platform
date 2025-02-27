<?php declare(strict_types=1);

namespace App\Domains\Permissions\Action;

use App\Domains\Permissions\Model\Permission as Model;
use App\Domains\Core\Action\ActionFactoryAbstract;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(array $data): Model
    {
        return $this->actionHandle(Create::class, $data, $data); // Truyền $data vào $args
    }
}