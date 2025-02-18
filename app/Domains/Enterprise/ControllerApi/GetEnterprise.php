<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\CheckPermission;

class GetEnterprise extends ControllerApiAbstract
{
    /**
     * Handle the request to fetch enterprises data dynamically.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Lấy tất cả tham số từ request
        $params = $request->all();

        $checkPermission = new CheckPermission();
        $targetEnterpriseId = isset($params['id']) ? $params['id'] : $params['user_enterprise_id'];

        $hasPermission = $checkPermission->validatePermission(request: $request, requiredEnterpriseId: $targetEnterpriseId);

        if (!$hasPermission) {
            return response()->json(['message' => 'Forbidden: You do not have permission'], 403);
        }

        // Lấy danh sách tất cả các cột trong bảng enterprises
        $tableColumns = Schema::getColumnListing('enterprises');



        Log::info('GetEnterprise:', ['params' => $params]);

        // Bắt đầu truy vấn dữ liệu
        $query = DB::table('enterprises');

        // Nếu có user_enterprise_id, ưu tiên lọc theo id trước
        if (!empty($params['user_enterprise_id'])) {
            $query->where('id', $params['user_enterprise_id']);
            unset($params['user_enterprise_id']); // Xóa để tránh lọc lại
        }

        // Nếu không có tham số nào còn lại, trả về kết quả
        if (empty($params)) {
            return response()->json($query->get());
        }

        // Lọc dữ liệu theo các tham số còn lại có trong bảng
        foreach ($params as $key => $value) {
            if (in_array($key, $tableColumns)) {
                $query->where($key, $value);
            }
        }

        // Lấy kết quả từ truy vấn
        $results = $query->get();

        return response()->json($results);
    }
}
