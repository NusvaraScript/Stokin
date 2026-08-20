<?php

use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;

new class extends Component {
    public string $customer_id = '';
    public string $note = '';
    public array $items = [];
    public float $total = 0;

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ];
    }

    public function mount(): void
    {
        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_name' => '',
            'quantity' => 1,
            'price' => 0,
            'total' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateTotal();
    }

    public function updatedItems($value, $key): void
    {
        if (str_contains((string) $key, 'quantity') || str_contains((string) $key, 'price')) {
            $this->recalculateItemTotal($key);
        }
    }

    private function recalculateItemTotal(string $key): void
    {
        $parts = explode('.', (string) $key);
        $index = (int) $parts[0];

        if (isset($this->items[$index])) {
            $item = $this->items[$index];
            $this->items[$index]['total'] = (float) ($item['quantity'] * $item['price']);
            $this->recalculateTotal();
        }
    }

    private function recalculateTotal(): void
    {
        $this->total = array_reduce($this->items, function ($carry, $item) {
            return $carry + (float) ($item['total'] ?? 0);
        }, 0);
    }

    public function save(): void
    {
        $this->validate();

        $transaction = Transaction::create([
            'customer_id' => $this->customer_id,
            'total' => $this->total,
            'status' => 'Hutang',
            'amount_paid' => 0,
            'remaining_debt' => $this->total,
            'note' => $this->note ?: null,
        ]);

        foreach ($this->items as $item) {
            $transaction->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        session()->flash('message', 'Transaksi berhasil dibuat.');
        $this->redirect(route('transactions.index'), navigate: true);
    }

    public function render()
    {
        return view('pages.transactions.⚡create', [
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Transaksi Baru</h1>
        <a wire:navigate href="{{ route('transactions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
            ← Kembali
        </a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Customer -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Data Pelanggan</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                <select wire:model="customer_id" class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Pelanggan</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                    @endforeach
                </select>
                @error('customer_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Item Barang</h2>
                <button type="button" wire:click="addItem" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    + Tambah Item
                </button>
            </div>
            @error('items') <p class="text-sm text-red-600 mb-3">{{ $message }}</p> @enderror

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-gray-600">
                        <th class="px-3 py-2 font-medium">Nama Barang</th>
                        <th class="px-3 py-2 font-medium w-24">Qty</th>
                        <th class="px-3 py-2 font-medium w-36">Harga</th>
                        <th class="px-3 py-2 font-medium w-36">Subtotal</th>
                        <th class="px-3 py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $index => $item)
                    <tr>
                        <td class="px-3 py-2">
                            <input type="text" wire:model="items.{{ $index }}.product_name" placeholder="Nama barang" class="w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error("items.{$index}.product_name") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="items.{{ $index }}.quantity" min="1" class="w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" wire:model.live="items.{{ $index }}.price" min="0" step="0.01" class="w-full px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </td>
                        <td class="px-3 py-2 font-medium">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2">
                            @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Ringkasan</h2>
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea wire:model="note" rows="2" class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Catatan transaksi (opsional)"></textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a wire:navigate href="{{ route('transactions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Transaksi</button>
        </div>
    </form>
</div>
