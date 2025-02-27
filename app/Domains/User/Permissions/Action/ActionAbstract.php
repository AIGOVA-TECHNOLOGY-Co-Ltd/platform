<?php declare(strict_types=1);

namespace App\Domains\User\Permissions\Action;

use App\Domains\User\Permissions\Model\Permission as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\User\Permissions\Model\Permission
     */
    protected ?Model $row;
}