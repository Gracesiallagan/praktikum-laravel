<div>
    {{-- ===================== HEADER ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Catatan Keuangan</h4>
        <a href="{{ route('app.finances.create') }}" class="btn btn-primary" wire:navigate>+ Tambah</a>
    </div>

    {{-- ===================== FILTER & SEARCH ===================== --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari judul...">
        </div>
        <div class="col-md-3">
            <select wire:model="filterType" class="form-select">
                <option value="">Semua Tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </div>
    </div>

    {{-- ===================== TABLE ===================== --}}
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($finances as $index => $f)
                <tr>
                    <td>{{ $finances->firstItem() + $index }}</td>
                    <td>{{ $f->title }}</td>
                    <td>
                        <span class="badge {{ $f->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ $f->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($f->amount, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($f->transaction_date)->format('d M Y') }}</td>
                    <td>
                        <button 
                            type="button" 
                            class="btn btn-sm btn-danger"
                            wire:click="confirmDelete({{ $f->id }})"
                        >
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===================== PAGINATION ===================== --}}
    <div class="mt-3">
        {{ $finances->links() }}
    </div>

    {{-- ===================== CHART ===================== --}}
    <div wire:ignore id="chart" class="mt-5"></div>

    {{-- ===================== SCRIPTS ===================== --}}
    @push('scripts')
    {{-- ✅ SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ✅ ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
    document.addEventListener('livewire:init', () => {

        // === SWEETALERT KONFIRMASI HAPUS ===
        Livewire.on('swal:confirm', (data) => {
            Swal.fire({
                title: data.title || 'Hapus Data?',
                text: data.text || 'Data ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim event ke Livewire dengan ID yang benar
                    Livewire.dispatch('deleteConfirmed', { id: data.id });
                }
            });
        });

        // === SWEETALERT SUKSES ===
        Livewire.on('swal:success', (data) => {
            Swal.fire({
                title: data.title || 'Berhasil!',
                text: data.text || 'Data berhasil dihapus.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });

        // === APEXCHART RENDER ===
        const chartData = @json($chartData);
        if (chartData.months.length > 0) {
            const options = {
                chart: { type: 'line', height: 300 },
                stroke: { width: 2, curve: 'smooth' },
                series: [
                    { name: 'Pemasukan', data: chartData.income },
                    { name: 'Pengeluaran', data: chartData.expense },
                ],
                xaxis: { categories: chartData.months },
                colors: ['#28a745', '#dc3545'],
            };
            new ApexCharts(document.querySelector("#chart"), options).render();
        }
    });
    </script>
    @endpush
</div>
