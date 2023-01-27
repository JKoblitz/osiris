<h1 class="mt-0">
    <i class="fad fa-user-graduate"></i>
    <?= lang('Users', 'Nutzer:innen') ?>
</h1>
<!-- <p class="text-muted">
    Achtung: Einteilung und Klassifizierung der Nutzer erfolgte automatisch
    und ist in vielen Fällen noch nicht korrekt!
</p> -->

<style>
    .table {
        border-collapse: separate !important;
        border-spacing: 0 .5rem;
        background-color: transparent;
        border: none;
        box-shadow: none;
    }

    .table tr {
        background-color: white;
        -webkit-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        -moz-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.15);
    }

    .table tr td,
    .table tr th {
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .table tr td:first-child,
    .table tr th:first-child {
        border-left: 1px solid var(--border-color);
    }

    .table tr td:last-child,
    .table tr th:last-child {
        border-right: 1px solid var(--border-color);
    }

    .table img {
        display: block;
        width: 5.2rem;
        border: 1px solid var(--border-color);
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
        <th><?= lang('Comment', 'Kommentar') ?></th>
        <th><?= lang('Scientist', 'Wissenschaftler:in') ?></th>
        <th><?= lang('Active', 'Aktiv') ?></th>
        <th><?= lang('Leader', 'Leiter:in') ?></th>
        <th><?= lang('Gender', 'Geschlecht') ?></th>
        <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
            <th></th>
        <?php
        }
        ?>
    </thead>
    <tbody>

        <?php
        $result = $osiris->users->find(['is_active'=>true])->toArray();

        foreach ($result as $document) {
            $username = strval($document['_id']);
            $img = ROOTPATH . "/img/person.jpg";
            if (file_exists(BASEPATH . "/img/users/${username}_sm.jpg")) {
                $img = ROOTPATH . "/img/users/${username}_sm.jpg";
            }
        ?>
            <tr class="">
                <td class="p-0">
                    <img src="<?= $img ?>" alt="">
                </td>
                <td><a href="<?= ROOTPATH ?>/profile/<?= $username ?>"><?= $username ?></a></td>
                <td><?= $document['academic_title'] ?? '' ?> <?= $document['last'] ?></td>
                <td><?= $document['first'] ?></td>
                <td class="text-<?= $document['dept'] ?>">
                    <?php if ($document['is_leader']) { ?>
                        <strong><?= $document['dept'] ?></strong>
                    <?php } else { ?>
                        <?= $document['dept'] ?>
                    <?php } ?>

                </td>
                <td><?php
                    if (!empty($document['telephone'])) {
                        $ph = str_replace('+49', '0', $document['telephone']);
                        $ph = preg_replace('/^0531-?/', '', $ph);
                        $ph = preg_replace('/^2616-?/', '', $ph);
                        echo $ph;
                    }
                    ?></td>
                <td><?= $document['unit'] ?></td>
                <td><?= bool_icon($document['is_scientist'] ?? false) ?> <span class="hidden"><?= $document['is_scientist'] ?></span></td>
                <td><?= bool_icon($document['is_active'] ?? false) ?> <span class="hidden"><?= $document['is_active'] ?></span></td>
                <td><?= bool_icon($document['is_leader'] ?? false) ?> <span class="hidden"><?= $document['is_leader'] ?></span></td>
                <td><?= $document['gender'] ?? 'n' ?></td>
                <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
                    <td>
                        <btn class="btn btn-link" type="button" onclick="editUser('<?= $username ?>')">
                            <i class="fas fa-edit"></i>
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
        sFilterInput: "form-control form-control-sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control form-control-sm d-inline w-auto",
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
            searchPanes: {
                initCollapsed: true,
                columns: [4, 7, 8, 9, 10]
            },

            columnDefs: [{
                    targets: [0],
                    searchable: false,
                    sortable: false,
                    visible: true
                },
                {
                    targets: [4, 7],
                    searchable: true,
                    visible: true
                },
                {
                    targets: [9, 10],
                    searchable: true,
                    visible: false
                }, 
                {
                    targets: [8],
                    searchable: true,
                    visible: false,
                    searchPanes: {
                        preSelect: ['1']
                    }
                },
            ],
            "order": [
                [1, 'asc'],
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