<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $finance_id ? 'Edit' : 'Tambah' }} Catatan</h4>
        <a href="{{ route('app.finances.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <form wire:submit.prevent="save">
        <div class="mb-2">
            <label>Judul</label>
            <input wire:model="title" class="form-control" />
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-2">
            <label>Tipe</label>
            <select wire:model="type" class="form-select">
                <option value="">Pilih tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-2">
            <label>Nominal</label>
            <input wire:model="amount" type="number" class="form-control" />
            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-2">
            <label>Tanggal Transaksi</label>
            <input wire:model="transaction_date" type="date" class="form-control" />
            @error('transaction_date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-2">
            <label>Deskripsi</label>
            <textarea wire:model="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-2">
            <label>Bukti / Cover (opsional)</label>
            <input type="file" wire:model="new_cover" class="form-control" />
            @if($cover)
                <div class="mt-2">
                    <img src="{{ asset('storage/uploads/'.$cover) }}" width="120" alt="cover">
                </div>
            @endif
        </div>

        <button class="btn btn-success">{{ $finance_id ? 'Update' : 'Simpan' }}</button>
    </form>
</div>

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Livewire.on('swal:success', data => {
        Swal.fire({
            icon: 'success',
            title: data.title,
            text: data.text
        });
    });
</script>
@endpush
