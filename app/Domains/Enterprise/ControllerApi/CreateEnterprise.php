<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEnterprise extends ControllerApiAbstract
{
    /**
     * Handle the request to create a new enterprise.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $tableColumns = Schema::getColumnListing('enterprises');
        $data = collect($request->all())->only($tableColumns)->toArray();

        if (empty($data)) {
            return response()->json(['message' => 'No valid data provided'], 400);
        }

        $id = DB::table('enterprises')->insertGetId($data);

        return response()->json(['id' => $id, 'message' => 'Enterprise created successfully'], 201);
    }
}
