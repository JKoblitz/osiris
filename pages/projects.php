<?php

/**
 * Page to see all projects
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /projects
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
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

<a href="<?= ROOTPATH ?>/projects/new" class="btn primary float-sm-right mb-10">
    <i class="ph ph-plus"></i>
    <?= lang('Add new project', 'Neues Projekt anlegen') ?>
</a>
<h1 class="mt-0">
    <i class="ph ph-tree-structure text-osiris"></i>
    <?= lang('Projects', 'Projekte') ?>
</h1>

<div class="btn-toolbar float-sm-right">
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
        <th><?= lang('Third-party funder', 'Drittmittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Contact person', 'Kontaktperson') ?></th>
        <th><?= lang('# activities', '# Aktivitäten') ?></th>
        <th><?= lang('Status') ?></th>
    </thead>
    <tbody>
        <?php
        $projects = $osiris->projects->find([]);
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
                    (<?= $project['funding_number'] ?? '-' ?>)
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
        dataTable = $('#project-table').DataTable({
            dom: 'frtipP',
            "order": [
                [2, 'desc'],
            ]
        });
        
        $('#project-table_wrapper').prepend($('.btn-toolbar'))
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