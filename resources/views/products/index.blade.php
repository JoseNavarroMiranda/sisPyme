<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Productos') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.export-excel') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Exportar CSV') }}
                </a>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Nuevo producto') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('products.import-csv') }}" method="POST" enctype="multipart/form-data" class="mb-4 p-4 flex items-center gap-3 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                @csrf
                <label for="import-file" class="text-sm font-medium text-gray-700 dark:text-gray-300">Importar CSV</label>
                <input id="import-file" type="file" name="file" required class="text-sm text-gray-700 dark:text-gray-300">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Importar') }}
                </button>
            </form>

            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/50 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($products->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay productos.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Imagen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">SKU</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Nombre</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Categoría</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Proveedor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Precio venta</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Stock</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($products as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3">
                                                @if ($product->image_path)
                                                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover rounded-md">
                                                @else
                                                    <span class="inline-block h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-600"></span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $product->sku }}</td>
                                            <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                                            <td class="px-4 py-3">{{ $product->category?->name ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $product->supplier?->name ?? '—' }}</td>
                                            <td class="px-4 py-3">${{ number_format($product->selling_price, 2) }}</td>
                                            <td class="px-4 py-3">
                                                @if ($product->stock <= 5)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">{{ $product->stock }}</span>
                                                @else
                                                    {{ $product->stock }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('products.show', $product) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ver</a>
                                                    <a href="{{ route('products.edit', $product) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar el producto "{{ $product->name }}"?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>