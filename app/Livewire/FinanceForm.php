<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Finance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinanceForm extends Component
{
    use WithFileUploads;

    public $finance_id;
    public $title;
    public $type = '';
    public $amount;
    public $date;
    public $description;
    public $cover;      // nama file lama (jika edit)
    public $new_cover;  // file baru di-upload

    protected $rules = [
        'title'       => 'required|string|max:255',
        'type'        => 'required|in:income,expense',
        'amount'      => 'required|numeric|min:0',
        'date'        => 'required|date',
        'description' => 'nullable|string',
        'new_cover'   => 'nullable|image|max:5120',

    ];

    /**
     * Mount form untuk tambah / edit.
     */
    public function mount($id = null)
    {
        if ($id) {
            $f = Finance::where('user_id', Auth::id())->findOrFail($id);

            $this->finance_id  = $f->id;
            $this->title       = $f->title;
            $this->type        = $f->type;
            $this->amount      = $f->amount;
            $this->date        = $f->date ? date('Y-m-d', strtotime($f->date)) : null;
            $this->description = $f->description;
            $this->cover       = $f->receipt_path ?? $f->cover ?? null;
        } else {
            $this->date = null;
        }
    }

    /**
     * Simpan (create/update) data.
     */
    public function save()
    {
        $this->validate();

        $data = [
            'user_id'     => Auth::id(),
            'title'       => $this->title,
            'amount'      => $this->amount,
            'type'        => $this->type,
            'date'        => $this->date,
            'description' => $this->description,
        ];

        // Upload gambar cover baru (jika ada)
        if ($this->new_cover) {
            // hapus cover lama jika ada dan file-nya ada di storage
            if ($this->cover && Storage::disk('public')->exists('uploads/' . $this->cover)) {
                Storage::disk('public')->delete('uploads/' . $this->cover);
            }

            $filename = time() . '_' . uniqid() . '.' . $this->new_cover->extension();
            $this->new_cover->storeAs('uploads', $filename, 'public');
            $data['receipt_path'] = $filename;
        } elseif ($this->cover) {
            $data['receipt_path'] = $this->cover;
        }

        if ($this->finance_id) {
            // Update data lama
            Finance::where('id', $this->finance_id)
                ->where('user_id', Auth::id())
                ->update($data);

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Data berhasil diperbarui.',
            ]);
        } else {
            // Tambah data baru
            Finance::create($data);

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => 'Data baru berhasil ditambahkan.',
            ]);
        }

        // Redirect kembali ke halaman daftar
        return redirect()->route('app.finances.index');
    }

    public function render()
    {
        return view('livewire.finance-form')->layout('layouts.app');
    }
}
