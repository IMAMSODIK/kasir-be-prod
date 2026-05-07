<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
            'items.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $grossAmount = 0;
            $itemDetails = [];
            $orderId = 'ORDER-' . Str::uuid();

            $order = Order::create([
                'order_id' => $orderId,
                'total_amount' => 0,
                'status' => 'pending',
                'meja_id' => null,
            ]);

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
                    'note' => $item['note'] ?? null
                ]);

                $itemDetails[] = [
                    'id' => $menu->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu
                ];
            }

            // Update total
            $order->update([
                'total_amount' => $grossAmount
            ]);

            // Midtrans parameters
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => 'Customer',
                ],
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 60
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bank_transfer',
                    'qris',
                    'gopay',
                    'shopeepay'
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($orderId)
    {
        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'order_id' => $order->order_id,
            'total_amount' => $order->total_amount
        ]);
    }
}
