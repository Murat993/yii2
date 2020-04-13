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
        rowGroup: {
            startRender: null,
            endRender: function (rows, group) {
                var sumTotal = rows
                    .data()
                    .pluck(2)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var sumNew = rows
                    .data()
                    .pluck(3)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);


                var sumMsAssigned = rows
                    .data()
                    .pluck(4)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var sumInProcess = rows
                    .data()
                    .pluck(5)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var sumOnModeration = rows
                    .data()
                    .pluck(6)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var sumModerationStart = rows
                    .data()
                    .pluck(7)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var sumCompleted = rows
                    .data()
                    .pluck(8)
                    .reduce(function (a, b) {
                        return a + b * 1;
                    }, 0);

                var percent = Number((sumCompleted * 100 / sumTotal).toFixed(1));

                return $('<tr/>')
                    .append('<td colspan="2" style="background: #b5afd2;">'
                        + group + ', ' + sumCompleted + ' из ' +  sumTotal +  ' (' + percent + '%)</td>')
                    .append('<td>' + sumTotal + '</td>')
                    .append('<td style="background: #bcbcbc">' + sumNew + '</td>')
                    .append('<td style="background: #9575CD">' + sumMsAssigned + '</td>')
                    .append('<td style="background: #5C6BC0">' + sumInProcess + '</td>')
                    .append('<td style="background: #FF5722">' + sumOnModeration + '</td>')
                    .append('<td style="background: #2196F3">' + sumModerationStart + '</td>')
                    .append('<td style="background: #4CAF50">' + sumCompleted + '</td>')
                // .append('<td>' + salaryAvg + '</td>');
            },
            dataSrc: 0
        },
        iDisplayLength: 25,
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
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
    var table = $('.datatable-custom').DataTable();

    $('.datatable-custom tbody').on('mouseover', 'td', function () {
        var rowIdx = table.cell(this).index().row;
        if (rowIdx !== lastIdx) {
            $(table.rows().nodes()).removeClass('active');
            $(table.row(rowIdx).nodes()).addClass('active');
        }
    }).on('mouseleave', function () {
        $(table.rows().nodes()).removeClass('active');
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
