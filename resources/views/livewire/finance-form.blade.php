<div class="container py-4" style="max-width: 650px;">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white p-3">
            <h4 class="m-0 fw-semibold">
                <i class="bi bi-pencil-square me-2"></i>{{ $finance_id ? 'Edit' : 'Tambah' }} Catatan
            </h4>
            <a href="{{ route('app.finances.index') }}" class="btn btn-light btn-sm fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card-body bg-light">
            <form wire:submit.prevent="save" class="needs-validation" id="financeForm">
                {{-- ===================== JUDUL ===================== --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Judul</label>
                    <input wire:model="title" class="form-control shadow-sm border-primary" placeholder="Masukkan judul catatan..." />
                    @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- ===================== TIPE ===================== --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Tipe</label>
                    <select wire:model="type" class="form-select shadow-sm border-primary">
                        <option value="">Pilih tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- ===================== NOMINAL & TANGGAL ===================== --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-primary">Nominal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-primary text-white border-primary">Rp</span>
                            <input wire:model="amount" type="number" class="form-control border-primary" placeholder="0" />
                        </div>
                        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-primary">Tanggal Transaksi</label>
                        <input wire:model="date" type="date" class="form-control shadow-sm border-primary" />
                        @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- ===================== DESKRIPSI (TEXTAREA BIASA) ===================== --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Deskripsi</label>
                    <textarea wire:model="description" class="form-control shadow-sm border-primary rounded-3" rows="4" placeholder=""></textarea>
                    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- ===================== COVER ===================== --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold text-primary">Bukti / Cover (opsional)</label>
                    <input type="file" wire:model="new_cover" accept="image/*" class="form-control shadow-sm border-primary" />

                    {{-- Preview gambar lama atau baru --}}
                    @if ($new_cover)
                        <div class="mt-3 text-center">
                            <img src="{{ $new_cover->temporaryUrl() }}" class="img-thumbnail rounded-3 shadow-sm" width="140" alt="preview">
                        </div>
                    @elseif ($cover)
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/uploads/'.$cover) }}" class="img-thumbnail rounded-3 shadow-sm" width="140" alt="cover">
                        </div>
                    @endif

                    @error('new_cover') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- ===================== SUBMIT ===================== --}}
              <div class="text-end mt-4">
    <button type="submit"
            wire:loading.attr="disabled"
            wire:target="save, new_cover"
            class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
        <span wire:loading.remove wire:target="save">
            <i class="bi bi-save me-1"></i>{{ $finance_id ? 'Update' : 'Simpan' }}
        </span>
        <span wire:loading wire:target="save">
            <i class="bi bi-hourglass-split me-1"></i> Menyimpan...
        </span>
    </button>
</div>


            </form>
        </div>
    </div>
</div>

{{-- ===================== STYLES ===================== --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #dbeafe, #ffffff);
        font-family: "Poppins", sans-serif;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .card { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

{{-- ===================== SCRIPTS ===================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('livewire:init', () => {
    // === SweetAlert sukses dari backend ===
    Livewire.on('swal:success', data => {
    Swal.fire({
        icon: 'success',
        title: data.title,
        text: data.text,
        confirmButtonColor: '#0d6efd',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        window.location.href = '{{ route("app.finances.index") }}';
    });
});

    // === Konfirmasi sebelum submit ===
    document.getElementById('saveBtn').addEventListener('click', () => {
        const form = document.getElementById('financeForm');
        Swal.fire({
            title: '{{ $finance_id ? "Update Catatan?" : "Simpan Catatan?" }}',
            text: 'Pastikan data sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed && form) {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });
    });
});
</script>
@endpush
