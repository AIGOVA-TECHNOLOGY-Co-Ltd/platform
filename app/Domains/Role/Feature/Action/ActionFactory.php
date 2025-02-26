<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Action;

use App\Domains\Role\Feature\Model\Feature as Model;
use App\Domains\Core\Action\ActionFactoryAbstract;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(array $data): Model
    {
        return $this->actionHandle(Create::class, $data, $data);
    }

    public function update(Model $feature, array $data): Model
    {
        $this->row = $feature;
        return $this->actionHandle(Update::class, $data, $data);
    }
}