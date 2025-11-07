<div> {{-- ROOT LIVEWIRE --}}

    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Catatan Keuangan</h4>
            <div>
                <button wire:click="create" class="btn btn-primary">+ Tambah</button>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-secondary ms-2">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        {{-- Filter & search --}}
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <input type="text" wire:model.debounce.500ms="search" placeholder="Cari judul / deskripsi..." class="form-control">
            </div>
            <div class="col-md-3">
                <select wire:model="filterType" class="form-select">
                    <option value="all">Semua</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-2">
                <select wire:model="perPage" class="form-select">
                    <option value="10">10 / halaman</option>
                    <option value="20">20 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
        </div>

        {{-- Chart --}}
        <div class="card mb-3">
            <div class="card-body">
                <div id="finance-chart" style="height: 320px;"></div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Tgl</th>
                                <th>Judul</th>
                                <th>Keterangan</th>
                                <th class="text-end">Jumlah</th>
                                <th>Type</th>
                                <th>Bukti</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finances as $f)
                            <tr>
                                <td>{{ $f->date->format('Y-m-d') }}</td>
                                <td>{{ $f->title }}</td>
                                <td style="max-width: 300px;">{!! \Illuminate\Support\Str::limit(strip_tags($f->description), 120) !!}</td>
                                <td class="text-end">{{ number_format($f->amount,2,',','.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $f->type === 'income' ? 'success' : 'danger' }}">{{ ucfirst($f->type) }}</span>
                                </td>
                                <td>
                                    @if($f->receipt_path)
                                        <a href="{{ asset('storage/' . $f->receipt_path) }}" target="_blank">Lihat</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="edit({{ $f->id }})" class="btn btn-sm btn-outline-primary">Edit</button>
                                    <button wire:click="confirmDelete({{ $f->id }})" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $finances->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal (Bootstrap) --}}
    <div class="modal fade" id="financeModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $financeId ? 'Edit' : 'Tambah' }} Catatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" wire:model.defer="title" class="form-control" />
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input id="description" type="hidden" wire:model.defer="description">
                            <trix-editor input="description"></trix-editor>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label>Jumlah</label>
                                <input type="number" step="0.01" wire:model.defer="amount" class="form-control" />
                                @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-3">
                                <label>Tipe</label>
                                <select wire:model.defer="type" class="form-select">
                                    <option value="income">Pemasukan</option>
                                    <option value="expense">Pengeluaran</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Tanggal</label>
                                <input type="date" wire:model.defer="date" class="form-control" />
                                @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label>Bukti (gambar)</label>
                            <input type="file" wire:model="receipt" class="form-control" accept="image/*" />
                            @if($receipt)
                                <div class="mt-2">Preview: <img src="{{ $receipt->temporaryUrl() }}" style="max-height:120px" /></div>
                            @elseif($existingReceipt)
                                <div class="mt-2">Existing: <a href="{{ asset('storage/' . $existingReceipt) }}" target="_blank">Lihat</a></div>
                            @endif
                            @error('receipt') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">{{ $financeId ? 'Simpan Perubahan' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>

        <script>
            document.addEventListener('livewire:load', function () {
                const financeModalEl = document.getElementById('financeModal');
                const financeModal = new bootstrap.Modal(financeModalEl);

                window.addEventListener('showFinanceModal', () => financeModal.show());
                window.addEventListener('closeFinanceModal', () => financeModal.hide());

                window.addEventListener('swal', event => {
                    Swal.fire({
                        icon: event.detail.icon || 'success',
                        title: event.detail.title || 'Info',
                        text: event.detail.text || '',
                    });
                });

                window.addEventListener('swalConfirm', event => {
                    Swal.fire({
                        title: event.detail.title || 'Hapus?',
                        text: event.detail.text || 'Apakah yakin?',
                        icon: 'warning',
                        showCancelButton: true,
                    }).then(result => {
                        if (result.isConfirmed) {
                            Livewire.emit('deleteConfirmed');
                        }
                    });
                });

                // Chart
                const chartEl = document.querySelector('#finance-chart');
                let options = {
                    chart: { type: 'area', height: 320 },
                    series: [
                        { name: 'Pemasukan', data: @json($chartData['income'] ?? array_fill(0,12,0)) },
                        { name: 'Pengeluaran', data: @json($chartData['expense'] ?? array_fill(0,12,0)) },
                    ],
                    xaxis: {
                        categories: @json($chartData['labels'] ?? array_map(fn($m)=>\Carbon\Carbon::create()->month($m)->format('M'), range(1,12)))
                    },
                    stroke: { curve: 'smooth' },
                    tooltip: { y: { formatter: val => Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val) } }
                };

                let chart = new ApexCharts(chartEl, options);
                chart.render();

                Livewire.hook('message.processed', () => {
                    try {
                        const income = @json($chartData['income'] ?? array_fill(0,12,0));
                        const expense = @json($chartData['expense'] ?? array_fill(0,12,0));
                        chart.updateSeries([
                            { name: 'Pemasukan', data: income },
                            { name: 'Pengeluaran', data: expense },
                        ]);
                    } catch (e) {}
                });

                document.addEventListener('trix-change', function(e) {
                    const el = e.target;
                    const inputId = el.inputElement ? el.inputElement.id : null;
                    if (inputId) {
                        const content = el.editor.getDocument().toString();
                        Livewire.emit('trixUpdated', inputId, content);
                    }
                });
            });
        </script>
    @endpush

</div> {{-- END ROOT --}}
