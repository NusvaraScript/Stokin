<?php

use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Expenses;

new class extends Component {
    public int $totalCustomers = 0;
    public int $totalTransactions = 0;
    public float $totalRevenue = 0;
    public float $totalExpenses = 0;
    public float $totalDebt = 0;
    public $recentTransactions;

    public function mount(): void
    {
        $this->totalCustomers = Customer::count();
        $this->totalTransactions = Transaction::count();
        $this->totalRevenue = Transaction::where('status', 'Lunas')->sum('total');
        $this->totalExpenses = Expenses::sum('amount');
        $this->totalDebt = Transaction::where('status', 'Hutang')->sum('remaining_debt');
        $this->recentTransactions = Transaction::with('customer')
            ->latest()
            ->take(10)
            ->get();
    }
};
?>

<div>
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Pelanggan</p>
            <p class="text-3xl font-bold mt-1">{{ number_format($totalCustomers) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Transaksi</p>
            <p class="text-3xl font-bold mt-1">{{ number_format($totalTransactions) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Pendapatan</p>
            <p class="text-3xl font-bold mt-1 text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Pengeluaran</p>
            <p class="text-3xl font-bold mt-1 text-red-600">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500 uppercase tracking-wider">Total Hutang</p>
            <p class="text-3xl font-bold mt-1 text-orange-600">Rp {{ number_format($totalDebt, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Transaksi Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-5 py-3 font-medium text-gray-600">Pelanggan</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Total</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Status</th>
                        <th class="px-5 py-3 font-medium text-gray-600">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentTransactions as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">{{ $t->customer?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-medium',
                                'bg-green-100 text-green-800' => $t->status === 'Lunas',
                                'bg-yellow-100 text-yellow-800' => $t->status === 'Hutang',
                            ])>
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3">{{ $t->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($recentTransactions->isEmpty())
            <p class="text-center py-8 text-gray-500">Belum ada transaksi</p>
        @endif
    </div>
</div>
