<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-start gap-6">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded-lg">
                        @endif
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm flex-1">
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">ID</dt>
                                <dd class="mt-1">{{ $product->id }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="mt-1">{{ $product->sku }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                                <dd class="mt-1">{{ $product->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Categoría</dt>
                                <dd class="mt-1">{{ $product->category?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Proveedor</dt>
                                <dd class="mt-1">{{ $product->supplier?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Precio de compra</dt>
                                <dd class="mt-1">${{ number_format($product->purchase_price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Precio de venta</dt>
                                <dd class="mt-1">${{ number_format($product->selling_price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Stock</dt>
                                <dd class="mt-1">
                                    @if ($product->stock <= 5)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ $product->stock }}</span>
                                    @else
                                        {{ $product->stock }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Editar') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>