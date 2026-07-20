<?php

session_start();
if (!empty($_SESSION['admin'])) {
    require '../../config/database.php';
    if (!empty($_GET['kategori'])) {
        $nama= htmlentities(htmlentities($_POST['kategori']));
        $tgl= date("j F Y, G:i");
        $data[] = $nama;
        $data[] = $tgl;
        $sql = 'INSERT INTO kategori (nama_kategori,tgl_input) VALUES(?,?)';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=kategori&&success=tambah-data"</script>';
    }

    if (!empty($_GET['barang'])) {
        $id = htmlentities($_POST['id']);
        $kategori = trim(htmlentities($_POST['kategori']));
        $nama = htmlentities($_POST['nama']);
        $merk = htmlentities($_POST['merk']);
        $beli = htmlentities($_POST['beli']);
        $jual = htmlentities($_POST['jual']);
        $satuan = htmlentities($_POST['satuan']);
        $stok = htmlentities($_POST['stok']);
        $tgl = htmlentities($_POST['tgl']);

        // Cari kategori berdasarkan ID atau nama. Jika belum ada, buat baru.
        $kategori_id = null;
        if (ctype_digit($kategori)) {
            $sql = 'SELECT id_kategori FROM kategori WHERE id_kategori = ? LIMIT 1';
            $row = $config->prepare($sql);
            $row->execute(array($kategori));
            $cek = $row->fetch();
            if ($cek) {
                $kategori_id = $cek['id_kategori'];
            }
        }

        if (empty($kategori_id)) {
            $sql = 'SELECT id_kategori FROM kategori WHERE nama_kategori = ? LIMIT 1';
            $row = $config->prepare($sql);
            $row->execute(array($kategori));
            $cek = $row->fetch();
            if ($cek) {
                $kategori_id = $cek['id_kategori'];
            }
        }

        if (empty($kategori_id)) {
            $tgl_kat = date("j F Y, G:i");
            $sql = 'INSERT INTO kategori (nama_kategori,tgl_input) VALUES(?,?)';
            $row = $config->prepare($sql);
            $row->execute(array($kategori, $tgl_kat));
            $kategori_id = $config->lastInsertId();
        }

        $deskripsi = trim(htmlentities($_POST['deskripsi']));
        $nama_file = null;

        if (!empty($_FILES['foto']['name'])) {
            $allowedTypes = [
                'image/png'   => 'png',
                'image/jpeg'  => 'jpg',
                'image/gif'   => 'gif',
                'image/jpg'   => 'jpeg',
                'image/webp'  => 'webp'
            ];
            $filepath = $_FILES['foto']['tmp_name'];
            if (file_exists($filepath)) {
                $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                $filetype = finfo_file($fileinfo, $filepath);
                if (in_array($filetype, array_keys($allowedTypes)) && $_FILES['foto']['error'] === 0 && round($_FILES['foto']['size'] / 1024) <= 4096) {
                    $dir = '../../assets/img/barang/';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $nama_file = time() . '_' . basename($_FILES['foto']['name']);
                    move_uploaded_file($filepath, $dir . $nama_file);
                }
            }
        }

        $data[] = $id;
        $data[] = $kategori_id;
        $data[] = $nama;
        $data[] = $merk;
        $data[] = $beli;
        $data[] = $jual;
        $data[] = $satuan;
        $data[] = $stok;
        $data[] = $tgl;
        $data[] = $nama_file;
        $data[] = $deskripsi;
        
        $sql = 'INSERT INTO barang (id_barang,id_kategori,nama_barang,merk,harga_beli,harga_jual,satuan_barang,stok,tgl_input,gambar,deskripsi) 
			    VALUES (?,?,?,?,?,?,?,?,?,?,?) ';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=barang&success=tambah-data"</script>';
    }
    
    if (!empty($_GET['jual'])) {
        $id = $_GET['id'];

        // get tabel barang id_barang
        $sql = 'SELECT * FROM barang WHERE id_barang = ?';
        $row = $config->prepare($sql);
        $row->execute(array($id));
        $hsl = $row->fetch();

        if ($hsl['stok'] > 0) {
            $kasir =  $_GET['id_kasir'];
            $jumlah = 1;
            
            // Check for active promotions
            $today = date('Y-m-d');
            $sql_promo = "SELECT * FROM promo WHERE status_promo = 1 AND tanggal_mulai <= ? AND tanggal_selesai >= ? AND (id_barang = ? OR id_barang IS NULL) ORDER BY id_barang DESC, nilai_promo DESC LIMIT 1";
            $row_promo = $config->prepare($sql_promo);
            $row_promo->execute([$today, $today, $id]);
            $promo = $row_promo->fetch();

            $harga_jual = $hsl['harga_jual'];
            if ($promo) {
                $harga_jual = $harga_jual - $promo['nilai_promo'];
                if ($harga_jual < 0) $harga_jual = 0;
            }

            $total = $harga_jual * $jumlah;
            $tgl = date("j F Y, G:i");

            $data1[] = $id;
            $data1[] = $kasir;
            $data1[] = $jumlah;
            $data1[] = $total;
            $data1[] = $tgl;

            $sql1 = 'INSERT INTO penjualan (id_barang,id_member,jumlah,total,tanggal_input) VALUES (?,?,?,?,?)';
            $row1 = $config -> prepare($sql1);
            $row1 -> execute($data1);

            echo '<script>window.location="../../dashboard.php?page=jual&success=tambah-data"</script>';
        } else {
            echo '<!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: "Stok Habis!",
                        text: "Stok barang anda telah habis.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(function() {
                        window.location = "../../dashboard.php?page=jual#keranjang";
                    });
                </script>
            </body>
            </html>';
        }
    }

    if (!empty($_GET['promo'])) {
        $nama_promo = htmlentities($_POST['nama_promo']);
        $tipe_promo = htmlentities($_POST['tipe_promo']);
        $nilai_promo = htmlentities($_POST['nilai_promo']);
        $tanggal_mulai = htmlentities($_POST['tanggal_mulai']);
        $tanggal_selesai = htmlentities($_POST['tanggal_selesai']);
        $id_barang = !empty($_POST['id_barang']) ? htmlentities($_POST['id_barang']) : null;
        $status_promo = isset($_POST['status_promo']) ? (int)$_POST['status_promo'] : 1;

        $data = [
            $nama_promo,
            $tipe_promo,
            $nilai_promo,
            $tanggal_mulai,
            $tanggal_selesai,
            $id_barang,
            $status_promo
        ];

        $sql = 'INSERT INTO promo (nama_promo, tipe_promo, nilai_promo, tanggal_mulai, tanggal_selesai, id_barang, status_promo, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../dashboard.php?page=promo&success=tambah-data"</script>';
    }
}
