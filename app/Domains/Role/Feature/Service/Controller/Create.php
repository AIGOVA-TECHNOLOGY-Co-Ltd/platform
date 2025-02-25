<?php declare(strict_types=1);

namespace App\Domains\Role\Feature\Service\Controller;

use App\Domains\Role\Feature\Model\Feature;
use App\Domains\Role\Model\Role;

class Create
{
    protected $request;
    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function create(): Feature
    {

        $data = $this->request->validate([
            'alias' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $feature = Feature::query()->create([
            'alias' => $data['alias'],
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return $feature;
    }

    public function data(): array
    {
        return [
            'roles' => Role::all(),
        ];
    }
}