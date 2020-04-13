/* ------------------------------------------------------------------------------
*
*  # Advanced datatables
*
*  Specific JS code additions for datatable_advanced.html page
*
*  Version: 1.0
*  Latest update: Aug 1, 2015
*
* ---------------------------------------------------------------------------- */

$(function () {


    // Table setup
    // ------------------------------

    // Setting datatable defaults
    $.extend($.fn.dataTable.defaults, {
        autoWidth: false,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Фильтр:</span> _INPUT_',
            lengthMenu: '<span>Показать:</span> _MENU_',
            info: "Показывается _START_ - _END_ из _TOTAL_ записей",
            infoEmpty: 'Записей нет',
            infoFiltered: "Отфильтровано из _MAX_ записей",
            paginate: {'first': 'Первая', 'last': 'Последняя', 'next': '&rarr;', 'previous': '&larr;'}
        },
        iDisplayLength: 25,
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        },
    });


    // Datatable 'length' options
    $('.datatable-show-all').DataTable({
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });


    // DOM positioning
    $('.datatable-dom-position').DataTable({
        dom: '<"datatable-header length-left"lp><"datatable-scroll"t><"datatable-footer info-right"fi>',
    });


    // Highlighting rows and columns on mouseover
    var lastIdx = null;
    var table = $('.datatable-highlight').DataTable();

    $('.datatable-highlight tbody').on('mouseover', 'td', function () {
        var rowIdx = table.cell(this).index().row;
        if (rowIdx !== lastIdx) {
            $(table.rows().nodes()).removeClass('active');
            $(table.row(rowIdx).nodes()).addClass('active');
        }
    }).on('mouseleave', function () {
        $(table.rows().nodes()).removeClass('active');
    });


    $('.datatable-highlight tfoot td').not(':last-child').each(function () {
        var title = $('.datatable-highlight thead th').eq($(this).index()).text();
        $(this).html('<input type="text" class="form-control input-sm" placeholder="' + title + '" />');
    });
    table.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change', function () {
            that.search(this.value).draw();
        });
    });


    var singleSelect = $('.datatable-selection-single').DataTable();
    $('.datatable-selection-single tbody').on('click', 'tr', function () {
        if ($(this).hasClass('success')) {
            $(this).removeClass('success');
        }
        else {
            singleSelect.$('tr.success').removeClass('success');
            $(this).addClass('success');
        }
    });


    // Columns rendering
    $('.datatable-columns').dataTable({
        columnDefs: [
            {
                // The `data` parameter refers to the data for the cell (defined by the
                // `data` option, which defaults to the column being worked with, in
                // this case `data: 0`.
                render: function (data, type, row) {
                    return data + ' (' + row[3] + ')';
                },
                targets: 0
            },
            {visible: false, targets: [3]}
        ]
    });


    // External table additions
    // ------------------------------

    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder', '...');


    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

});
