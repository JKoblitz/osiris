<?php
/**
 * Page to search users
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /search/user
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
$Format = new Document(true);
?>

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/query-builder.default.min.css">
<script src="<?= ROOTPATH ?>/js/query-builder.standalone.js"></script>
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<!-- <script src="<?= ROOTPATH ?>/js/query-builder.mongodb-support.js"></script> -->

<div class="content">
    <div class="btn-group float-right">
        <a href="<?= ROOTPATH ?>/search/activities" class="btn osiris">
            <i class="ph ph-magnifying-glass-plus"></i> <?= lang('Activities', 'Aktivitäten') ?>
        </a>
        <a href="#close-modal" class="btn osiris active">
            <i class="ph ph-student"></i> <?= lang('Users', 'Nutzer:innen') ?>
        </a>
    </div>

    <h1>
        <i class="ph ph-student text-osiris"></i>
        <?= lang('Advanced user search', 'Erweiterte Nutzer-Suche') ?>
    </h1>
    <!-- <form action="#" method="get"> -->

    <div id="builder"></div>

    <button class="btn osiris" onclick="getResult()"><i class="ph ph-magnifying-glass"></i> <?= lang('Search', 'Suchen') ?></button>

    <pre id="result" class="code my-20"></pre>

    <table class="table" id="activity-table">
        <thead>
            <th>User</th>
            <th>Name</th>
            <th><?= lang('Department', 'Abteilung') ?></th>
        </thead>
        <tbody>
        </tbody>
    </table>

    <?php
    $depts = [];
    foreach ($Settings->getDepartments() as $d => $val) {
        $depts[$d] = $val['name'];
    }
    ?>


    <script>
        // var mongo = $('#builder').queryBuilder('getMongo');
        var mongoQuery = $('#builder').queryBuilder({
            filters: [{
                    id: 'username',
                    label: lang('Username', 'Kürzel'),
                    type: 'string'
                },
                {
                    id: 'first',
                    label: lang('First name', 'Vorname'),
                    type: 'string'
                },
                {
                    id: 'last',
                    label: lang('Last name', 'Nachname'),
                    type: 'string'
                },
                {
                    id: 'academic_title',
                    label: lang('Acad. title', 'Akad. Titel'),
                    type: 'string',
                    default_value: 'Dr.'
                },
                {
                    id: 'dept',
                    label: lang('Department', 'Abteilung'),
                    type: 'string',
                    input: 'select',
                    values: JSON.parse('<?= json_encode($depts) ?>')
                },
                {
                    id: 'telephone',
                    label: lang('Telephone', 'Telefon'),
                    type: 'string'
                },
                {
                    id: 'mail',
                    label: lang('Mail', 'Email'),
                    type: 'string'
                },
                {
                    id: 'is_scientist',
                    label: lang('Is scientist', 'Ist Wissenschaftler:in'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio',
                    default_value: true
                },
                {
                    id: 'is_admin',
                    label: lang('Is admin', 'Ist Admin'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio',
                    default_value: true
                },
                {
                    id: 'is_controlling',
                    label: lang('Is controlling', 'Ist Controlling'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio',
                    default_value: true
                },
                {
                    id: 'is_active',
                    label: lang('Is active', 'Ist aktiv'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio',
                    default_value: true
                },
                {
                    id: 'is_leader',
                    label: lang('Is leader', 'Ist AG-Leiter'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio',
                    default_value: true
                },

            ],

            'lang_code': lang('en', 'de'),
            'icons': {
                add_group: 'ph ph-plus-circle',
                add_rule: 'ph ph-plus',
                remove_group: 'ph ph-x-circle',
                remove_rule: 'ph ph-x',
                error: 'ph-fill ph-warning',
            },
            allow_empty: true,
            default_filter: 'is_active'
        });

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
        function getResult() {
            dataTable.ajax.reload()
        }


        $(document).ready(function() {
            var hash = window.location.hash.substr(1);
            if (hash !== undefined && hash != "") {
                try {
                    var rules = JSON.parse(decodeURI(hash))
                    $('#builder').queryBuilder('setRulesFromMongo', rules);
                } catch (SyntaxError) {
                    console.info('invalid hash')
                }
            }

            dataTable = $('#activity-table').DataTable({
                ajax: {
                    "url": ROOTPATH + '/api/users',
                    data: function(d) {
                        // https://medium.com/code-kings/datatables-js-how-to-update-your-data-object-for-ajax-json-data-retrieval-c1ac832d7aa5
                        var rules = $('#builder').queryBuilder('getMongo')
                        if (rules === null) rules = []
                        console.log(rules);

                        rules = JSON.stringify(rules)
                        $('#result').html('filter = ' + rules)
                        d.json = rules

                        window.location.hash = rules
                    },
                },
                language: {
                    "zeroRecords": lang("No matching records found", 'Keine passenden Aktivitäten gefunden'),
                    "emptyTable": lang('No activities found for your filters.', 'Für diese Filter konnten keine Aktivitäten gefunden werden.'),
                },
                // "pageLength": 5,
                columnDefs: [{
                        targets: 0,
                        data: 'username',
                        "render": function(data, type, full, meta) {
                            return `<a href="${ROOTPATH}/profile/${data}">${data}</a>`;
                        }
                    },
                    {
                        targets: 1,
                        data: 'displayname'
                    },
                    {
                        targets: 2,
                        data: 'dept'
                    },
                ]
            });

        });
    </script>

</div>