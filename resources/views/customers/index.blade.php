@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="mb-4"><p class="text-success fw-bold small text-uppercase mb-1">Database pelanggan</p><h2 class="fw-bold mb-1" style="letter-spacing:-.04em">Pelanggan</h2><p class="text-muted mb-0">Kelola kontak dan riwayat pelanggan lapangan.</p></div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2"></i>Data Customer</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="fas fa-plus me-2"></i>Tambah Customer
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3"><div class="col-md-5 col-lg-4"><div class="input-group"><span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span><input type="search" class="form-control" data-table-search="#customerTable" placeholder="Cari nama atau telepon..."></div></div><div class="col-md-7 col-lg-8 d-flex align-items-center"><small class="text-muted">Cari pada halaman yang sedang dibuka.</small></div></div>
        <div class="table-responsive">
            <table class="table table-hover" id="customerTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama</th>
                        <th>No. Telp</th>
                        <th>Alamat</th>
                        <th>Total Transaksi</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $index => $customer)
                    <tr>
                        <td>{{ $customers->firstItem() + $index }}</td>
                        <td><strong>{{ $customer->nama }}</strong></td>
                        <td><i class="fas fa-phone me-1"></i>{{ $customer->no_telp }}</td>
                        <td>{{ $customer->alamat ?: '-' }}</td>
                        <td><span class="badge bg-info">{{ $customer->transactions->count() }} transaksi</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editCustomer({{ $customer->id }}, '{{ $customer->nama }}', '{{ $customer->no_telp }}', '{{ $customer->alamat }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus customer ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                            Belum ada data customer
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Tambah Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telp <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
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

<!-- Modal Edit Customer -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telp <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" id="edit_no_telp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
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
window.editCustomer = function(id, nama, no_telp, alamat) {
    $('#editCustomerForm').attr('action', '/customers/' + id);
    $('#edit_nama').val(nama);
    $('#edit_no_telp').val(no_telp);
    $('#edit_alamat').val(alamat);
    $('#editCustomerModal').modal('show');
};
</script>
@endpush
