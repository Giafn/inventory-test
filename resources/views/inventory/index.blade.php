<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                Inventory
            </h2>
        </div>
    </x-slot>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex mb-4 justify-end">
            <div class="flex gap-2">
                @if (Auth::user()->hasPermission('inventory-export'))
                <x-button variant="black" x-data="" x-on:click.prevent="$dispatch('open-modal', 'export-modal')"
                    size="sm" class="dark:bg-white dark:text-black dark:hover:bg-gray-200 dark:hover:text-black">
                    Export
                </x-button>
                @endif
                @if (Auth::user()->hasPermission('inventory-manage'))
                <x-button variant="black" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-modal')"
                    size="sm" class="dark:bg-white dark:text-black dark:hover:bg-gray-200 dark:hover:text-black">
                    Add Item
                </x-button>
                @endif
            </div>
        </div>
        <table class="table-auto w-full border-collapse border" id="dataTable">
            <thead>
                <tr>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">No</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Code</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Name</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Price</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Stock</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1" width="105">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    @if (Auth::user()->hasPermission('inventory-manage'))
    <x-modal name="add-modal" :show="$errors->addItem->isNotEmpty()" focusable>
        <form method="post" action="{{ route('inventory.create') }}" class="p-6">
            @csrf
            @method('post')
            <h2 class="text-lg font-medium">
                Create Item
            </h2>
            <div class="mt-6 space-y-1">
                <x-form.label for="code" value="Code" />
                <x-form.input id="code" name="code" type="text" class="block w-full" placeholder="Code" value="{{ old('code') }}" />
                <x-form.error class="errorMessage" :messages="$errors->addItem->get('code')" />
                <x-form.label for="name" value="Name" />
                <x-form.input id="name" name="name" type="text" class="block w-full" placeholder="Name" value="{{ old('name') }}" />
                <x-form.error class="errorMessage" :messages="$errors->addItem->get('name')" />
                <x-form.label for="price" value="Price (in rupiah)" />
                <x-form.input id="price" name="price" type="number" class="block w-full" placeholder="Price" value="{{ old('price') }}" />
                <x-form.error class="errorMessage" :messages="$errors->addItem->get('price')" />
                <x-form.label for="stock" value="Stock" />
                <x-form.input id="stock" name="stock" type="number" class="block w-full" placeholder="Stock" value="{{ old('stock') }}" />
                <x-form.error class="errorMessage" :messages="$errors->addItem->get('stock')" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                <x-button variant="success" class="ml-3">
                    Create
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif
    
    <x-modal name="edit-modal" :show="$errors->editItem->isNotEmpty()" focusable>
        <form method="post" class="p-6" x-data="" id="editForm">
            @csrf
            @method('patch')
            <h2 class="text-lg font-medium">
                {{ Auth::user()->hasPermission('inventory-manage') ? 'Edit Item' : 'View Item' }}
            </h2>
            <div class="mt-6 space-y-2">
                <input type="hidden" name="id" id="idEdit" value="" />
                <div class="space-y-1">
                    <x-form.label for="code" value="Code" />
                    <x-form.input id="codeEdit" name="code" type="text" class="block w-full edit-field"
                        placeholder="Code" value="{{ old('code') }}" />
                    <x-form.error class="errorMessage" :messages="$errors->editItem->get('code')" />
                </div>
                <div class="space-y-1">
                    <x-form.label for="name" value="Name" />
                    <x-form.input id="nameEdit" name="name" type="text" class="block w-full edit-field"
                        placeholder="Name" value="{{ old('name') }}" />
                    <x-form.error class="errorMessage" :messages="$errors->editItem->get('name')" />
                </div>
                <div class="space-y-1">
                    <x-form.label for="price" value="Price (in rupiah)" />
                    <x-form.input id="priceEdit" name="price" type="number" step="any"
                        class="block w-full edit-field" placeholder="Price" value="{{ old('price') }}" />
                    <x-form.error class="errorMessage" :messages="$errors->editItem->get('price')" />
                </div>
                <div class="space-y-1">
                    <x-form.label for="stock" value="Stock" />
                    <x-form.input id="stockEdit" name="stock" type="number" class="block w-full edit-field"
                        placeholder="Stock" value="{{ old('stock') }}" />
                    <x-form.error class="errorMessage" :messages="$errors->editItem->get('stock')" />
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                @if (Auth::user()->hasPermission('inventory-manage'))
                <x-button variant="success" class="ml-3">
                    Edit
                </x-button>
                @endif
            </div>
        </form>
    </x-modal>

    @if (Auth::user()->hasPermission('inventory-manage'))
    <x-modal name="delete-modal" :show="false" focusable>
        <form method="post" class="p-6" x-data="" id="deleteForm">
            @csrf
            @method('delete')
            <h2 class="text-lg font-medium">
                Delete Item
            </h2>
            <div class="mt-6 space-y-2">
                <input type="hidden" name="id" id="idDelete" value="" />
                <p>Are you sure you want to delete this item?</p>
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                <x-button variant="danger" class="ml-3">
                    Delete
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif

    @if (Auth::user()->hasPermission('inventory-export'))
    <x-modal name="export-modal" :show="false">
        <div class="p-4">
            <h2 class="text-lg font-medium">
                Export
            </h2>
            <div class="mt-6 space-y-2">
                <x-form.label for="type" value="Type" />
                <x-form.select id="typeExport" class="w-full" aria-label="Type" name="type">
                    <option value="Excel">Excel</option>
                    <option value="PDF">PDF</option>
                    <option value="CSV">CSV</option>
                </x-form.select>
                <x-form.error class="errorMessage" :messages="$errors->exportItem->get('type')" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                <x-button variant="info" class="ml-3 exportBtn">
                    Export
                </x-button>
            </div>
        </div>
    </x-modal>
    @endif

    @push('scripts')
    <script type="module">
        @if (!Auth::user()->hasPermission('inventory-manage'))
            $('.edit-field').attr('disabled', true);
        @endif
    </script>
    @if (Auth::user()->hasPermission('inventory-export'))
        @include('js.export')
    @endif
    @include('inventory.js')
    @endpush
</x-app-layout>
