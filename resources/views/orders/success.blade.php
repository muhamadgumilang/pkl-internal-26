{{-- resources/views/orders/success.blade.php --}}

@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container py-5">
    <div class="text-center">

        <h1 class="text-success mb-3">✅ Pembayaran Berhasil</h1>

        <p class="lead">
            Terima kasih, pesanan <strong class="fw-bold">#{{ $order->order_number }}</strong>
            telah berhasil dibayar.
        </p>

        <div class="card mt-4 mx-auto" style="max-width: 400px;">
            <div class="card-body text-start">
                <p><strong>Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                <p><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary mt-4">
            Lihat Detail Pesanan
        </a>

    </div>
</div>
@endsection