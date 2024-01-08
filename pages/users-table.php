<?php

/**
 * Page to browse all users
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /user/browse
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>
<style>
    .table.cards {
        border: none;
        background: transparent;
        box-shadow: none;
    }

    .table.cards thead {
        display: none;
    }

    .table.cards tbody {
        display: flex;
        flex-grow: column;
        flex-direction: row;
        flex-wrap: wrap;

    }

    .table.cards tbody tr {
        width: 100%;
        margin: 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        background: white;
        display: flex;
        align-items: center;
    }

    .table.cards tbody tr td {
        border: 0;
        box-shadow: none;
        /* width: 100%; */
        height: 100%;
        display: block;
    }

    .table.cards tbody tr img,
    .table#persons img {
        max-height: 6rem;
    }

    .table.cards tbody tr td {
        display: flex;
        align-items: center;
        border: 0;
    }

    @media (min-width: 768px) {
        .table.cards tbody tr {
            width: 48%;
        }
    }

    @media (min-width: 1200px) {
        .table.cards tbody tr {
            width: calc(33.3% - 1rem);
        }
    }
</style>
<?php if ($Settings->featureEnabled('portal')) { ?>
    <a href="<?= ROOTPATH ?>/preview/persons" class="btn float-right"><i class="ph ph-eye"></i> <?= lang('Preview', 'Vorschau') ?></a>
<?php } ?>
<h1>
    <i class="ph ph-student"></i>
    <?= lang('Users', 'Personen') ?>
</h1>

