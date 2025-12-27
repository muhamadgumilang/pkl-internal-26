{{-- resources/views/orders/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Header Halaman --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Pesanan Saya</h1>
                    <p class="text-muted mb-0">Pantau status dan riwayat belanja Anda</p>
                </div>
                <i class="bi bi-bag-check fs-1 text-primary opacity-25"></i>
            </div>

            @if($orders->isEmpty())
                {{-- Tampilan Jika Kosong --}}
                <div class="card shadow-sm border-0 py-5">
                    <div class="card-body text-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" alt="Empty" style="width: 120px;" class="mb-4 opacity-50">
                        <h5 class="fw-bold">Belum Ada Pesanan</h5>
                        <p class="text-muted">Sepertinya Anda belum melakukan pemesanan apapun.</p>
                        <a href="{{ url('/') }}" class="btn btn-primary px-4 mt-2">Mulai Belanja</a>
                    </div>
                </div>
            @else
                {{-- List Pesanan --}}
                @foreach($orders as $order)
                    <div class="card shadow-sm border-0 mb-3 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="p-4">
                                <div class="row align-items-center">
                                    {{-- Info Order --}}
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-3 rounded-3 me-3">
                                                <i class="bi bi-receipt text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1">#{{ $order->order_number }}</h6>
                                                <small class="text-muted">{{ $order->created_at->format('d M Y') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Total Harga --}}
                                    <div class="col-6 col-md-3">
                                        <small class="text-muted d-block">Total Tagihan</small>
                                        <span class="fw-bold text-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>

                                    {{-- Status Badge --}}
                                    <div class="col-6 col-md-3">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-warning text-dark',
                                                'processing' => 'bg-info text-white',
                                                'shipped' => 'bg-primary text-white',
                                                'delivered' => 'bg-success text-white',
                                                'cancelled' => 'bg-danger text-white',
                                            ];
                                            $badgeClass = $statusClasses[$order->status] ?? 'bg-secondary text-white';
                                        @endphp
                                        <small class="text-muted d-block mb-1">Status</small>
                                        <span class="badge rounded-pill px-3 py-2 {{ $badgeClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    {{-- Tombol Aksi --}}
                                    <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            Detail
                                        </a>


                                        </div>

                                </div>
                            </div>

                            {{-- Preview Item Terakhir (Opsional) --}}
                            @if($order->items->count() > 0)
                            <div class="bg-light px-4 py-2 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-box-seam me-1"></i>
                                    {{ $order->items->first()->product_name }}
                                    @if($order->items->count() > 1)
                                        <span class="fw-medium">dan {{ $order->items->count() - 1 }} produk lainnya...</span>
                                    @endif
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Pagination (Jika ada) --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->links() }}
                    <a href="{{ url('home') }}" class="btn btn-outline-primary shadow-sm btn-sm border btn-sm rounded-pill px-4">
                                            <i class="bi bi-house-door"></i> Home
                                            </a>
                </div>

            @endif
        </div>
    </div>
</div>
@endsection