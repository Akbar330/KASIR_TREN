@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-box me-2"></i>Data Produk</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus me-2"></i>Tambah Produk
        </button>
    </div>
    <div class="card-body">
        <!-- Filter Kategori -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <a href="{{ route('products.index') }}" class="btn btn-sm {{ request('kategori') ? 'btn-outline-primary' : 'btn-primary' }}">Semua</a>
                <a href="?kategori=minuman" class="btn btn-sm {{ request('kategori') == 'minuman' ? 'btn-primary' : 'btn-outline-primary' }}">Minuman</a>
                <a href="?kategori=makanan" class="btn btn-sm {{ request('kategori') == 'makanan' ? 'btn-primary' : 'btn-outline-primary' }}">Makanan</a>
                <a href="?kategori=equipment" class="btn btn-sm {{ request('kategori') == 'equipment' ? 'btn-primary' : 'btn-outline-primary' }}">Equipment</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Barcode</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                    <tr>
                        <td>{{ $products->firstItem() + $index }}</td>
                        <td><strong>{{ $product->nama }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $product->kategori == 'minuman' ? 'info' : ($product->kategori == 'makanan' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($product->kategori) }}
                            </span>
                        </td>
                        <td><strong>Rp {{ number_format($product->harga, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $product->stok <= 10 ? 'danger' : 'success' }}">
                                {{ $product->stok }} unit
                            </span>
                        </td>
                        <td><code>{{ $product->barcode ?: '-' }}</code></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editProduct({{ $product }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                            Belum ada data produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <option value="minuman">Minuman</option>
                            <option value="makanan">Makanan</option>
                            <option value="equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <input type="number" name="harga" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProductForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="edit_kategori" class="form-control" required>
                            <option value="minuman">Minuman</option>
                            <option value="makanan">Makanan</option>
                            <option value="equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga <span class="text-danger">*</span></label>
                        <input type="number" name="harga" id="edit_harga" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" id="edit_stok" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" id="edit_barcode" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editProduct(product) {
    $('#editProductForm').attr('action', '/products/' + product.id);
    $('#edit_nama').val(product.nama);
    $('#edit_kategori').val(product.kategori);
    $('#edit_harga').val(product.harga);
    $('#edit_stok').val(product.stok);
    $('#edit_barcode').val(product.barcode);
    $('#editProductModal').modal('show');
}
</script>
@endpush
