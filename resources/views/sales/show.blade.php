<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ticket de venta #') . $sale->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Cliente</dt>
                            <dd class="mt-1">{{ $sale->customer?->first_name }} {{ $sale->customer?->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Fecha</dt>
                            <dd class="mt-1">{{ $sale->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Estatus</dt>
                            <dd class="mt-1">
                                @if ($sale->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Completada</span>
                                @elseif ($sale->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">Cancelada</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">{{ ucfirst($sale->status) }}</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <h3 class="mt-6 text-lg font-semibold mb-3">Detalles</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Producto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Precio</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Cantidad</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($sale->details as $detail)
                                    <tr>
                                        <td class="px-4 py-3">{{ $detail->product?->name }}</td>
                                        <td class="px-4 py-3">{{ $detail->product?->sku }}</td>
                                        <td class="px-4 py-3">${{ number_format($detail->price, 2) }}</td>
                                        <td class="px-4 py-3">{{ $detail->quantity }}</td>
                                        <td class="px-4 py-3">${{ number_format($detail->price * $detail->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-4 text-lg font-semibold">Total: ${{ number_format($sale->total_amount, 2) }}</p>

                    @if ($sale->status === 'completed')
                        <div class="mt-6">
                            <form method="POST" action="{{ route('sales.cancel', $sale) }}" onsubmit="return confirm('¿Cancelar la venta #{{ $sale->id }}?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Cancelar venta') }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('sales.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>