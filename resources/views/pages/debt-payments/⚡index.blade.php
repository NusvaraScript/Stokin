<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DebtPayment;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $payment = DebtPayment::findOrFail($id);
        $transaction = $payment->transaction;

        if ($transaction) {
            $newPaid = $transaction->amount_paid - $payment->payment;
            $newRemaining = $transaction->remaining_debt + $payment->payment;

            $transaction->update([
                'amount_paid' => max(0, $newPaid),
                'remaining_debt' => max(0, $newRemaining),
                'status' => $newRemaining > 0 ? 'Hutang' : $transaction->status,
            ]);
        }

        $payment->delete();
        session()->flash('message', 'Pembayaran berhasil dihapus.');
    }

    public function render()
    {
        $payments = DebtPayment::with('transaction.customer')
            ->whereHas('transaction', function ($q) {
                if ($this->search) {
                    $q->whereHas('customer', function ($sq) {
                        $sq->where('name', 'like', "%{$this->search}%");
                    });
                }
            })
            ->orWhere('note', 'like', "%{$this->search}%")
            ->latest()
            ->paginate(15);

        return view('pages.debt-payments.⚡index', ['payments' => $payments]);
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Pembayaran Hutang</h1>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg">{{ session('message') }}</div>
    @endif

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
                    <th class="px-5 py-3 font-medium text-gray-600">Tanggal</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Pelanggan</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Transaksi</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Jumlah</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Catatan</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">{{ $p->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-3">{{ $p->transaction?->customer?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3">
                        <a wire:navigate href="{{ route('transactions.show', $p->transaction_id) }}" class="text-blue-600 hover:text-blue-800">#{{ $p->transaction_id }}</a>
                    </td>
                    <td class="px-5 py-3 font-medium text-green-600">Rp {{ number_format($p->payment, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">{{ $p->note ?? '-' }}</td>
                    <td class="px-5 py-3 text-right">
                        <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus pembayaran ini?" class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($payments->isEmpty())
            <p class="text-center py-8 text-gray-500">Belum ada pembayaran</p>
        @endif
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
