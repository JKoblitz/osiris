<?php

/**
 * Page to see all activities
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /activities
 * @link /my-activities
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$user = $user ?? $_SESSION['username'];
?>


<?php if ($page == 'activities' && $Settings->hasPermission('scientist')) { ?>
    <h1 class='m-0'>
        <i class="ph ph-book-open"></i>
        <?= lang("All activities", "Alle Aktivitäten") ?>
    </h1>
    <!-- <a href="<?= ROOTPATH ?>/my-activities" class="btn small mb-10" id="user-btn">
        <i class="ph ph-student"></i>
        <?= lang('Show only my own activities', "Zeige nur meine eigenen Aktivitäten") ?>
    </a> -->

    <a class="mt-10" href="<?= ROOTPATH ?>/add-activity"><i class="ph ph-plus"></i> <?= lang('Add activity', 'Aktivität hinzufügen') ?></a>

<?php
} elseif (isset($_GET['user'])) { ?>
    <h1 class='m-0'>
        <i class="ph ph-folder-user"></i>

        <?= lang("Activities of ", "Aktivitäten von ") ?>
        <a href="<?= ROOTPATH ?>/profile/<?= $user ?>"><?= $DB->getNameFromId($user) ?></a>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn small mb-10" id="user-btn">
        <i class="ph ph-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } elseif ($page == 'my-activities') { ?>
    <h1 class='m-0'>
        <i class="ph ph-folder-user"></i>
        <?= lang("My activities", "Meine Aktivitäten") ?>
    </h1>
    <a href="<?= ROOTPATH ?>/activities" class="btn small mb-10" id="user-btn">
        <i class="ph ph-book-open"></i>
        <?= lang('Show  all activities', "Zeige alle Aktivitäten") ?>
    </a>
<?php } ?>

<div class="row row-eq-spacing">
    <div class="col-lg-9">

        <table class="table dataTable" id="result-table" style="width:100%">
            <thead>
                <tr>
                    <th><?= lang('Quarter', 'Quartal') ?></th>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Activity', 'Aktivität') ?></th>
                    <th></th>
                    <th><?= lang('Print', 'Print') ?></th>
                    <th>Start</th>
                    <th><?= lang('End', 'Ende') ?></th>
                    <th><?= lang('Units', 'Einheiten') ?></th>
                    <th><?= lang('Online ahead of print') ?></th>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Subtype', 'Subtyp') ?></th>
                    <th><?= lang('Title', 'Titel') ?></th>
                    <th><?= lang('Authors', 'Autoren') ?></th>
                    <th><?= lang('Year', 'Jahr') ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="col-lg-3 d-none d-lg-block">

        <div class="on-this-page-filter filters content" id="filters">
            <!-- <div class="content" > -->
            <div class="title">Filter</div>

            <!-- <div id="searchpanes"></div> -->

            <div id="active-filters"></div>


            <h6>
                <?= lang('By type', 'Nach Typ') ?>
                <a class="float-right" onclick="filterActivities('#filter-type .active', null, 7)"><i class="ph ph-x"></i></a>
            </h6>
            <div class="filter">
                <table id="filter-type" class="table small simple">
                    <?php foreach ($Settings->getActivities() as $a) { 
                        $id = $a['id'];
                        ?>
                        <tr style="--highlight-color:  <?=$a['color']?>;">
                            <td>
                                <a data-type="<?= $id ?>" onclick="filterActivities(this, '<?= $id ?>', 1)" class="item" id="<?= $id ?>-btn">
                                    <span class="text-<?= $id ?>">
                                        <span class="mr-5"><?= $Settings->icon($id, null, false) ?> </span>
                                        <?= $Settings->title($id, null) ?>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>

            </div>

            <h6>
                <?= lang('By organisational unit', 'Nach Organisationseinheit') ?>
                <a class="float-right" onclick="filterActivities('#filter-unit .active', null, 7)"><i class="ph ph-x"></i></a>
            </h6>
            <div class="filter">
                <table id="filter-unit" class="table small simple">
                    <?php foreach ($Departments as $id => $dept) { ?>
                        <tr  <?=$Groups->cssVar($id)?>>
                            <td>
                                <a data-type="<?= $id ?>" onclick="filterActivities(this, '<?= $id ?>', 7)" class="item d-block colorless" id="<?= $id ?>-btn">
                                    <span><?= $dept ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <h6>
                <?= lang('By time', 'Nach Zeitraum') ?>
                <a class="float-right" onclick="resetTime()"><i class="ph ph-x"></i></a>
            </h6>

            <div class="input-group">
                <div class="input-group-prepend">
                    <label for="filter-from" class="input-group-text w-50"><?= lang('From', 'Von') ?></label>
                </div>
                <input type="date" name="from" id="filter-from" class="form-control">
            </div>
            <div class="input-group mt-10">
                <div class="input-group-prepend">
                    <label for="filter-from" class="input-group-text w-50"><?= lang('To', 'Bis') ?></label>
                </div>
                <input type="date" name="to" id="filter-to" class="form-control">
            </div>


            <h6><?= lang('More', 'Weiteres') ?></h6>
            <div class="custom-switch">
                <input type="checkbox" id="epub-switch" value="" onchange="filterEpub(this)">
                <label for="epub-switch"><?= lang('without Epub', 'ohne Epub') ?></label>
            </div>

        </div>
    </div>
</div>
<!-- </div> -->

<script src="<?= ROOTPATH ?>/js/datatables/jszip.min.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/dataTables.responsive.min.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/dataTables.buttons.min.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/buttons.html5.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script> -->

<!-- <link type="css" src="<?= ROOTPATH ?>/css/datatables.buttons.min.css"></link> -->
<!-- <link rel="stylesheet" href="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/css/searchPanes.dataTables.css">
<script src="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/js/dataTables.searchPanes.min.js"></script>
<script src="<?= ROOTPATH ?>/js/Select-1.5.0/js/dataTables.select.min.js"></script> -->

<script>
    const CARET_DOWN = ' <i class="ph ph-caret-down"></i>';
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control d-inline w-auto ml-10 ",
        sButtons: "d-inline-block ",
        sLengthSelect: "form-control d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
    // $.fn.DataTable.moment( 'HH:mm MMM D, YY' );
    var dataTable;

    const minEl = document.querySelector('#filter-from');
    const maxEl = document.querySelector('#filter-to');

    const activeFilters = $('#active-filters')
    const headers = [{
            title: lang('Quarter', 'Quartal'),
            'key': 'quarter'
        },
        {
            title: lang('Type', 'Typ'),
            'key': 'type'
        },
        {
            title: lang('Activity', 'Aktivität'),
            'key': 'activity'
        },
        {
            title: '',
            'key': 'links'
        },
        {
            title: lang('Print', 'Print'),
            'key': 'search-text'
        },
        {
            title: lang('Start', 'Start'),
            'key': 'start'
        },
        {
            title: lang('End', 'Ende'),
            'key': 'end'
        },
        {
            title: lang('Units', 'Einheiten'),
            'key': 'unit'
        },
        {
            title: lang('Online ahead of print'),
            'key': 'epub'
        },
        {
            title: lang('Type', 'Typ'),
            'key': 'type'
        },
        {
            title: lang('Subtype', 'Subtyp'),
            'key': 'subtype'
        },
        {
            title: lang('Title', 'Titel'),
            'key': 'title'
        },
        {
            title: lang('Authors', 'Autoren'),
            'key': 'authors'
        },
        {
            title: lang('Year', 'Jahr'),
            'key': 'year'
        },
    ]

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
            deferRender: true,
            responsive: true,
            // searchPanes: {
            //     layout: 'columns-1',
            //     // preSelect: [{
            //     //     rows: ['false'],
            //     //     column: 8
            //     // }],
            //     order: ['type', 'units', 'year', 'epub'],
            //     // cascadePanes: true,
            //     // viewTotal: true
            // },
            language: {
                url: lang(null, ROOTPATH + '/js/datatables/de-DE.json')
            },
            buttons: [{
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [4]
                    },
                    className: 'btn'
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 4, 5, 6, 9, 10, 11, 12, 13]
                    },
                    className: 'btn',
                    title: function() {
                        var filters = []
                        activeFilters.find('.badge').find('span').each(function(i, el) {
                            filters.push(el.innerHTML)
                        })
                        console.log(filters);
                        if (filters.length == 0) return "OSIRIS All Activities";
                        return 'OSIRIS ' + filters.join('_')
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        // columns: ':visible'
                        columns: [0, 5, 6, 9, 10, 11, 12, 13]
                    },
                    className: 'btn',
                    title: function() {
                        var filters = []
                        activeFilters.find('.badge').find('span').each(function(i, el) {
                            filters.push(el.innerHTML)
                        })
                        console.log(filters);
                        if (filters.length == 0) return "OSIRIS All Activities";
                        return 'OSIRIS ' + filters.join('_')
                    }
                },
                // {
                //     extend: 'pdfHtml5',
                //     exportOptions: {
                //         columns: [0, 1, 2, 5]
                //     },
                //     className: 'btn'
                // }
            ],
            dom: 'fBrtip',
            // dom: '<"dtsp-dataTable"frtip>',
            columnDefs: [{
                    targets: 0,
                    data: "quarter",
                    searchPanes: {
                        show: false
                    },
                },
                {
                    targets: 1,
                    data: 'icon'
                },
                {
                    targets: 2,
                    data: 'activity',
                },
                {
                    targets: 3,
                    data: 'links',
                    sortable: false,
                    className: 'unbreakable',
                },
                {
                    targets: 4,
                    data: 'search-text',
                    searchable: true,
                    visible: false,
                    searchPanes: {
                        show: false
                    },
                },
                {
                    targets: 5,
                    header: 'Start',
                    name: 'start',
                    data: 'start',
                    searchable: true,
                    visible: false,
                    searchPanes: {
                        show: true,
                        name: 'start',
                        header: 'Start'
                    },
                },
                {
                    targets: 6,
                    data: 'end',
                    searchable: true,
                    visible: false,
                    searchPanes: {
                        show: true,
                        name: 'end',
                        header: 'End'
                    },
                },
                {
                    targets: 7,
                    data: 'departments',
                    searchable: true,
                    visible: false,
                    // searchPanes: {
                    //     name: 'units',
                    //     header: lang('Organizational Units', 'Organisationseinheiten'),
                    //     orthogonal: 'sp'
                    // }
                },
                {
                    targets: 8,
                    data: 'epub',
                    visible: false,
                    // searchPanes: {
                    //     name: 'epub',
                    //     header: 'Online ahead of print',
                    //     initCollapsed: true
                    // }
                },
                {
                    targets: 9,
                    data: 'type',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 10,
                    data: 'subtype',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 11,
                    data: 'title',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 12,
                    data: 'authors',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 13,
                    data: 'year',
                    searchable: true,
                    visible: false,
                    // searchPanes: {
                    //     show: true,
                    //     name: 'year',
                    //     header: lang('Year', 'Jahr')
                    // },
                },
            ],
            "order": [
                [5, 'desc'],
                [1, 'asc']
            ],  
            <?php if (isset($_GET['q'])) { ?> "oSearch": {
                    "sSearch": "<?= $_GET['q'] ?>"
                }
            <?php } ?>

        });



        // Custom range filtering function
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var min = null,
                max = null;
            if (minEl.value !== null && minEl.value !== '')
                min = new Date(minEl.value);
            if (maxEl.value !== null && maxEl.value !== '')
                max = new Date(maxEl.value);

            var minDate = new Date(data[5]);
            var maxDate = new Date(data[6]);

            if (
                (min === null && max === null) ||
                (min === null && minDate <= max) ||
                (min <= minDate && max === null) ||
                (min < maxDate && minDate < max)) {
                return true;
            }

            return false;
        });

        <?php if (isset($_GET['type'])) { ?>
            window.location.hash = "type=<?= $_GET['type'] ?>";
        <?php } ?>

        var hash = readHash();
        if (hash.type !== undefined) {
            filterActivities(document.getElementById(hash.type + '-btn'), hash.type, 1)
        }
        if (hash.unit !== undefined) {
            filterActivities(document.getElementById(hash.unit + '-btn'), hash.unit, 7)
        }

        if (hash.start !== undefined) {
            minEl.value = hash.start
            minEl.dispatchEvent(new Event('input'));
        }
        if (hash.end !== undefined) {
            maxEl.value = hash.end
            maxEl.dispatchEvent(new Event('input'));
        }

    });






    function filterEpub() {
        if ($('#epub-switch').prop('checked')) {
            dataTable.columns(8).search("false", true, false, true).draw();
        } else {
            dataTable.columns(8).search("", true, false, true).draw();
        }
    }

    function filterActivities(btn, activity = null, column = 1) {
        var tr = $(btn).closest('tr')
        var table = tr.closest('table')
        $('#filter-' + column).remove()
        const field = headers[column]
        const hash = {}
        hash[field.key] = activity

        if (tr.hasClass('active') || activity === null) {
            hash[field.key] = null
            table.find('.active').removeClass('active')
            dataTable.columns(column).search("", true, false, true).draw();

        } else {

            table.find('.active').removeClass('active')
            tr.addClass('active')
            dataTable.columns(column).search(activity, true, false, true).draw();
            // indicator
            const filterBtn = $('<span class="badge" id="filter-' + column + '">')
            filterBtn.html(`<b>${field.title}:</b> <span>${activity}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                filterActivities(btn, null, column);
            })
            filterBtn.append(a)
            activeFilters.append(filterBtn)
        }
        writeHash(hash)

    }

    // Changes to the inputs will trigger a redraw to update the table
    minEl.addEventListener('input', function() {
        dataTable.draw();
        writeHash({
            start: minEl.value
        })

        $('#filter-5').remove()
        if (minEl.value != '') {
            const filterBtn = $('<span class="badge" id="filter-5">')
            filterBtn.html(`<b>Start:</b> <span>${minEl.value}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                minEl.value = ''
                minEl.dispatchEvent(new Event('input'));
            })
            filterBtn.append(a)
            activeFilters.append(filterBtn)
        }
    });
    maxEl.addEventListener('input', function() {
        dataTable.draw();
        writeHash({
            end: maxEl.value
        })

        $('#filter-6').remove()
        if (maxEl.value != '') {
            const filterBtn = $('<span class="badge" id="filter-6">')
            filterBtn.html(`<b>End:</b> <span>${maxEl.value}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                maxEl.value = ''
                maxEl.dispatchEvent(new Event('input'));
            })
            filterBtn.append(a)
            activeFilters.append(filterBtn)
        }

    });

    function resetTime() {
        minEl.value = ""
        maxEl.value = ""
        dataTable.draw();
        writeHash({
            time: null
        })
    }
</script>