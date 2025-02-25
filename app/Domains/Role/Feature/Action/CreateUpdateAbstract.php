<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Action;

use App\Domains\Role\Feature\Model\Feature as Model;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    /**
     * @return void
     */
    abstract protected function save(): void;

    /**
     * @return \App\Domains\Role\Feature\Model\Feature
     */
    public function handle(): Model
    {
        $this->data();
        $this->save();

        return $this->row;
    }

    /**
     * @return void
     */
    protected function data(): void
    {
        $this->dataName();
        $this->dataAlias();
        $this->dataDescription();

    }

    /**
     * @return void
     */
    protected function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    /**
     * @return void
     */
    protected function dataAlias(): void
    {
        $this->data['alias'] = trim($this->data['alias']);
    }

    /**
     * @return void
     */
    protected function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }


}
