@php
    $totalBeli = 0;
    $totalJual = 0;
    $totalStok = 0;
    
    // Generate next product ID
    $lastProduct = DB::table('barang')->orderByDesc('id')->first();
    $lastNum = $lastProduct ? (int) substr($lastProduct->id_barang, 2) : 0;
    $nextId = 'BR' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h3 class="fw-bold text-white mb-0">Data Barang</h3>
    <div class="d-flex gap-2 flex-wrap">
        @if(session('role') !== 'kasir')
            <button type="button" class="btn btn-success rounded px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahBarangModal">
                <i class="fa fa-plus me-1"></i> Tambah Data
            </button>
        @endif
        <a href="{{ route('dashboard', ['page' => 'barang', 'stok' => 'yes']) }}" class="btn btn-warning text-dark rounded px-4 py-2 fw-semibold">
            <i class="fa fa-list me-1"></i> Stok Menipis
        </a>
        <a href="{{ route('dashboard', ['page' => 'barang']) }}" class="btn btn-info rounded px-4 py-2 fw-semibold">
            <i class="fa fa-refresh me-1"></i> Segarkan Data
        </a>
    </div>
</div>

<div class="premium-glass-card border-0 rounded-4 overflow-hidden mb-5">
    <div class="card-body p-4 p-md-5">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" id="barangTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID Barang</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang as $key => $isi)
                        @php
                            $totalBeli += $isi->harga_beli * $isi->stok;
                            $totalJual += $isi->harga_jual * $isi->stok;
                            $totalStok += $isi->stok;
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-bold" style="color: #c084fc;">{{ $isi->id_barang }}</td>
                            <td>{{ $isi->nama_kategori }}</td>
                            <td class="fw-semibold text-white">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $isi->gambar ? asset('assets/img/barang/' . $isi->gambar) : asset('assets/img/barang/default.png') }}" class="rounded-3" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                                    <div>
                                        <span class="d-block">{{ $isi->nama_barang }}</span>
                                        @if(!empty($isi->deskripsi))
                                            <small class="text-white-50 d-block fw-normal" style="font-size: 0.75rem;">{{ \Illuminate\Support\Str::limit($isi->deskripsi, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $isi->merk }}</td>
                            <td>
                                @if((int)$isi->stok === 0)
                                    <span class="badge bg-danger rounded px-2">Habis</span>
                                @elseif((int)$isi->stok <= 3)
                                    <span class="badge bg-warning text-dark rounded px-2">{{ $isi->stok }} (Kritis)</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-20 text-white rounded px-2">{{ $isi->stok }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($isi->harga_beli, 0, ',', '.') }}</td>
                            <td class="fw-bold text-white">Rp {{ number_format($isi->harga_jual, 0, ',', '.') }}</td>
                            <td>{{ $isi->satuan_barang }}</td>
                            <td>
                                <div class="d-flex gap-1 align-items-center flex-wrap">
                                    @if((int)$isi->stok <= 3)
                                        <form method="POST" action="/fungsi/edit/edit.php?stok=edit" class="d-flex align-items-center gap-1 me-2">
                                            <input type="number" name="restok" class="form-control form-control-sm py-1 px-2" style="width: 60px;" placeholder="Qty" required>
                                            <input type="hidden" name="id" value="{{ $isi->id_barang }}">
                                            <button type="submit" class="btn btn-success btn-sm px-2 py-1"><i class="fas fa-plus"></i></button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('dashboard', ['page' => 'barang/details', 'barang' => $isi->id_barang]) }}" class="btn btn-primary btn-sm px-2.5 py-1">Detail</a>
                                    
                                    @if(session('role') !== 'kasir')
                                        <a href="{{ route('dashboard', ['page' => 'barang/edit', 'barang' => $isi->id_barang]) }}" class="btn btn-warning text-dark btn-sm px-2.5 py-1">Ubah</a>
                                        <a href="/fungsi/hapus/hapus.php?barang=hapus&id={{ $isi->id_barang }}" class="btn btn-danger btn-sm px-2.5 py-1" onclick="return confirm('Hapus Data barang?');">Hapus</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold text-white">
                        <td colspan="5" class="text-end">Total Summary:</td>
                        <td>{{ number_format($totalStok) }}</td>
                        <td>Rp {{ number_format($totalBeli, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalJual, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Barang -->
<div class="modal fade" id="tambahBarangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content premium-glass-card border-0 rounded-4 shadow-lg" style="background: rgba(20, 20, 28, 0.95) !important;">
            <div class="modal-header border-bottom border-white-5">
                <h5 class="modal-title fw-bold text-white"><i class="fa fa-plus me-2"></i>Tambah Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/fungsi/tambah/tambah.php?barang=tambah" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white-50">ID Barang</label>
                        <input type="text" readonly required value="{{ $nextId }}" class="form-control" name="id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Nama Barang</label>
                        <input type="text" placeholder="Nama Barang" required class="form-control" name="nama">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Merk Barang</label>
                        <input type="text" placeholder="Merk Barang" required class="form-control" name="merk">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Harga Beli</label>
                        <input type="number" placeholder="Harga Beli" required class="form-control" name="beli">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Harga Jual</label>
                        <input type="number" placeholder="Harga Jual" required class="form-control" name="jual">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Satuan Barang</label>
                        <select name="satuan" class="form-select" required>
                            <option value="PCS">PCS</option>
                            <option value="UNIT">UNIT</option>
                            <option value="PACK">PACK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Stok Awal</label>
                        <input type="number" required placeholder="Stok" class="form-control" name="stok">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Gambar Produk</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Deskripsi Produk</label>
                        <textarea class="form-control" name="deskripsi" rows="3" placeholder="Masukkan deskripsi produk..."></textarea>
                    </div>
                    <input type="hidden" name="tgl" value="{{ date('j F Y, G:i') }}">
                </div>
                <div class="modal-footer border-top border-white-5">
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-pill"><i class="fa fa-plus me-1"></i>Insert Data</button>
                    <button type="button" class="btn btn-secondary px-4 py-2 rounded-pill" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#barangTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
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
