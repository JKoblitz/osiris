
<?php

$user = $user ?? $_SESSION['username'];

?>


<link rel="stylesheet" href="<?= ROOTPATH ?>/css/datatables.css">

<a class="btn btn-primary float-right" href="<?= ROOTPATH ?>/activities/new"><i class="fas fa-plus"></i> <?= lang('Add activity', 'Aktivität hinzufügen') ?></a>


<?php if ($page == 'activities' && $USER['is_scientist']) { ?>
    <h1 class='m-0'>
        <i class="fa-regular fa-book-open"></i>
        <?= lang("All activities", "Alle Aktivitäten") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/my-activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="fa-solid fa-circle-user"></i>
        <?= lang('Show only my own activities', "Zeige nur meine eigenen Aktivitäten") ?>
    </a>
<?php
} elseif (isset($_GET['user'])) { ?>
    <h1 class='m-0'>
        <i class="icon-activity-user"></i>
        <?= lang("Activities of $user", "Aktivitäten von $user") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="fa-solid fa-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } elseif ($page == 'my-activities') { ?>
    <h1 class='m-0'>
        <i class="icon-activity-user"></i>
        <?= lang("My activities", "Meine Aktivitäten") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn btn-sm mb-10" id="user-btn">
        <i class="fa-solid fa-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } ?>
<br>

<div class="mb-5 btn-group" id="select-btns">
    <button onclick="filterDataTable(1, 'publication')" class="btn btn-select- text-publication" id="publication-btn"><?= activity_icon('publication', false) ?> <?= lang('Publication', "Publikationen") ?></button>
    <button onclick="filterDataTable(1, 'poster')" class="btn btn-select- text-poster" id="poster-btn"><?= activity_icon('poster', false) ?> <?= lang('Posters', 'Poster') ?></button>
    <button onclick="filterDataTable(1, 'lecture')" class="btn btn-select- text-lecture" id="lecture-btn"><?= activity_icon('lecture', false) ?> <?= lang('Lectures', 'Vorträge') ?></button>
    <button onclick="filterDataTable(1, 'review')" class="btn btn-select- text-review" id="review-btn"><?= activity_icon('review', false) ?> <?= lang('Reviews &amp; editorials', 'Reviews &amp; Editorials') ?></button>
    <button onclick="filterDataTable(1, 'teaching')" class="btn btn-select- text-teaching" id="teaching-btn"><?= activity_icon('teaching', false) ?> <?= lang('Teaching', 'Lehre') ?></button>
    <button onclick="filterDataTable(1, 'students')" class="btn btn-select- text-students" id="students-btn"><?= activity_icon('students', false) ?> <?= lang('Students &amp; Guests', 'Studierende &amp; Gäste') ?></button>
    <button onclick="filterDataTable(1, 'software')" class="btn btn-select- text-software" id="software-btn"><?= activity_icon('software', false) ?> <?= lang('Software') ?></button>
    <button onclick="filterDataTable(1, 'misc')" class="btn btn-select- text-misc" id="misc-btn"><?= activity_icon('misc', false) ?> <?= lang('Misc') ?></button>
</div>

<div class="input-group mb-10 w-400 mw-full">
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
        <button class="btn" type="button" onclick="filtertime(true)">&times;</button>
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
<!-- <script src="<?= ROOTPATH ?>/js/gridjs.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script> -->

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
        // className: 'quarter'
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
            filterDataTable(1, hash.type)
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

    function filtertime(reset = false) {
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

        var today = new Date();
        var fromMonth = $("#from-month").val()
        if (fromMonth.length == 0 || parseInt(fromMonth) < 1 || parseInt(fromMonth) > 12) {
            fromMonth = 1
        }
        var fromYear = $("#from-year").val()
        if (fromYear.length == 0 || parseInt(fromYear) < <?=$Settings->startyear?> || parseInt(fromYear) > today.getFullYear()) {
            fromYear = <?=$Settings->startyear?>
        }
        var toMonth = $("#to-month").val()
        if (toMonth.length == 0 || parseInt(toMonth) < 1 || parseInt(toMonth) > 12) {
            toMonth = 12
        }
        var toYear = $("#to-year").val()
        if (toYear.length == 0 || parseInt(toYear) < <?=$Settings->startyear?> || parseInt(toYear) > today.getFullYear()) {
            toYear = today.getFullYear()
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

        writeHash({
            time: `${fromMonth},${fromYear},${toMonth},${toYear}`
        })

        $("#from-month").val(fromMonth)
        $("#from-year").val(fromYear)
        $("#to-month").val(toMonth)
        $("#to-year").val(toYear)

        var range = dateRange(fromMonth, fromYear, toMonth, toYear)
        console.log(range);
        regExSearch = '(' + range.join('|') + ')';
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