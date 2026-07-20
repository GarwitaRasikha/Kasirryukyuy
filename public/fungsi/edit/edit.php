<?php
session_start();
if (!empty($_SESSION['admin'])) {
    require '../../config/database.php';
    if (!empty($_GET['pengaturan'])) {
        $nama= htmlentities($_POST['namatoko']);
        $alamat = htmlentities($_POST['alamat']);
        $kontak = htmlentities($_POST['kontak']);
        $pemilik = htmlentities($_POST['pemilik']);
        $id = '1';

        $data[] = $nama;
        $data[] = $alamat;
        $data[] = $kontak;
        $data[] = $pemilik;
        $data[] = $id;
        $sql = 'UPDATE toko SET nama_toko=?, alamat_toko=?, tlp=?, nama_pemilik=? WHERE id_toko = ?';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=pengaturan&success=edit-data"</script>';
    }

    if (!empty($_GET['kategori'])) {
        $nama= htmlentities($_POST['kategori']);
        $id= htmlentities($_POST['id']);
        $data[] = $nama;
        $data[] = $id;
        $sql = 'UPDATE kategori SET  nama_kategori=? WHERE id_kategori=?';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=kategori&uid='.$id.'&success-edit=edit-data"</script>';
    }

    if (!empty($_GET['stok'])) {
        $restok = htmlentities($_POST['restok']);
        $id = htmlentities($_POST['id']);
        $dataS[] = $id;
        $sqlS = 'select*from barang WHERE id_barang=?';
        $rowS = $config -> prepare($sqlS);
        $rowS -> execute($dataS);
        $hasil = $rowS -> fetch();

        $stok = $restok + $hasil['stok'];

        $data[] = $stok;
        $data[] = $id;
        $sql = 'UPDATE barang SET stok=? WHERE id_barang=?';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=barang&success-stok=stok-data"</script>';
    }

    if (!empty($_GET['barang'])) {
        $id = htmlentities($_POST['id']);
        $kategori = htmlentities($_POST['kategori']);
        $nama = htmlentities($_POST['nama']);
        $merk = htmlentities($_POST['merk']);
        $beli = htmlentities($_POST['beli']);
        $jual = htmlentities($_POST['jual']);
        $satuan = htmlentities($_POST['satuan']);
        $stok = htmlentities($_POST['stok']);
        $tgl = htmlentities($_POST['tgl']);
        $deskripsi = trim(htmlentities($_POST['deskripsi']));
        $foto_lama = !empty($_POST['foto_lama']) ? $_POST['foto_lama'] : null;
        $nama_file = $foto_lama;

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
                    if (move_uploaded_file($filepath, $dir . $nama_file)) {
                        if ($foto_lama && file_exists($dir . $foto_lama)) {
                            unlink($dir . $foto_lama);
                        }
                    }
                }
            }
        }

        $data[] = $kategori;
        $data[] = $nama;
        $data[] = $merk;
        $data[] = $beli;
        $data[] = $jual;
        $data[] = $satuan;
        $data[] = $stok;
        $data[] = $tgl;
        $data[] = $nama_file;
        $data[] = $deskripsi;
        $data[] = $id;
        
        $sql = 'UPDATE barang SET id_kategori=?, nama_barang=?, merk=?, 
				harga_beli=?, harga_jual=?, satuan_barang=?, stok=?, tgl_update=?, gambar=?, deskripsi=? WHERE id_barang=?';
        $row = $config -> prepare($sql);
        $row -> execute($data);
        echo '<script>window.location="../../dashboard.php?page=barang/edit&barang='.$id.'&success=edit-data"</script>';
    }

    if (!empty($_GET['gambar'])) {
        $id = htmlentities($_POST['id']);
        set_time_limit(0);
        $allowedImageType = array("image/gif", "image/JPG", "image/jpeg", "image/pjpeg", "image/png", "image/x-png", 'image/webp');
        $filepath = $_FILES['foto']['tmp_name'];
        $fileSize = filesize($filepath);
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($fileinfo, $filepath);
        $allowedTypes = [
            'image/png'   => 'png',
            'image/jpeg'  => 'jpg',
            'image/gif'   => 'gif',
            'image/jpg'   => 'jpeg',
            'image/webp'  => 'webp'
        ];
        if(!in_array($filetype, array_keys($allowedTypes))) {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Upload", text: "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
            exit;
        }else if ($_FILES['foto']["error"] > 0) {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Upload", text: "Terjadi kesalahan saat upload.", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
            exit;
        } elseif (!in_array($_FILES['foto']["type"], $allowedImageType)) {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Upload", text: "Format file tidak didukung. Gunakan JPG, PNG, atau GIF.", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
            exit;
        } elseif (round($_FILES['foto']["size"] / 1024) > 4096) {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Upload", text: "Ukuran gambar terlalu besar (Max 4MB).", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
            exit;
        } else {
            $dir = '../../assets/img/user/';
            $tmp_name = $_FILES['foto']['tmp_name'];
            $name = time().basename($_FILES['foto']['name']);
            if (move_uploaded_file($tmp_name, $dir.$name)) {
                //post foto lama
                $foto2 = $_POST['foto2'];
                //remove foto di direktori
                unlink('../../assets/img/user/'.$foto2.'');
                //input foto
                $id = $_POST['id'];
                $data[] = $name;
                $data[] = $id;
                $sql = 'UPDATE member SET gambar=?  WHERE member.id_member=?';
                $row = $config -> prepare($sql);
                $row -> execute($data);
                echo '<script>window.location="../../dashboard.php?page=user&success=edit-data"</script>';
            } else {
                echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Upload", text: "Gagal memindahkan file gambar.", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
                exit;
            }
        }
    }

    if (!empty($_GET['profil'])) {
        $id = htmlentities($_POST['id']);
        $nama = htmlentities($_POST['nama']);
        $user = htmlentities($_POST['user']);
        $alamat = htmlentities($_POST['alamat']);
        $tlp = htmlentities($_POST['tlp']);
        $email = htmlentities($_POST['email']);
        $nik = htmlentities($_POST['nik']);

        $data[] = $nama;
        $data[] = $alamat;
        $data[] = $tlp;
        $data[] = $email;
        $data[] = $nik;
        $data[] = $id;
        $sql = 'UPDATE member SET nm_member=?,alamat_member=?,telepon=?,email=?,NIK=? WHERE id_member=?';
        $row = $config -> prepare($sql);
        $row -> execute($data);

        // Update username in login table
        $data_login[] = $user;
        $data_login[] = $id;
        $sql_login = 'UPDATE login SET user=? WHERE id_member=?';
        $row_login = $config -> prepare($sql_login);
        $row_login -> execute($data_login);

        echo '<script>window.location="../../dashboard.php?page=user&success=edit-data"</script>';
    }
    
    if (!empty($_GET['pass'])) {
        $id = htmlentities($_POST['id']);
        $pass_lama = htmlentities($_POST['pass_lama']);
        $pass_baru = htmlentities($_POST['pass_baru']);

        // Cek password lama
        $sql_cek = 'SELECT * FROM login WHERE id_member=? AND pass=md5(?)';
        $row_cek = $config -> prepare($sql_cek);
        $row_cek -> execute(array($id, $pass_lama));
        $count = $row_cek -> rowCount();

        if($count > 0){
            $data[] = $pass_baru;
            $data[] = $id;
            $sql = 'UPDATE login SET pass=md5(?) WHERE id_member=?';
            $row = $config -> prepare($sql);
            $row -> execute($data);
            echo '<script>window.location="../../dashboard.php?page=user&success=edit-data"</script>';
        } else {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Gagal Ganti Password", text: "Password lama salah.", icon: "error"}).then(function(){window.location="../../dashboard.php?page=user"});</script></body></html>';
        }
    }

    if (!empty($_GET['jual'])) {
        $id = htmlentities($_POST['id']);
        $id_barang = htmlentities($_POST['id_barang']);
        $jumlah = htmlentities($_POST['jumlah']);

        $sql_tampil = "select *from barang where barang.id_barang=?";
        $row_tampil = $config -> prepare($sql_tampil);
        $row_tampil -> execute(array($id_barang));
        $hasil = $row_tampil -> fetch();

        if ($hasil['stok'] > $jumlah) {
            // Check for active promotions
            $today = date('Y-m-d');
            $sql_promo = "SELECT * FROM promo WHERE status_promo = 1 AND tanggal_mulai <= ? AND tanggal_selesai >= ? AND (id_barang = ? OR id_barang IS NULL) ORDER BY id_barang DESC, nilai_promo DESC LIMIT 1";
            $row_promo = $config->prepare($sql_promo);
            $row_promo->execute([$today, $today, $id_barang]);
            $promo = $row_promo->fetch();

            $harga_jual = $hasil['harga_jual'];
            if ($promo) {
                $harga_jual = $harga_jual - $promo['nilai_promo'];
                if ($harga_jual < 0) $harga_jual = 0;
            }

            $total = $harga_jual * $jumlah;
            $data1[] = $jumlah;
            $data1[] = $total;
            $data1[] = $id;
            $sql1 = 'UPDATE penjualan SET jumlah=?,total=? WHERE id_penjualan=?';
            $row1 = $config -> prepare($sql1);
            $row1 -> execute($data1);
            echo '<script>window.location="../../dashboard.php?page=jual#keranjang"</script>';
        } else {
            echo '<!DOCTYPE html><html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body><script>Swal.fire({title: "Stok Tidak Cukup", text: "Jumlah di keranjang melebihi stok yang tersedia.", icon: "warning"}).then(function(){window.location="../../dashboard.php?page=jual#keranjang"});</script></body></html>';
        }
    }

    if (!empty($_GET['cari_barang'])) {
        $cari = trim(strip_tags($_POST['keyword']));
        
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori";
        
        if ($cari != '') {
            $sql .= " where barang.id_barang like '%$cari%' or barang.nama_barang like '%$cari%' or barang.merk like '%$cari%'";
        }
        
        $row = $config -> prepare($sql);
        $row -> execute();
        $hasil1= $row -> fetchAll();
        ?>
		<table class="table table-stripped align-middle" width="100%" id="example2">
			<tr>
				<th>ID Barang</th>
				<th>Nama Barang</th>
				<th>Merk</th>
				<th>Harga Jual</th>
				<th>Aksi</th>
			</tr>
		<?php foreach ($hasil1 as $hasil) {
            $today = date('Y-m-d');
            $sql_promo = "SELECT * FROM promo WHERE status_promo = 1 AND tanggal_mulai <= ? AND tanggal_selesai >= ? AND (id_barang = ? OR id_barang IS NULL) ORDER BY id_barang DESC, nilai_promo DESC LIMIT 1";
            $row_promo = $config->prepare($sql_promo);
            $row_promo->execute([$today, $today, $hasil['id_barang']]);
            $promo = $row_promo->fetch();

            $harga_final = $hasil['harga_jual'];
            $promo_badge = '';
            if ($promo) {
                $harga_final = $hasil['harga_jual'] - $promo['nilai_promo'];
                $promo_badge = '<span class="badge bg-danger rounded-pill px-2 py-1 ms-2" style="font-size: 0.65rem;">Pot. Rp' . number_format($promo['nilai_promo'], 0, ',', '.') . '</span>';
                if ($harga_final < 0) $harga_final = 0;
            }
        ?>
			<tr>
				<td class="fw-bold" style="color: #c084fc;"><?php echo $hasil['id_barang'];?></td>
				<td>
                    <div class="d-flex align-items-center gap-2 text-white">
                        <img src="<?php echo $hasil['gambar'] ? 'assets/img/barang/' . $hasil['gambar'] : 'assets/img/barang/default.png'; ?>" class="rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                        <div>
                            <span><?php echo $hasil['nama_barang'];?></span>
                            <?php echo $promo_badge; ?>
                        </div>
                    </div>
                </td>
				<td><?php echo $hasil['merk'];?></td>
				<td>
                    <?php if ($promo) { ?>
                        <span class="text-white-50 text-decoration-line-through" style="font-size: 0.85rem;">Rp <?php echo number_format($hasil['harga_jual'], 0, ',', '.');?></span>
                        <br><span class="fw-bold text-success">Rp <?php echo number_format($harga_final, 0, ',', '.');?></span>
                    <?php } else { ?>
                        Rp <?php echo number_format($hasil['harga_jual'], 0, ',', '.');?>
                    <?php } ?>
                </td>
				<td>
				<a href="fungsi/tambah/tambah.php?jual=jual&id=<?php echo $hasil['id_barang'];?>&id_kasir=<?php echo $_SESSION['admin']['id_member'];?>" 
					class="btn btn-primary btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
					<i class="fa fa-shopping-cart" style="font-size: 0.8rem;"></i></a></td>
			</tr>
		<?php }?>
		</table>
<?php
    }

    if (!empty($_GET['promo'])) {
        $id = htmlentities($_POST['id']);
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
            $status_promo,
            $id
        ];

        $sql = 'UPDATE promo SET nama_promo=?, tipe_promo=?, nilai_promo=?, tanggal_mulai=?, tanggal_selesai=?, id_barang=?, status_promo=?, updated_at=NOW() WHERE id_promo=?';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../dashboard.php?page=promo&success=edit-data"</script>';
    }

    if (!empty($_GET['promo_toggle'])) {
        $id = htmlentities($_GET['id']);
        $status = (int)$_GET['status'];
        
        $sql = 'UPDATE promo SET status_promo=?, updated_at=NOW() WHERE id_promo=?';
        $row = $config->prepare($sql);
        $row->execute([$status, $id]);
        echo '<script>window.location="../../dashboard.php?page=promo&success=edit-data"</script>';
    }
}

