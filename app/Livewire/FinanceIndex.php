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

    // Listener untuk event dari JavaScript
    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Finance::where('user_id', Auth::id())
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->latest();

        $finances = $query->paginate($this->perPage);
        $chartData = $this->getChartData();

        return view('livewire.finance-index', compact('finances', 'chartData'))
               ->layout('layouts.app');
    }

    /**
     * Panggil SweetAlert konfirmasi
     */
    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal:confirm', [
            'id' => $id,
            'title' => 'Hapus Data?',
            'text' => 'Data ini akan dihapus permanen.',
        ]);
    }

    /**
     * Eksekusi penghapusan setelah konfirmasi
     */
    public function delete($payload = null)
    {
        $id = null;

        // Ambil ID dari berbagai kemungkinan format event
        if (is_array($payload) && isset($payload['id'])) {
            $id = $payload['id'];
        } elseif (is_object($payload) && isset($payload->id)) {
            $id = $payload->id;
        } elseif (is_scalar($payload)) {
            $id = $payload;
        } elseif ($this->deleteId) {
            $id = $this->deleteId;
        }

        if (!$id) {
            $this->dispatch('swal:success', [
                'title' => 'Gagal!',
                'text' => 'ID tidak ditemukan.',
            ]);
            return;
        }

        Finance::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text'  => 'Data berhasil dihapus.',
        ]);

        $this->resetPage();
    }

    /**
     * Ambil data untuk chart (agar tidak error di Postgres)
     */
    protected function getChartData(): array
    {
        $col = Schema::hasColumn('finances', 'transaction_date') ? 'transaction_date' : 'created_at';

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
            return ['months' => [], 'income' => [], 'expense' => []];
        }

        return [
            'months' => $data->pluck('month')->map(fn($m) => date('M', mktime(0,0,0,$m,1)))->toArray(),
            'income' => $data->pluck('income')->toArray(),
            'expense' => $data->pluck('expense')->toArray(),
        ];
    }
}
