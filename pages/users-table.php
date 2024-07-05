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
                <a class="float-right" onclick="filterUnit('#filter-unit .active', null)"><i class="ph ph-x"></i></a>
            </h6>
            <div class="filter">
                <table id="filter-unit" class="table simple">
                    <?php foreach ($Departments as $id => $dept) { ?>
                        <tr <?= $Groups->cssVar($id) ?>>
                            <td>
                                <a data-type="<?= $id ?>" onclick="filterUnit(this, '<?= $id ?>')" class="item d-block colorless" id="<?= $id ?>-btn">
                                    <span><?= $dept ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <h6><?= lang('Active workers', 'Aktive Mitarbeitende') ?></h6>
            <div class="custom-switch">
                <input type="checkbox" id="active-switch" value="" onchange="filterActive(this)">
                <label for="active-switch"><?= lang('include Inactive', 'Inkl. Inaktiv') ?></label>
            </div>
        </div>
    </div>
</div>


<script>
    var dataTable;
    const activeFilters = $('#active-filters')
    $(document).ready(function() {
        dataTable = userTable('#user-table', {
            subtitle: 'position',
        })
        filterActive();

        var hash = readHash();
        if (hash.unit !== undefined) {
            filterUnit(document.getElementById(hash.unit + '-btn'), hash.unit)
        }
    });

    function filterUnit(btn, unit = null) {
        const column = 2
        var tr = $(btn).closest('tr')
        var table = tr.closest('table')
        $('#filter-' + column).remove()
        const hash = {}
        hash.unit = unit

        if (tr.hasClass('active') || unit === null) {
            hash.unit = null
            table.find('.active').removeClass('active')
            dataTable.columns(column).search("", true, false, true).draw();

        } else {
            table.find('.active').removeClass('active')
            tr.addClass('active')
            dataTable.columns(column).search(unit, true, false, true).draw();
            // indicator
            const filterBtn = $('<span class="badge" id="filter-' + column + '">')
            filterBtn.html(`<b>${lang('Unit', 'Einheit')}:</b> <span>${unit}</span>`)
            const a = $('<a>')
            a.html('&times;')
            a.on('click', function() {
                filterUnit(btn, null, column);
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
    }


    // function editUser(id) {
    //     // loadModal();

    //     $.ajax({
    //         type: "GET",
    //         dataType: "html",
    //         // data: {},
    //         url: ROOTPATH + '/form/user/' + id,
    //         success: function(response) {
    //             $('#modal-content').html(response)
    //             $('#the-modal').addClass('show')

    //             console.log($('#the-modal form'));
    //             $('#the-modal form').on('submit', function(event, element) {
    //                 event.preventDefault()
    //                 data = {}
    //                 var raw = objectifyForm(this)
    //                 console.log(raw);
    //                 for (var key in raw) {
    //                     var val = raw[key];
    //                     if (key.includes('values')) {
    //                         key = key.slice(7).replace(']', '')
    //                         data[key] = val
    //                     }
    //                 }
    //                 console.log(data);
    //                 // return
    //                 _updateUser(id, data)
    //                 $('#the-modal').removeClass('show')
    //                 toastWarning("Table will be updated after reload.")

    //                 return false;
    //             })
    //         },
    //         error: function(response) {
    //             console.log(response);
    //             toastError(response.responseText)
    //             $('.loader').removeClass('show')
    //         }
    //     })

    // }
</script>