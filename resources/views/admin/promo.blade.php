@php
    $uid = request('uid');
    $editPromo = null;
    if ($uid) {
        $editPromo = DB::table('promo')->where('id_promo', $uid)->first();
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold text-white mb-0">Manajemen Promo</h3>
</div>

@if(session('role') !== 'kasir')
    <div class="premium-glass-card border-0 rounded-4 p-4 mb-4">
        @if($editPromo)
            <h5 class="fw-bold text-white mb-3"><i class="fa fa-edit me-2 text-warning"></i>Ubah Promo</h5>
            <form method="POST" action="/fungsi/edit/edit.php?promo=edit">
                <input type="hidden" name="id" value="{{ $editPromo->id_promo }}">
                <input type="hidden" name="tipe_promo" value="potongan_harga">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-white-50">Nama Promo</label>
                        <input type="text" class="form-control" value="{{ $editPromo->nama_promo }}" required name="nama_promo" placeholder="Contoh: Promo Weekend">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Potongan Harga (Rp)</label>
                        <input type="number" class="form-control" value="{{ (int)$editPromo->nilai_promo }}" required name="nilai_promo" placeholder="Contoh: 5000">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-white-50">Target Produk</label>
                        <select name="id_barang" class="form-select">
                            <option value="">Semua Barang (Global)</option>
                            @foreach($barang as $brg)
                                <option value="{{ $brg->id_barang }}" {{ $editPromo->id_barang === $brg->id_barang ? 'selected' : '' }}>[{{ $brg->id_barang }}] {{ $brg->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Tanggal Mulai</label>
                        <input type="date" class="form-control" value="{{ $editPromo->tanggal_mulai }}" required name="tanggal_mulai">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Tanggal Selesai</label>
                        <input type="date" class="form-control" value="{{ $editPromo->tanggal_selesai }}" required name="tanggal_selesai">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50">Status</label>
                        <select name="status_promo" class="form-select" required>
                            <option value="1" {{ (int)$editPromo->status_promo === 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ (int)$editPromo->status_promo === 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-warning px-4 py-2 rounded-pill fw-semibold"><i class="fa fa-save me-2"></i>Simpan</button>
                        <a href="{{ route('dashboard', ['page' => 'promo']) }}" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">Batal</a>
                    </div>
                </div>
            </form>
        @else
            <h5 class="fw-bold text-white mb-3"><i class="fa fa-plus me-2 text-primary"></i>Tambah Promo Baru</h5>
            <form method="POST" action="/fungsi/tambah/tambah.php?promo=tambah">
                <input type="hidden" name="tipe_promo" value="potongan_harga">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-white-50">Nama Promo</label>
                        <input type="text" class="form-control" required name="nama_promo" placeholder="Contoh: Diskon Ramadhan">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Potongan Harga (Rp)</label>
                        <input type="number" class="form-control" required name="nilai_promo" placeholder="Contoh: 5000">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-white-50">Target Produk</label>
                        <select name="id_barang" class="form-select">
                            <option value="">Semua Barang (Global)</option>
                            @foreach($barang as $brg)
                                <option value="{{ $brg->id_barang }}">[{{ $brg->id_barang }}] {{ $brg->nama_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Tanggal Mulai</label>
                        <input type="date" class="form-control" required value="{{ date('Y-m-d') }}" name="tanggal_mulai">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Tanggal Selesai</label>
                        <input type="date" class="form-control" required value="{{ date('Y-m-d', strtotime('+7 days')) }}" name="tanggal_selesai">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50">Status</label>
                        <select name="status_promo" class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold"><i class="fa fa-plus me-2"></i>Tambah Promo</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endif

<div class="premium-glass-card border-0 rounded-4 overflow-hidden mb-5">
    <div class="card-body p-4 p-md-5">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" id="promoTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Nama Promo</th>
                        <th>Target Produk</th>
                        <th>Potongan Harga</th>
                        <th>Periode Promo</th>
                        <th>Status</th>
                        @if(session('role') !== 'kasir')
                            <th style="width: 200px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($promo as $key => $isi)
                        @php
                            $today = date('Y-m-d');
                            $isExpired = $today > $isi->tanggal_selesai;
                            $isUpcoming = $today < $isi->tanggal_mulai;
                            $isActive = (int)$isi->status_promo === 1 && !$isExpired && !$isUpcoming;
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <span class="fw-semibold text-white d-block">{{ $isi->nama_promo }}</span>
                                <small class="text-white-50">Dibuat: {{ date('d M Y', strtotime($isi->created_at)) }}</small>
                            </td>
                            <td>
                                @if($isi->id_barang)
                                    <span class="badge bg-secondary bg-opacity-20 text-white border border-white-10 px-2 py-1"><i class="fa fa-tag me-1 text-primary"></i>{{ $isi->nama_barang }}</span>
                                @else
                                    <span class="badge bg-info bg-opacity-20 text-info border border-info-30 px-2 py-1"><i class="fa fa-globe me-1"></i>Semua Barang</span>
                                @endif
                            </td>
                            <td class="fw-bold text-white">
                                Rp {{ number_format($isi->nilai_promo, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-white fw-semibold">{{ date('d M Y', strtotime($isi->tanggal_mulai)) }}</span>
                                    <span class="text-white-50">s/d</span>
                                    <span class="text-white fw-semibold">{{ date('d M Y', strtotime($isi->tanggal_selesai)) }}</span>
                                </div>
                            </td>
                            <td>
                                @if($isExpired)
                                    <span class="badge bg-danger rounded px-2">Kedaluwarsa</span>
                                @elseif($isUpcoming)
                                    <span class="badge bg-warning text-dark rounded px-2">Akan Datang</span>
                                @elseif((int)$isi->status_promo === 1)
                                    <span class="badge bg-success rounded px-2">Aktif</span>
                                @else
                                    <span class="badge bg-secondary rounded px-2">Nonaktif</span>
                                @endif
                            </td>
                            @if(session('role') !== 'kasir')
                                <td>
                                    <div class="d-flex gap-2">
                                        @if((int)$isi->status_promo === 1)
                                            <a href="/fungsi/edit/edit.php?promo_toggle=yes&id={{ $isi->id_promo }}&status=0" class="btn btn-outline-warning btn-sm px-2 py-1"><i class="fa fa-power-off me-1"></i>Matikan</a>
                                        @else
                                            <a href="/fungsi/edit/edit.php?promo_toggle=yes&id={{ $isi->id_promo }}&status=1" class="btn btn-success btn-sm px-2 py-1"><i class="fa fa-check me-1"></i>Aktifkan</a>
                                        @endif
                                        <a href="{{ route('dashboard', ['page' => 'promo', 'uid' => $isi->id_promo]) }}" class="btn btn-warning text-dark btn-sm px-2.5 py-1"><i class="fa fa-edit"></i></a>
                                        <a href="/fungsi/hapus/hapus.php?promo=hapus&id={{ $isi->id_promo }}" class="btn btn-danger btn-sm px-2.5 py-1" onclick="return confirm('Hapus Promo ini?');"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#promoTable').DataTable({
            language: {
                search: "Cari Promo:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ promo",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });
    });
</script>
