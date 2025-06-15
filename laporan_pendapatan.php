<?php
session_start();

// Cek session admin
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); 
    exit;
}

include 'koneksi.php';

// Fungsi untuk mendapatkan pendapatan berdasarkan periode
function getPendapatan($koneksi, $periode) {
    $today = date('Y-m-d');
    $query = "";
    
    switch($periode) {
        case 'today':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE DATE(order_date) = '$today' 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'yesterday':
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE DATE(order_date) = '$yesterday' 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'week':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE YEARWEEK(order_date) = YEARWEEK(NOW()) 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'last_week':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE YEARWEEK(order_date) = YEARWEEK(NOW()) - 1 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'month':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE MONTH(order_date) = MONTH(NOW()) 
                     AND YEAR(order_date) = YEAR(NOW()) 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'last_month':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE MONTH(order_date) = MONTH(NOW()) - 1 
                     AND YEAR(order_date) = YEAR(NOW()) 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'year':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE YEAR(order_date) = YEAR(NOW()) 
                     AND status IN ('shipped', 'delivered')";
            break;
            
        case 'total':
            $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM orders 
                     WHERE status IN ('shipped', 'delivered')";
            break;
    }
    
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// Hitung semua pendapatan
$pendapatan_hari_ini = getPendapatan($koneksi, 'today');
$pendapatan_kemarin = getPendapatan($koneksi, 'yesterday');
$pendapatan_minggu_ini = getPendapatan($koneksi, 'week');
$pendapatan_minggu_lalu = getPendapatan($koneksi, 'last_week');
$pendapatan_bulan_ini = getPendapatan($koneksi, 'month');
$pendapatan_bulan_lalu = getPendapatan($koneksi, 'last_month');
$pendapatan_tahun_ini = getPendapatan($koneksi, 'year');
$total_pendapatan = getPendapatan($koneksi, 'total');

// Hitung persentase perubahan
function hitungPersentase($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return round((($current - $previous) / $previous) * 100, 1);
}

$perubahan_harian = hitungPersentase($pendapatan_hari_ini, $pendapatan_kemarin);
$perubahan_mingguan = hitungPersentase($pendapatan_minggu_ini, $pendapatan_minggu_lalu);
$perubahan_bulanan = hitungPersentase($pendapatan_bulan_ini, $pendapatan_bulan_lalu);

// Data untuk chart - 7 hari terakhir
$chart_7_hari = [];
$labels_7_hari = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
              FROM orders 
              WHERE DATE(order_date) = '$date' 
              AND status IN ('shipped', 'delivered')";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    $chart_7_hari[] = $row['total'] / 1000000; // Konversi ke jutaan
    $labels_7_hari[] = date('d M', strtotime($date));
}

// Data untuk chart - 4 minggu terakhir
$chart_30_hari = [];
$labels_30_hari = [];
for ($i = 3; $i >= 0; $i--) {
    $start_date = date('Y-m-d', strtotime("-" . (($i + 1) * 7) . " days"));
    $end_date = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
    
    $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
              FROM orders 
              WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date' 
              AND status IN ('shipped', 'delivered')";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    $chart_30_hari[] = $row['total'] / 1000000;
    $labels_30_hari[] = 'Minggu ' . (4 - $i);
}

// Data untuk chart - 3 bulan terakhir
$chart_3_bulan = [];
$labels_3_bulan = [];
for ($i = 2; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
              FROM orders 
              WHERE DATE_FORMAT(order_date, '%Y-%m') = '$month' 
              AND status IN ('shipped', 'delivered')";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    
    $chart_3_bulan[] = $row['total'] / 1000000;
    $labels_3_bulan[] = date('M Y', strtotime($month . '-01'));
}

