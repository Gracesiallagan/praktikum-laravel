<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Finance;
use Illuminate\Support\Facades\Auth;

class FinanceForm extends Component
{
    use WithFileUploads;

    public $finance_id;
    public $title;
    public $type = '';
    public $amount;
    public $transaction_date;
    public $description;
    public $cover;
    public $new_cover;

    public function mount($id = null)
    {
        if ($id) {
            $f = Finance::where('user_id', Auth::id())->findOrFail($id);
            $this->finance_id = $f->id;
            $this->title = $f->title;
            $this->type = $f->type;
            $this->amount = $f->amount;
            $this->transaction_date = $f->transaction_date->format('Y-m-d');
            $this->description = $f->description;
            $this->cover = $f->cover;
        } else {
            $this->transaction_date = now()->format('Y-m-d');
        }
    }

    public function save()
    {
        $data = $this->validate([
            'title' => 'required|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'new_cover' => 'nullable|image|max:2048',
        ]);

        if ($this->new_cover) {
            $filename = time().'.'.$this->new_cover->extension();
            $this->new_cover->storeAs('uploads', $filename, 'public');
            $data['cover'] = $filename;
        } elseif ($this->cover) {
            $data['cover'] = $this->cover;
        }

        $data['user_id'] = Auth::id();
        $data['date'] = $data['transaction_date'];
        unset($data['transaction_date']);

      if ($this->finance_id) {
    Finance::where('id', $this->finance_id)
        ->where('user_id', Auth::id())
        ->update($data);
} else {
    Finance::create($data);
}

// 🔹 Gunakan dispatch untuk Livewire 3.6
$this->dispatch('swal:success', [
    'title' => 'Berhasil!',
    'text'  => 'Data berhasil disimpan.',
]);


        return redirect()->route('app.finances.index');
    }

    public function render()
    {
        return view('livewire.finance-form')->layout('layouts.app');
    }
}
