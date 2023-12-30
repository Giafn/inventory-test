<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
<script>
    function exportToExcel(type, filename) {
        let data = [];
        $('#dataTable tr').each(function(rowIndex, row) {
            let rowData = [];
            $(row).find('td:not(:last), th:not(:last)').each(function(colIndex, col) {
                let cellContent = $(col).html();
                cellContent = cellContent.replace(/&nbsp;/g, ' ');

                if ($(col).find('ul').length > 0) {
                    let ulItems = [];
                    $(col).find('ul li').each(function(index, listItem) {
                        ulItems.push($(listItem).text());
                    });
                    cellContent = ulItems.join(", ");
                }

                rowData.push(cellContent);
            });
            data.push(rowData);
        });

        let wb = XLSX.utils.book_new();
        let ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

        XLSX.writeFile(wb, filename + Date.now() + '.' + type);
    }




    function exportToPDF(filename) {
        window.jsPDF = window.jspdf.jsPDF;
        let tableClone = $('#dataTable').clone();

        tableClone.find('tr').each(function() {
            $(this).find('th:last, td:last').remove();
        });

        let doc = new jsPDF();

        tableClone.find('td, th').each(function() {
            let $cell = $(this);
            let cellContent = $cell.html();

            if ($cell.find('ul').length > 0) {
                let ulItems = [];
                $cell.find('ul li').each(function(index, listItem) {
                    ulItems.push($(listItem).text());
                });
                cellContent = ulItems.join(', ');
            }

            $cell.html(cellContent);
        });

        doc.autoTable({
            html: tableClone[0],
            theme: 'grid',
            headStyles: {
                fillColor: [0, 0, 0],
                textColor: [255, 255, 255],
            },
            bodyStyles: {
                fillColor: [255, 255, 255],
                textColor: [0, 0, 0],
            },
            didDrawCell: function(data) {},
        });

        const name = filename + Date.now() + '.pdf';
        doc.save(name);
    }
</script>