// Transaksi terbaru
$query_transaksi = "SELECT o.order_id, o.order_date, o.total_amount, o.status, u.username
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    WHERE o.status IN ('shipped', 'delivered')
                    ORDER BY o.order_date DESC
                    LIMIT 10";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Pendapatan - Admin Panel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

       .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 250px;
      background-color: #3e2723;
      color: white;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar h2 {
      color: white !important;
      margin-bottom: 30px;
    }

    .nav-link {
      color: white;
      text-decoration: none;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 8px;
      display: block;
      transition: all 0.3s ease;
    }

    .nav-link:hover, .nav-link.active {
      background-color: #5d4037;
    }

    .profile {
      text-align: center;
      margin-top: 30px;
      font-size: 14px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .main-content {
      margin-left: 280px;
      padding: 30px;
    }
        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            color: #333;
            font-weight: 700;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #8b4513;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 14px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-change.positive {
            color: #10b981;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Revenue Chart Section */
        .chart-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .chart-filters {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #8b4513;
            color: white;
            border-color: #8b4513;
        }

        .chart-container {
            height: 350px;
            position: relative;
        }

        /* Recent Transactions */
        .transactions-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .transactions-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            color: #666;
        }

        .amount-positive {
            color: #10b981;
            font-weight: 600;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
                transition: transform 0.3s ease;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
     <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1>💰 Total Pendapatan</h1>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pendapatan Hari Ini</span>
                    <div class="stat-icon">💰</div>
                </div>
                <div class="stat-value">Rp<?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></div>
                <div class="stat-change <?= $perubahan_harian >= 0 ? 'positive' : 'negative' ?>">
                    <?= $perubahan_harian >= 0 ? '↗' : '↘' ?> <?= abs($perubahan_harian) ?>% dari kemarin
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pendapatan Minggu Ini</span>
                    <div class="stat-icon">📊</div>
                </div>
                <div class="stat-value">Rp<?= number_format($pendapatan_minggu_ini, 0, ',', '.') ?></div>
                <div class="stat-change <?= $perubahan_mingguan >= 0 ? 'positive' : 'negative' ?>">
                    <?= $perubahan_mingguan >= 0 ? '↗' : '↘' ?> <?= abs($perubahan_mingguan) ?>% dari minggu lalu
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pendapatan Bulan Ini</span>
                    <div class="stat-icon">📈</div>
                </div>
                <div class="stat-value">Rp<?= number_format($pendapatan_bulan_ini, 0, ',', '.') ?></div>
                <div class="stat-change <?= $perubahan_bulanan >= 0 ? 'positive' : 'negative' ?>">
                    <?= $perubahan_bulanan >= 0 ? '↗' : '↘' ?> <?= abs($perubahan_bulanan) ?>% dari bulan lalu
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Pendapatan</span>
                    <div class="stat-icon">💎</div>
                </div>
                <div class="stat-value">Rp<?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                <div class="stat-change positive">
                    📊 Total keseluruhan
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="chart-section">
            <div class="chart-header">
                <h2 class="chart-title">📈 Grafik Pendapatan</h2>
                <div class="chart-filters">
                    <button class="filter-btn active" onclick="updateChart('7_hari')">7 Hari</button>
                    <button class="filter-btn" onclick="updateChart('30_hari')">30 Hari</button>
                    <button class="filter-btn" onclick="updateChart('3_bulan')">3 Bulan</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="transactions-section">
            <h2 class="section-title">💳 Transaksi Terbaru</h2>
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                            <tr>
                                <td>#<?= $row['order_id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= date('d M Y H:i', strtotime($row['order_date'])) ?></td>
                                <td class="amount-positive">Rp<?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="status-badge status-success">
                                        <?= strtoupper($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">
                                Belum ada transaksi yang selesai
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Data pendapatan dari PHP
        const revenueData = {
            '7_hari': {
                labels: <?= json_encode($labels_7_hari) ?>,
                data: <?= json_encode($chart_7_hari) ?>
            },
            '30_hari': {
                labels: <?= json_encode($labels_30_hari) ?>,
                data: <?= json_encode($chart_30_hari) ?>
            },
            '3_bulan': {
                labels: <?= json_encode($labels_3_bulan) ?>,
                data: <?= json_encode($chart_3_bulan) ?>
            }
        };

        let currentChart = null;
        let currentPeriod = '7_hari';

        // Inisialisasi Chart
        function initChart() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const data = revenueData[currentPeriod];
            
            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Pendapatan (Juta Rp)',
                        data: data.data,
                        borderColor: '#8b4513',
                        backgroundColor: 'rgba(139, 69, 19, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8b4513',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#8b4513',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#333',
                                font: {
                                    size: 14,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#8b4513',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: Rp ' + (context.parsed.y * 1000000).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return 'Rp ' + value + 'Jt';
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Update chart berdasarkan periode
        function updateChart(period) {
            currentPeriod = period;
            
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update chart data
            const data = revenueData[period];
            currentChart.data.labels = data.labels;
            currentChart.data.datasets[0].data = data.data;
            currentChart.update('active');
        }

        // Initialize chart when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });

        // Responsive chart resize
        window.addEventListener('resize', function() {
            if (currentChart) {
                currentChart.resize();
            }
        });
    </script>
</body>
</html>
