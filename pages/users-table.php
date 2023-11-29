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
        margin: 0.5em;
        border: 1px solid var(--border-color);
        border-radius: 0.5em;
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
            width: 31%;
        }
    }
</style>
<a href="<?= ROOTPATH ?>/preview/persons" class="btn float-right"><i class="ph ph-eye"></i> <?= lang('Preview', 'Vorschau') ?></a>

<h1>
    <i class="ph ph-student"></i>
    <?= lang('Users', 'Personen') ?>
</h1>

<?php if (isset($_GET['inactive'])) { ?>
    <a href="?" class="btn float-right active"><?= lang('See inactive users', 'Zeige inaktive Personen') ?></a>
<?php } else { ?>
    <a href="?inactive" class="btn float-right"><?= lang('See inactive users', 'Zeige inaktive Personen') ?></a>
<?php } ?>

<table class="table cards" id="result-table" id="user-table">
    <thead>
        <th></th>
        <th></th>
    </thead>
    <tbody>

        <?php
        $filter = ['username' => ['$ne' => null]];
        if (!isset($_GET['inactive'])) {
            $filter['is_active'] = true;
        }
        $result = $osiris->persons->find($filter);
        $result = $DB->doc2Arr($result);

        foreach ($result as $document) {
            $username = strval($document['username']);
            $img = ROOTPATH . "/img/no-photo.png";
            if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
            }
        ?>
            <tr class="">
                <td>
                    <img src="<?= $img ?>" alt="" class="rounded">
                </td>
                <td class="flex-grow-1">
                    <div class="w-full">
                        <!-- hidden field for sorting without title -->
                        <div style="display: none;"><?= $document['first'] ?> <?= $document['last'] ?></div>
                        <span class="float-right text-muted"><?=$document['username']?></span>
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

            </tr>
        <?php
        }
        ?>
    </tbody>
</table>



<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
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
    $(document).ready(function() {
        dataTable = $('#result-table').DataTable({
            // searchPanes: {
            //     viewTotal: true,
            //     columns: [6]
            // },
            // searchPanes: true,
            // dom: 'Plfrtip',
            dom: 'frtipP',
            // searchPanes: {
            //     initCollapsed: true,
            //     columns: [4],
            //     // preSelect: [{
            //     //     column: 7,
            //     //     rows: ['yes']
            //     // }]
            // },

            columnDefs: [{
                targets: [0],
                searchable: false,
                sortable: false,
                visible: true
            }],
            "order": [
                [1, 'asc'],
            ],

            paging: true,
            autoWidth: true,
            pageLength: 18,
            // "search": {
            //     "search": "1"
            // }
        });
    });


    function editUser(id) {
        // loadModal();

        $.ajax({
            type: "GET",
            dataType: "html",
            // data: {},
            url: ROOTPATH + '/form/user/' + id,
            success: function(response) {
                $('#modal-content').html(response)
                $('#the-modal').addClass('show')

                console.log($('#the-modal form'));
                $('#the-modal form').on('submit', function(event, element) {
                    event.preventDefault()
                    data = {}
                    var raw = objectifyForm(this)
                    console.log(raw);
                    for (var key in raw) {
                        var val = raw[key];
                        if (key.includes('values')) {
                            key = key.slice(7).replace(']', '')
                            data[key] = val
                        }
                    }
                    console.log(data);
                    // return
                    _updateUser(id, data)
                    $('#the-modal').removeClass('show')
                    toastWarning("Table will be updated after reload.")

                    return false;
                })
            },
            error: function(response) {
                console.log(response);
                toastError(response.responseText)
                $('.loader').removeClass('show')
            }
        })

    }
</script>