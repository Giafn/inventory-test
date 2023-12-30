
<script>
    function addItem(id, name, price, stock, qty, type = 'add') {
        let target;
        if (type == 'edit') {
            target = $('#listItemStoredEdit');
        } else {
            target = $('#listItemStored');
        }
        if ($(`#${type}item${id}`).length) {
            alert('Item already added!');
            return;
        }
        $(`#emptyItem`).addClass('hidden');
        let html = `
            <div class="flex justify-between items-center border rounded-md p-3" id="${type}item${id}">
                <input type="hidden" name="item[]" value="${id}" />
                <input type="hidden" name="price[]" value="${price}" />
                <input type="hidden" name="total[]" value="${price * qty}" />
                <div class="flex gap-2">
                    <div class="flex flex-col">
                        <span class="font-semibold">${name}</span>
                        <span class="text-sm text-gray-500">Rp. ${price} stock ${stock}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="flex flex-row items-center gap-2">
                        <x-form.label for="${type}qty${id}" value="Qty" />
                        @if (Auth::user()->hasPermission('purchase-manage'))
                        <x-form.input id="${type}qty${id}" name="qty[]" type="number" min=1 class="w-20 qtyInput" placeholder="Qty" value="${qty}" />
                        @else
                        <x-form.input id="${type}qty${id}" name="qty[]" type="number" min=1 class="w-20 qtyInput" placeholder="Qty" value="${qty}" readonly />
                        @endif
                    </div>
                    @if (Auth::user()->hasPermission('purchase-manage'))
                    <x-button variant="danger" size="sm" href="#" x-data="" onclick="removeItem(${id}, '${type}')">
                        <span>Remove</span>
                    </x-button>
                    @endif
                </div>
            </div>
        `;
        target.append(html);

    }

    function removeItem(id, type = 'add') {
        if (type == 'edit') {
            if ($('#listItemStoredEdit').children().length == 1) {
                alert('Item cannot be empty!');
                return;
            }
        } 
        $(`#${type}item${id}`).remove();
    }
</script>
<script type="module">
    $(document).ready(function() {
        jQuery('#typeExport').select2();
        let table = jQuery('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('purchase') }}",
            },
            order: [
                [0, 'desc']
            ],
            columns: [
                @if (auth()->user()->role == 1 || auth()->user()->role == 4)
                {
                    data: null,
                    name: null,
                    render: function(data) {
                        return data.user.name;
                    }
                },
                @endif
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'number',
                    name: 'number'
                },
                {
                    data: null,
                    name: null,
                    render: function(data) {
                        let html = '';
                        html += `
                            <ul class="list-disc list-inside">
                        `;
                        data.details.forEach((details, index) => {
                            html += `
                                <li>${details.inventory.name} (${details.qty})</li>
                            `;
                        });
                        html += `
                            </ul>
                        `;
                        return html;
                    }

                },
                {
                    data: null,
                    name: null,
                    render: function(data) {
                        let total = 0;
                        data.details.forEach((details, index) => {
                            total += Math.round(details.price);
                        });
                        // format to IDR
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(total);
                    }
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        @if (Auth::user()->hasPermission('purchase-manage'))
                        return `
                            <div class="flex justify-center gap-2">
                                <x-button target="_blank" href="#" variant="info" size="sm" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-modal')" class="editBtn" data-id="${data.id}">
                                    <span>Edit</span>
                                </x-button>
                                <x-button target="_blank" href="#" variant="danger" size="sm" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-modal')" class="deleteBtn" data-id="${data.id}">
                                    <span>Delete</span>
                                </x-button>
                            </div>
                        `;
                        @else
                        return `
                            <div class="flex justify-center gap-2">
                                <x-button target="_blank" href="#" variant="info" size="sm" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-modal')" class="editBtn" data-id="${data.id}">
                                    <span>Detail</span>
                                </x-button>
                            </div>
                        `;
                        @endif
                    }
                }
            ]
        });
    });

    $(document).on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $('#searchInputEdit').val('');
        $('#editSearchResults').html('');
        $('#listItemStoredEdit').html('');
        let url = "{{ route('purchase.show', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                data = data.data;
                $('#idEdit').val(data.id);
                $('#dateEdit').val(data.date);
                $('#numberEdit').val(data.number);
                data.details.forEach(detail => {
                    addItem(detail.inventory.id, detail.inventory.name, detail.inventory.price, detail.inventory.stock, detail.qty, 'edit');
                });
            },
            error: function() {
                alert('Something went wrong!');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        let url = "{{ route('purchase.destroy', ':id') }}";
        url = url.replace(':id', id);
        $('#deleteForm').attr('action', url);
        $('#idDelete').val(id);
    });

    // search item
    $(document).on('keyup', '#searchInput', function() {
        const search = $(this).val();
        searchItem(search, 'add'); 
    });

    $(document).on('keyup', '#searchInputEdit', function() {
        const search = $(this).val();
        searchItem(search, 'edit');
    });

    function searchItem(search, type) {
        let url = "{{ route('inventory.search') }}";
        let target;
        if (type == 'edit') {
            target = $('#editSearchResults');
        } else {
            target = $('#searchResults');
        }
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                search: search
            },
            success: function(data) {
                let html = '';
                data.data.forEach(item => {
                    html += `
                        <div class="p-3 flex justify-between items-center border rounded-md">
                            <div class="flex gap-2">
                                <div class="flex flex-col">
                                    <span class="font-semibold">${item.name}</span>
                                    <span class="text-sm text-gray-500">Rp. ${item.price} stock ${item.stock}</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <x-button variant="success" size="sm" href="#" x-data="" onclick="addItem(${item.id}, '${item.name}', ${item.price}, ${item.stock}, 1, '${type}')">
                                    <span>Add</span>
                                </x-button>
                            </div>
                        </div>
                    `;
                });
                target.html(html);
            },
            error: function() {
                alert('Something went wrong!');
            }
        });
    }

    $(document).on('keyup', '.qtyInput', function() {
        const qty = $(this).val();
        if (qty < 1) {
            $(this).val(1);
        }
    });

    @if ($errors->editItem->isNotEmpty())
        let id = '{{ old('id') }}';
        let item = JSON.parse('{!! json_encode(old('item')) !!}');
        let qty = JSON.parse('{!! json_encode(old('qty')) !!}');
        let price = JSON.parse('{!! json_encode(old('price')) !!}');
        let total = JSON.parse('{!! json_encode(old('total')) !!}');
        let itemName = JSON.parse('{!! json_encode(session("itemName")) !!}');
        $('#idEdit').val(id);
        $('#dateEdit').val('{{ old('date') }}');
        $('#numberEdit').val('{{ old('number') }}');
        $('#searchInputEdit').val('');
        $('#editSearchResults').html('');
        $('#listItemStoredEdit').html('');
        item.forEach((item, index) => {
            addItem(item, itemName[index], price[index], 1, qty[index], 'edit');
        });
    @endif
    
// exportBtn click
    $(document).on('click', '.exportBtn', function() {
        let type = $('#typeExport').val();
        if (type == 'Excel') {
            exportToExcel('xlsx', 'inventory');
        } else if (type == 'PDF') {
            exportToPDF('inventory');
        } else if (type == 'CSV') {
            exportToExcel('csv', 'inventory');
        }
    });
</script>