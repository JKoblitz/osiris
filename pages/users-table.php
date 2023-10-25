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

<h1 class="mt-0">
    <i class="ph ph-student"></i>
    <?= lang('Users', 'Nutzer:innen') ?>
</h1>

<?php if (isset($_GET['inactive'])) { ?>
    <a href="?" class="btn float-right active"><?= lang('See inactive users', 'Zeige inaktive Nutzer:innen') ?></a>
<?php } else { ?>
    <a href="?inactive" class="btn float-right"><?= lang('See inactive users', 'Zeige inaktive Nutzer:innen') ?></a>
<?php } ?>

<style>

    .table img {
        display: block;
        width: 4.2rem;
        border-radius: var(--border-radius);
    }
</style>

<table class="table dataTable" id="result-table" id="user-table">
    <thead>
        <th></th>
        <th>User</th>
        <th><?= lang('Last name', 'Nachname') ?></th>
        <th><?= lang('First name', 'Vorname') ?></th>
        <th><?= lang('Dept', 'Abteilung') ?></th>
        <th><?= lang('Phone', 'Telefon') ?></th>
        <?php if ($Settings->hasPermission('edit-user-profile')) { ?>
            <th></th>
        <?php
        }
        ?>
    </thead>
    <tbody>

        <?php
        $filter = ['username' => ['$ne' => null]];
        if (!isset($_GET['inactive'])) {
            // $users = $osiris->account->distinct('username', ['is_active'=>false]);
            // $filter['username'] = ['$nin'=>$users];
            $filter['is_active'] = true;
        }
        $result = $osiris->persons->find($filter);
        $result = $DB->doc2Arr($result);

        foreach ($result as $document) {
            $username = strval($document['username']);
            $img = ROOTPATH . "/img/person.jpg";
            if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
            }
        ?>
            <tr class="">
                <td class="py-5">
                    <img src="<?= $img ?>" alt="">
                </td>
                <td><a href="<?= ROOTPATH ?>/profile/<?= $username ?>"><?= $username ?></a></td>
                <td><?= $document['academic_title'] ?? '' ?> <?= $document['last'] ?></td>
                <td><?= $document['first'] ?></td>
                <td class="text-<?= $document['dept'] ?? '' ?>">
                    <?php if ($document['is_leader'] ?? false) { ?>
                        <strong><?= $document['dept'] ?? '' ?></strong>
                    <?php } else { ?>
                        <?= $document['dept'] ?? '' ?>
                    <?php } ?>

                </td>
                <td><?php
                    if (!empty($document['telephone'] ?? '')) {
                        $ph = str_replace('+49', '0', $document['telephone']);
                        $ph = preg_replace('/^0531-?/', '', $ph);
                        $ph = preg_replace('/^2616-?/', '', $ph);
                        echo $ph;
                    }
                    ?></td>
                <?php if ($Settings->hasPermission('edit-user-profile')) { ?>
                    <td>
                        <btn class="btn link" type="button" onclick="editUser('<?= $username ?>')">
                            <i class="ph ph-note-pencil"></i>
                        </btn>
                    </td>
                <?php
                }
                ?>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>



<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/css/searchPanes.dataTables.css">
<script src="<?= ROOTPATH ?>/js/SearchPanes-2.1.0/js/dataTables.searchPanes.min.js"></script>
<script src="<?= ROOTPATH ?>/js/Select-1.5.0/js/dataTables.select.min.js"></script>

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
            searchPanes: {
                viewTotal: true,
                columns: [6]
            },
            searchPanes: true,
            // dom: 'Plfrtip',
            dom: 'frtipP',
            searchPanes: {
                initCollapsed: true,
                columns: [4],
                // preSelect: [{
                //     column: 7,
                //     rows: ['yes']
                // }]
            },

            columnDefs: [{
                    targets: [0],
                    searchable: false,
                    sortable: false,
                    visible: true
                }
            ],
            "order": [
                [2, 'asc'],
            ],
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