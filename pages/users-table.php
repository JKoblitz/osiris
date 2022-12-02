<h1 class="mt-0">
    <i class="fad fa-user-graduate"></i>
    <?= lang('Users', 'Nutzer:innen') ?>
</h1>
<p class="text-muted">
    Achtung: Einteilung und Klassifizierung der Nutzer erfolgte automatisch
    und ist in vielen Fällen noch nicht korrekt!
</p>


<table class="table dataTable" id="result-table">
    <thead>
        <th>User</th>
        <th><?= lang('Last name', 'Nachname') ?></th>
        <th><?= lang('First name', 'Vorname') ?></th>
        <th><?= lang('Dept', 'Abteilung') ?></th>
        <th><?= lang('Phone', 'Telefon') ?></th>
        <th><?= lang('Comment', 'Kommentar') ?></th>
        <th><?= lang('Scientist', 'Wissenschaftler:in') ?></th>
        <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
            <th></th>
        <?php
        }
        ?>
    </thead>
    <tbody>

        <?php
        $result = $osiris->users->find(['is_active' => true])->toArray();

        foreach ($result as $document) {
        ?>
            <tr class="row-<?= $document['dept'] ?>">
                <td><a href="<?= ROOTPATH ?>/profile/<?= $document['_id'] ?>"><?= $document['_id'] ?></a></td>
                <td><?= $document['academic_title'] ?? '' ?> <?= $document['last'] ?></td>
                <td><?= $document['first'] ?></td>
                <td>
                    <?php if ($document['is_leader']) { ?>
                        <strong><?= $document['dept'] ?></strong>
                    <?php } else { ?>
                        <?= $document['dept'] ?>
                    <?php } ?>

                </td>
                <td><?php
                    if (!empty($document['telephone'])) {
                        // $ph = explode('-', $document['telephone']);
                        $ph = str_replace('+49', '0', $document['telephone']);
                        $ph = preg_replace('/^0531-?/', '', $ph);
                        $ph = preg_replace('/^2616-?/', '', $ph);
                        echo $ph;
                    }
                    ?></td>
                <td><?= $document['unit'] ?></td>
                <td>
                    <!-- <span class="hidden"><?= $document['is_scientist'] ?></span> -->
                    <?= bool_icon($document['is_scientist']) ?>
                </td>
                <!-- <td><?= bool_icon($document['is_active']) ?></td> -->
                <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
                    <td>
                        <!-- <a href="<?= ROOTPATH ?>/edit/user/<?= $document['_id'] ?>" class="btn btn-link">
                        <i class="fas fa-edit"></i>
                    </a> -->
                        <btn class="btn btn-link" type="button" onclick="editUser('<?= $document['_id'] ?>')">
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
                [0, 'asc'],
            ]
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
                        if (key.includes('values')){
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