@extends('layouts.app')

@section('title', 'Kategori Lapangan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-tags me-2"></i>Data Kategori Lapangan</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
            <i class="fas fa-plus me-2"></i>Tambah Kategori
        </button>
    </div>

    <div class="card-body">
        <div class="row">
            @forelse($kategori as $k)
            <div class="col-md-4 mb-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5>
                            <i class="fas fa-tag me-2 text-primary"></i>
                            {{ $k->nama }}
                        </h5>

                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-sm btn-warning flex-grow-1" onclick="editKategori({{ $k }})">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>

                            <form action="{{ route('kategori-lapangan.destroy', $k->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                    onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                    Belum ada data kategori lapangan
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('kategori-lapangan.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Contoh: Futsal, Mini Soccer">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editKategoriForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editKategori(k) {
    $('#editKategoriForm').attr('action', '/kategori-lapangan/' + k.id);
    $('#edit_nama').val(k.nama);
    $('#editKategoriModal').modal('show');
}
</script>
@endpush
