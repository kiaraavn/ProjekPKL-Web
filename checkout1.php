<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error_message = null;

// Ambil data user untuk pre-fill form
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($user_query);

$data_checkout = [];
$subtotal = 0;

// Proses checkout langsung dari detail produk
if (isset($_POST['beli_langsung']) && $_POST['beli_langsung'] == '1') {
    $id_produk = intval($_POST['id_produk']);
    $quantity = intval($_POST['quantity']);
    $nama_produk = $_POST['nama_produk'];
    $price = floatval($_POST['price']);
    $image_url = $_POST['image_url'];
    
    // Siapkan data untuk ditampilkan
    $data_checkout[] = [
        'id_produk' => $id_produk,
        'nama_produk' => $nama_produk,
        'price' => $price,
        'quantity' => $quantity,
        'image_url' => $image_url
    ];
    
    $subtotal = $price * $quantity;
    
    // Simpan ke session untuk proses selanjutnya
    $_SESSION['checkout_data'] = $data_checkout;
    $_SESSION['checkout_subtotal'] = $subtotal;
} elseif (isset($_SESSION['checkout_data']) && isset($_SESSION['checkout_subtotal'])) {
    // Ambil data dari session jika sudah ada
    $data_checkout = $_SESSION['checkout_data'];
    $subtotal = $_SESSION['checkout_subtotal'];
} else {
    // Jika tidak ada data yang valid, redirect ke shop
    header("Location: shop1.php");
    exit;
}

