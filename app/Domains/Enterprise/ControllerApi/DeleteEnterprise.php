<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\CheckPermission;

class DeleteEnterprise extends ControllerApiAbstract
{
    /**
     * Handle the request to delete an enterprise.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {


        $checkPermission = new CheckPermission();
        $hasPermission = $checkPermission->validatePermission(request: $request, requiredHighestPrivilegeRole: 1);
        if (!$hasPermission) {
            return response()->json(['message' => 'Forbidden: You do not have permission'], 403);
        }

        $tableColumns = Schema::getColumnListing('enterprises');
        $params = collect($request->all())->only($tableColumns)->toArray();

        if (empty($params)) {
            return response()->json(['message' => 'No valid parameters provided'], 400);
        }

        $query = DB::table('enterprises');

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        $deleted = $query->delete();

        return response()->json(['message' => $deleted ? 'Enterprise deleted successfully' : 'No matching enterprise found']);
    }
}
