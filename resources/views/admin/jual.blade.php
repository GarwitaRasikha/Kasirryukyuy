@php
    $hitung = 0;
    $bayar = request('bayar', 0);
    $notaSuccess = false;
    
    // Fetch active cart items
    $penjualan = DB::table('penjualan')
        ->leftJoin('barang', 'penjualan.id_barang', '=', 'barang.id_barang')
        ->leftJoin('member', 'penjualan.id_member', '=', 'member.id_member')
        ->select('penjualan.*', 'barang.nama_barang', 'member.nm_member')
        ->where('penjualan.id_member', session('admin')['id_member'])
        ->get();

    $total_bayar = $penjualan->sum('total');

    if (request('nota') === 'yes') {
        $total = request('total');
        if (!empty($bayar)) {
            $hitung = $bayar - $total;
            if ($bayar >= $total) {
                $id_barang = request('id_barang');
                $id_member = request('id_member');
                $jumlah = request('jumlah');
                $total1 = request('total1');
                $tgl_input = request('tgl_input');
                $periode = request('periode');
                $jumlah_dipilih = is_array($id_barang) ? count($id_barang) : 0;
                
                for ($x = 0; $x < $jumlah_dipilih; $x++) {
                    DB::table('nota')->insert([
                        'id_barang' => $id_barang[$x],
                        'id_member' => $id_member[$x],
                        'jumlah' => $jumlah[$x],
                        'total' => $total1[$x],
                        'tanggal_input' => $tgl_input[$x],
                        'periode' => $periode[$x],
                    ]);
                    
                    // Decrement stock
                    DB::table('barang')
                        ->where('id_barang', $id_barang[$x])
                        ->decrement('stok', $jumlah[$x]);
                }
                
                // Clear active cart
                DB::table('penjualan')->where('id_member', session('admin')['id_member'])->delete();
                $penjualan = collect([]); // empty cart list
                $total_bayar = 0;
                $notaSuccess = true;
            }
        }
    }
@endphp

@if($notaSuccess)
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Pembayaran Berhasil!",
                text: "Belanjaan berhasil dibayar.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
                background: "#14141c",
                color: "#ffffff"
            });
        });
    </script>
@elseif(request('nota') === 'yes' && $bayar < request('total'))
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Uang Kurang!",
                text: "Uang yang dimasukkan kurang Rp {{ number_format(request('total') - $bayar, 0, ',', '.') }}",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#a855f7",
                background: "#14141c",
                color: "#ffffff"
            });
        });
    </script>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-white mb-0">Keranjang Penjualan</h3>
</div>

@php
    $today = date('Y-m-d');
    $activePromos = DB::table('promo')
        ->leftJoin('barang', 'promo.id_barang', '=', 'barang.id_barang')
        ->where('promo.status_promo', 1)
        ->where('promo.tanggal_mulai', '<=', $today)
        ->where('promo.tanggal_selesai', '>=', $today)
        ->select('promo.*', 'barang.nama_barang')
        ->get();
@endphp

