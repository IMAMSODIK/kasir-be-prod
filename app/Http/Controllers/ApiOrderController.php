<?php
// app/Http/Controllers/Api/ApiOrderController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class ApiOrderController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $grossAmount = 0;
            $itemDetails = [];
            $orderId = 'ORDER-' . Str::uuid();

            // Create order
            $order = Order::create([
                'order_id' => $orderId,
                'total_amount' => 0,
                'status' => 'pending',
                'meja_id' => null,
            ]);

            // Create order items
            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['id']);

                $price = (int) $menu->harga;
                $qty = (int) $item['qty'];
                $subtotal = $price * $qty;
                $grossAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'nama_menu' => $menu->nama_menu,
                    'harga' => $price,
                    'qty' => $qty,
                    'note' => $item['note'] ?? null,
                ]);

                $itemDetails[] = [
                    'id' => $menu->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu,
                ];
            }

            // Update total amount
            $order->update(['total_amount' => $grossAmount]);

            // Get environment (sandbox or production)
            $isProduction = config('midtrans.is_production', false);
            $baseUrl = $isProduction 
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com';

            // Midtrans parameters
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $request->user()?->name ?? 'Customer',
                    'email' => $request->user()?->email ?? 'customer@example.com',
                ],
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 60,
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bank_transfer',
                    'qris',
                    'gopay',
                    'shopeepay',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            
            // Generate redirect URL (lebih stabil untuk mobile)
            $redirectUrl = "{$baseUrl}/snap/v2/vtweb/{$snapToken}";

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl, // Kirim redirect URL
                'order_id' => $orderId,
                'message' => 'Checkout berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkStatus($orderId)
    {
        try {
            $order = Order::where('order_id', $orderId)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_found',
                    'message' => 'Order tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $order->status,
                'order_id' => $order->order_id,
                'total_amount' => $order->total_amount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status: ' . $e->getMessage(),
            ], 500);
        }
    }
}