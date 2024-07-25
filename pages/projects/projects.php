<?php

/**
 * Page to see all projects
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /projects
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

//  TODO: rowGroup nach Mittelgeber bzw. status

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
        '$or' => [
            ['persons.user' => $_SESSION['username']],
            ['created_by' => $_SESSION['username']],
            ['contact' => $_SESSION['username']]
        ]
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
        background-color: var(--secondary-color);
        box-shadow: 0 0 3px 0.2rem rgba(238, 114, 3, 0.6);
    }
    table.dataTable td.dt-control:before {
    display: inline-block;
    box-sizing: border-box;
    content: "";
    border-top: 5px solid transparent;
    border-left: 10px solid rgba(0, 0, 0, 0.5);
    border-bottom: 5px solid transparent;
    border-right: 0px solid transparent;
}
</style>

<div class="btn-toolbar float-right">
    <a href="<?= ROOTPATH ?>/visualize/map" class="btn secondary">
        <i class="ph ph-map-trifold"></i>
        <?= lang('Show on map', 'Zeige auf Karte') ?>
    </a>
    <!-- <a href="#<?= ROOTPATH ?>/visualize/projects" class="btn secondary" onclick="todo()">
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
        <th></th>
        <th><?= lang('ID', 'ID') ?></th>
        <th><?= lang('Type', 'Art') ?></th>
        <th><?= lang('Funder', 'Mittelgeber') ?></th>
        <th><?= lang('Project time', 'Projektlaufzeit') ?></th>
        <th><?= lang('Role', 'Rolle') ?></th>
        <th><?= lang('Applicant', 'Antragsteller:in') ?></th>
        <th><?= lang('Status') ?></th>
    </thead>
    <tbody>
        <tr>
            <td colspan="8" class="text-center">
                <i class="ph ph-spinner-third text-muted"></i>
                <?= lang('Loading projects', 'Lade Projekte') ?>
            </td>
        </tr>
    </tbody>
</table>



<script>
    var dataTable;

    // Formatting function for row details - modify as you need
    function format(d) {
        // `d` is the original data object for the row
        return (
            `
            <dl>
            <dt>Full name:</dt>
            <dd>${d.title}</dd>
            <dt>Funding numbers:</dt>
            <dd>${d.funding_numbers}</dd>
            <dt>Partners:</dt>
            <dd>${d.collaborators ? d.collaborators.length : 0}</dd>
            </dl>
            `
        );
    }



    $(document).ready(function() {
        dataTable = new DataTable('#project-table', {
            ajax: {
                url: '<?= ROOTPATH ?>/api/projects',
                // add data to the request
                data: {
                    json: '<?= json_encode($filter) ?>',
                    formatted: true
                },
            },
            type: 'GET',
            dom: 'frtipP',
            columns: [{
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {
                    data: 'name',
                    render: function(data, type, row) {
                        return `<a href="<?= ROOTPATH ?>/projects/view/${row.id}">${data}</a>`
                    }
                },
                {
                    data: 'type',
                    render: function(data) {
                        if (data == 'Eigenfinanziert') {
                            return `<span class="badge text-signal">
                        <i class="ph ph-piggy-bank"></i>
                        ${lang('Self-funded', 'Eigenfinanziert')}
                        </span>`
                        }
                        if (data == 'Stipendium') {
                            return `<span class="badge text-success no-wrap">
                        <i class="ph ph-tip-jar"></i>
                        ${lang('Stipendiate', 'Stipendium')}
                        </span>`
                        }
                        if (data == 'Drittmittel') {
                        return `<span class="badge text-danger">
                        <i class="ph ph-hand-coins"></i>
                        ${lang('Third-party funded', 'Drittmittel')}
                        </span>`
                        }
                    }
                },
                {
                    data: 'funder', render: function(data, type, row) {
                        if (!data && row.scholarship) return row.scholarship;
                        return data;
                    }
                },
                {
                    data: 'date_range'
                },
                {
                    data: 'role', render: function(data) {
                        if (data == 'coordinator') {
                            return `<span class="badge text-signal">
                        <i class="ph ph-crown"></i>
                        ${lang('Coordinator', 'Koordinator')}
                        </span>`
                        }
                        return `<span class="badge text-muted">
                        <i class="ph ph-handshake"></i>
                        ${lang('Partner')}
                        </span>`
                    }
                },
                {
                    data: 'applicant',
                    render: function(data, type, row) {
                        if (!row.contact && row.supervisor)  
                            return `<a href="<?= ROOTPATH ?>/profile/${row.supervisor}">${data}</a>`;
                        if (!row.contact) 
                            return data;
                        return `<a href="<?= ROOTPATH ?>/profile/${row.contact}">${data}</a>`
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        switch (data) {
                            case 'approved':
                                return `<span class='badge success'><?= lang('approved', 'bewilligt') ?></span>`;
                            case 'finished':
                                return `<span class='badge success'><?= lang('finished', 'abgeschlossen') ?></span>`;
                            case 'applied':
                                return `<span class='badge signal'><?= lang('applied', 'beantragt') ?></span>`;
                            case 'rejected':
                                return `<span class='badge danger'><?= lang('rejected', 'abgelehnt') ?></span>`;
                            case 'expired':
                                return `<span class='badge dark'><?= lang('expired', 'abgelaufen') ?></span>`;
                        }
                    }
                }
            ],
            order: [
                [4, 'desc']
            ]
        });

        // Add event listener for opening and closing details
        dataTable.on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = dataTable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
            } else {
                // Open this row
                row.child(format(row.data())).show();
            }
        });
        // dataTable = $('#project-table').DataTable({
        //     dom: 'frtipP',
        //     "order": [
        //         [2, 'desc'],
        //     ]
        // });

        $('#project-table_wrapper').prepend($('.filters'))
    });

    function filterStatus(btn, status) {
        let active = $(btn).hasClass('active')
        $('#filter-status').find('.active').removeClass('active')
        if (!active) {
            dataTable.columns(7).search(status, true, false, true).draw();
            $('#filter-status').find('.index').addClass('active')
            $(btn).addClass('active')
        } else
            dataTable.columns(7).search("", true, false, true).draw();
    }

    function filterRole(btn, role) {
        let active = $(btn).hasClass('active')
        $('#filter-role').find('.active').removeClass('active')
        if (!active) {
            dataTable.columns(5).search(role, true, false, true).draw();
            $('#filter-role').find('.index').addClass('active')
            $(btn).addClass('active')
        } else
            dataTable.columns(5).search("", true, false, true).draw();
    }
</script>