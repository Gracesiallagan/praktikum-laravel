<?php

namespace App\Livewire;

use App\Models\Finance;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FinanceLivewire extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterType = 'all';
    public $perPage = 20;

    public $financeId;
    public $title;
    public $description;
    public $amount;
    public $type = 'income';
    public $date;
    public $receipt;
    public $existingReceipt;

    public $deleteId;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'type' => 'required|in:income,expense',
        'date' => 'required|date',
        'receipt' => 'nullable|image|max:2048',
    ];

    protected $listeners = ['deleteConfirmed', 'trixUpdated' => 'updateTrixField'];

    public function mount()
    {
        $this->date = now()->toDateString();
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function updateTrixField($field, $value)
    {
        $this->$field = $value;
    }

    public function resetForm()
    {
        $this->reset(['financeId', 'title', 'description', 'amount', 'type', 'date', 'receipt', 'existingReceipt']);
        $this->date = now()->toDateString();
    }

    // Tampilkan modal tambah
    public function create()
    {
        $this->resetForm();
        $this->dispatch('showFinanceModal'); // Livewire 12.x compatible
    }

    // Tampilkan modal edit
    public function edit($id)
    {
        $f = Finance::where('user_id', Auth::id())->findOrFail($id);
        $this->financeId = $f->id;
        $this->title = $f->title;
        $this->description = $f->description;
        $this->amount = $f->amount;
        $this->type = $f->type;
        $this->date = $f->date->format('Y-m-d');
        $this->existingReceipt = $f->receipt_path;

        $this->dispatch('showFinanceModal'); // Livewire 12.x compatible
    }

    // Simpan data finance
    public function save()
    {
        $this->validate();

        $path = $this->existingReceipt;

        if ($this->receipt) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $this->receipt->store('receipts', 'public');
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'amount' => $this->amount,
            'type' => $this->type,
            'date' => $this->date,
            'receipt_path' => $path,
        ];

        if ($this->financeId) {
            Finance::where('id', $this->financeId)->where('user_id', Auth::id())->update($data);
            $message = 'Data berhasil diperbarui.';
        } else {
            Auth::user()->finances()->create($data);
            $message = 'Data berhasil ditambahkan.';
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Sukses',
            'text' => $message,
        ]);

        $this->resetForm();
    }

    // Konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('swalConfirm', [
            'title' => 'Hapus data?',
            'text' => 'Data akan dihapus permanen.',
        ]);
    }

    // Hapus data setelah konfirmasi
    public function deleteConfirmed()
    {
        $f = Finance::where('user_id', Auth::id())->find($this->deleteId);
        if ($f) {
            if ($f->receipt_path) Storage::disk('public')->delete($f->receipt_path);
            $f->delete();

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data dihapus.',
            ]);
        }
    }

    // Data chart
    public function getChartDataProperty()
    {
        $data = Finance::where('user_id', Auth::id())
            ->selectRaw("EXTRACT(MONTH FROM COALESCE(date, created_at)) AS month,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS expense")
            ->groupByRaw('EXTRACT(MONTH FROM COALESCE(date, created_at))')
            ->orderByRaw('EXTRACT(MONTH FROM COALESCE(date, created_at))')
            ->get();

        $income = array_fill(0, 12, 0);
        $expense = array_fill(0, 12, 0);

        foreach ($data as $row) {
            $m = (int) $row->month;
            if ($m >= 1 && $m <= 12) {
                $income[$m-1] = (float) $row->income;
                $expense[$m-1] = (float) $row->expense;
            }
        }

        $labels = array_map(fn($m) => Carbon::create()->month($m)->format('M'), range(1,12));

        return [
            'labels' => $labels,
            'income' => $income,
            'expense' => $expense,
        ];
    }

    public function render()
    {
        $query = Finance::where('user_id', Auth::id());

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'ilike', "%{$this->search}%")
                  ->orWhere('description', 'ilike', "%{$this->search}%");
            });
        }

        if (in_array($this->filterType, ['income','expense'])) {
            $query->where('type', $this->filterType);
        }

        $finances = $query->orderByDesc('date')->paginate($this->perPage);

        return view('livewire.finance-livewire', [
            'finances' => $finances,
            'chartData' => $this->chartData,
        ])->layout('layouts.app');
    }
}
