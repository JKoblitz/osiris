<!-- <div class="form-group with-icon mb-10 mw-full w-350 d-inline-block">
    <input type="search" class="form-control" placeholder="<?= lang('Filter') ?>" oninput="filter_results(this.value)">
    <i class="fas fa-arrow-rotate-left" onclick="$(this).prev().val(''); filter_results('')"></i>
</div> -->

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/datatables.css">

<a class="" href="<?= ROOTPATH ?>/activities/new"><i class="fas fa-plus"></i> <?= lang('Add activity', 'Aktivität hinzufügen') ?></a>

<div class="mb-5" id="select-btns">
    <button onclick="filterDataTable(1, 'publication')" class="btn btn-select text-primary" id="publication-btn"><i class="fa-regular fa-file-lines"></i> <?= lang('Publication', "Publikationen") ?></button>
    <button onclick="filterDataTable(1, 'poster')" class="btn btn-select text-danger" id="poster-btn"><i class="fa-regular fa-presentation-screen"></i><?= lang('Posters', 'Poster') ?></button>
    <button onclick="filterDataTable(1, 'lecture')" class="btn btn-select text-signal" id="lecture-btn"><i class="fa-regular fa-keynote"></i><?= lang('Lectures', 'Vorträge') ?></button>
    <button onclick="filterDataTable(1, 'review')" class="btn btn-select text-success" id="review-btn"><i class="fa-regular fa-book-open-cover"></i><?= lang('Reviews &amp; editorials', 'Reviews &amp; Editorials') ?></button>
    <button onclick="filterDataTable(1, 'misc')" class="btn btn-select text-muted" id="misc-btn"><i class="fa-regular fa-icons"></i><?= lang('Misc') ?></button>
    <button onclick="filterDataTable(1, 'teaching')" class="btn btn-select text-muted" id="teaching-btn"><i class="fa-regular fa-people"></i><?= lang('Teaching &amp; Guests') ?></button>
    <button onclick="filterDataTable(1, 'software')" class="btn btn-select text-muted disabled" id="software-btn"><i class="fa-regular fa-desktop"></i><?= lang('Software') ?></button>
</div>


<div class="input-group mb-10 w-400 mw-full">
    <div class="input-group-prepend">
        <span class="input-group-text"><?= lang('From', 'Von') ?></span>
    </div>
    <input type="number" name="time[from][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="from-month" onchange="filtertime()">
    <input type="number" name="time[from][year]" class="form-control" placeholder="year" min="2017" max="<?= CURRENTYEAR ?>" step="1" id="from-year" onchange="filtertime()">
    <div class="input-group-prepend">
        <span class="input-group-text"><?= lang('to', 'bis') ?></span>
    </div>
    <input type="number" name="time[to][month]" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month" onchange="filtertime()">
    <input type="number" name="time[to][year]" class="form-control" placeholder="year" min="2017" max="<?= CURRENTYEAR ?>" step="1" id="to-year" onchange="filtertime()">

    <div class="input-group-append">
        <button class="btn" type="button" onclick="filtertime(true)">&times;</button>
    </div>
</div>

