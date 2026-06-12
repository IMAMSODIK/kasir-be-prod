<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;

use Midtrans\Config;
use Midtrans\Transaction;

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
            'payment_type' => 'nullable|string',
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

            $order = Order::create([
                'order_id' => $orderId,
                'total_amount' => 0,
                'status' => $paymentType === 'tunai'
                    ? 'paid'
                    : 'pending',
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
                    'id' => (string) $menu->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => substr($menu->nama_menu, 0, 50),
                ];
            }

            $order->update([
                'total_amount' => $grossAmount,
            ]);

            // =========================
            // CASH
            // =========================

            if ($paymentType === 'tunai') {

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Order tunai berhasil dibuat',
                    'payment_type' => 'tunai',
                    'order_id' => $orderId,
                    'order' => $order,
                ]);
            }

            // =========================
            // SNAP MIDTRANS
            // =========================

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
                    'unit' => 'minutes',
                    'duration' => 60,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'success' => true,
                'payment_type' => 'qris',
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'gross_amount' => $grossAmount,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Checkout gagal',
                'error' => $e->getMessage(),
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

            // =========================
            // CHECK MIDTRANS STATUS
            // =========================

            if ($order->payment_type === 'qris') {

                $status = Transaction::status($orderId);

                $transactionStatus = $status->transaction_status ?? 'pending';

                // settlement = paid
                if (
                    $transactionStatus === 'settlement' ||
                    $transactionStatus === 'capture'
                ) {

                    $order->update([
                        'status' => 'paid'
                    ]);
                }

                // expired
                if ($transactionStatus === 'expire') {

                    $order->update([
                        'status' => 'expired'
                    ]);
                }

                // cancel
                if ($transactionStatus === 'cancel') {

                    $order->update([
                        'status' => 'cancelled'
                    ]);
                }

                $order->refresh();
            }

            return response()->json([
                'success' => true,
                'status' => $order->status,
                'order_id' => $order->order_id,
                'payment_type' => $order->payment_type,
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
