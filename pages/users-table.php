<?php

/**
 * Page to browse all users
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /user/browse
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/usertable.css">

<?php if ($Settings->featureEnabled('portal')) { ?>
    <a href="<?= ROOTPATH ?>/preview/persons" class="btn float-right"><i class="ph ph-eye"></i> <?= lang('Preview', 'Vorschau') ?></a>
<?php } ?>
<?php if ($Settings->hasPermission('user.synchronize') && strtoupper(USER_MANAGEMENT) === 'LDAP') { ?>
    <a href="<?= ROOTPATH ?>/synchronize-users" class="btn float-right"><i class="ph ph-sync"></i> <?= lang('Synchronize users', 'Nutzende synchronisieren') ?></a>
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
                <th></th>
                <th></th>
            </thead>
            <tbody>

            </tbody>
        </table>

    </div>
    </style>

    <div class="col-lg-3 d-none d-lg-block">

        <div class="on-this-page-filter filters content" id="filters">

            <div class="title">Filter</div>

            <div id="active-filters"></div>

            <h6>
                <?= lang('By organisational unit', 'Nach Organisationseinheit') ?>
                <a class="float-right" onclick="filterUsers('#filter-unit .active', null, 2)"><i class="ph ph-x"></i></a>
            </h6>
            <div class="filter">
                <table id="filter-unit" class="table simple">
                    <?php foreach ($Departments as $id => $dept) { ?>
                        <tr <?= $Groups->cssVar($id) ?>>
                            <td>
                                <a data-type="<?= $id ?>" onclick="filterUsers(this, '<?= $id ?>', 2)" class="item d-block colorless" id="<?= $id ?>-btn">
                                    <span><?= $dept ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>


            <?php if ($Settings->featureEnabled('topics')) { ?>
                <h6><?= lang('Research Topics', 'Forschungsbereiche') ?>
                <a class="float-right" onclick="filterUsers('#filter-unit .active', null, 5)"><i class="ph ph-x"></i></a>
            </h6>

                <div class="filter">
                    <table id="filter-type" class="table small simple">
                        <?php foreach ($osiris->topics->find([], ['sort' => ['order' => 1]]) as $a) {
                            $id = $a['id'];
                        ?>
                            <tr style="--highlight-color:  <?= $a['color'] ?>;">
                                <td>
                                    <a data-type="<?= $id ?>" onclick="filterUsers(this, '<?= $id ?>', 5)" class="item" id="<?= $id ?>-btn">
                                        <span style="color: var(--highlight-color)">
                                            <?= lang($a['name'], $a['name_en'] ?? null) ?>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>


            <h6><?= lang('Active workers', 'Aktive Mitarbeitende') ?></h6>
            <div class="custom-switch">
                <input type="checkbox" id="active-switch" value="" onchange="filterActive(this)">
                <label for="active-switch"><?= lang('Include Inactive', 'Inkl. Inaktiv') ?></label>
            </div>
        </div>
    </div>
</div>


<script>

const headers = [{
            title: lang('Image', 'Bild'),
            'key': 'img'
        },
        {
            title: '',
            'key': 'html'
        },
        {
            title: lang('Units', 'Einheiten'),
            'key': 'unit'
        },
        {
            title: lang('Active', 'Aktiv'),
            'key': 'active'
        },
        {
            title: lang('Names', 'Namen'),
            'key': 'names'
        },
        {
            title: lang('Research topics', 'Forschungsbereiche'),
            'key': 'topics'
        }
    ]

    var dataTable;
    const activeFilters = $('#active-filters')
    $(document).ready(function() {
        dataTable = userTable('#user-table', {
            subtitle: 'position',
        })
        
        var hash = readHash();
        
        $('#active-switch').prop('checked', hash.active === 'yes')
        filterActive()

        if (hash === undefined)
            return;
        
        if (hash.unit !== undefined) {
            filterUsers(document.getElementById(hash.unit + '-btn'), hash.unit, 2)
        }
        if (hash.topics !== undefined) {
            filterUsers(document.getElementById(hash.topics + '-btn'), hash.topics, 5)
        }
    });

    
    function filterUsers(btn, attr = null, column = 2) {
        var tr = $(btn).closest('tr')
        var table = tr.closest('table')
        $('#filter-' + column).remove()
        const field = headers[column]
        const hash = {}
        hash[field.key] = attr

        if (tr.hasClass('active') || attr === null) {
            hash[field.key] = null
            table.find('.active').removeClass('active')
            dataTable.columns(column).search("", true, false, true).draw();

        } else {

            table.find('.active').removeClass('active')
            tr.addClass('active')
            dataTable.columns(column).search(attr, true, false, true).draw();
            // indicator
            const filterBtn = $('<span class="badge" id="filter-' + column + '">')
            filterBtn.html(`<b>${field.title}:</b> <span>${attr}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                filterUsers(btn, null, column);
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

        // write hash
        const hash = {
            active: $('#active-switch').prop('checked') ? 'yes' : null
        }
        writeHash(hash)
    }

</script>