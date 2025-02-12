<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Lấy danh sách tất cả các cột trong bảng enterprises
        $tableColumns = Schema::getColumnListing('enterprises');

        // Lấy tất cả tham số từ request
        $params = $request->all();

        // Bắt đầu truy vấn dữ liệu
        $query = DB::table('enterprises');

        // Nếu không có tham số nào, trả về toàn bộ bảng
        if (empty($params)) {
            return response()->json($query->get());
        }

        // Lọc dữ liệu theo các tham số có trong bảng
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
