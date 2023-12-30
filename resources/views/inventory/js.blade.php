<script type="module">
    $(document).ready(function() {
        jQuery('#typeExport').select2();
        let table = jQuery('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('inventory') }}",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'stock',
                    name: 'stock'
                },
                {
                    data: null,
                    name: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        @if (Auth::user()->hasPermission('inventory-manage'))
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
                                    <span>Show</span>
                                </x-button>
                            </div>
                        `;
                        @endif
                    },
                    className: 'noPrint'
                }
            ]
        });
    });

    $(document).on('click', '.editBtn', function() {
        $('.errorMessage').html('');
        $('.edit-field').val('...');
        const id = $(this).data('id');
        let url = "{{ route('inventory.update', ':id') }}";
        url = url.replace(':id', id);
        $('#editForm').attr('action', url);

        let showUrl = "{{ route('inventory.show', ':id') }}";
        showUrl = showUrl.replace(':id', id);

        $.ajax({
            url: showUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                id: id
            },
            success: function(data) {
                data = data.data;
                $('#idEdit').val(data.id);
                $('#codeEdit').val(data.code);
                $('#nameEdit').val(data.name);
                $('#priceEdit').val(data.price);
                $('#stockEdit').val(data.stock);
            },
            error: function() {
                alert('Something went wrong!');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        let url = "{{ route('inventory.destroy', ':id') }}";
        url = url.replace(':id', id);
        $('#deleteForm').attr('action', url);
        $('#idDelete').val(id);
    });

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
