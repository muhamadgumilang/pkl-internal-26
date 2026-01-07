<?php

// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk admin.
     * Dilengkapi filter by status.
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with('user')
            ->when($request->status, function ($q, $status) {
                // Mapping completed → delivered
                if ($status === 'completed') {
                    $status = 'delivered';
                }

                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Detail order untuk admin.
     */
    public function show(Order $order)
    {
        // Load item produk dan data user
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan (misal: kirim barang)
     * Handle otomatis pengembalian stok jika status diubah jadi Cancelled.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // VALIDASI SESUAI YANG DIKIRIM DARI BLADE
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // 🔁 MAPPING completed → delivered (ENUM DB)
        if ($newStatus === 'completed') {
            $newStatus = 'delivered';
        }

        // 🔄 RESTOCK JIKA CANCELLED
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }
}
