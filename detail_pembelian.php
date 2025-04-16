<?php
include '../koneksi.php';
include 'sidebar.php';
$PelangganID = $_GET['PelangganID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Detail Pembelian</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
	<style>
		.content {
			margin-left: 250px;
			padding: 2rem;
		}
	</style>
</head>
<body>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">

                <?php 
                $query = mysqli_query($koneksi,"SELECT * FROM pelanggan 
                    INNER JOIN penjualan ON pelanggan.PelangganID=penjualan.PelangganID 
                    WHERE pelanggan.PelangganID='$PelangganID'");
                $d = mysqli_fetch_array($query);
                ?>

                <h4 class="mb-4">Detail Pembelian Pelanggan</h4>

                <!-- Info Pelanggan -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr><td><strong>ID Pelanggan</strong></td><td><?= $d['PelangganID']; ?></td></tr>
                        <tr><td><strong>Nama Pelanggan</strong></td><td><?= $d['NamaPelanggan']; ?></td></tr>
                        <tr><td><strong>No. Telepon</strong></td><td><?= $d['NomorTelepon']; ?></td></tr>
                        <tr><td><strong>Alamat</strong></td><td><?= $d['Alamat']; ?></td></tr>
                        <tr><td><strong>Total Pembelian</strong></td><td>Rp. <?= number_format($d['TotalHarga']); ?></td></tr>
                    </table>
                </div>

                <!-- Form Tambah Barang -->
                <form method="post" action="tambah_detail_penjualan.php" class="row g-3 align-items-end mb-4">
                    <input type="hidden" name="PenjualanID" value="<?= $d['PenjualanID']; ?>">
                    <input type="hidden" name="PelangganID" value="<?= $d['PelangganID']; ?>">

                    <div class="col-md-6">
                        <label class="form-label">Scan / Masukkan Kode Produk</label>
                        <input type="text" name="ProdukID" class="form-control" placeholder="Masukkan ID Produk atau scan barcode" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="JumlahProduk" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Tambah Barang</button>
                    </div>
                </form>

                <!-- Tabel Daftar Produk -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Jumlah Beli</th>
                                <th>Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $detail = mysqli_query($koneksi, "
                                SELECT dp.*, p.NamaProduk 
                                FROM detailpenjualan dp 
                                JOIN produk p ON dp.ProdukID = p.ProdukID 
                                WHERE dp.PenjualanID = '$d[PenjualanID]'
                            ");
                            while($item = mysqli_fetch_array($detail)){ ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $item['NamaProduk']; ?></td>
                                <td><?= $item['JumlahProduk']; ?></td>
                                <td>Rp. <?= number_format($item['Subtotal']); ?></td>
                                <td class="text-center">
                                    <form method="post" action="hapus_detail_pembelian.php" class="d-inline">
                                        <input type="hidden" name="DetailID" value="<?= $item['DetailID']; ?>">
                                        <input type="hidden" name="PelangganID" value="<?= $d['PelangganID']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Form Simpan Total Harga -->
                <form method="post" action="simpan_total_harga.php" class="row g-3 mt-4">
                    <?php 
                    $total = mysqli_query($koneksi, "SELECT SUM(Subtotal) AS TotalHarga FROM detailpenjualan WHERE PenjualanID='$d[PenjualanID]'");
                    $sum = mysqli_fetch_assoc($total)['TotalHarga'];
                    ?>
                    <div class="col-md-10">
                        <label class="form-label">Total Harga</label>
                        <input type="text" class="form-control" name="TotalHarga" value="<?= $sum ?>" readonly>
                        <input type="hidden" name="PenjualanID" value="<?= $d['PenjualanID']; ?>">
                        <input type="hidden" name="PelangganID" value="<?= $d['PelangganID']; ?>">
                    </div>
                    <div class="col-md-2 d-grid gap-2">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="cetak.php?PelangganID=<?= $d['PelangganID']; ?>" target="_blank" class="btn btn-secondary">Cetak</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
