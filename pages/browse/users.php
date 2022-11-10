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
        <th>user</th>
        <th><?= lang('Last name', 'Nachname') ?></th>
        <th><?= lang('First name', 'Vorname') ?></th>
        <th><?= lang('Dept', 'Abteilung') ?></th>
        <th><?= lang('Details') ?></th>
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
        $result = $osiris->users->find(['is_active'=>true])->toArray();

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
                <td><?= $document['department'] ?></td>
                <td><?= $document['unit'] ?></td>
                <td>
                    <span class="hidden"><?= $document['is_scientist'] ?></span>
                    <?= bool_icon($document['is_scientist']) ?>
                </td>
                <!-- <td><?= bool_icon($document['is_active']) ?></td> -->
                    <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
                <td>
                    <a href="<?= ROOTPATH ?>/edit/user/<?= $document['_id'] ?>" class="btn btn-link">
                        <i class="fas fa-edit"></i>
                    </a>
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
</script>