<x-app-layout>
    <x-slot name="header">
        <h2>Kasir (POS)</h2>
    </x-slot>

    <section class="w-full">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-outline">Manajemen Transaksi</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-on-surface">Kasir (POS)</h1>
            <p class="mt-1 text-sm text-outline">Lakukan penjualan barang langsung ke anggota atau umum.</p>
        </div>

        <div x-data="posApp()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Panel Kiri: Daftar Barang -->
            <div class="lg:col-span-2 space-y-6">
                <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6">
                    <h3 class="text-lg font-bold text-on-surface mb-4">Pilih Barang</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($products as $product)
                            <button @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})"
                                class="dashboard-card p-4 text-left rounded-2xl bg-surface-container hover:bg-surface-container-high transition">
                                <p class="font-bold text-on-surface">{{ $product->name }}</p>
                                <p class="text-sm text-outline">Stok: {{ $product->stock }}</p>
                                <p class="text-sm font-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Panel Kanan: Keranjang -->
            <div class="lg:col-span-1">
                <div class="dashboard-card rounded-3xl bg-surface-container-lowest p-6 sticky top-20">
                    <h3 class="text-lg font-bold text-on-surface mb-4">Keranjang</h3>
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4 mb-6">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate" x-text="item.name"></p>
                                        <p class="text-xs text-outline" x-text="formatRupiah(item.price)"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.id">
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" 
                                            class="w-16 rounded-lg border-outline-variant text-sm p-1" min="1" :max="item.stock">
                                        <button type="button" @click="removeFromCart(index)" class="text-error">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-outline-variant pt-4 mb-6">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span x-text="formatRupiah(total)"></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-primary-container py-3 font-bold text-on-primary hover:opacity-90">
                            Proses Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function posApp() {
            return {
                cart: [],
                addToCart(id, name, price, stock) {
                    const existingItem = this.cart.find(item => item.id === id);
                    if (existingItem) {
                        if (existingItem.quantity < stock) existingItem.quantity++;
                    } else {
                        this.cart.push({ id, name, price, quantity: 1, stock });
                    }
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                formatRupiah(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        }
    </script>
</x-app-layout>
