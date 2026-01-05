<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik user yang sedang login.
     */
    public function index()
    {
        // PENTING: Jangan gunakan Order::all() !
        // Kita hanya mengambil order milik user yg sedang login menggunakan relasi hasMany.
        // auth()->user()->orders() akan otomatis memfilter: WHERE user_id = current_user_id
        $orders = auth()->user()->orders()
            ->with(['items.product']) // Eager Load nested: Order -> OrderItems -> Product
            ->latest()                // Urutkan dari pesanan terbaru
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     */
    public function show(Order $order)
    {
        // 1. Authorize (Security Check)
        // User A TIDAK BOLEH melihat pesanan User B.
        // Kita cek apakah ID pemilik order sama dengan ID user yang login.
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Ambil snap_token dari database (jika ada)
        // Jika kolom di DB namanya snap_token, kita ambil itu
        $snapToken = $order->snap_token;

        // Jika status masih pending DAN belum punya snap_token di database
        if ($order->status === 'pending' && ! $snapToken) {
            // 1. Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 2. Buat parameter transaksi
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => $order->shipping_phone,
                ],
                // TAMBAHKAN CALLBACK URL agar user bisa diarahkan sesuai status pembayaran
                'callbacks' => [
                    'finish' => route('orders.success', $order->id),
                    'unfinish' => route('orders.show', $order->id),
                    'error' => route('orders.show', $order->id),
                ],
            ];

            try {
                // 3. Minta token dari Midtrans
                $snapToken = Snap::getSnapToken($params);

                // 4. Simpan token ke database agar tidak request ulang terus-menerus
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                // Jika gagal (misal server key salah), log errornya
                \Log::error('Midtrans Error: '.$e->getMessage());
            }
        }

        // 2. Load relasi detail
        // Kita butuh data items dan gambar produknya untuk ditampilkan di invoice view.
        $order->load(['items.product', 'items.product.primaryImage']);

        // 3. Return view dengan order dan snap token
        return view('orders.show', compact('order', 'snapToken'));
    }
}
