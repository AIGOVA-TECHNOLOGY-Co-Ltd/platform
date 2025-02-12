<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class UpdateEnterprise extends ControllerApiAbstract
{
    /**
     * Handle the request to update an existing enterprise.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $tableColumns = Schema::getColumnListing('enterprises');

        // Kết hợp dữ liệu từ cả query params (?id=3) và body
        $requestData = array_merge($request->query(), $request->all());

        if (empty($requestData)) {
            return response()->json(['message' => 'No valid parameters provided'], 400);
        }

        // Xác định điều kiện WHERE (ưu tiên 'id', nếu có)
        $conditions = [];
        if (!empty($requestData['id'])) {
            $conditions['id'] = $requestData['id'];
        }

        // Lấy dữ liệu cập nhật (trừ ID, vì ID không thể cập nhật)
        $dataToUpdate = collect($requestData)
            ->only($tableColumns)
            ->except(['id'])
            ->toArray();

        if (empty($conditions)) {
            return response()->json(['message' => 'No valid conditions provided, skipping update'], 400);
        }

        if (empty($dataToUpdate)) {
            return response()->json(['message' => 'No update data provided'], 400);
        }

        Log::info('Request UpdateEnterprise Data:', $requestData);

        // Kiểm tra xem có bản ghi nào khớp điều kiện không
        $exists = DB::table('enterprises')->where($conditions)->exists();
        if (!$exists) {
            return response()->json(['message' => 'No matching enterprise found'], 404);
        }

        // Thực hiện cập nhật
        $updated = DB::table('enterprises')
            ->where($conditions)
            ->update($dataToUpdate);

        return response()->json([
            'message' => $updated ? 'Enterprise updated successfully' : 'No changes made'
        ]);
    }
}
