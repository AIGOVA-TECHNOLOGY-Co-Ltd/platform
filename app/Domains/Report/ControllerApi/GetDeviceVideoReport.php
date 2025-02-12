<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class GetDeviceVideoReport extends ControllerApiAbstract
{
    /**
     * Handle the request to fetch device videos by device ID.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Lấy các tham số đầu vào
        $id = $request->input('id');
        $deviceId = $request->input('device_id');
        $vehicleId = $request->input('vehicle_id');
        $videoId = $request->input('video_id');
        $createdAt = $request->input('date'); // Tham số ngày đầu vào dạng dd-mm-yyyy

        // Lọc dữ liệu dựa trên các tham số đã được cung cấp
        $query = DB::table('device_video_reports');

        // Thêm điều kiện theo các tham số nếu có giá trị
        if ($id) {
            $query->where('id', $id);
        }

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($videoId) {
            $query->where('video_id', $videoId);
        }

        // Xử lý tham số created_at nếu được cung cấp
        if ($createdAt) {
            try {
                // Chuyển đổi định dạng từ dd-mm-yyyy sang Y-m-d
                $createdAtFormatted = \DateTime::createFromFormat('d-m-Y', $createdAt)->format('Y-m-d');
                $query->whereDate('created_at', $createdAtFormatted);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format. Expected dd-mm-yyyy.'], 400);
            }
        }

        // Lấy kết quả từ bảng device_video_reports
        $results = $query->get();

        // Trường hợp không có kết quả và tất cả tham số đều không có giá trị
        if ($results->isEmpty()) {
            // Kiểm tra nếu không có tham số nào
            if (!$id && !$deviceId && !$vehicleId && !$videoId && !$createdAt) {
                return response()->json([]); // Nếu không có tham số, trả về mảng rỗng
            }
           return response()->json((object) []); // Nếu có tham số mà không có kết quả, trả về mảng rỗng
        }

        // Nếu chỉ có một kết quả và tất cả tham số đầu vào đều không có giá trị, trả về toàn bộ bảng
        if ($results->count() === 1 && !$id && !$deviceId && !$vehicleId && !$videoId && !$createdAt) {
            $allResults = DB::table('device_video_reports')->get(); // Lấy toàn bộ bảng
            return response()->json($allResults);
        }

        // Nếu chỉ có một kết quả, trả về bản ghi đầu tiên
        if ($results->count() === 1) {
            return response()->json($results->first());
        }

        // Trả về kết quả dưới dạng JSON nếu có nhiều hơn một bản ghi
        return response()->json($results);
    }
}
