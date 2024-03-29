<?php

/**
 * Page to see all projects
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /projects
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

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

$pagetitle = lang('Projects', 'Projekte');
$filter = [];
if (!$Settings->hasPermission('projects.view')) {
    $filter = [
        'persons.user' => $_SESSION['username']
    ];
    $pagetitle = lang('My projects', 'Meine Projekte');
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

<div class="btn-toolbar float-right">
    <a href="<?= ROOTPATH ?>/visualize/map" class="btn primary">
        <i class="ph ph-map-trifold"></i>
        <?= lang('Show on map', 'Zeige auf Karte') ?>
    </a>
    <!-- <a href="#<?= ROOTPATH ?>/visualize/projects" class="btn primary" onclick="todo()">
        <i class="ph ph-chart-line-up"></i>
        <?= lang('Show metrics', 'Zeige Metriken') ?>
    </a> -->
</div>

<h1 class="mt-0">
    <i class="ph ph-tree-structure text-osiris"></i>
    <?= $pagetitle ?>
</h1>


<?php if ($Settings->hasPermission('projects.add')) { ?>
    <a href="<?= ROOTPATH ?>/projects/new" class="mb-10 d-inline-block">
        <i class="ph ph-plus"></i>
        <?= lang('Add new project', 'Neues Projekt anlegen') ?>
    </a>
<?php } ?>


<div class="btn-toolbar float-sm-right filters">
    <span>
        <i class="ph ph-funnel-simple"></i>
        <span class="sr-only">Filters</span>
    </span>
    <div class="dropdown toggle-on-hover" id="filter-status">
        <button class="btn" data-toggle="dropdown" type="button" id="filter-status-btn" aria-haspopup="true" aria-expanded="false">
            <i class="ph ph-caret-down"></i>
            Status
            <span class="index"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filter-status-btn" style="min-width: auto;">
            <a onclick="filterStatus(this, '<?= lang('approved', 'bewilligt') ?>')" class="item"><span class='badge success'><?= lang('approved', 'bewilligt') ?></span></a>
            <a onclick="filterStatus(this, '<?= lang('finished', 'abgeschlossen') ?>')" class="item"><span class='badge success'><?= lang('finished', 'abgeschlossen') ?></span></a>
            <a onclick="filterStatus(this, '<?= lang('applied', 'beantragt') ?>')" class="item"><span class='badge signal'><?= lang('applied', 'beantragt') ?></span></a>
            <a onclick="filterStatus(this, '<?= lang('rejected', 'abgelehnt') ?>')" class="item"><span class='badge danger'><?= lang('rejected', 'abgelehnt') ?></span></a>
            <a onclick="filterStatus(this, '<?= lang('expired', 'abgelaufen') ?>')" class="item"><span class='badge dark'><?= lang('expired', 'abgelaufen') ?></span></a>
        </div>
    </div>

    <div class="dropdown toggle-on-hover" id="filter-role">
        <button class="btn" data-toggle="dropdown" type="button" id="filter-role-btn" aria-haspopup="true" aria-expanded="false">
            <i class="ph ph-caret-down"></i>
            Role
            <span class="index"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filter-role-btn" style="min-width: auto;">
            <a onclick="filterRole(this, '<?= lang('Coordinator', 'Koordinator') ?>')" class="item"><span class='badge'><i class="ph ph-crown text-signal"></i> <?= lang('Coordinator', 'Koordinator') ?></span></a>
            <a onclick="filterRole(this, '<?= lang('Partner') ?>')" class="item"><span class='badge'><i class="ph ph-handshake text-muted"></i> <?= lang('Partner') ?></span></a>
        </div>
    </div>

</div>

<table class="table" id="project-table">
    <thead>
        <th><?= lang('ID', 'ID') ?></th>
        <!-- <th><?= lang('Title', 'Title') ?></th> -->
        <th><?= lang('Funder', 'Mittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Contact person', 'Kontaktperson') ?></th>
        <th><?= lang('# activities', '# Aktivitäten') ?></th>
        <th><?= lang('Status') ?></th>
    </thead>
    <tbody>
        <?php
        $projects = $osiris->projects->find($filter);
        foreach ($projects as $project) {
            $Project->setProject($project);
        ?>
            <tr id="<?= $project['_id'] ?>">
                <td>
                    <a href="<?= ROOTPATH ?>/projects/view/<?= $project['_id'] ?>">
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
                    <a href="<?= ROOTPATH ?>/profile/<?= $project['contact'] ?? '' ?>"><?= $DB->getNameFromId($project['contact'] ?? '') ?></a>
                </td>
                <td>
                    <?php
                    echo $osiris->activities->count(['projects' => strval($project['name'])]);
                    ?>
                </td>
                <td>
                    <?= $Project->getStatus() ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



<script>
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

    function filterStatus(btn, status) {
        let active = $(btn).hasClass('active')
        $('#filter-status').find('.active').removeClass('active')
        if (!active) {
            dataTable.columns(6).search(status, true, false, true).draw();
            $('#filter-status').find('.index').addClass('active')
            $(btn).addClass('active')
        } else
            dataTable.columns(6).search("", true, false, true).draw();
    }

    function filterRole(btn, role) {
        let active = $(btn).hasClass('active')
        $('#filter-role').find('.active').removeClass('active')
        if (!active) {
            dataTable.columns(3).search(role, true, false, true).draw();
            $('#filter-role').find('.index').addClass('active')
            $(btn).addClass('active')
        } else
            dataTable.columns(3).search("", true, false, true).draw();
    }
</script>