<div class="mt-20">

    <table class="table dataTable" id="result-table">
        <thead>
            <tr>
                <th><?= lang('Quarter', 'Quartal') ?></th>
                <th><?= lang('Type', 'Typ') ?></th>
                <th><?= lang('Activity', 'Aktivität') ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            // $options = ['sort' => ["year" => -1, "month" => -1]];
            if ($USER['is_controlling'] || $USER['is_admin']) {
                // controlling sees everything from the current year
                $filter = [];
            } else {
                // everybody else sees their own work (all)
                $filter = ['$or' => [['authors.user' => $user], ['editors.user' => $user], ['user' => $user]]];
            }
            $cursor = $osiris->activities->find($filter);
            //, 'year' => intval(SELECTEDYEAR)
            if (empty($cursor)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No activities found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($cursor as $document) {
                $id = $document['_id'];
                $q = getQuarter($document);
                $y = getYear($document);
                $quarter = $endQuarter = $y . "Q" . $q;
                // $author = getUserAuthor($document['authors'] ?? array(), $user);
            ?>
                <tr class="" id="<?= $id ?>">
                    <td class="quarter">
                        <?= $y ?>Q<?= $q ?>

                        <span class="hidden">
                            <!-- for date filtering -->
                            <?= $document['month'] . "M" . $document['year'] . "Y" ?>
                            <?php if (isset($document['end']) && !empty($document['end'])) {

                                $em = $document['end']['month'];
                                $ey = $document['end']['year'];
                                $sm = $document['month'];
                                $sy = $document['year'];
                                for ($i = $y; $i <= $ey; $i++) {
                                    $endMonth = $i != $ey ? 11 : $em - 1;
                                    $startMon = $i === $y ? $sm - 1 : 0;
                                    for ($j = $startMon; $j <= $endMonth; $j = $j > 12 ? $j % 12 || 11 : $j + 1) {
                                        $month = $j + 1;
                                        $displayMonth = $month < 10 ? '0' + $month : $month;
                                        echo $displayMonth . "M" . $i . "Y ";
                                        // QUARTER:
                                        $endQuarter = $i . "Q" . ceil($displayMonth / 3);
                                    }
                                }
                            } ?>
                        </span>
                        <?php if ($quarter != $endQuarter) {
                            echo "-" . $endQuarter;
                        } ?>

                    </td>
                    <td class="text-center ">
                        <?php
                        echo activity_icon($document);
                        ?>
                        <span class="hidden">
                            <?= $document['type'] ?>
                        </span>
                    </td>
                    <td>
                        <?php echo format($document['type'], $document); ?>
                    </td>
                    <td class="unbreakable">
                        <!-- <button class="btn btn-sm text-success" onclick="toggleEditForm('<?= $document['type'] ?>', '<?= $id ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button> -->
                        <a class="btn btn-sm text-success" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                            <i class="fa-regular fa-lg fa-search"></i>
                        </a>
                        <a class="btn btn-sm text-success" href="<?= ROOTPATH . "/activities/edit/" . $id ?>">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>
</div>

<script src="//cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
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
            "order": [
                [0, 'desc'],
                [1, 'asc']
            ]
        });
    });

    function filterDataTable(col, item) {
        if ($('#select-btns #' + item + '-btn').hasClass('active')) {
            $('#select-btns .btn').removeClass('active')
            dataTable.columns(col).search("", true, false, true).draw();

        } else {
            $('#select-btns .btn').removeClass('active')
            $('#select-btns #' + item + '-btn').addClass('active')
            dataTable.columns(col).search(item, true, false, true).draw();

        }
    }

    function filtertime(reset = false) {
        if (reset) {
            $("#from-month").val("")
            $("#from-year").val("")
            $("#to-month").val("")
            $("#to-year").val("")
            dataTable.columns(0).search("", true, false, true).draw();
            return
        }




        var today = new Date();
        var fromMonth = $("#from-month").val()
        if (fromMonth.length == 0 || parseInt(fromMonth) < 1 || parseInt(fromMonth) > 12) {
            fromMonth = 1
        }
        var fromYear = $("#from-year").val()
        if (fromYear.length == 0 || parseInt(fromYear) < 2017 || parseInt(fromYear) > today.getFullYear()) {
            fromYear = 2017
        }
        var toMonth = $("#to-month").val()
        if (toMonth.length == 0 || parseInt(toMonth) < 1 || parseInt(toMonth) > 12) {
            toMonth = 12
        }
        var toYear = $("#to-year").val()
        if (toYear.length == 0 || parseInt(toYear) < 2017 || parseInt(toYear) > today.getFullYear()) {
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
    //     new gridjs.Grid({
    //   from: document.getElementById('result-table') 
    // // columns: ['Name', 'Email', 'Phone Number'],
    // //   data: [
    // //     ['John', 'john@example.com', '(353) 01 222 3333'],
    // //     ['Mark', 'mark@gmail.com',   '(01) 22 888 4444']
    // //   ]
    // }).render(document.getElementById("result"));
</script>