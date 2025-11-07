<div style="background: linear-gradient(180deg, #f0f6ff, #ffffff); min-height: 100vh; padding: 20px; border-radius: 12px;">

    {{-- ===================== HEADER ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 shadow-sm rounded bg-white border border-light">
    <h4 class="fw-bold text-primary m-0">
        <i class="bi bi-journal-text me-2"></i> Catatan Keuangan
    </h4>

    <div class="d-flex gap-2">
        <a href="{{ route('app.finances.create') }}" class="btn btn-primary shadow-sm px-3" wire:navigate>
            <i class="bi bi-plus-circle me-1"></i> Tambah
        </a>
        <button wire:click="logout" class="btn btn-outline-danger shadow-sm px-3">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </div>
</div>


    {{-- ===================== FILTER & SEARCH ===================== --}}
    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-primary text-white border-primary">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" wire:model.live="search" class="form-control border-primary"
                       placeholder="Cari judul catatan...">
            </div>
        </div>
        <div class="col-md-3">
            <select wire:model="filterType" class="form-select border-primary shadow-sm">
                <option value="">Semua Tipe</option>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
            </select>
        </div>
    </div>

    

    {{-- ===================== TABLE ===================== --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-center bg-primary text-white">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Cover</th>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <th style="width: 180px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($finances as $index => $f)
                        <tr class="animate__animated animate__fadeIn">
                            <td class="text-center text-muted fw-semibold">
                                {{ $finances->firstItem() + $index }}
                            </td>
                            <td class="text-center">
                                @if($f->receipt_path)
                                    <img src="{{ asset('storage/uploads/' . $f->receipt_path) }}" 
                                         alt="cover" width="60" height="60"
                                         class="rounded shadow-sm border">
                                @else
                                    <i class="bi bi-image text-muted fs-4"></i>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $f->title }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2 {{ $f->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $f->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">
                                Rp {{ number_format($f->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center text-secondary">
                                {{ \Carbon\Carbon::parse($f->date)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('app.finances.edit', $f->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                                    <i class="bi bi-pencil-square me-1"></i> Ubah
                                </a>
                                <button 
                                    type="button"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    wire:click="confirmDelete({{ $f->id }})">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-frown fs-3 d-block mb-2"></i>
                                Belum ada data catatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== PAGINATION ===================== --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $finances->links() }}
    </div>

    {{-- ===================== CHART ===================== --}}
    <div class="mt-5 p-4 bg-white rounded-4 shadow-sm">
        <h5 class="text-primary fw-bold mb-3">
            <i class="bi bi-graph-up me-2"></i> Statistik Keuangan
        </h5>
        <div wire:ignore id="chart" style="height: 320px;"></div>
    </div>

    {{-- ===================== FOOTER INFO ===================== --}}
    <div class="text-center mt-5 text-muted small">
        <i class="bi bi-wallet2 me-1"></i> Sistem Catatan Keuangan - IT Del
    </div>

    {{-- ===================== SCRIPTS ===================== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
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

            // === CHART ===
            let chart;
            const renderChart = (chartData) => {
                const options = {
                    chart: { type: 'line', height: 300, toolbar: { show: false } },
                    stroke: { width: 3, curve: 'smooth' },
                    series: [
                        { name: 'Pemasukan', data: chartData.income },
                        { name: 'Pengeluaran', data: chartData.expense },
                    ],
                    xaxis: { categories: chartData.months, labels: { style: { colors: '#0d6efd' } } },
                    yaxis: {
                        labels: {
                            formatter: val => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    colors: ['#0d6efd', '#dc3545'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    grid: { borderColor: '#e9ecef' },
                };

                if (chart) {
                    chart.updateOptions(options);
                } else {
                    chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                }
            };

            renderChart(@json($chartData));
            Livewire.on('chartDataUpdated', (data) => renderChart(data));
        });
    </script>
    @endpush
</div>
