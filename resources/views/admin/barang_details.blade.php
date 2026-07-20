<a href="{{ route('dashboard', ['page' => 'barang']) }}" class="btn btn-outline-light rounded-pill px-4 mb-4"><i class="fa fa-angle-left me-2"></i> Kembali</a>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-white mb-0">Detail Barang</h3>
</div>

<div class="row g-4 mb-5">
    <!-- Image Card Column -->
    <div class="col-md-4">
        <div class="premium-glass-card border-0 rounded-4 p-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
            <img src="{{ $barang->gambar ? asset('assets/img/barang/' . $barang->gambar) : asset('assets/img/barang/default.png') }}" 
                 class="img-fluid rounded-4 shadow-lg mb-3" 
                 style="max-height: 250px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
            <h5 class="fw-bold text-white mb-1">{{ $barang->nama_barang }}</h5>
            <span class="text-primary fw-semibold">{{ $barang->id_barang }}</span>
        </div>
    </div>
    
    <!-- Details Table Column -->
    <div class="col-md-8">
        <div class="premium-glass-card border-0 rounded-4 p-5 h-100">
            <div class="table-responsive">
                <table class="table align-middle text-white mb-0">
                    <tr>
                        <td style="width: 200px;" class="text-white-50">Kategori</td>
                        <td class="text-white">{{ $barang->nama_kategori }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Nama Barang</td>
                        <td class="fw-semibold text-white">{{ $barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Merk Barang</td>
                        <td>{{ $barang->merk }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Harga Beli</td>
                        <td>Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Harga Jual</td>
                        <td class="fw-bold text-white">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Satuan Barang</td>
                        <td>{{ $barang->satuan_barang }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Stok</td>
                        <td><span class="badge bg-secondary bg-opacity-20 text-white rounded px-2">{{ $barang->stok }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Deskripsi</td>
                        <td>
                            @if(!empty($barang->deskripsi))
                                <div class="text-white-50" style="white-space: pre-line;">{{ $barang->deskripsi }}</div>
                            @else
                                <span class="text-secondary font-italic">Tidak ada deskripsi.</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Tanggal Input</td>
                        <td class="text-secondary">{{ $barang->tgl_input }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Tanggal Update</td>
                        <td class="text-secondary">{{ $barang->tgl_update ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
