<?php

/**
 * Page to perform advanced activity search
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /search/activities
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$Format = new Document(true);
?>

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/query-builder.default.min.css">
<script src="<?= ROOTPATH ?>/js/query-builder.standalone.js"></script>
<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>

<div class="content">
    <div class="btn-group float-right">
        <a href="#close-modal" class="btn osiris active">
            <i class="ph ph-magnifying-glass-plus"></i> <?= lang('Activities', 'Aktivitäten') ?>
        </a>
        <a href="<?= ROOTPATH ?>/search/user" class="btn osiris">
            <i class="ph ph-student"></i> <?= lang('Users', 'Personen') ?>
        </a>
    </div>

    <h1>
        <i class="ph ph-magnifying-glass-plus text-osiris"></i>
        <?= lang('Advanced activity search', 'Erweiterte Aktivitäten-Suche') ?>
    </h1>
    <!-- <form action="#" method="get"> -->

    <div id="builder"></div>

    <button class="btn osiris" onclick="getResult()"><i class="ph ph-magnifying-glass"></i> <?= lang('Search', 'Suchen') ?></button>

    <pre id="result" class="code my-20"></pre>

    <table class="table" id="activity-table">
        <thead>
            <th>Type</th>
            <th>Activity</th>
            <th>Link</th>
        </thead>
        <tbody>
        </tbody>
    </table>

<?php
$activities = $Settings->getActivities();
$types = array_keys($activities);
$subtypes = array_map(function($a){
    return array_column($a['subtypes'], 'id');
}, $activities);

$subtypes = flatten($subtypes);
?>

    <script>
        // var mongo = $('#builder').queryBuilder('getMongo');
        const types = JSON.parse('<?=json_encode($types)?>')
        const subtypes = JSON.parse('<?=json_encode($subtypes)?>')
        var mongoQuery = $('#builder').queryBuilder({
            filters: [{
                    id: 'type',
                    label: lang('Category', 'Kategorie'),
                    type: 'string',
                    input: 'select',
                    values: types
                },
                {
                    id: 'subtype',
                    label: lang('Type', 'Typ'),
                    type: 'string',
                    input: 'select',
                    values: subtypes
                },
                {
                    id: 'title',
                    label: lang('Title', 'Titel'),
                    type: 'string'
                },
                {
                    id: 'authors.first',
                    label: lang('Author (first name)', 'Autor (Vorname)'),
                    type: 'string'
                },
                {
                    id: 'authors.last',
                    label: lang('Author (last name)', 'Autor (Nachname)'),
                    type: 'string'
                },
                {
                    id: 'authors.user',
                    label: lang('Author (username)', 'Autor (Username)'),
                    type: 'string'
                },
                {
                    id: 'authors.position',
                    label: lang('Author (position)', 'Autor (Position)'),
                    type: 'string',
                    input: 'select',
                    values: ['first', 'middle', 'last', 'corresponding']
                },
                {
                    id: 'authors.approved',
                    label: lang('Author (approved)', 'Autor (Bestätigt)'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                {
                    id: 'authors.aoi',
                    label: lang('Author (affiliated)','Autor (Affiliated)'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                {
                    id: 'journal',
                    label: lang('Journal'),
                    type: 'string'
                },
                {
                    id: 'issn',
                    label: lang('ISSN'),
                    type: 'string'
                },
                {
                    id: 'magazine',
                    label: lang('Magazine', 'Magazin'),
                    type: 'string'
                },
                {
                    id: 'year',
                    label: lang('Year', 'Jahr'),
                    type: 'integer',
                    default_value: <?= CURRENTYEAR ?>
                },
                {
                    id: 'month',
                    label: lang('Month', 'Monat'),
                    type: 'integer'
                },
                {
                    id: 'lecture_type',
                    label: lang('Lecture type', 'Vortragstyp'),
                    input: 'select',
                    values: ['short', 'long', 'repetition']
                },
                {
                    id: 'editor_type',
                    label: lang('Editor type', 'Editortyp'),
                    type: 'string'
                },
                {
                    id: 'doi',
                    label: lang('DOI'),
                    type: 'string'
                },
                {
                    id: 'link',
                    label: lang('Link'),
                    type: 'string'
                },
                {
                    id: 'pubmed',
                    label: lang('Pubmed-ID'),
                    type: 'integer'
                },
                {
                    id: 'pubtype',
                    label: lang('Publication type', 'Publikationstyp'),
                    type: 'string',
                    input: 'select',
                    values: ['article', 'book', 'chapter', 'preprint', 'magazine', 'dissertation', 'others']
                },
                {
                    id: 'issue',
                    label: lang('Issue'),
                    type: 'string'
                },
                {
                    id: 'volume',
                    label: lang('Volume'),
                    type: 'string'
                },
                {
                    id: 'pages',
                    label: lang('Pages', 'Seiten'),
                    type: 'string'
                },
                {
                    id: 'impact',
                    label: lang('Impact factor'),
                    type: 'double'
                },
                {
                    id: 'book',
                    label: lang('Book title', 'Buchtitel'),
                    type: 'string'
                },
                {
                    id: 'publisher',
                    label: lang('Publisher', 'Verlag'),
                    type: 'string'
                },
                {
                    id: 'city',
                    label: lang('Location (Publisher)', 'Ort (Verlag)'),
                    type: 'string'
                },
                {
                    id: 'edition',
                    label: lang('Edition'),
                    type: 'string'
                },
                {
                    id: 'isbn',
                    label: lang('ISBN'),
                    type: 'string'
                },
                {
                    id: 'doc_type',
                    label: lang('Document type', 'Dokumententyp'),
                    type: 'string'
                },
                {
                    id: 'iteration',
                    label: lang('Iteration (Misc)', 'Wiederholung (misc)'),
                    type: 'string',
                    input: 'select',
                    values: ['once', 'annual']
                },
                {
                    id: 'software_type',
                    label: lang('Type of software', 'Art der Software'),
                    type: 'string',
                    input: 'select',
                    values: ['software', 'database', 'dataset', 'webtool', 'report']
                },
                {
                    id: 'software_venue',
                    label: lang('Publication venue (Software)', 'Ort der Veröffentlichung (Software)'),
                    type: 'string'
                },
                {
                    id: 'version',
                    label: lang('Version'),
                    type: 'string'
                },
                // {
                //         id: 'affiliation',
                //         label: lang('Affiliation', ''),
                //         type: 'string'
                // },
                {
                    id: 'sws',
                    label: lang('SWS'),
                    type: 'string'
                },
                {
                    id: 'category',
                    label: lang('Category (students/guests)', 'Kategorie (Studenten/Gäste)'),
                    type: 'string',
                    input: 'select',
                    values: {
                        'guest scientist': lang('Guest Scientist', 'Gastwissenschaftler:in'),
                        'lecture internship': lang('Lecture Internship', 'Pflichtpraktikum im Rahmen des Studium'),
                        'student internship': lang('Student Internship', 'Schülerpraktikum'),
                        'other': lang('Other', 'Sonstiges'),
                        'doctoral thesis': lang('Doctoral Thesis', 'Doktorand:in'),
                        'master thesis': lang('Master Thesis', 'Master-Thesis'),
                        'bachelor thesis': lang('Bachelor Thesis', 'Bachelor-Thesis')
                    }

                },
                {
                    id: 'status',
                    label: lang('Status (Thesis)'),
                    type: 'string',
                    input: 'select',
                    values: ['in progress', 'completed', 'aborted']

                },
                {
                    id: 'role',
                    label: lang('Role (Reviews)', 'Rolle (Reviews)'),
                    type: 'string',
                    input: 'select',
                    values: {
                        'review': 'Reviewer',
                        'editorial': 'Editorial board',
                        'grant-rev': 'Grant proposal',
                        'thesis-rev': 'Thesis review'
                    }

                },
                {
                    id: 'name',
                    label: lang('Name of guest', 'Name des Gastes'),
                    type: 'string'
                },
                {
                    id: 'academic_title',
                    label: lang('Academic title of guest', 'Akad. Titel des Gastes'),
                    type: 'string'
                },
                {
                    id: 'details',
                    label: lang('Details (Students/guests)', 'Details (Studenten/Gäste)'),
                    type: 'string'
                },
                {
                    id: 'conference',
                    label: lang('Conference', 'Konferenz'),
                    type: 'string'
                },
                {
                    id: 'location',
                    label: lang('Location', 'Ort'),
                    type: 'string'
                },
                {
                    id: 'open_access',
                    label: lang('Open Access'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                {
                    id: 'epub',
                    label: lang('Online ahead of print'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                {
                    id: 'correction',
                    label: lang('Correction'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                {
                    id: 'invited_lecture',
                    label: lang('Invited lecture'),
                    type: 'boolean',
                    values: {
                        'true': 'yes',
                        'false': 'no'
                    },
                    input: 'radio'
                },
                // {
                //         id: 'files',
                //         label: lang('Files', ''),
                //         type: 'string'
                // },
                // {
                //         id: 'comment',
                //         label: lang('Comment', ''),
                //         type: 'string'
                // },
                {
                    id: 'created_by',
                    label: lang('Created by (Abbreviation)', 'Erstellt von (Kürzel)'),
                    type: 'string'
                },
                {
                        id: 'created',
                        label: lang('Created at', 'Erstellt am'),
                        type: 'string'
                },
                {
                    id: 'updated_by',
                    label: lang('Updated by (Abbreviation)', 'Aktualisiert von (Kürzel)'),
                    type: 'string'
                },
                // {
                //         id: 'updated',
                //         label: lang('Updated', ''),
                //         type: 'string'
                // },
            ],

            'lang_code': lang('en', 'de'),
            'icons': {
                add_group: 'ph ph-plus-circle',
                add_rule: 'ph ph-plus',
                remove_group: 'ph ph-x-circle',
                remove_rule: 'ph ph-x',
                error: 'ph ph-warning',
            },
            allow_empty: true,
            default_filter: 'type'
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
                    "url": ROOTPATH + '/api/activities',
                    data: function(d) {
                        // https://medium.com/code-kings/datatables-js-how-to-update-your-data-object-for-ajax-json-data-retrieval-c1ac832d7aa5
                        var rules = $('#builder').queryBuilder('getMongo')
                        if (rules === null) rules = []
                        console.log(rules);

                        rules = JSON.stringify(rules)
                        $('#result').html('filter = ' + rules)
                        d.json = rules
                        d.formatted = true

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
                        data: 'icon'
                    },
                    {
                        targets: 1,
                        data: 'activity'
                    },
                    {
                        "targets": 2,
                        "data": "name",
                        "render": function(data, type, full, meta) {
                            return `<a href="${ROOTPATH}/activities/view/${full.id}"><i class="ph ph-arrow-fat-line-right"></a>`;
                        }
                    },
                ]
            });

            // getResult()
        });
    </script>

</div>