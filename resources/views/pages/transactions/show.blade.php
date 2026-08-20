<?php

use Livewire\Component;
use App\Models\Transaction;
use App\Models\DebtPayment;

new class extends Component {
    public Transaction $transaction;
    public float $paymentAmount = 0;
    public string $paymentNote = '';

    public function mount(Transaction $transaction): void
    {
        $this->transaction = $transaction->load(['customer', 'items', 'photos', 'debtPayments']);
    }

    public function addPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1|max:' . $this->transaction->remaining_debt,
            'paymentNote' => 'nullable|string',
        ]);

        DebtPayment::create([
            'transaction_id' => $this->transaction->id,
            'payment' => $this->paymentAmount,
            'note' => $this->paymentNote ?: null,
        ]);

        $newRemaining = $this->transaction->remaining_debt - $this->paymentAmount;
        $newPaid = $this->transaction->amount_paid + $this->paymentAmount;

        if ($newRemaining <= 0) {
            $this->transaction->update([
                'amount_paid' => $this->transaction->total,
                'remaining_debt' => 0,
                'status' => 'Lunas',
            ]);
        } else {
            $this->transaction->update([
                'amount_paid' => $newPaid,
                'remaining_debt' => $newRemaining,
            ]);
        }

        $this->paymentAmount = 0;
        $this->paymentNote = '';
        $this->transaction->refresh();
        $this->transaction->load('debtPayments');

        session()->flash('message', 'Pembayaran berhasil dicatat.');
    }

    public function render()
    {
        return view('pages.transactions.⚡show');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Detail Transaksi #{{ $transaction->id }}</h1>
        <a wire:navigate href="{{ route('transactions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            ← Kembali
        </a>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg">{{ session('message') }}</div>
    @endif

    <!-- Info Transaksi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Informasi Transaksi</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Pelanggan</dt>
                    <dd class="font-medium">{{ $transaction->customer?->name ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total</dt>
                    <dd class="font-medium">Rp {{ number_format($transaction->total, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Dibayar</dt>
                    <dd class="font-medium text-green-600">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Sisa Hutang</dt>
                    <dd class="font-medium text-orange-600">Rp {{ number_format($transaction->remaining_debt, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        <span @class([
                            'px-2 py-1 rounded-full text-xs font-medium',
                            'bg-green-100 text-green-800' => $transaction->status === 'Lunas',
                            'bg-yellow-100 text-yellow-800' => $transaction->status === 'Hutang',
                        ])>
                            {{ $transaction->status }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium">{{ $transaction->created_at->format('d M Y H:i') }}</dd>
                </div>
                @if($transaction->note)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Catatan</dt>
                    <dd class="font-medium text-right max-w-xs">{{ $transaction->note }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Add Payment (khusus Hutang) -->
        @if($transaction->status === 'Hutang')
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Catat Pembayaran</h2>
            <form wire:submit="addPayment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                    <input type="number" wire:model="paymentAmount" min="1" max="{{ $transaction->remaining_debt }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Maks: Rp {{ number_format($transaction->remaining_debt, 0, ',', '.') }}</p>
                    @error('paymentAmount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea wire:model="paymentNote" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Bayar</button>
            </form>
        </div>
        @endif
    </div>

    <!-- Items -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Item Barang</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-5 py-3 font-medium text-gray-600">Nama Barang</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Qty</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Harga</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transaction->items as $item)
                    <tr>
                        <td class="px-5 py-3">{{ $item->product_name }}</td>
                        <td class="px-5 py-3">{{ $item->quantity }}</td>
                        <td class="px-5 py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 font-medium">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Debt Payments -->
    @if($transaction->debtPayments->isNotEmpty())
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Riwayat Pembayaran</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-5 py-3 font-medium text-gray-600">Tanggal</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Jumlah</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transaction->debtPayments as $dp)
                    <tr>
                        <td class="px-5 py-3">{{ $dp->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3 font-medium text-green-600">Rp {{ number_format($dp->payment, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">{{ $dp->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Photos -->
    @if($transaction->photos->isNotEmpty())
    <div class="bg-white rounded-lg shadow">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Foto</h2>
        </div>
        <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($transaction->photos as $photo)
            <img src="{{ asset('storage/' . $photo->image) }}" alt="Foto transaksi" class="w-full h-32 object-cover rounded-lg">
            @endforeach
        </div>
    </div>
    @endif
</div>
