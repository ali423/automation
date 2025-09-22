{{-- DataTable Scripts --}}
<script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
<script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
<script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
<script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
<script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
<script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>

{{-- DataTable Configuration --}}
<script type="text/javascript">
    $(document).ready(function () {
        pdfMake.fonts = {
            Roboto: {
                normal: 'Roboto-Regular.ttf',
                bold: 'Roboto-Medium.ttf',
                italics: 'Roboto-Italic.ttf',
                bolditalics: 'Roboto-MediumItalic.ttf'
            },
            IRANSansWeb: {
                normal: "IRANSansWeb400.ttf",
                bold: "IRANSansWeb400.ttf",
                italics: "IRANSansWeb400.ttf",
                bolditalics: "IRANSansWeb400.ttf"
            }
        };

        $('#datatable-buttons-inventory').DataTable({
            dom: 'Bfrtip',
            paging: false, // Disable DataTables pagination since we're using server-side pagination
            searching: false, // Disable DataTables search since we're using server-side search
            ordering: true, // Keep DataTables sorting for current page
            order: [], // Start with no default ordering
            info: false, // Hide DataTables info since we have custom pagination info
            buttons: [{
                extend: 'copy',
                text: "کپی",
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: [6, 5, 4, 3, 2, 1, 0],
                    modifier: {
                        page: 'current'
                    },
                    orthogonal: "rtlexport"
                }
            },
                {
                    extend: 'pdf',
                    text: 'pdf',
                    className: 'btn btn-outline-primary',
                    exportOptions: {
                        columns: [6, 5, 4, 3, 2, 1, 0],
                        modifier: {
                            page: 'current'
                        },
                        orthogonal: "rtlexport"
                    },
                    customize: function (doc) {
                        doc.defaultStyle.font = "IRANSansWeb";
                        doc.content[1].table.widths = ['14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%'];
                        doc.styles.tableBodyEven.alignment = 'center';
                        doc.styles.tableBodyOdd.alignment = 'center';
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-outline-primary',
                    exportOptions: {
                        columns: [6, 5, 4, 3, 2, 1, 0],
                        modifier: {
                            page: 'current'
                        }
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn btn-outline-primary',
                    exportOptions: {
                        columns: [6, 5, 4, 3, 2, 1, 0],
                        modifier: {
                            page: 'current'
                        }
                    }
                },
                {
                    extend: 'print',
                    text: "پرینت",
                    className: 'btn btn-outline-primary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        modifier: {
                            page: 'current'
                        },
                        orthogonal: "rtlexport"
                    }
                }
            ],
            columnDefs: [{
                targets: '_all',
                render: function (data, type, row) {
                    if (type === 'rtlexport') {
                        return data.split(' ').reverse().join(' ');
                    }
                    return data;
                }
            }],
            "language": {
                "paginate": {
                    "previous": "قبلی",
                    "next": "بعدی"
                }
            }
        });
    });
</script>
