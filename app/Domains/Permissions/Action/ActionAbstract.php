<?php declare(strict_types=1);

namespace App\Domains\Permissions\Action;

use App\Domains\Permissions\Model\Permission as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\Permissions\Model\Permission
     */
    protected ?Model $row;
}