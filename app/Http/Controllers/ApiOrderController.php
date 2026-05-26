<?php

namespace App\Http\Controllers;

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
            'payment_type' => 'nullable|string', // cash | qris
        ]);

        DB::beginTransaction();

        try {

            $paymentType = $request->payment_type ?? 'qris';

            $grossAmount = 0;
            $itemDetails = [];
            $today = now()->format('dmy');

            $lastOrder = Order::whereDate('created_at', today())
                ->where('order_id', 'like', 'TRX-' . $today . '-%')
                ->latest('id')
                ->first();

            if ($lastOrder) {
                $lastNumber = (int) substr($lastOrder->order_id, -3);
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $orderId = 'TRX-' . $today . '-' . $newNumber;

            // =========================
            // CREATE ORDER
            // =========================
            $order = Order::create([
                'order_id' => $orderId,
                'total_amount' => 0,
                'status' => $paymentType === 'cash' ? 'paid' : 'pending',
                'payment_type' => $paymentType,
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
                    'note' => $item['note'] ?? null,
                ]);

                $itemDetails[] = [
                    'id' => $menu->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu,
                ];
            }

            $order->update([
                'total_amount' => $grossAmount,
            ]);

            // =========================
            // CASH FLOW (NO MIDTRANS)
            // =========================
            if ($paymentType === 'cash') {

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Order tunai berhasil dibuat',
                    'order_id' => $orderId,
                    'order' => $order,
                ]);
            }

            // =========================
            // MIDTRANS FLOW (QRIS / ONLINE)
            // =========================
            $isProduction = config('midtrans.is_production', false);

            $baseUrl = $isProduction
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com';

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
                    'qris',
                    'bank_transfer',
                    'gopay',
                    'shopeepay',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $redirectUrl = "{$baseUrl}/snap/v2/vtweb/{$snapToken}";

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
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

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