<div class="row row-eq-spacing">
    <div class="col-lg-9">

        <table class="table cards w-full" id="user-table">
            <thead>
                <th></th>
                <th></th>
                <th></th>
            </thead>
            <tbody>

                <?php
                $filter = ['username' => ['$ne' => null]];
                // if (!isset($_GET['inactive'])) {
                //     $filter['is_active'] = true;
                // }
                $result = $osiris->persons->find($filter);
                $result = $DB->doc2Arr($result);

                foreach ($result as $document) {
                    $username = strval($document['username']);
                    // $img = ROOTPATH . "/img/no-photo.png";
                    // if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                    //     $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
                    // }
                ?>
                    <tr class="">
                        <td>
                            <!-- <img src="<?= ROOTPATH ?>/user/picture/<?=$username?>" alt="" class="rounded"> -->
                            <?=$DB->printProfilePicture($username, 'profile-img')?>
                        </td>
                        <td class="flex-grow-1">
                            <div class="w-full">
                                <!-- hidden field for sorting without title -->
                                <div style="display: none;"><?= $document['first'] ?> <?= $document['last'] ?></div>
                                <span class="float-right text-muted"><?= $document['username'] ?></span>
                                <h5 class="my-0">
                                    <a href="<?= ROOTPATH ?>/profile/<?= $username ?>" class="">
                                        <?= $document['academic_title'] ?? '' ?>
                                        <?= $document['first'] ?>
                                        <?= $document['last'] ?>
                                    </a>
                                </h5>
                                <small>
                                    <?php
                                    foreach ($document['depts'] as $i => $d) {
                                        $dept = implode('/', $Groups->getParents($d));
                                    ?>
                                        <a href="<?= ROOTPATH ?>/groups/view/<?= $d ?>">
                                            <?= $dept ?>
                                        </a>
                                    <?php } ?>
                                </small>
                            </div>
                        </td>
                        <td>
                            <?= $Groups->personDept($document['depts'], 1)['id'] ?>
                        </td>

                        <td>
                            <?= ($document['is_active'] ?? true) ? 'yes' : 'no' ?>
                        </td>

                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

    </div>
    </style>

    <div class="col-lg-3 d-none d-lg-block">

        <div class="on-this-page-filter filters content" id="filters">
            <!-- <div class="content" > -->
            <div class="title">Filter</div>

            <!-- <div id="searchpanes"></div> -->

            <div id="active-filters"></div>

            <h6>
                <?= lang('By organisational unit', 'Nach Organisationseinheit') ?>
                <a class="float-right" onclick="filterUnit('#filter-unit .active', null)"><i class="ph ph-x"></i></a>
            </h6>
            <div class="filter">
                <table id="filter-unit" class="table simple">
                    <?php foreach ($Departments as $id => $dept) { ?>
                        <tr <?= $Groups->cssVar($id) ?>>
                            <td>
                                <a data-type="<?= $id ?>" onclick="filterUnit(this, '<?= $id ?>')" class="item d-block colorless" id="<?= $id ?>-btn">
                                    <span><?= $dept ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <!-- <h6>
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


           -->
            <h6><?= lang('Active workers', 'Aktive Mitarbeitende') ?></h6>
            <div class="custom-switch">
                <input type="checkbox" id="active-switch" value="" onchange="filterActive(this)">
                <label for="active-switch"><?= lang('include Inactive', 'Inkl. Inaktiv') ?></label>
            </div>
        </div>
    </div>
</div>


<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/dataTables.responsive.min.js"></script>
<!-- <link rel="stylesheet" href="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/css/searchPanes.dataTables.css">
<script src="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/js/dataTables.searchPanes.min.js"></script>
<script src="<?= ROOTPATH ?>/js/Select-1.5.0/js/dataTables.select.min.js"></script> -->

<script>
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control sm d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
    var dataTable;
    const activeFilters = $('#active-filters')
    $(document).ready(function() {
        dataTable = $('#user-table').DataTable({
            dom: 'frtipP',
            deferRender: true,
            responsive: true,
            language: {
                url: lang(null, ROOTPATH + '/js/datatables/de-DE.json')
            },
            columnDefs: [{
                targets: [0],
                searchable: false,
                sortable: false,
                visible: true
            }, {
                targets: [2, 3],
                searchable: true,
                sortable: false,
                visible: false
            }],
            "order": [
                [1, 'asc'],
            ],

            paging: true,
            autoWidth: true,
            pageLength: 18,
        });
        filterActive();

        var hash = readHash();
        if (hash.unit !== undefined) {
            filterUnit(document.getElementById(hash.unit + '-btn'), hash.unit)
        }
    });

    function filterUnit(btn, unit = null) {
        const column = 2
        var tr = $(btn).closest('tr')
        var table = tr.closest('table')
        $('#filter-' + column).remove()
        const hash = {}
        hash.unit = unit

        if (tr.hasClass('active') || unit === null) {
            hash.unit = null
            table.find('.active').removeClass('active')
            dataTable.columns(column).search("", true, false, true).draw();

        } else {
            table.find('.active').removeClass('active')
            tr.addClass('active')
            dataTable.columns(column).search(unit, true, false, true).draw();
            // indicator
            const filterBtn = $('<span class="badge" id="filter-' + column + '">')
            filterBtn.html(`<b>${lang('Unit', 'Einheit')}:</b> <span>${unit}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                filterUnit(btn, null, column);
            })
            filterBtn.append(a)
            activeFilters.append(filterBtn)
        }
        writeHash(hash)

    }

    function filterActive() {
        if ($('#active-switch').prop('checked')) {
            dataTable.columns(3).search("", true, false, true).draw();
        } else {
            dataTable.columns(3).search("yes", true, false, true).draw();
        }
    }


    // function editUser(id) {
    //     // loadModal();

    //     $.ajax({
    //         type: "GET",
    //         dataType: "html",
    //         // data: {},
    //         url: ROOTPATH + '/form/user/' + id,
    //         success: function(response) {
    //             $('#modal-content').html(response)
    //             $('#the-modal').addClass('show')

    //             console.log($('#the-modal form'));
    //             $('#the-modal form').on('submit', function(event, element) {
    //                 event.preventDefault()
    //                 data = {}
    //                 var raw = objectifyForm(this)
    //                 console.log(raw);
    //                 for (var key in raw) {
    //                     var val = raw[key];
    //                     if (key.includes('values')) {
    //                         key = key.slice(7).replace(']', '')
    //                         data[key] = val
    //                     }
    //                 }
    //                 console.log(data);
    //                 // return
    //                 _updateUser(id, data)
    //                 $('#the-modal').removeClass('show')
    //                 toastWarning("Table will be updated after reload.")

    //                 return false;
    //             })
    //         },
    //         error: function(response) {
    //             console.log(response);
    //             toastError(response.responseText)
    //             $('.loader').removeClass('show')
    //         }
    //     })

    // }
</script>