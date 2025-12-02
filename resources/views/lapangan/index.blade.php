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
                <div class="accordion" id="accordionKategori">
                    @foreach ($kategori as $k)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $k->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $k->id }}" aria-expanded="false"
                                    aria-controls="collapse{{ $k->id }}">
                                    {{ str_replace('_',' ',ucwords($k->nama)) }}
                                </button>
                            </h2>

                            <div id="collapse{{ $k->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $k->id }}" data-bs-parent="#accordionKategori">

                                <div class="accordion-body">

                                    @if ($k->lapangans->count() == 0)
                                        <p class="text-muted">Belum ada lapangan</p>
                                    @else
                                        <div class="row">
                                            @foreach ($k->lapangans as $lapangan)
                                                <div class="col-md-4 mb-4">
                                                    <div
                                                        class="card h-100 border-{{ $lapangan->status == 'aktif' ? 'success' : 'danger' }}">
                                                        <div class="card-body">

                                                            <h5 class="mb-2 d-flex justify-content-between">
                                                                <span><i
                                                                        class="fas fa-futbol me-2"></i>{{ $lapangan->nama }}</span>
                                                                <span
                                                                    class="badge bg-{{ $lapangan->status == 'aktif' ? 'success' : 'danger' }}">
                                                                    {{ ucfirst($lapangan->status) }}
                                                                </span>
                                                            </h5>

                                                            <div class="mb-2">
                                                                <small class="text-muted">Harga/Jam</small>
                                                                <h4 class="text-success">Rp
                                                                    {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}
                                                                </h4>
                                                            </div>

                                                            <div class="mb-3">
                                                                <small class="text-muted">Keterangan</small>
                                                                <p class="small">{{ $lapangan->keterangan ?? '-' }}</p>
                                                            </div>

                                                            <div class="d-flex gap-2">
                                                                <button onclick='editLapangan(@json($lapangan))'
                                                                    class="btn btn-warning btn-sm flex-grow-1">
                                                                    Edit
                                                                </button>

                                                                <form
                                                                    action="{{ route('lapangan.destroy', $lapangan->id) }}"
                                                                    method="POST" class="flex-grow-1">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="btn btn-danger btn-sm w-100"
                                                                        onclick="return confirm('Yakin hapus?')">Hapus</button>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

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
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_lapangs_id" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                            <input type="number" name="harga_per_jam" class="form-control" required>
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
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
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
                            <label class="form-label">Nama Lapangan</label>
                            <input type="text" id="edit_nama" name="nama" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_lapangs_id" id="edit_kategori" class="form-control">
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga per Jam</label>
                            <input type="number" id="edit_harga" name="harga_per_jam" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="aktif">Aktif</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea id="edit_keterangan" name="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function editLapangan(l) {
            $('#editLapanganForm').attr('action', '/lapangan/' + l.id);
            $('#edit_nama').val(l.nama);
            $('#edit_kategori').val(l.kategori_lapangs_id);
            $('#edit_harga').val(l.harga_per_jam);
            $('#edit_status').val(l.status);
            $('#edit_keterangan').val(l.keterangan);
            $('#editLapanganModal').modal('show');
        }
    </script>
@endpush
