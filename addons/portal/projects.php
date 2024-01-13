
<?php 
require_once BASEPATH . "/php/Project.php";
$Project = new Project();

// $Format = new Document(true);
$form = $form ?? array();

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

?>

<style>
    .index {
        /* color: transparent; */
        height: 1rem;
        width: 1rem;
        background-color: transparent;
        border-radius: 50%;
        display: inline-block;
        margin-left: .5rem;
    }

    .index.active {
        background-color: var(--primary-color);
        box-shadow: 0 0 3px 0.2rem rgba(238, 114, 3, 0.6);
    }
</style>
<div class="container">
<!--     
<div class="btn-toolbar float-right">
    <a href="<?= ROOTPATH ?>/visualize/map" class="btn primary">
        <i class="ph ph-map-trifold"></i>
        <?= lang('Show on map', 'Zeige auf Karte') ?>
    </a>
    <a href="#<?= ROOTPATH ?>/visualize/projects" class="btn primary" onclick="todo()">
        <i class="ph ph-chart-line-up"></i>
        <?= lang('Show metrics', 'Zeige Metriken') ?>
    </a>
</div> -->

<h1 class="mt-0">
    <i class="ph ph-tree-structure text-osiris"></i>
    <?= lang('Projects', 'Projekte') ?>
</h1>



<table class="table" id="project-table">
    <thead>
        <th><?= lang('Name') ?></th>
        <!-- <th><?= lang('Title', 'Title') ?></th> -->
        <th><?= lang('Funder', 'Mittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Contact person', 'Kontaktperson') ?></th>
        <th><?= lang('# activities', '# Aktivitäten') ?></th>
    </thead>
    <tbody>
        <?php
        foreach ($data as $project) {
            $Project->setProject($project);
        ?>
            <tr id="<?= $project['_id'] ?>">
                <td>
                    <a href="<?= PORTALPATH ?>/project/<?= $project['_id'] ?>">
                        <?= $project['name'] ?>
                    </a>
                </td>
                <td>
                    <?= $project['funder'] ?? '-' ?>
                    (<?= $Project->getFundingNumbers('<br>') ?>)
                </td>
                <td>
                    <?= $Project->getDateRange() ?>
                </td>
                <td>
                    <?= $Project->getRole() ?>
                </td>
                <td>
                    <a href="<?= PORTALPATH ?>/person/<?= $project['contact'] ?? '' ?>"><?= $DB->getNameFromId($project['contact'] ?? '') ?></a>
                </td>
                <td>
                    <?php
                    echo $osiris->activities->count(['projects' => strval($project['name'])]);
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

</div>



<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>

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
        dataTable = $('#project-table').DataTable({
            dom: 'frtipP',
            "order": [
                [2, 'desc'],
            ]
        });

        $('#project-table_wrapper').prepend($('.filters'))
    });

    // function filterStatus(btn, status) {
    //     let active = $(btn).hasClass('active')
    //     $('#filter-status').find('.active').removeClass('active')
    //     if (!active) {
    //         dataTable.columns(6).search(status, true, false, true).draw();
    //         $('#filter-status').find('.index').addClass('active')
    //         $(btn).addClass('active')
    //     } else
    //         dataTable.columns(6).search("", true, false, true).draw();
    // }

    // function filterRole(btn, role) {
    //     let active = $(btn).hasClass('active')
    //     $('#filter-role').find('.active').removeClass('active')
    //     if (!active) {
    //         dataTable.columns(3).search(role, true, false, true).draw();
    //         $('#filter-role').find('.index').addClass('active')
    //         $(btn).addClass('active')
    //     } else
    //         dataTable.columns(3).search("", true, false, true).draw();
    // }
</script>