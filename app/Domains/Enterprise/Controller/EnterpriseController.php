<?php

namespace App\Domains\Enterprise\Controller;

use App\Domains\Enterprise\Service\EnterpriseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnterpriseController extends ControllerAbstract
{
    private EnterpriseService $service;

    public function __construct(EnterpriseService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $data = $this->service->getAll($this->request, $this->auth);

        return $this->page('enterprise.index', compact('data'));
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $users = $this->service->getUsersWithoutEnterprise($this->request, $this->auth);

        return $this->page('enterprise.create', compact('users'));
    }

    public function store(Request $request)
    {
        // validation data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:enterprises,code',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|digits_between:10,15',
            'email' => 'required|email|unique:enterprises,email',
            'owner_id' => 'required|exists:user,id',

        ]);


        $this->service->store($validated, $this->auth);

        return redirect()->route('enterprise.index');
    }
}
