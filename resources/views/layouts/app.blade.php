{{-- =====================================================================
FILE: resources/views/layouts/app.blade.php
FUNGSI: Master layout utama (dipakai semua halaman)
===================================================================== --}}

<!DOCTYPE html>
<html lang="id">

<head>
    {{-- Encoding karakter --}}
    <meta charset="UTF-8">

    {{-- Responsive --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token untuk form & AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title dinamis per halaman --}}
    <title>
        @yield('title', 'Beranda') - {{ config('app.name', 'Toko Online') }}
    </title>

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Assets dari Vite --}}
    {{-- app.scss: Bootstrap + custom CSS --}}
    {{-- app.js : Bootstrap JS + custom JS --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- CSS tambahan per halaman --}}
    @stack('styles')

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Stack untuk
    script tambahan dari child view --}} @stack('scripts')
</head>

<script>
    /**
     * Fungsi AJAX untuk Toggle Wishlist
     * Menggunakan Fetch API (Modern JS) daripada jQuery.
     */
    async function toggleWishlist(productId) {
        try {
            // 1. Ambil CSRF token dari meta tag HTML
            // Laravale mewajibkan token ini untuk setiap request POST demi keamanan.
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // 2. Kirim Request ke Server
            const response = await fetch(`/wishlist/toggle/${productId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token, // Tempel token di header
                },
            });

            // 3. Handle jika user belum login (Error 401 Unauthorized)
            if (response.status === 401) {
                window.location.href = "/login"; // Lempar ke halaman login
                return;
            }

            // 4. Baca respon JSON dari server
            const data = await response.json();

            if (data.status === "success") {
                // 5. Update UI tanpa reload halaman
                updateWishlistUI(productId, data.added); // Ganti warna ikon
                updateWishlistCounter(data.count); // Update angka di header
                showToast(data.message); // Tampilkan notifikasi
            }
        } catch (error) {
            console.error("Error:", error);
            showToast("Terjadi kesalahan sistem.", "error");
        }
    }

    function updateWishlistUI(productId, isAdded) {
        // Cari semua tombol wishlist untuk produk ini (bisa ada di card & detail page)
        const buttons = document.querySelectorAll(`.wishlist-btn-${productId}`);

        buttons.forEach((btn) => {
            const icon = btn.querySelector("i"); // Menggunakan tag <i> untuk Bootstrap Icons
            if (isAdded) {
                // Ubah jadi merah solid (Love penuh)
                icon.classList.remove("bi-heart", "text-secondary");
                icon.classList.add("bi-heart-fill", "text-danger");
            } else {
                // Ubah jadi abu-abu outline (Love kosong)
                icon.classList.remove("bi-heart-fill", "text-danger");
                icon.classList.add("bi-heart", "text-secondary");
            }
        });
    }

    function updateWishlistCounter(count) {
        const badge = document.getElementById("wishlist-count");
        if (badge) {
            badge.innerText = count;
            // Bootstrap badge display toggle logic
            badge.style.display = count > 0 ? "inline-block" : "none";
        }
    }
</script>

<body>
    {{-- ===============================================================
    NAVBAR
    =============================================================== --}}
    @include('partials.navbar')

    {{-- ===============================================================
    FLASH MESSAGE
    =============================================================== --}}
    <div class="container mt-3">
        @include('partials.flash-messages')
    </div>

    {{-- ===============================================================
    MAIN CONTENT
    =============================================================== --}}
    <main class="min-vh-100">
        @yield('content')
    </main>

    {{-- ===============================================================
    FOOTER
    CATATAN: Footer hanya dipanggil SATU KALI di layout
    =============================================================== --}}
    @include('partials.footer')

    {{-- ===============================================================
    SCRIPT TAMBAHAN PER HALAMAN
    =============================================================== --}}
    @stack('scripts')
</body>

{{-- nonaktifkan dulu sementara baris --}}
