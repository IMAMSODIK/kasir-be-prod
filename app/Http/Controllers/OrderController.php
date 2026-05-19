<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Snap;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Config;

class OrderController extends Controller
{
    // midtrans
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
            'items.*.id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
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
                'meja_id' => $request->customer_table ? Meja::where('slug', $request->customer_table)->first()->id : null,
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

                // 🔹 untuk midtrans
                $itemDetails[] = [
                    'id' => $menu->id,
                    'price' => $price,
                    'quantity' => $qty,
                    'name' => $menu->nama_menu
                ];
            }

            // 🔹 update total
            $order->update([
                'total_amount' => $grossAmount
            ]);

            // 🔹 PARAM MIDTRANS
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => 'Customer',
                ],
                'expiry' => config('midtrans.expiry'),
                'enabled_payments' => config('midtrans.enabled_payments'),
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Checkout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($orderId)
    {
        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status' => $order->status
        ]);
    }
    // end midtrans


    // utils
    public function index()
    {
        return view('order.index', [
            'pageTitle' => 'Data Order'
        ]);
    }

    public function pending(Request $request)
    {
        $keyword = $request->keyword;

        $orders = Order::with([
            'items',
            'meja'
        ])
            ->when($keyword, function ($q) use ($keyword) {

                $q->where(function ($query) use ($keyword) {

                    $query->where('order_id', 'like', "%{$keyword}%")
                        ->orWhere('customer_name', 'like', "%{$keyword}%");
                });
            })
            ->whereIn('status', [
                'pending',
                'challenge'
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER SELESAI
    |--------------------------------------------------------------------------
    */
    public function done(Request $request)
    {
        $keyword = $request->keyword;

        $limit = $request->limit ?? 10;

        $orders = Order::with([
            'items',
            'meja'
        ])
            ->when($keyword, function ($q) use ($keyword) {

                $q->where(function ($query) use ($keyword) {

                    $query->where('order_id', 'like', "%{$keyword}%")
                        ->orWhere('customer_name', 'like', "%{$keyword}%");
                });
            })
            ->whereIn('status', [
                'paid',
                'cancelled',
                'deny',
                'expired'
            ])
            ->latest()
            ->paginate($limit);

        return response()->json($orders);
    }

    /*
    |--------------------------------------------------------------------------
    | SELESAIKAN ORDER
    |--------------------------------------------------------------------------
    */
    public function selesai($id)
    {
        $order = Order::findOrFail($id);

        $order->status = 'paid';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diselesaikan'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BATALKAN ORDER
    |--------------------------------------------------------------------------
    */
    public function batalkan($id)
    {
        $order = Order::findOrFail($id);

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibatalkan'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS ORDER
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus'
        ]);
    }

    public function indexApi(Request $request)
    {
        $status = $request->status;

        $query = Order::with([
            'items',
            'meja'
        ])->latest();

        // ROLE KITCHEN
        if (auth()->user()->role === 'kitchen') {

            if ($status === 'active') {
                $query->where('status', 'paid');
            }

            if ($status === 'completed') {
                $query->where('status', 'completed');
            }
        } else {

            // ROLE LAIN
            if ($status === 'active') {
                $query->whereIn('status', [
                    'pending',
                    'paid',
                    'challenge'
                ]);
            }

            if ($status === 'completed') {
                $query->where('status', 'completed');
            }
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
