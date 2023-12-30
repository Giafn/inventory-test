<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                Purchase History
            </h2>
        </div>
    </x-slot>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex mb-4 justify-end">
            <div class="flex gap-2">
                @if (Auth::user()->hasPermission('purchase-export'))
                <x-button variant="black" x-data="" x-on:click.prevent="$dispatch('open-modal', 'export-modal')"
                    size="sm" class="dark:bg-white dark:text-black dark:hover:bg-gray-200 dark:hover:text-black">
                    Export
                </x-button>
                @endif
                @if (Auth::user()->hasPermission('purchase-manage'))
                <x-button variant="black" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-modal')"
                    size="sm" class="dark:bg-white dark:text-black dark:hover:bg-gray-200 dark:hover:text-black">
                    Make purchase
                </x-button>
                @endif
            </div>
        </div>
        <table class="table-auto w-full border-collapse border" id="dataTable">
            <thead>
                <tr>
                    @if (Auth::user()->role == '1' || Auth::user()->role == '4') 
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Purchase By</th>
                    @endif
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Date</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Number</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Item</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1">Total</th>
                    <th class="border px-4 py-2 dark:border-dark-eval-1" width="105">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    
    @if (Auth::user()->hasPermission('purchase-manage'))
    <x-modal name="add-modal" :show="$errors->addItem->isNotEmpty()" :maxWidth="'2xl'" focusable>
        <form method="post" action="{{ route('purchase.upsert') }}" class="p-6">
            @csrf
            @method('post')
            <h2 class="text-lg font-medium">
                Purchase Item
            </h2>
            <div class="mt-6 space-y-2">
                <x-form.label for="searchInput" value="Search Item" />
                <x-form.input id="searchInput" type="text" class="block w-full" placeholder="Search" />
                <div id="searchResults" class="mt-2">
                    <div class="p-3 flex justify-between items-center border rounded-md hidden">
                        <div class="flex gap-2">
                            <div class="flex flex-col">
                                <span class="font-semibold">XXXXXXXXXX</span>
                                <span class="text-sm text-gray-500">Rp. xx.xxx stock xx</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex flex-row items-center gap-2">
                                <x-form.label for="qty" value="Qty" />
                                <x-form.input id="qty" type="number" class="w-20" placeholder="Qty" />
                            </div>
                            <x-button variant="success" size="sm">
                                <span>Add</span>
                            </x-button>
                        </div>
                    </div>
                </div>

                <div class="mt1">
                    <h2 class="text-md font-medium">
                        Item List
                    </h2>
                    <div id="listItemStored" class="mt-2 p-3 border rounded-md">
                        <div class="flex justify-between items-center border rounded-md p-3 " id="emptyItem">
                            <div class="flex gap-2">
                                <div class="flex flex-col">
                                    Empty
                                </div>
                            </div>
                            <div class="flex gap-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                <x-button variant="success" class="ml-3">
                    Purchase
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif


    <x-modal name="edit-modal" :show="$errors->editItem->isNotEmpty()" focusable>
        <form method="post" class="p-6" x-data="" action="{{ route('purchase.upsert') }}">
            @csrf
            @method('post')
            <h2 class="text-lg font-medium">
                {{ Auth::user()->hasPermission('purchase-manage') ? 'Edit Purchase' : 'Show Purchase' }}
            </h2>
            <div class="mt-6 space-y-2">
                @if (Auth::user()->hasPermission('purchase-manage'))
                <input type="hidden" name="id" id="idEdit" value="" />
                <x-form.label for="dateEdit" value="Date" />
                <x-form.input id="dateEdit" name="date" type="date" class="block w-full bg-gray-100 dark:bg-dark-eval-1 border-transparent" placeholder="Date" readonly />
                <x-form.label for="numberEdit" value="Number" />
                <x-form.input id="numberEdit" name="number" type="text" class="block w-full bg-gray-100 dark:bg-dark-eval-1 border-transparent" placeholder="Number" readonly />
                <x-form.label for="searchInputEdit" value="Search Item" />
                <x-form.input id="searchInputEdit" type="text" class="block w-full" placeholder="Search" />
                <div id="editSearchResults" class="mt-2">
                </div>
                @endif
                <div class="mt1">
                    <h2 class="text-md font-medium">
                        Item List
                    </h2>
                    <div id="listItemStoredEdit" class="mt-2 p-3 border rounded-md">
                        <div class="flex justify-between items-center border rounded-md p-3 " id="emptyItem">
                            <div class="flex gap-2">
                                <div class="flex flex-col">
                                    Empty
                                </div>
                            </div>
                            <div class="flex gap-2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>
                @if (Auth::user()->hasPermission('purchase-manage'))
                <x-button variant="success" class="ml-3">
                    Update Purchase
                </x-button>
                @endif
            </div>
        </form>
    </x-modal>

    @if (Auth::user()->hasPermission('purchase-manage'))
    <x-modal name="delete-modal" :show="false" focusable>
        <form method="post" class="p-6" x-data="" id="deleteForm">
            @csrf
            @method('delete')
            <h2 class="text-lg font-medium">
                Cancel Purchase
            </h2>
            <div class="mt-6 space-y-2">
                <input type="hidden" name="id" id="idDelete" value="" />
                <p>Are you sure you want to cancel this purchase?</p>
            </div>
            <div class="mt-6 flex justify-end">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    No
                </x-button>
                <x-button variant="danger" class="ml-3">
                    Yes, Cancel
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif

    @if (Auth::user()->hasPermission('purchase-export'))
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
        </div>
    </x-modal>
    @endif

    @push('scripts')
    @include('js.export')
    @include('purchase.js')
    @endpush
</x-app-layout>
