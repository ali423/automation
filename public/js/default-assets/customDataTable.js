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

    $('#datatable-buttons').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'copy',
            text: "کپی",
            className: 'btn btn-outline-primary',
            exportOptions: {
                columns: [4, 3, 2, 1, 0],
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
                columns: [4, 3, 2, 1, 0],
                modifier: {
                    page: 'current'
                },
                orthogonal: "rtlexport"
            },
            customize: function (doc) {
                doc.defaultStyle.font = "IRANSansWeb";
                doc.content[1].table.widths = ['20%', '20%', '20%', '20%', '20%'];
                doc.styles.tableBodyEven.alignment = 'center';
                doc.styles.tableBodyOdd.alignment = 'center';
            }
        },
        {
            extend: 'excel',
            className: 'btn btn-outline-primary',
            exportOptions: {
                columns: [4, 3, 2, 1, 0],
                modifier: {
                    page: 'current'
                }
            }
        },
        {
            extend: 'csv',
            className: 'btn btn-outline-primary',
            exportOptions: {
                columns: [4, 3, 2, 1, 0],
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
                columns: [0, 1, 2, 3, 4],
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