<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TheGioiViaService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViaController extends Controller
{
    protected $apiService;

    public function __construct(TheGioiViaService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Lấy thông tin Profile (Số dư của User trên web mình)
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Trả về số dư thực tế trong DB của web bạn
        return response()->json([
            'status' => 'success',
            'data' => [
                'username' => $user->name,
                'email' => $user->email,
                'balance' => $user->balance // Đây là số dư user nạp vào web bạn
            ]
        ]);
    }

    /**
     * Lấy danh sách sản phẩm từ API gốc về để hiển thị
     */
    public function getProducts()
    {
        try {
            // Vẫn gọi API để lấy danh sách sản phẩm realtime
            $data = $this->apiService->get('products.php');
            
            $products = [];
            // Tỉ lệ giá bán (Ví dụ: bán đắt hơn 20% so với giá gốc)
            $markupPercentage = 1.2; 

            if (isset($data['categories'])) {
                foreach ($data['categories'] as $cat) {
                    if (isset($cat['products'])) {
                        foreach ($cat['products'] as $prod) {
                            $prod['category_name'] = $cat['name'];
                            
                            // *** QUAN TRỌNG: Tăng giá bán ở đây để có lời ***
                            // Giá hiển thị cho khách = Giá gốc * 1.2
                            $prod['price'] = ceil($prod['price'] * $markupPercentage); 
                            
                            $products[] = $prod;
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'data' => $products]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mua hàng (Xử lý trừ tiền ví nội bộ -> Gọi API mua)
     */
    public function buyProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'product_name' => 'nullable|string', 
            'current_price' => 'required|numeric' // Giá bán cho khách
        ]);

        $user = Auth::user();
        $amount = (int)$request->input('amount');
        $productId = $request->input('product_id');
        $sellPrice = (float)$request->input('current_price'); // Giá bán (đã markup)
        $totalCostForUser = $sellPrice * $amount;

        // 1. Kiểm tra số dư nội bộ của khách
        if ($user->balance < $totalCostForUser) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Số dư không đủ. Vui lòng nạp thêm tiền!'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // 2. Trừ tiền user trước (Lock để tránh race condition nếu cần, ở đây làm đơn giản)
            $user->balance -= $totalCostForUser;
            $user->save();

            // 3. Gọi API bên thứ 3 để lấy hàng
            // Lưu ý: Gọi API này sẽ trừ tiền thật trong tài khoản Đại lý của bạn
            $payload = [
                'action' => 'buyProduct',
                'id'     => $productId,
                'amount' => $amount,
            ];
            
            // API key nằm trong Service, khách không bao giờ biết
            $apiResult = $this->apiService->post('buy_product', $payload);

            // Kiểm tra kết quả trả về từ API
            if (!isset($apiResult['data']) || !is_array($apiResult['data'])) {
                 throw new \Exception($apiResult['msg'] ?? 'Lỗi không xác định từ nhà cung cấp');
            }

            // 4. Lưu lịch sử đơn hàng vào DB
            $orderContent = implode("\n", $apiResult['data']); // Nối các dòng account lại
            
            Order::create([
                'user_id' => $user->id,
                'trans_id' => $apiResult['trans_id'] ?? uniqid(),
                'product_id' => $productId,
                'product_name' => $request->input('product_name', 'Unknown Product'),
                'quantity' => $amount,
                'price' => $sellPrice, // Giá bán cho khách
                'cost' => 0, // Giá gốc (nếu muốn lưu chính xác thì phải lấy từ API product detail, tạm thời để 0 hoặc lấy logic khác)
                'total_price' => $totalCostForUser,
                'data' => $orderContent,
                'status' => 'success'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'msg' => 'Mua hàng thành công!',
                'data' => $apiResult['data'], // Trả hàng về cho khách
                'new_balance' => $user->balance // Trả về số dư mới để update UI
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Buy Error: " . $e->getMessage());

            // Nếu lỗi là do API bên kia hết tiền hoặc hết hàng, tiền của user đã được hoàn lại nhờ rollback
            return response()->json([
                'status' => 'error',
                'msg' => 'Giao dịch thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}