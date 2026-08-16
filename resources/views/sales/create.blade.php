<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Punto de Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sales.store') }}" id="sale-form">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                            <div>
                                <x-input-label for="customer_id" value="Cliente" />
                                <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Seleccionar cliente</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="product-select" value="Agregar producto" />
                                <select id="product-select" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">
                                            {{ $product->name }} ({{ $product->sku }}) - ${{ number_format($product->selling_price, 2) }} - Stock: {{ $product->stock }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 flex items-center gap-3">
                                    <x-text-input id="quantity-input" type="number" min="1" value="1" class="block w-32" />
                                    <button type="button" id="add-item" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Agregar') }}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-3">Carrito</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="cart-table">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Producto</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Precio</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Cantidad</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Subtotal</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-body" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                                    </table>
                                    <div id="cart-empty" class="py-3 text-sm text-gray-500 dark:text-gray-400">No hay productos en el carrito.</div>
                                </div>

                                <p class="mt-4 text-lg font-semibold">Total: $<span id="cart-total">0</span></p>
                                <div id="hidden-items"></div>
                                @if ($errors->has('items'))
                                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        @foreach ($errors->get('items') as $message)
                                            <p>{{ $message }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" id="submit-sale" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Registrar venta') }}
                                </button>
                                <a href="{{ route('sales.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Volver</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-3">Productos disponibles</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">SKU</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Nombre</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Precio</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($products as $product)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3">{{ $product->sku }}</td>
                                                <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                                                <td class="px-4 py-3">${{ number_format($product->selling_price, 2) }}</td>
                                                <td class="px-4 py-3">{{ $product->stock }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const cart = {};

        document.getElementById('add-item').addEventListener('click', () => {
            const select = document.getElementById('product-select');
            const quantity = parseInt(document.getElementById('quantity-input').value, 10);

            if (!select.value || !quantity || quantity < 1) {
                alert('Selecciona un producto y una cantidad valida');
                return;
            }

            const option = select.options[select.selectedIndex];
            const price = parseFloat(option.dataset.price);

            if (quantity > parseInt(option.dataset.stock, 10)) {
                alert('Stock insuficiente');
                return;
            }

            if (cart[select.value]) {
                cart[select.value].quantity = Math.min(cart[select.value].quantity + quantity, parseInt(option.dataset.stock, 10));
            } else {
                cart[select.value] = {
                    id: select.value,
                    name: option.textContent.split(' - ')[0],
                    price: price,
                    quantity: quantity,
                };
            }

            renderCart();
        });

        function renderCart() {
            const body = document.getElementById('cart-body');
            const empty = document.getElementById('cart-empty');
            const hidden = document.getElementById('hidden-items');
            let total = 0;

            body.innerHTML = '';
            hidden.innerHTML = '';

            const items = Object.values(cart);

            if (items.length === 0) {
                empty.style.display = 'block';
            } else {
                empty.style.display = 'none';
            }

            items.forEach((item) => {
                const subtotal = item.price * item.quantity;
                total += subtotal;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">${item.name}</td>
                    <td class="px-4 py-3">$${item.price.toFixed(2)}</td>
                    <td class="px-4 py-3">${item.quantity}</td>
                    <td class="px-4 py-3">$${subtotal.toFixed(2)}</td>
                    <td class="px-4 py-3"><button type="button" onclick="removeItem(${item.id})" class="text-red-600 dark:text-red-400 hover:underline">Quitar</button></td>
                `;
                body.appendChild(row);

                hidden.innerHTML += `
                    <input type="hidden" name="items[${item.id}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">
                `;
            });

            document.getElementById('cart-total').textContent = total.toFixed(2);
        }

        function removeItem(id) {
            delete cart[id];
            renderCart();
        }
    </script>
</x-app-layout>