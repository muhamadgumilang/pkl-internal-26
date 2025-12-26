{{-- resources/views/checkout/index.blade.php --}}

@extends('layouts.app')
@section('content')
{{-- Ganti bagian grid dan card di checkout/index.blade.php --}}
<div class="container py-5">
    <h1 class="h3 mb-4 fw-bold">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Form Alamat (Kiri) --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-4 fw-semibold">Informasi Pengiriman</h2>
                        <div class="mb-3">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="name" value="{{ auth()->user()->name }}" class="form-control" required>
                        </div> welllll
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="form-control" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan (Kanan) --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h2 class="h5 mb-4 fw-semibold">Ringkasan Pesanan</h2>
                        <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach($cart->items as $item)
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                                    <span class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold h5">
                            <span>Total</span>
                            <span>Rp {{ number_format($cart->items->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-full mt-4 py-2 fw-bold">
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection