<a href="{{ route('dashboard', ['page' => 'barang']) }}" class="btn btn-outline-light rounded-pill px-4 mb-4"><i class="fa fa-angle-left me-2"></i> Kembali</a>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-white mb-0">Ubah Barang</h3>
</div>

<div class="premium-glass-card border-0 rounded-4 p-5 mb-5">
    <form action="/fungsi/edit/edit.php?barang=edit" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-white-50">ID Barang</label>
                    <input type="text" readonly required class="form-control" value="{{ $barang->id_barang }}" name="id">
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="{{ $barang->id_kategori }}">{{ $barang->nama_kategori }}</option>
                        <option value="">-- Pilih Kategori Lain --</option>
                        @foreach($kategori as $kat)
                            @if($kat->id_kategori !== $barang->id_kategori)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Nama Barang</label>
                    <input type="text" class="form-control" value="{{ $barang->nama_barang }}" name="nama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Merk Barang</label>
                    <input type="text" class="form-control" value="{{ $barang->merk }}" name="merk" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Deskripsi Produk</label>
                    <textarea class="form-control" name="deskripsi" rows="4" placeholder="Masukkan deskripsi produk...">{{ $barang->deskripsi }}</textarea>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-white-50">Harga Beli</label>
                    <input type="number" class="form-control" value="{{ $barang->harga_beli }}" name="beli" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Harga Jual</label>
                    <input type="number" class="form-control" value="{{ $barang->harga_jual }}" name="jual" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Satuan Barang</label>
                    <select name="satuan" class="form-select" required>
                        <option value="{{ $barang->satuan_barang }}">{{ $barang->satuan_barang }}</option>
                        <option value="PCS">PCS</option>
                        <option value="UNIT">UNIT</option>
                        <option value="PACK">PACK</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Stok</label>
                    <input type="number" class="form-control" value="{{ $barang->stok }}" name="stok" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50 d-block">Gambar Produk</label>
                    @if($barang->gambar)
                        <div class="mb-2">
                            <img src="{{ asset('assets/img/barang/' . $barang->gambar) }}" class="rounded shadow-sm" style="max-width: 120px; border: 1px solid rgba(255,255,255,0.1);">
                            <input type="hidden" name="foto_lama" value="{{ $barang->gambar }}">
                        </div>
                    @endif
                    <input type="file" class="form-control" name="foto" accept="image/*">
                    <small class="text-white-50">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>
                <input type="hidden" name="tgl" value="{{ date('j F Y, G:i') }}">
            </div>
            
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-semibold"><i class="fa fa-edit me-2"></i> Ubah Data</button>
            </div>
        </div>
    </form>
</div>
