<table class="table" id="result-table">
    <thead>
        <th>Journal name</th>
        <th>Abbr</th>
        <th>ISSN</th>
        <th><span data-toggle="tooltip" data-title="Last year impact factor if available">IF</span></th>
    </thead>
    <tbody>
    </tbody>
</table>




<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.naturalsort.js"></script>


<script>
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control form-control-sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control form-control-sm d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
    var dataTable;
    $(document).ready(function() {
        // dataTable = $('#result-table').DataTable({
        //     "order": [
        //         [0, 'asc'],
        //     ]
        // });
        $('#result-table').DataTable({
            ajax: ROOTPATH + '/test/journals',
            columnDefs: [

                {
                    "targets": 0,
                    "data": "name",
                    "render": function(data, type, full, meta) {
                        return `<a href="${ROOTPATH}/view/journal/${full.id}">${data}</a>`;
                    }
                },
                {
                    targets: 1,
                    data: 'abbr'
                },
                {
                    targets: 2,
                    data: 'issn'
                },
                {
                    type: 'natural',
                    targets: 3,
                    data: 'if'
                },
            ],
        });
    });
</script>