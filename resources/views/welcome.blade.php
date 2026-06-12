<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ config('app.name') }} - Order Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('landing_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* CSS Classes untuk tema */
        .bg-theme-primary {
            background-color: #074b88;
        }

        .bg-theme-dark {
            background-color: #063d6e;
        }

        .text-theme-primary {
            color: #074b88;
        }

        .border-theme-primary {
            border-color: #074b88;
        }

        .hover\:bg-theme-dark:hover {
            background-color: #063d6e;
        }

        .float-cart:hover {
            transform: scale(1.05);
            background-color: #063d6e;
        }

        /* Hover effect untuk tombol kategori */
        .tab-category:hover {
            background-color: #074b88;
            color: white !important;
            border-color: #074b88 !important;
        }

        /* Style untuk link di sidebar */
        .sidebar nav a:hover {
            background-color: #f0f7ff;
        }

        /* Animasi scale untuk modal */
        @keyframes scale {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scale {
            animation: scale 0.2s ease-out;
        }

        /* Scrollbar styling */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .img-wrapper {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .img-skeleton {
            position: absolute;
            inset: 0;
            border-radius: 12px;
            background: linear-gradient(90deg,
                    #f1f5f9 25%,
                    #e2e8f0 50%,
                    #f1f5f9 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.2s linear infinite;
        }

        .menu-img {
            opacity: 0;
            transition: opacity .3s ease;
            position: relative;
            z-index: 2;
        }

        .menu-img.loaded {
            opacity: 1;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <div id="overlay" class="overlay fixed inset-0 bg-black/50"></div>

    <aside id="sidebar" class="sidebar fixed top-0 left-0 w-72 h-full bg-white shadow-2xl z-50 flex flex-col">
        <div class="p-4 md:p-5 border-b flex justify-between items-center">
            <div class="flex items-center gap-2 md:gap-3">
                <img src="{{ asset('own_assets/logo/logo.png') }}" class="h-8 md:h-10 w-auto">
                <div>
                    <span class="text-base md:text-lg font-bold" style="color: #074b88;">Universitas Kopi</span>
                    <i>
                        <p class="text-xs text-gray-500 hidden md:block">#MAKEEVERYONEHAPPY</p>
                    </i>
                </div>
            </div>
            <button id="closeSidebarBtn"
                class="text-gray-500 text-xl md:text-2xl hover:text-[#074b88] transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-3">
            <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-50 transition"
                style="color: #074b88;"><i class="fas fa-home w-6" style="color: #074b88;"></i><span>Beranda</span></a>
            <a href="#" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-blue-50 transition"
                style="color: #074b88;"><i class="fas fa-history w-6"
                    style="color: #074b88;"></i><span>Pesanan</span></a>
        </nav>
        <div class="p-5 border-t bg-gray-50">
            <h3 class="font-semibold mb-3" style="color: #074b88;"><i class="fas fa-address-card mr-2"></i>Kontak Kami
            </h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p><i class="fab fa-whatsapp w-5 text-green-600"></i> +62 822-7344-8313</p>
                <p><i class="fas fa-map-marker-alt w-5 text-gray-600"></i> Jl. Prada, Peurada, Kec. Syiah Kuala, Kota
                    Banda Aceh</p>
            </div>
            <div class="flex mt-4 space-x-3">
                <a href="https://www.instagram.com/universitaskopi.id" target="_blank"><i
                        class="fab fa-instagram text-2xl text-pink-500"></i></a>
                <a href="" target="_blank"><i class="fab fa-facebook text-2xl text-blue-700"></i></a>
                <a href="https://www.tiktok.com/@universitaskopi.id?_r=1&_t=ZS-96CttSm8ef4" target="_blank"><i
                        class="fab fa-tiktok text-2xl text-black"></i></a>
            </div>
        </div>
    </aside>

    <main class="pb-28">
        <header class="bg-white shadow-sm sticky top-0 z-20">
            <div class="container mx-auto px-4 py-3 flex items-center gap-3">
                <button id="menuToggleBtn" class="text-2xl focus:outline-none" style="color: #074b88;">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('own_assets/logo/logo.png') }}" class="h-8 md:h-9 w-auto">
                    <div>
                        <span class="font-bold text-base md:text-lg" style="color: #074b88;">Universitas Kopi</span>
                        <i>
                            <p class="text-xs text-gray-500 hidden md:block">#MAKEEVERYONEHAPPY</p>
                        </i>
                    </div>
                </div>
            </div>
        </header>

        <div class="container mx-auto px-4 mt-4">
            <div class="w-full h-40 md:h-56 rounded-2xl overflow-hidden shadow-md">
                <img src="https://picsum.photos/id/106/1200/400" alt="Banner Makanan"
                    class="w-full h-full object-cover">
            </div>
        </div>

        <div class="container mx-auto px-4 mt-6">
            <div class="flex overflow-x-auto space-x-3 pb-2 no-scrollbar scroll-smooth" id="categoryTabs">
                <button
                    class="tab-category flex items-center gap-2 px-4 md:px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-700 font-medium whitespace-nowrap transition hover:shadow flex-shrink-0"
                    data-cat="0" style="border-color: #074b88;">
                    <img src="{{ asset('own_assets/icon/all_kategori.png') }}" class="w-5 h-5">
                    <span>Semua</span>
                </button>
                @foreach ($kategori as $kat)
                    <button
                        class="tab-category flex items-center gap-2 px-4 md:px-5 py-2 rounded-full border border-gray-300 bg-white text-gray-700 font-medium whitespace-nowrap transition hover:shadow flex-shrink-0"
                        data-cat="{{ $kat->id }}" style="border-color: #074b88;">

                        <img src="{{ asset('storage/' . $kat->icon) }}" class="w-5 h-5">

                        <span class="pr-1">{{ $kat->nama_kategori }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="container mx-auto px-4 mt-6">
            <h2 id="categoryTitle" class="text-xl font-bold text-gray-800 border-l-4 pl-3 mb-4"
                style="border-color: #074b88;">
                Makanan</h2>
            <div id="menuList" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

            </div>
        </div>
    </main>

    <!-- FLOATING CART BUTTON -->
    <div id="floatCartBtn" class="float-cart" style="background-color: #074b88;">
        <i class="fas fa-shopping-bag text-2xl text-white"></i>
        <span id="cartCountBadge" style="background-color: #063d6e;">0</span>
    </div>

    <!-- BOTTOM SHEET OVERLAY & SHEET -->
    <div id="sheetOverlay" class="sheet-overlay"></div>
    <div id="bottomSheet" class="bottom-sheet">
        <div class="sticky top-0 bg-white rounded-t-2xl border-b px-5 py-3 flex justify-between items-center">
            <h3 class="font-bold text-lg" style="color: #074b88;">Tambah Pesanan</h3>
            <button id="closeSheetBtn" class="text-gray-500 text-2xl"><i class="fas fa-times-circle"></i></button>
        </div>
        <div class="p-5">
            <div class="flex items-center gap-4 mb-4">
                <img id="sheetMenuImg" src="" alt="preview"
                    class="w-16 h-16 rounded-xl object-cover bg-gray-100">
                <div>
                    <h4 id="sheetMenuName" class="font-bold text-gray-800">Nama Menu</h4>
                    <p id="sheetMenuPrice" class="font-semibold" style="color: #074b88;">Rp 0</p>
                </div>
            </div>
            <div class="flex items-center justify-between bg-gray-100 rounded-full p-2 mb-6">
                <button id="qtyMinus"
                    class="w-10 h-10 rounded-full bg-white shadow text-xl font-bold text-gray-700">-</button>
                <span id="qtyValue" class="text-xl font-semibold w-12 text-center">1</span>
                <button id="qtyPlus"
                    class="w-10 h-10 rounded-full bg-white shadow text-xl font-bold text-gray-700">+</button>
            </div>
            <button id="addToCartBtn"
                class="w-full text-white font-bold py-3 rounded-2xl transition flex items-center justify-center gap-2"
                style="background-color: #074b88;">
                <i class="fas fa-cart-plus"></i>Tambahkan Pesanan
            </button>
        </div>
    </div>

    <!-- MODAL CART -->
    <!-- OVERLAY -->
    <div id="cartOverlay" class="fixed inset-0 bg-black/50 hidden z-40"></div>

    <!-- MODAL -->
    <div id="modalCart" class="fixed inset-0 flex items-center justify-center hidden z-50 px-4">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">

            <!-- HEADER -->
            <div class="flex justify-between items-center p-4 border-b">
                <h5 class="font-semibold text-lg flex items-center gap-2" style="color: #074b88;">
                    <i class="fas fa-bag-shopping"></i> Keranjang Belanja
                </h5>
                <button id="closeCartModal" class="text-gray-500 text-xl">&times;</button>
            </div>

            <!-- BODY -->
            <div class="p-4 max-h-[60vh] overflow-y-auto">
                <div id="cartList"></div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-between items-center p-4 border-t">
                <div class="text-sm font-medium">
                    Total: <span id="cartTotalFooter" style="color: #074b88;">Rp 0</span>
                </div>

                <div class="flex gap-2">
                    <button id="closeCartBtn" class="px-4 py-2 bg-gray-200 rounded-lg">
                        Tutup
                    </button>
                    <button id="checkoutBtn" class="px-4 py-2 text-white rounded-lg"
                        style="background-color: #074b88;">
                        Checkout
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">
        <div class="bg-white rounded-2xl p-6 w-80 text-center animate-scale">
            <div id="paymentIcon" class="text-5xl mb-3"></div>
            <h3 id="paymentTitle" class="text-xl font-bold mb-2" style="color: #074b88;"></h3>
            <p id="paymentDesc" class="text-gray-500 mb-4"></p>

            <button onclick="closePaymentModal()" class="w-full text-white py-2 rounded-xl"
                style="background-color: #074b88;">
                OK
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    </script>
    <script>
        function showPaymentPopup(type) {

            let icon = '';
            let title = '';
            let desc = '';

            if (type === 'success') {
                icon = '<i class="fas fa-circle-check text-green-500"></i>';
                title = 'Pembayaran Berhasil';
                desc = 'Pesanan kamu sedang diproses';
            }

            if (type === 'failed') {
                icon = '<i class="fas fa-circle-xmark text-red-500"></i>';
                title = 'Pembayaran Gagal';
                desc = 'Silakan coba lagi';
            }

            if (type === 'pending') {
                icon = '<i class="fas fa-clock text-yellow-500"></i>';
                title = 'Menunggu Pembayaran';
                desc = 'Selesaikan pembayaran kamu';
            }

            $('#paymentIcon').html(icon);
            $('#paymentTitle').text(title);
            $('#paymentDesc').text(desc);

            $('#paymentModal').removeClass('hidden').addClass('flex');
        }

        function closePaymentModal() {
            $('#paymentModal').addClass('hidden').removeClass('flex');
        }

        function startCheckingStatus(orderId) {

            if (checkInterval) clearInterval(checkInterval);

            checkInterval = setInterval(() => {

                $.get(`/order/status/${orderId}`, function(res) {

                    console.log('status:', res.status);

                    if (res.status === 'paid') {

                        clearInterval(checkInterval);
                        showStatusPopup('success', 'Pembayaran berhasil');

                        cart = [];
                        updateCart();

                        setTimeout(() => location.reload(), 1500);
                    }

                    if (res.status === 'failed' || res.status === 'deny' || res.status === 'cancelled') {

                        clearInterval(checkInterval);
                        showStatusPopup('error', 'Pembayaran gagal');
                    }

                });

            }, 2000); // ⏱ tiap 2 detik
        }

        $(document).ready(function() {

            let activeCategory = null;
            let cart = [];
            let selectedMenu = null;
            let qty = 1;
            let orderIdGlobal = null;
            let intervalCheck = null;

            let firstTab = $('.tab-category').first();

            if (firstTab.length) {
                activeCategory = firstTab.data('cat');
                firstTab.addClass('bg-[#074b88] text-white active').removeClass('bg-white text-gray-700');
                loadMenu(activeCategory);
                $('#categoryTitle').text(firstTab.text());
            }

            $(document).on('click', '.tab-category', function() {

                // Reset semua tab ke tampilan default
                $('.tab-category')
                    .removeClass('bg-theme-primary text-white')
                    .addClass('bg-white text-gray-700')
                    .css('border-color', '#074b88');

                // Aktifkan tab yang diklik
                $(this)
                    .addClass('bg-theme-primary text-white')
                    .removeClass('bg-white text-gray-700');

                // Simpan kategori aktif
                activeCategory = $(this).data('cat');

                // Update title kategori (ambil teks dari tab yang aktif)
                $('#categoryTitle').text($(this).text());

                // Load menu berdasarkan kategori
                loadMenu(activeCategory);
            });

            function loadMenu(kategori) {

                $('#menuList').html('<div class="text-center py-5">Loading...</div>');

                $.get('/daftar-menu/load-data', {
                    kategori: kategori
                }, function(res) {

                    if (!res.data.length) {
                        $('#menuList').html(
                            '<div class="text-center py-5 text-gray-400">Menu kosong</div>');
                        return;
                    }

                    let html = '';

                    res.data.forEach(menu => {

                        let img = '/storage/default.png';

                        if (menu.foto_menus && menu.foto_menus.length > 0) {
                            img = '/storage/' + menu.foto_menus[0].foto_path;
                        }

                        html += `
                            <div class="menu-item">

                                <div class="w-full img-wrapper" style="aspect-ratio:1/1;">

                                    <div class="img-skeleton"></div>

                                    <img src="${img}" 
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-contain menu-img"
                                        style="object-position:center bottom;"
                                        onload="this.classList.add('loaded');
                                                this.previousElementSibling.style.display='none';">

                                </div>

                                <div class="p-4 text-center">

                                    <h3 class="font-bold text-gray-800">
                                        ${menu.nama_menu}
                                    </h3>

                                    <p class="font-semibold mt-1" style="color:#074b88;">
                                        Rp ${formatRupiah(menu.harga)}
                                    </p>

                                    <button class="btn-add mt-3 w-full py-2 rounded-xl transition"
                                        style="background-color:#074b88;color:white;"
                                        data-menu='${JSON.stringify(menu)}'>

                                        Pesan +

                                    </button>

                                </div>

                            </div>
                        `;
                    });

                    $('#menuList').html(html);
                });
            }

            $(document).on('click', '.btn-add', function() {

                selectedMenu = $(this).data('menu');
                qty = 1;

                $('#qtyValue').text(qty);
                $('#sheetMenuName').text(selectedMenu.nama_menu);
                $('#sheetMenuPrice').text('Rp ' + formatRupiah(selectedMenu.harga));

                let img = '/storage/default.png';
                if (selectedMenu.foto_menus && selectedMenu.foto_menus.length > 0) {
                    img = '/storage/' + selectedMenu.foto_menus[0].foto_path;
                }

                $('#sheetMenuImg').attr('src', img);

                openBottomSheet();
            });

            function openBottomSheet() {
                $('#bottomSheet').addClass('open');
                $('#sheetOverlay').addClass('active');
                $('body').css('overflow', 'hidden');
            }

            function closeBottomSheet() {
                $('#bottomSheet').removeClass('open');
                $('#sheetOverlay').removeClass('active');
                $('body').css('overflow', '');
            }

            $('#closeSheetBtn, #sheetOverlay').click(closeBottomSheet);

            $('#qtyPlus').click(function() {
                qty++;
                $('#qtyValue').text(qty);
            });

            $('#qtyMinus').click(function() {
                if (qty > 1) {
                    qty--;
                    $('#qtyValue').text(qty);
                }
            });

            $('#addToCartBtn').click(function() {
                let exist = cart.find(i => i.id === selectedMenu.id);

                if (exist) {
                    exist.qty += qty;
                } else {
                    cart.push({
                        id: selectedMenu.id,
                        nama: selectedMenu.nama_menu,
                        harga: selectedMenu.harga,
                        qty: qty,
                        note: ''
                    });
                }

                updateCart();
                closeBottomSheet();
            });

            function updateCart() {

                let total = cart.reduce((sum, i) => sum + i.qty, 0);
                $('#cartCountBadge').text(total);
            }

            toastr.options = {
                "closeButton": false,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "timeOut": "2000",
                "extendedTimeOut": "1000"
            };

            // buka modal
            $('#floatCartBtn').click(function() {

                if (!cart.length) {
                    toastr.warning('Keranjang masih kosong');
                    return;
                }

                renderCart();

                $('#modalCart').removeClass('hidden');
                $('#cartOverlay').removeClass('hidden');
            });

            // close modal
            function closeCartModal() {
                $('#modalCart').addClass('hidden');
                $('#cartOverlay').addClass('hidden');
            }

            $('#closeCartModal, #closeCartBtn, #cartOverlay').click(function() {
                closeCartModal();
            });

            $('#menuToggleBtn').click(function() {
                $('#sidebar').addClass('open');
                $('#overlay').addClass('active');
            });

            $('#closeSidebarBtn, #overlay').click(function() {
                $('#sidebar').removeClass('open');
                $('#overlay').removeClass('active');
            });

            $(document).on('click', '.btn-remove', function() {
                let id = $(this).data('id');

                cart = cart.filter(i => i.id !== id);

                updateCartBadge();

                if (!cart.length) {
                    closeCartModal();
                    return;
                }

                renderCart();
            });

            function renderCart() {

                let html = '';
                let total = 0;

                cart.forEach(item => {

                    let subtotal = item.harga * item.qty;
                    total += subtotal;

                    html += `
                        <div class="border-b pb-4 mb-4">

                            <div class="flex justify-between items-start">

                                <div class="flex-1 pr-3">
                                    <h6 class="font-semibold text-gray-800">${item.nama}</h6>

                                    <p class="text-sm text-gray-500">
                                        Rp ${formatRupiah(item.harga)}
                                    </p>

                                    <input type="text"
                                        class="mt-2 w-full border rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 note-input"
                                        placeholder="Tambah catatan..."
                                        data-id="${item.id}"
                                        value="${item.note ?? ''}">
                                </div>

                                <div class="flex flex-col items-end gap-2">

                                    <!-- HARGA -->
                                    <span class="font-semibold" style="color: #074b88;">
                                        Rp ${formatRupiah(subtotal)}
                                    </span>

                                    <!-- QTY CONTROL -->
                                    <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-2 py-1">

                                        <button class="qty-minus text-lg px-2 hover:text-[#074b88] transition-colors" data-id="${item.id}" style="color: #4b5563;">-</button>

                                        <span class="w-6 text-center font-medium">${item.qty}</span>

                                        <button class="qty-plus text-lg px-2 hover:text-[#074b88] transition-colors" data-id="${item.id}" style="color: #4b5563;">+</button>

                                    </div>

                                    <!-- DELETE -->
                                    <button class="btn-remove text-red-500 text-sm hover:text-red-700 transition-colors" data-id="${item.id}">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>

                                </div>

                            </div>

                        </div>
                        `;
                });

                $('#cartList').html(html);
                $('#cartTotalFooter').text('Rp ' + formatRupiah(total));
            }

            function updateCartBadge() {
                let total = 0;

                cart.forEach(item => {
                    total += item.qty;
                });

                if (total < 0) {
                    total = 0;
                }

                $('#cartCountBadge').text(total);
                $('#cartCountBadge').show();
            }

            function formatRupiah(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $(document).on('click', '.qty-plus', function() {
                let id = $(this).data('id');

                let item = cart.find(i => i.id === id);
                item.qty++;

                renderCart();
                updateCartBadge();
            });

            $(document).on('click', '.qty-minus', function() {
                let id = $(this).data('id');

                let item = cart.find(i => i.id === id);

                if (item.qty > 1) {
                    item.qty--;
                }

                renderCart();
                updateCartBadge();
            });

            $(document).on('input', '.note-input', function() {
                let id = $(this).data('id');
                let value = $(this).val();

                let item = cart.find(i => i.id === id);
                item.note = value;
            });

            $('#checkoutBtn').click(function() {

                if (!cart.length) {
                    toastr.warning('Keranjang kosong');
                    return;
                }

                let items = cart.map(i => ({
                    id: i.id,
                    qty: i.qty,
                    note: i.note
                }));

                $.ajax({
                    url: '/checkout',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        items: items,
                        customer_table: '{{ request()->query('table') ?? '' }}'
                    },
                    success: function(res) {

                        orderIdGlobal = res.order_id;

                        snap.pay(res.snap_token, {
                            onSuccess: function() {
                                // tutup popup snap
                                snap.hide();
                                showPaymentPopup('success');

                                cart = [];
                                updateCartBadge();
                            },

                            onPending: function() {
                                startCheckingStatus(res.order_id);
                            },

                            onError: function() {
                                showPaymentPopup('failed');
                            },

                            onClose: function() {
                                console.log('popup ditutup');
                            }
                        });

                    },
                    error: function() {
                        toastr.error('Checkout gagal');
                    }
                });
            });

        });
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</body>

</html>
