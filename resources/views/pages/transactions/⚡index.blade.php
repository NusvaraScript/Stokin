<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Transaction::findOrFail($id)->delete();
        session()->flash('message', 'Transaksi berhasil dihapus.');
    }

    public function render()
    {
        $query = Transaction::with('customer');

        if ($this->search) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $transactions = $query->latest()->paginate(10);

        return view('pages.transactions.⚡index', ['transactions' => $transactions]);
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Transaksi</h1>
        <a wire:navigate href="{{ route('transactions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Transaksi Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 mb-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Cari pelanggan..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
        <select wire:model.live="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Semua Status</option>
            <option value="Lunas">Lunas</option>
            <option value="Hutang">Hutang</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Pelanggan</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Total</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Dibayar</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Sisa</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Tanggal</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transactions as $t)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">#{{ $t->id }}</td>
                    <td class="px-5 py-3">{{ $t->customer?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">Rp {{ number_format($t->amount_paid, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">Rp {{ number_format($t->remaining_debt, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span @class([
                            'px-2 py-1 rounded-full text-xs font-medium',
                            'bg-green-100 text-green-800' => $t->status === 'Lunas',
                            'bg-yellow-100 text-yellow-800' => $t->status === 'Hutang',
                        ])>
                            {{ $t->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3">{{ $t->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right space-x-2">
                        <a wire:navigate href="{{ route('transactions.show', $t->id) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                        <button wire:click="delete({{ $t->id }})" wire:confirm="Yakin hapus transaksi ini?" class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($transactions->isEmpty())
            <p class="text-center py-8 text-gray-500">Belum ada transaksi</p>
        @endif
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