@if($activePromos->count() > 0)
    <div class="alert border-0 rounded-4 p-4 mb-4 premium-glass-card shadow-sm" style="background: rgba(168, 85, 247, 0.08) !important; border: 1px solid rgba(168, 85, 247, 0.15) !important;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa fa-percentage text-primary fs-5"></i>
            <h5 class="fw-bold text-white mb-0">Promo Aktif Hari Ini!</h5>
        </div>
        <div class="row g-3">
            @foreach($activePromos as $ap)
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-3 bg-white bg-opacity-5 border border-white-5 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <span class="fw-bold text-white d-block mb-1" style="font-size: 0.9rem;">{{ $ap->nama_promo }}</span>
                            <span class="text-white-50 d-block mb-2" style="font-size: 0.75rem;">
                                Target: {{ $ap->id_barang ? $ap->nama_barang : 'Semua Barang' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-white-5">
                            <span class="badge bg-danger rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                Pot. Rp {{ number_format($ap->nilai_promo, 0, ',', '.') }}
                            </span>
                            <small class="text-secondary" style="font-size: 0.7rem;">Hingga: {{ date('d M Y', strtotime($ap->tanggal_selesai)) }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Cari Barang Card -->
    <div class="col-lg-4">
        <div class="card premium-glass-card border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header py-3" style="background: rgba(168, 85, 247, 0.1) !important; border-bottom: 1px solid rgba(168, 85, 247, 0.2) !important; color: #c084fc !important;">
                <h6 class="mb-0 fw-bold"><i class="fa fa-search me-2"></i>Cari Barang</h6>
            </div>
            <div class="card-body p-4">
                <input type="text" id="cari" class="form-control py-2.5 px-3" placeholder="Ketik Kode / Nama Barang...">
            </div>
        </div>
    </div>
    
    <!-- Hasil Pencarian Card -->
    <div class="col-lg-8">
        <div class="card premium-glass-card border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header py-3" style="background: rgba(168, 85, 247, 0.1) !important; border-bottom: 1px solid rgba(168, 85, 247, 0.2) !important; color: #c084fc !important;">
                <h6 class="mb-0 fw-bold"><i class="fa fa-list me-2"></i>Hasil Pencarian</h6>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <div id="hasil_cari"></div>
                    <div id="tunggu" class="text-center text-secondary py-3">Ketik kode atau nama barang pada pencarian...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kasir Keranjang Card -->
<div class="card premium-glass-card border-0 rounded-4 overflow-hidden mb-5">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: rgba(168, 85, 247, 0.1) !important; border-bottom: 1px solid rgba(168, 85, 247, 0.2) !important; color: #c084fc !important;">
        <h6 class="mb-0 fw-bold"><i class="fa fa-shopping-cart me-2"></i>KASIR</h6>
        <a class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Apakah Anda ingin reset keranjang?');" href="/fungsi/hapus/hapus.php?penjualan=jual">
            <b>RESET KERANJANG</b>
        </a>
    </div>
    <div class="card-body p-4 p-md-5">
        <div class="table-responsive mb-4">
            <table class="table align-middle mb-0">
                <tr>
                    <td style="width: 150px;"><b>Tanggal</b></td>
                    <td><input type="text" readonly class="form-control" value="{{ date('j F Y, G:i') }}" name="tgl"></td>
                </tr>
            </table>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-4">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th style="width: 120px;">Jumlah</th>
                        <th>Total</th>
                        <th>Kasir</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $key => $isi)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-semibold text-white">{{ $isi->nama_barang }}</td>
                            <td>
                                <form method="POST" action="/fungsi/edit/edit.php?jual=jual">
                                    <input type="number" name="jumlah" value="{{ $isi->jumlah }}" class="form-control form-control-sm text-center" style="width: 80px;" required>
                                    <input type="hidden" name="id" value="{{ $isi->id_penjualan }}">
                                    <input type="hidden" name="id_barang" value="{{ $isi->id_barang }}">
                            </td>
                            <td class="fw-bold text-white">Rp {{ number_format($isi->total, 0, ',', '.') }}</td>
                            <td>{{ $isi->nm_member }}</td>
                            <td>
                                    <button type="submit" class="btn btn-warning btn-sm px-2 py-1"><i class="fa fa-edit me-1"></i>Edit</button>
                                </form>
                                <a href="/fungsi/hapus/hapus.php?jual=jual&id={{ $isi->id_penjualan }}&brg={{ $isi->id_barang }}&jml={{ $isi->jumlah }}" class="btn btn-danger btn-sm px-2 py-1"><i class="fa fa-times"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4"><i class="fas fa-folder-open me-2"></i> Keranjang kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="kasirnya" class="border-top border-white-5 pt-4">
            <table class="table table-striped align-middle text-white mb-0">
                <form method="POST" action="/dashboard?page=jual&nota=yes#kasirnya">
                    @csrf
                    @foreach($penjualan as $isi)
                        <input type="hidden" name="id_barang[]" value="{{ $isi->id_barang }}">
                        <input type="hidden" name="id_member[]" value="{{ $isi->id_member }}">
                        <input type="hidden" name="jumlah[]" value="{{ $isi->jumlah }}">
                        <input type="hidden" name="total1[]" value="{{ $isi->total }}">
                        <input type="hidden" name="tgl_input[]" value="{{ $isi->tanggal_input }}">
                        <input type="hidden" name="periode[]" value="{{ date('m-Y') }}">
                    @endforeach
                    <tr>
                        <td style="width: 150px;" class="text-white-50">Total Semua</td>
                        <td>
                            <input type="number" class="form-control" name="total" value="{{ $total_bayar }}" readonly>
                        </td>
                        <td style="width: 100px;" class="text-white-50">Bayar</td>
                        <td>
                            <input type="number" class="form-control" name="bayar" value="{{ $bayar }}" required>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button type="submit" class="btn btn-success px-4 fw-semibold me-2"><i class="fa fa-shopping-cart me-2"></i>Bayar</button>
                                @if(request('nota') === 'yes')
                                    <a class="btn btn-danger px-3" href="/fungsi/hapus/hapus.php?penjualan=jual"><b>RESET</b></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                </form>
                <tr>
                    <td class="text-white-50">Kembali</td>
                    <td>
                        <input type="text" class="form-control" value="Rp {{ number_format($hitung, 0, ',', '.') }}" readonly>
                    </td>
                    <td></td>
                    <td colspan="2">
                        @if($notaSuccess || (request('nota') === 'yes' && $bayar >= $total_bayar))
                            <a href="/print.php?nm_member={{ session('admin')['nm_member'] }}&bayar={{ $bayar }}&kembali={{ $hitung }}" target="_blank" class="btn btn-secondary px-4 fw-semibold">
                                <i class="fa fa-print me-2"></i> Cetak Bukti Pembayaran
                            </a>
                        @else
                            <button class="btn btn-secondary px-4 fw-semibold" disabled>
                                <i class="fa fa-print me-2"></i> Cetak Bukti Pembayaran
                            </button>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Load all items by default on startup
        $.ajax({
            type: "POST",
            url: "/fungsi/edit/edit.php?cari_barang=yes",
            data: 'keyword=',
            beforeSend: function(){
                $("#hasil_cari").hide();
                $("#tunggu").html('<p style="color:#c084fc"><i class="fas fa-spinner fa-spin me-2"></i>Mencari barang...</p>');
            },
            success: function(html){
                $("#tunggu").html('');
                $("#hasil_cari").show();
                $("#hasil_cari").html(html);
            }
        });

        $("#cari").keyup(function(){
            $.ajax({
                type: "POST",
                url: "/fungsi/edit/edit.php?cari_barang=yes",
                data: 'keyword=' + $(this).val(),
                beforeSend: function(){
                    $("#hasil_cari").hide();
                    $("#tunggu").html('<p style="color:#c084fc"><i class="fas fa-spinner fa-spin me-2"></i>Mencari barang...</p>');
                },
                success: function(html){
                    $("#tunggu").html('');
                    $("#hasil_cari").show();
                    $("#hasil_cari").html(html);
                }
            });
        });
    });
</script>
