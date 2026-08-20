<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editId = null;
    public string $name = '';
    public string $phone = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->editId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone ?? '';
        $this->address = $customer->address ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            $customer = Customer::findOrFail($this->editId);
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
            ]);
            session()->flash('message', 'Pelanggan berhasil diperbarui.');
        } else {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->phone ?: null,
                'address' => $this->address ?: null,
            ]);
            session()->flash('message', 'Pelanggan berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Customer::findOrFail($id)->delete();
        session()->flash('message', 'Pelanggan berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->showModal = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', "%{$this->search}%")
            ->orWhere('phone', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->paginate(10);

        return view('pages.customers.⚡index', ['customers' => $customers]);
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Pelanggan</h1>
        <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Tambah Pelanggan
        </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Cari pelanggan..."
            class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">Nama</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Telepon</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Alamat</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Tanggal Daftar</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium">{{ $customer->name }}</td>
                    <td class="px-5 py-3">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-5 py-3 max-w-xs truncate">{{ $customer->address ?? '-' }}</td>
                    <td class="px-5 py-3">{{ $customer->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right space-x-2">
                        <button wire:click="edit({{ $customer->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                        <button wire:click="delete({{ $customer->id }})" wire:confirm="Yakin ingin menghapus pelanggan ini?" class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($customers->isEmpty())
            <p class="text-center py-8 text-gray-500">Tidak ada pelanggan</p>
        @endif
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="resetForm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">{{ $editId ? 'Edit' : 'Tambah' }} Pelanggan</h2>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" wire:model="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea wire:model="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
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