// Proses form checkout saat disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_checkout'])) {
    // Validasi input
    if (empty($_POST['nama_penerima']) || empty($_POST['telepon_penerima']) || 
        empty($_POST['kabupaten']) || empty($_POST['alamat_detail'])) {
        $error_message = "Semua field harus diisi";
    } else {
        // Ambil data dari session
        $checkout_data = $_SESSION['checkout_data'];
        $subtotal = $_SESSION['checkout_subtotal'];
        
        // Hitung ongkir
        $kabupaten = $_POST['kabupaten'];
        $shipping_method = $_POST['shipping_method'];
        
        $ongkir_rates = [
            'Purbalingga' => 15000,
            'Banyumas' => 18000,
            'Wonosobo' => 20000,
            'Sleman' => 25000
        ];
        
        $ongkir = isset($ongkir_rates[$kabupaten]) ? $ongkir_rates[$kabupaten] : 20000;
        if ($shipping_method === 'express') {
            $ongkir += 10000;
        }
        
        $total_amount = $subtotal + $ongkir;
        
        // Buat order baru - sesuai struktur tabel orders
        $order_date = date('Y-m-d H:i:s');
        $status = 'pending';
        
        // Insert ke tabel orders (hanya kolom yang ada)
        $query = "INSERT INTO orders (user_id, order_date, status, total_amount) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("issd", $user_id, $order_date, $status, $total_amount);
        
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            
            // Insert order items
            foreach ($checkout_data as $item) {
                $query = "INSERT INTO order_item (order_id, id_produk, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $koneksi->prepare($query);
                $stmt->bind_param("iiid", $order_id, $item['id_produk'], $item['quantity'], $item['price']);
                $stmt->execute();
            }
            
            // Insert ke tabel shipments
            $shipping_address = $_POST['alamat_detail'] . ', ' . $_POST['kabupaten'];
            $shipment_status = 'pending';
            
            $query = "INSERT INTO shipments (order_id, shipping_addres, shipping_method, shipment_status) VALUES (?, ?, ?, ?)";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("isss", $order_id, $shipping_address, $shipping_method, $shipment_status);
            $stmt->execute();
            
            // Simpan data penerima ke session atau tabel terpisah (opsional)
            $_SESSION['recipient_data'] = [
                'order_id' => $order_id,
                'nama_penerima' => $_POST['nama_penerima'],
                'telepon_penerima' => $_POST['telepon_penerima']
            ];
            
            // Clear session data
            unset($_SESSION['checkout_data']);
            unset($_SESSION['checkout_subtotal']);

            // Simpan order_id ke session untuk halaman pembayaran
            $_SESSION['order_id'] = $order_id;

            // Redirect ke halaman pembayaran
            header("Location: pembayaran.php?order_id=" . $order_id);
            exit;
        } else {
            $error_message = "Gagal membuat pesanan: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .checkout-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }

        .shipping-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .shipping-option {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .shipping-option:hover {
            border-color: #007bff;
        }

        .shipping-option.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .shipping-option input[type="radio"] {
            margin-right: 10px;
        }

        .product-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-quantity {
            color: #666;
            font-size: 14px;
        }

        .product-price {
            font-weight: 600;
            color: #007bff;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
        }

        .summary-row.total {
            border-top: 2px solid #f0f0f0;
            margin-top: 15px;
            padding-top: 15px;
            font-weight: 700;
            font-size: 18px;
            color: #333;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .ongkir-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 14px;
        }

        .ongkir-info h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }

        .ongkir-list {
            list-style: none;
            padding: 0;
        }

        .ongkir-list li {
            padding: 5px 0;
            border-bottom: 1px solid #e3f2fd;
        }

        .ongkir-list li:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }
            
            .shipping-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; color: #333; margin-bottom: 30px;">Checkout</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="checkoutForm">
            <div class="checkout-wrapper">
                <!-- Left Side - Shipping & Products -->
                <div>
                    <!-- Alamat Pengiriman -->
                    <div class="card">
                        <h3>📍 Alamat Pengiriman</h3>
                        
                        <div class="form-group">
                            <label for="nama_penerima">Nama Penerima</label>
                            <input type="text" id="nama_penerima" name="nama_penerima" class="form-control" 
                                   value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="telepon_penerima">Nomor Telepon</label>
                            <input type="tel" id="telepon_penerima" name="telepon_penerima" class="form-control" 
                                   value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kabupaten">Kabupaten</label>
                            <select id="kabupaten" name="kabupaten" class="form-control" required onchange="updateOngkir()">
                                <option value="">Pilih Kabupaten</option>
                                <option value="Purbalingga">Kabupaten Purbalingga</option>
                                <option value="Banyumas">Kabupaten Banyumas</option>
                                <option value="Wonosobo">Kabupaten Wonosobo</option>
                                <option value="Sleman">Kabupaten Sleman</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="alamat_detail">Alamat Lengkap</label>
                            <textarea id="alamat_detail" name="alamat_detail" class="form-control" 
                                      rows="3" placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Metode Pengiriman</label>
                            <div class="shipping-options">
                                <div class="shipping-option selected" onclick="selectShipping('standard')">
                                    <input type="radio" name="shipping_method" value="standard" checked>
                                    <div>
                                        <strong>Standard</strong><br>
                                        <small>3-5 hari kerja</small><br>
                                        <span class="product-price">+Rp0</span>
                                    </div>
                                </div>
                                <div class="shipping-option" onclick="selectShipping('express')">
                                    <input type="radio" name="shipping_method" value="express">
                                    <div>
                                        <strong>Express</strong><br>
                                        <small>1-2 hari kerja</small><br>
                                        <span class="product-price">+Rp10.000</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Ongkir -->
                        <div class="ongkir-info">
                            <h4>📦 Tarif Ongkos Kirim</h4>
                            <ul class="ongkir-list">
                                <li><strong>Purbalingga:</strong> Rp15.000</li>
                                <li><strong>Banyumas:</strong> Rp18.000</li>
                                <li><strong>Wonosobo:</strong> Rp20.000</li>
                                <li><strong>Sleman:</strong> Rp25.000</li>
                            </ul>
                            <small><em>*Express +Rp10.000 dari tarif standard</em></small>
                        </div>
                    </div>

                    <!-- Produk yang Dibeli -->
                    <div class="card">
                        <h3>🛍️ Produk yang Dibeli</h3>
                        <?php foreach($data_checkout as $d): ?>
                            <div class="product-item">
                                <img src="<?= htmlspecialchars($d['image_url']) ?>" alt="<?= htmlspecialchars($d['nama_produk']) ?>" class="product-image">
                                <div class="product-info">
                                    <div class="product-name"><?= htmlspecialchars($d['nama_produk']) ?></div>
                                    <div class="product-quantity">Jumlah: <?= intval($d['quantity']) ?></div>
                                </div>
                                <div class="product-price">
                                    Rp<?= number_format(intval($d['price']) * intval($d['quantity']), 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right Side - Summary -->
                <div>
                    <div class="card">
                        <h3>💰 Ringkasan Pembayaran</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span id="shipping-cost">Pilih kabupaten dulu</span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="total-amount">Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>

                        <button type="submit" name="do_checkout" class="btn">
                            🛒 Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const subtotal = <?= $subtotal ?>;
        const ongkirRates = {
            'Purbalingga': 15000,
            'Banyumas': 18000,
            'Wonosobo': 20000,
            'Sleman': 25000
        };
        
        function selectShipping(method) {
            document.querySelector(`input[value="${method}"]`).checked = true;
            
            document.querySelectorAll('.shipping-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            updateOngkir();
        }

        function updateOngkir() {
            const kabupaten = document.getElementById('kabupaten').value;
            const shippingMethod = document.querySelector('input[name="shipping_method"]:checked').value;
            
            if (!kabupaten) {
                document.getElementById('shipping-cost').textContent = 'Pilih kabupaten dulu';
                document.getElementById('total-amount').textContent = 'Rp' + subtotal.toLocaleString('id-ID');
                return;
            }
            
            let ongkir = ongkirRates[kabupaten] || 20000;
            if (shippingMethod === 'express') {
                ongkir += 10000;
            }
            
            const total = subtotal + ongkir;
            
            document.getElementById('shipping-cost').textContent = 'Rp' + ongkir.toLocaleString('id-ID');
            document.getElementById('total-amount').textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const kabupaten = document.getElementById('kabupaten').value;
            if (!kabupaten) {
                e.preventDefault();
                alert('Mohon pilih kabupaten terlebih dahulu!');
                return false;
            }
            
            const requiredFields = ['nama_penerima', 'telepon_penerima', 'alamat_detail'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan!');
            }
        });
    </script>
</body>
</html>
