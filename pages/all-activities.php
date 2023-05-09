<?php

$user = $user ?? $_SESSION['username'];

?>


<link rel="stylesheet" href="<?= ROOTPATH ?>/css/datatables.css">

<a class="btn btn-primary float-right" href="<?= ROOTPATH ?>/activities/new"><i class="ph ph-regular ph-plus"></i> <?= lang('Add activity', 'Aktivität hinzufügen') ?></a>


<?php if ($page == 'activities' && $USER['is_scientist']) { ?>
    <h1 class='m-0'>
        <i class="ph ph-regular ph-book-open"></i>
        <?= lang("All activities", "Alle Aktivitäten") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/my-activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="ph ph-student"></i>
        <?= lang('Show only my own activities', "Zeige nur meine eigenen Aktivitäten") ?>
    </a>
<?php
} elseif (isset($_GET['user'])) { ?>
    <h1 class='m-0'>
        <i class="ph ph-regular ph-folder-user"></i>
        <?= lang("Activities of $user", "Aktivitäten von $user") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="ph ph-regular ph-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } elseif ($page == 'my-activities') { ?>
    <h1 class='m-0'>
        <i class="ph ph-regular ph-folder-user"></i>
        <?= lang("My activities", "Meine Aktivitäten") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="ph ph-regular ph-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } ?>
<br>
<div class="btn-bar d-flex">

    <div class="dropdown with-arrow mr-10" id="select-dropdown">
        <button class="btn" data-toggle="dropdown" type="button" id="select-activity" aria-haspopup="true" aria-expanded="false">
            <?= lang('Filter by type', 'Nach Typ filtern') ?>
            <i class="ph ph-caret-down"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="select-activity">
            <?php
            foreach ($Settings->getActivities() as $id => $a) { ?>
                <a data-type="<?= $id ?>" onclick="selectActivity(this, '<?= $id ?>')" class="item text-<?= $id ?>" id="<?= $id ?>-btn">
                    <span class="mr-5"><?= $Settings->icon($id, null, false) ?> </span>
                    <?= $Settings->title($id, null) ?>
                </a>
            <?php
            }
            ?>
        </div>
    </div>



    <div class="input-group mb-10 w-400 mw-full d-md-inline-flex">
        <div class="input-group-prepend">
            <span class="input-group-text"><?= lang('From', 'Von') ?></span>
        </div>
        <input type="number" name="time[from][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="from-month" onchange="filtertime()">
        <input type="number" name="time[from][year]" class="form-control" placeholder="year" min="<?= $Settings->startyear ?>" max="<?= CURRENTYEAR ?>" step="1" id="from-year" onchange="filtertime()">
        <div class="input-group-prepend">
            <span class="input-group-text"><?= lang('to', 'bis') ?></span>
        </div>
        <input type="number" name="time[to][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month" onchange="filtertime()">
        <input type="number" name="time[to][year]" class="form-control" placeholder="year" min="<?= $Settings->startyear ?>" max="<?= CURRENTYEAR ?>" step="1" id="to-year" onchange="filtertime()">

        <div class="input-group-append">
            <button class="btn" type="button" onclick="resetTime()">&times;</button>
        </div>
    </div>

</div>

<div class="mt-20">

    <table class="table dataTable responsive" id="result-table">
        <thead>
            <tr>
                <th><?= lang('Quarter', 'Quartal') ?></th>
                <th><?= lang('Type', 'Typ') ?></th>
                <th><?= lang('Activity', 'Aktivität') ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>

    </table>
</div>

<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>

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
        dataTable = $('#result-table').DataTable({
            "ajax": {
                "url": ROOTPATH + '/api/all-activities',
                "data": {
                    "page": '<?= $page ?>',
                    'display_activities': '<?= $USER['display_activities'] ?>',
                    'user': '<?= $user ?>'
                },
                dataSrc: 'data'
            },
            columnDefs: [{
                    "targets": 0,
                    "data": "quarter",
                },
                {
                    targets: 1,
                    data: 'type'
                },
                {
                    targets: 2,
                    data: 'activity'
                },
                {
                    targets: 3,
                    data: 'links',
                    className: 'unbreakable'
                },
                {
                    targets: 4,
                    data: 'search-text',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 5,
                    data: 'start',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 6,
                    data: 'end',
                    searchable: true,
                    visible: false,
                }
            ],
            "order": [
                [0, 'desc'],
                [1, 'asc']
            ],
            <?php if (isset($_GET['q'])) { ?> "oSearch": {
                    "sSearch": "<?= $_GET['q'] ?>"
                }
            <?php } ?>
        });

        <?php if (isset($_GET['type'])) { ?>
            window.location.hash = "type=<?= $_GET['type'] ?>";
        <?php } ?>

        var hash = readHash();
        if (hash.type !== undefined) {
            selectActivity(document.getElementById(hash.type+'-btn'), hash.type)
        }

        if (hash.time !== undefined) {
            var time = hash.time.split(',')
            $("#from-month").val(time[0])
            $("#from-year").val(time[1])
            $("#to-month").val(time[2])
            $("#to-year").val(time[3])
            filtertime()
        }

    });


    function selectActivity(btn, activity) {

        if ($(btn).hasClass('active')) {
            writeHash({
                type: null
            })
            $('#select-dropdown a.item').removeClass('active')
            $('#select-activity')
                .html(lang('Filter by type', 'Nach Typ filtern') + ' <i class="ph ph-caret-down"></i>')
                .removeClass('active')

            $('#select-dropdown')
                .removeClass('show')
            dataTable.columns(1).search("", true, false, true).draw();

        } else {
            writeHash({
                type: activity
            })
            $('#select-dropdown a.item').removeClass('active')
            $(btn).addClass('active')
            $('#select-activity')
                .html(btn.innerHTML)
                .removeClass('active')

            $('#select-dropdown')
                .removeClass('show')
            dataTable.columns(1).search(activity, true, false, true).draw();

        }
    }

    function filterDataTable(col, item) {
        if ($('#select-btns #' + item + '-btn').hasClass('active')) {
            writeHash({
                type: null
            })
            $('#select-btns .btn').removeClass('active')
            dataTable.columns(col).search("", true, false, true).draw();

        } else {
            writeHash({
                type: item
            })
            $('#select-btns .btn').removeClass('active')
            $('#select-btns #' + item + '-btn').addClass('active')
            dataTable.columns(col).search(item, true, false, true).draw();

        }
    }

    function filterUserTable(item) {
        if ($('#user-btn').hasClass('active')) {
            $('#user-btn').removeClass('active')
            dataTable.columns(0).search("", true, false, true).draw();

        } else {
            $('#user-btn').addClass('active')
            dataTable.columns(0).search(item, true, false, true).draw();

        }
    }

    let [fromMonth, fromYear, toMonth, toYear] = getFromToDate()

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var min = null,
                max = null;

            // console.log([fromMonth, fromYear, toMonth, toYear])

            if (fromMonth !== null && fromYear !== null)
                min = new Date(fromYear, fromMonth - 1, 1, 0, 0, 0, 0);
            if (toMonth !== null && toYear !== null)
                max = new Date(toYear, toMonth - 1, 31, 0, 0, 0, 0);
            // var max = maxDate.val();
            // var date = new Date(data[5]);

            var minDate = new Date(data[5]);
            var maxDate = new Date(data[6]);

            if (
                (min === null && max === null) ||
                (min === null && minDate <= max) ||
                (min <= minDate && max === null) ||
                (min < maxDate && minDate < max)) {
                return true;
            }

            // if (
            //     (min === null && max === null) ||
            //     (min === null && minDate <= max) ||
            //     (min <= minDate && max === null) ||
            //     (min <= minDate && minDate <= max)
            // ) {
            //     return true;
            // }
            return false;
        }
    );

    function getFromToDate() {
        var today = new Date();
        var fromMonth = $("#from-month").val()
        if (fromMonth.length == 0) {
            return [null, null, null, null];
        }

        var maxYear = today.getFullYear() + 1,
            minYear = <?= $Settings->startyear ?>;

        if (fromMonth.length == 0 || parseInt(fromMonth) < 1 || parseInt(fromMonth) > 12) {
            fromMonth = 1
        }
        var fromYear = $("#from-year").val()
        if (fromYear.length == 0 || parseInt(fromYear) < minYear || parseInt(fromYear) > maxYear) {
            fromYear = minYear
        }
        var toMonth = $("#to-month").val()
        if (toMonth.length == 0 || parseInt(toMonth) < 1 || parseInt(toMonth) > 12) {
            toMonth = 12
        }
        var toYear = $("#to-year").val()
        if (toYear.length == 0 || parseInt(toYear) < minYear || parseInt(toYear) > maxYear) {
            toYear = maxYear
        }
        // take care that from is not larger than to
        fromMonth = parseInt(fromMonth)
        fromYear = parseInt(fromYear)
        toMonth = parseInt(toMonth)
        toYear = parseInt(toYear)
        if (fromYear > toYear) {
            fromYear = toYear
        }
        if (fromYear == toYear && fromMonth > toMonth) {
            fromMonth = toMonth
        }

        $("#from-month").val(fromMonth)
        $("#from-year").val(fromYear)
        $("#to-month").val(toMonth)
        $("#to-year").val(toYear)

        writeHash({
            time: `${fromMonth},${fromYear},${toMonth},${toYear}`
        })

        return [fromMonth, fromYear, toMonth, toYear];

    }

    function filtertime() {
        [fromMonth, fromYear, toMonth, toYear] = getFromToDate()
        dataTable.draw();
    }

    function resetTime() {
        $("#from-month").val("")
        $("#from-year").val("")
        $("#to-month").val("")
        $("#to-year").val("")
        dataTable.draw();
        writeHash({
            time: null
        })
    }

    function filtertime_(reset = false) {
        if (reset) {
            $("#from-month").val("")
            $("#from-year").val("")
            $("#to-month").val("")
            $("#to-year").val("")
            dataTable.columns(0).search("", true, false, true).draw();
            writeHash({
                time: null
            })
            return
        }

        let [fromMonth, fromYear, toMonth, toYear] = getFromToDate()
        var range = dateRange(fromMonth, fromYear, toMonth, toYear)
        console.log(range);
        regExSearch = ' (' + range.join('|') + ')';
        console.log(regExSearch);
        dataTable.columns(0).search(regExSearch, true, false, true).draw();
        // table.column(columnNo).search(regExSearch, true, false).draw();
    }

    function dateRange(startMonth, startYear, endMonth, endYear) {
        var dates = [];

        for (var i = startYear; i <= endYear; i++) {
            var endMonth = i != endYear ? 11 : endMonth - 1;
            var startMon = i === startYear ? startMonth - 1 : 0;
            for (var j = startMon; j <= endMonth; j = j > 12 ? j % 12 || 11 : j + 1) {
                var month = j + 1;
                // var displayMonth = month < 10 ? '0' + month : month;
                // var f = 
                // [i, displayMonth, '01'].join('-')
                dates.push(month + "M" + i + "Y");
            }
        }
        return dates;
    }
</script>