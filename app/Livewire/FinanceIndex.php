<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FinanceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $perPage = 20;
    public $deleteId;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

  public function render()
{
    $query = Finance::where('user_id', Auth::id())
        ->when($this->search, fn($q) =>
            $q->where('title', 'ILIKE', "%{$this->search}%") // agar tidak case-sensitive di PostgreSQL
        )
        ->when($this->filterType !== '', fn($q) =>
            $q->where('type', $this->filterType)
        )
        ->orderByDesc('date');

    // ✅ Pagination 20 data per halaman (sudah dari $perPage)
    $finances = $query->paginate($this->perPage);

    // ✅ Grafik tetap bekerja seperti semula
    $chartData = $this->getChartData();

    return view('livewire.finance-index', compact('finances', 'chartData'))
        ->layout('layouts.app');
}


    /** 🔥 Konfirmasi hapus data */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', [
            'id' => $id,
            'title' => 'Hapus Data?',
            'text' => 'Data ini akan dihapus permanen.',
        ]);
    }

    /** 🗑️ Eksekusi hapus data setelah konfirmasi */
    public function delete($payload = null)
    {
        $id = $payload['id'] ?? $payload->id ?? $payload ?? $this->deleteId;

        if (!$id) {
            $this->dispatch('swal:success', [
                'title' => 'Gagal!',
                'text'  => 'ID tidak ditemukan.',
            ]);
            return;
        }

        Finance::where('id', $id)->where('user_id', Auth::id())->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => 'Data berhasil dihapus.',
        ]);

        $this->resetPage();
    }

    /** 📈 Data untuk chart (PostgreSQL aman) */
    protected function getChartData(): array
    {
        $col = Schema::hasColumn('finances', 'date')
            ? 'date'
            : (Schema::hasColumn('finances', 'transaction_date') ? 'transaction_date' : 'created_at');

        $data = Finance::selectRaw("
            extract(month from \"$col\")::int as month,
            SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
        ")
        ->whereYear($col, now()->year)
        ->where('user_id', Auth::id())
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        if ($data->isEmpty()) {
            return [
                'months' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                'income' => array_fill(0, 12, 0),
                'expense' => array_fill(0, 12, 0),
            ];
        }

        return [
            'months' => $data->pluck('month')->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)))->toArray(),
            'income' => $data->pluck('income')->toArray(),
            'expense' => $data->pluck('expense')->toArray(),
        ];
    }
}
