<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expenses;

new class extends Component {
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;
    public string $category = '';
    public string $description = '';
    public float $amount = 0;

    protected function rules(): array
    {
        return [
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $expense = Expenses::findOrFail($id);
        $this->editId = $expense->id;
        $this->category = $expense->category;
        $this->description = $expense->description ?? '';
        $this->amount = (float) $expense->amount;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            Expenses::findOrFail($this->editId)->update([
                'category' => $this->category,
                'description' => $this->description ?: null,
                'amount' => $this->amount,
            ]);
            session()->flash('message', 'Pengeluaran berhasil diperbarui.');
        } else {
            Expenses::create([
                'category' => $this->category,
                'description' => $this->description ?: null,
                'amount' => $this->amount,
            ]);
            session()->flash('message', 'Pengeluaran berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Expenses::findOrFail($id)->delete();
        session()->flash('message', 'Pengeluaran berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->category = '';
        $this->description = '';
        $this->amount = 0;
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $expenses = Expenses::latest()->paginate(10);

        return view('pages.expenses.⚡index', ['expenses' => $expenses]);
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Pengeluaran</h1>
        <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Tambah Pengeluaran
        </button>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg">{{ session('message') }}</div>
    @endif

    <!-- Summary -->
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <p class="text-sm text-gray-500 uppercase tracking-wider">Total Pengeluaran</p>
        <p class="text-3xl font-bold text-red-600 mt-1">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Tanggal</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Kategori</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Deskripsi</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Jumlah</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($expenses as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">{{ $e->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ $e->category }}
                        </span>
                    </td>
                    <td class="px-5 py-3">{{ $e->description ?? '-' }}</td>
                    <td class="px-5 py-3 font-medium text-red-600">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right space-x-2">
                        <button wire:click="edit({{ $e->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                        <button wire:click="delete({{ $e->id }})" wire:confirm="Yakin hapus pengeluaran ini?" class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($expenses->isEmpty())
            <p class="text-center py-8 text-gray-500">Belum ada pengeluaran</p>
        @endif
    </div>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="resetForm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">{{ $editId ? 'Edit' : 'Tambah' }} Pengeluaran</h2>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select wire:model="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Stok">Stok Barang</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    @error('category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <input type="text" wire:model="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                    <input type="number" wire:model="amount" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
