@extends('layouts.app')

@section('title', 'Data Lapangan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-th-large me-2"></i>Data Lapangan</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLapanganModal">
            <i class="fas fa-plus me-2"></i>Tambah Lapangan
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            @forelse($lapangans as $lapangan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-{{ $lapangan->status == 'aktif' ? 'success' : 'danger' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0"><i class="fas fa-futbol me-2 text-primary"></i>{{ $lapangan->nama }}</h5>
                            <span class="badge bg-{{ $lapangan->status == 'aktif' ? 'success' : 'danger' }}">
                                {{ ucfirst($lapangan->status) }}
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">Jenis:</small>
                            <strong class="d-block">{{ ucfirst(str_replace('_', ' ', $lapangan->jenis)) }}</strong>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Harga per Jam:</small>
                            <h4 class="text-success mb-0">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</h4>
                        </div>
                        
                        @if($lapangan->keterangan)
                        <div class="mb-3">
                            <small class="text-muted">Keterangan:</small>
                            <p class="small mb-0">{{ $lapangan->keterangan }}</p>
                        </div>
                        @endif
                        
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-warning flex-grow-1" onclick="editLapangan({{ $lapangan }})">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <form action="{{ route('lapangan.destroy', $lapangan->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Yakin ingin menghapus lapangan ini?')">
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
                    Belum ada data lapangan
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Tambah Lapangan -->
<div class="modal fade" id="addLapanganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lapangan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Lapangan A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" class="form-control" required>
                            <option value="">Pilih Jenis</option>
                            <option value="vinyl">Vinyl</option>
                            <option value="rumput_sintetis">Rumput Sintetis</option>
                            <option value="matras">Matras</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                        <input type="number" name="harga_per_jam" class="form-control" required min="0" placeholder="150000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional: Deskripsi lapangan"></textarea>
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

<!-- Modal Edit Lapangan -->
<div class="modal fade" id="editLapanganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLapanganForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" id="edit_jenis" class="form-control" required>
                            <option value="vinyl">Vinyl</option>
                            <option value="rumput_sintetis">Rumput Sintetis</option>
                            <option value="matras">Matras</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                        <input type="number" name="harga_per_jam" id="edit_harga_per_jam" class="form-control" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
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
function editLapangan(lapangan) {
    $('#editLapanganForm').attr('action', '/lapangan/' + lapangan.id);
    $('#edit_nama').val(lapangan.nama);
    $('#edit_jenis').val(lapangan.jenis);
    $('#edit_harga_per_jam').val(lapangan.harga_per_jam);
    $('#edit_status').val(lapangan.status);
    $('#edit_keterangan').val(lapangan.keterangan);
    $('#editLapanganModal').modal('show');
}
</script>
@endpush