<?php

/**
 * Page to see details on a single project
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /project/<id>
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

require_once BASEPATH . "/php/Project.php";
$Project = new Project($project);

$user_project = false;
$persons = $project['persons'] ?? array();
foreach ($persons as $p) {
    if (strval($p['user']) == $_SESSION['username']) {
        $user_project = True;
        break;
    }
}
$edit_perm = ($Settings->hasPermission('projects.edit') || ($Settings->hasPermission('projects.edit-own') && $user_project));

$N = $osiris->activities->count(['projects' => $project['name']]);


$institute = $Settings->get('affiliation_details');
?>

<script>
    const PROJECT = '<?= $project['name'] ?>';
    const CURRENT_USER = '<?= $_SESSION['username'] ?>';
    var layout = {
        mapbox: {
            style: "open-street-map",
            center: {
                lat: <?=$institute['lat']??52?>,
                lon: <?=$institute['lng']??10?>
            },
            zoom: 1
        },

        margin: {
            r: 0,
            t: 0,
            b: 0,
            l: 0
        },
        hoverinfo: 'text',
        // autosize:true
    };
</script>

<script src="<?=ROOTPATH?>/js/plotly-2.27.1.min.js" charset="utf-8"></script>
<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/projects.js?v=1"></script>

<style>
    td .key {
        display: block;
        color: var(--muted-color);
        font-size: 1.2rem;
    }
</style>


<?php if ($Settings->featureEnabled('portal')) { ?>
    <a class="btn float-right" href="<?= ROOTPATH ?>/preview/project/<?= $id ?>">
        <i class="ph ph-eye ph-fw"></i>
        <?= lang('Preview', 'Vorschau') ?>
    </a>
<?php } ?>

<h1>
    <?= $project['name'] ?>
</h1>

<h2 class="subtitle">
    <?= $project['title'] ?>
</h2>

<?= $Project->getStatus() ?>
<br>


<!-- TAB AREA -->

<nav class="pills mt-20 mb-0">
    <a onclick="navigate('general')" id="btn-general" class="btn active">
        <i class="ph ph-info" aria-hidden="true"></i>
        <?= lang('General', 'Allgemein') ?>
    </a>

    <!-- <a onclick="navigate('persons')" id="btn-persons" class="btn">
        <i class="ph ph-users" aria-hidden="true"></i>
        <?= lang('Project members', 'Projektmitarbeiter') ?>
        <span class="index"><?= count($project['persons'] ?? array()) ?></span> -->
    </a>
    <a onclick="navigate('collabs')" id="btn-collabs" class="btn">
        <i class="ph ph-handshake" aria-hidden="true"></i>
        <?= lang('Collaborators', 'Kooperationspartner') ?>
        <span class="index"><?= count($project['collaborators'] ?? array()) ?></span>
    </a>
    <a onclick="navigate('activities')" id="btn-activities" class="btn">
        <i class="ph ph-suitcase" aria-hidden="true"></i>
        <?= lang('Activities', 'Aktivitäten') ?>
        <span class="index"><?= $N ?></span>
    </a>

    <?php if ($Settings->hasPermission('raw-data') || isset($_GET['verbose'])) { ?>
        <a onclick="navigate('raw')" id="btn-raw" class="btn">
            <i class="ph ph-code" aria-hidden="true"></i>
            <?= lang('Raw data', 'Rohdaten')  ?>
        </a>
    <?php } ?>

</nav>


<section id="general">
    <div class="row row-eq-spacing mt-0">

        <div class="col-md-6">
            <h2>
                <?= lang('Project details', 'Projektdetails') ?>
            </h2>

            <div class="btn-toolbar mb-10">

                <?php if ($edit_perm) { ?>
                    <a href="<?= ROOTPATH ?>/projects/edit/<?= $id ?>" class="btn primary">
                        <i class="ph ph-edit"></i>
                        <?= lang('Edit', 'Bearbeiten') ?>
                    </a>
                <?php } ?>

            </div>

            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Short title', 'Kurztitel') ?></span>
                            <?= $project['name'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Full title of the project', 'Voller Titel des Projekts') ?></span>
                            <?= $project['title'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Type of the project', 'Projekt-Typ') ?></span>
                            <?= $project['type'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Contact person', 'Ansprechpartner:in') ?></span>
                            <a href="<?= ROOTPATH ?>/profile/<?= $project['contact'] ?? '' ?>"><?= $DB->getNameFromId($project['contact'] ?? '') ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Status', 'Status') ?></span>
                            <?= $Project->getStatus() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Third-party funder', 'Drittmittelgeber') ?></span>
                            <?= $project['funder'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Funding organization', 'Förderorganisation') ?></span>
                            <?= $project['funding_organization'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Funding reference number(s)', 'Förderkennzeichen') ?></span>
                            <?php $Project->getFundingNumbers('<br>') ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Purpose of the project', 'Zwecks des Projekts') ?></span>
                            <?= $Project->getPurpose() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Role of', 'Rolle von') ?> <?= $Settings->get('affiliation') ?></span>
                            <?= $Project->getRole() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Coordinator facility', 'Koordinator-Einrichtung') ?></span>
                            <?= $project['coordinator'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key">Projektbeginn</span>
                            <?= Document::format_date($project['start'] ?? null) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key">Projektende</span>
                            <?= Document::format_date($project['end'] ?? null) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Personnel measures planned', 'Geplante Personalmaßnahmen') ?></span>
                            <?= $project['personal'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Project website', 'Webseite des Projekts') ?></span>
                            <a href="<?= $project['website'] ?? '' ?>" target="_blank" rel="noopener noreferrer"> <?= $project['website'] ?? '-' ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Abstract', 'Kurzbeschreibung') ?></span>
                            <?= $project['abstract'] ?? '-' ?>
                        </td>
                    </tr>
                    <tr>

                        <td>
                            <span class="key">Zustimmung zur Internetpräsentation des bewilligten Vorhaben</span>
                            <?= bool_icon($project['public'] ?? false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Created by', 'Erstellt von') ?></span>
                            <?= $DB->getNameFromId($project['created_by']) ?? '-' ?> (<?= $project['created'] ?>)
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-md-6">

            <h2>
            <?= lang('Project members', 'Projektmitarbeiter') ?> @
            <?=$Settings->get('affiliation')?>
            </h2>

            <?php if ($edit_perm) { ?>
                <div class="modal" id="persons" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <a data-dismiss="modal" class="btn float-right" role="button" aria-label="Close" href="#close-modal">
                                <span aria-hidden="true">&times;</span>
                            </a>
                            <h5 class="modal-title">
                                <?= lang('Connect persons', 'Personen verknüpfen') ?>
                            </h5>
                            <div>

                                <form action="<?= ROOTPATH ?>/crud/projects/update-persons/<?= $id ?>" method="post">

                                    <table class="table simple">
                                        <thead>
                                            <tr>
                                                <th><?= lang('Project-ID', 'Projekt-ID') ?></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="project-list">
                                            <?php
                                            $persons = $project['persons'] ?? array();
                                            if (empty($persons)) {
                                                $persons = [
                                                    ['user' => '', 'role' => '']
                                                ];
                                            }
                                            foreach ($persons as $i => $con) { ?>
                                                <tr>
                                                    <td class="">
                                                        <select name="persons[<?= $i ?>][user]" id="persons-<?= $i ?>" class="form-control">
                                                            <?php
                                                            $all_users = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ['last' => 1]]);
                                                            foreach ($all_users as $s) { ?>
                                                                <option value="<?= $s['username'] ?>" <?= ($con['user'] == $s['username'] ? 'selected' : '') ?>>
                                                                    <?= "$s[last], $s[first] ($s[username])" ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <select name="persons[<?= $i ?>][role]" id="persons-<?= $i ?>" class="form-control">
                                                            <option value="associate" <?= $con['role'] == 'associate' ? 'selected' : '' ?>><?= Project::personRole('associate') ?></option>
                                                            <option value="worker" <?= $con['role'] == 'worker' ? 'selected' : '' ?>><?= Project::personRole('worker') ?></option>
                                                            <option value="PI" <?= $con['role'] == 'PI' ? 'selected' : '' ?>><?= Project::personRole('PI') ?></option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn danger" type="button" onclick="$(this).closest('tr').remove()"><i class="ph ph-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr id="last-row">
                                                <td colspan="2">
                                                    <button class="btn" type="button" onclick="addProjectRow()"><i class="ph ph-plus"></i> <?= lang('Add row', 'Zeile hinzufügen') ?></button>
                                                </td>
                                            </tr>
                                        </tfoot>

                                    </table>

                                    <button class="btn primary mt-20">
                                        <i class="ph ph-check"></i>
                                        <?= lang('Submit', 'Bestätigen') ?>
                                    </button>
                                </form>


                                <script>
                                    var counter = <?= $i ?? 0 ?>;
                                    const tr = $('#project-list tr').first()

                                    function addProjectRow() {
                                        counter++;
                                        const row = tr.clone()
                                        row.find('select').first().attr('name', 'persons[' + counter + '][user]');
                                        row.find('select').last().attr('name', 'persons[' + counter + '][role]');
                                        $('#project-list').append(row)
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="btn-toolbar mb-10">
                <?php if ($edit_perm) { ?>
                    <a href="#persons" class="btn primary">
                        <i class="ph ph-edit"></i>
                        <?= lang('Edit', 'Bearbeiten') ?>
                    </a>
                <?php } ?>
            </div>

            <table class="table">
                <tbody>
                    <?php
                    if (empty($project['persons'] ?? array())) {
                    ?>
                        <tr>
                            <td>
                                <?= lang('No persons connected.', 'Keine Personen verknüpft.') ?>
                            </td>
                        </tr>
                    <?php
                    } else foreach ($project['persons'] as $person) {
                        $username = strval($person['user']);

                        $img = ROOTPATH . "/img/no-photo.png";
                        if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                            $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
                        }
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">

                                    <img src="<?= $img ?>" alt="" style="max-height: 7rem;" class="mr-20 rounded">
                                    <div class="">
                                        <h5 class="my-0">
                                            <a href="<?= ROOTPATH ?>/profile/<?= $username ?>" class="colorless">
                                                <?= $person['name'] ?>
                                            </a>
                                        </h5>
                                        <?= Project::personRole($person['role']) ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } ?>

                </tbody>
            </table>

        </div>
    </div>

</section>

<section id="collabs" style="display:none">

    <h2>
        <?= lang('Collaborators', 'Kooperationspartner') ?>
    </h2>

    <?php if ($edit_perm) { ?>
        <div class="btn-toolbar mb-10">
            <a href="<?= ROOTPATH ?>/projects/collaborators/<?= $id ?>" class="btn primary">
                <i class="ph ph-edit"></i>
                <?= lang('Edit', 'Bearbeiten') ?>
            </a>
            <?php if (empty($project['collaborators'] ?? array())) { ?>
                <script>
                    collabChart = true;
                </script>
            <?php } ?>

        </div>
    <?php } ?>

    <div class="row row-eq-spacing">
        <div class="col-lg-4">
            
            <table class="table">
                <tbody>
                    <?php
                    if (empty($project['collaborators'] ?? array())) {
                    ?>
                        <tr>
                            <td>
                                <?= lang('No collaborators connected.', 'Keine Partner verknüpft.') ?>
                            </td>
                        </tr>
                    <?php
                    } else foreach ($project['collaborators'] as $collab) {
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">

                                    <span data-toggle="tooltip" data-title="<?= $collab['type'] ?>" class="badge mr-10">
                                        <?= Project::getCollaboratorIcon($collab['type'], 'ph-fw ph-2x m-0') ?>
                                    </span>
                                    <div class="">
                                        <h5 class="my-0">
                                            <?= $collab['name'] ?>
                                        </h5>
                                        <?= $collab['location'] ?>
                                        <a href="<?= $collab['ror'] ?>" class="ml-10" target="_blank" rel="noopener noreferrer">ROR <i class="ph ph-arrow-square-out"></i></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php
                    } ?>

                </tbody>
            </table>

            <div class="alert secondary my-20">

                <small class="text-muted float-right">
                    <?= lang('Based on partners', 'Basierend auf Partnern') ?>
                </small>

                <h5 class="title mb-0">
                    Scope
                </h5>
                <?php
                $scope = $Project->getScope();

                echo  $scope['scope'] . ' (' . $scope['region'] . ')';
                ?>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="box my-0">
                <div id="map" class=""></div>
            </div>
            <p>
                <i class="ph ph-fill ph-circle" style="color:#f78104"></i>
                <?=lang('Coordinator', 'Koordinator')?>
                <br>
                <i class="ph ph-fill ph-circle" style="color:#008083"></i>
                Partner
            </p>
        </div>
    </div>

    <script>
        const id = '<?= $_GET['project'] ?? null ?>';
        console.log(id);
        
    </script>


</section>

<section id="activities" style="display:none">


    <?php

    ?>

    <h2>
        <?= lang('Connected activities', 'Verknüpfte Aktivitäten') ?>
        (<?= $N ?>)
    </h2>

    <div class="dropdown with-arrow btn-group ">
        <button class="btn primary mb-10" <?= $N == 0 ? 'disabled' : '' ?> data-toggle="dropdown" type="button" id="download-btn" aria-haspopup="true" aria-expanded="false">
            <i class="ph ph-download"></i> Download
            <i class="ph ph-caret-down ml-5" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu" aria-labelledby="download-btn">
            <div class="content">
                <form action="<?= ROOTPATH ?>/download" method="post">

                    <input type="hidden" name="filter[project]" value="<?= $project['name'] ?>">

                    <div class="form-group">

                        <?= lang('Highlight:', 'Hervorheben:') ?>

                        <div class="custom-radio ml-10">
                            <input type="radio" name="highlight" id="highlight-user" value="user" checked="checked">
                            <label for="highlight-user"><?= lang('Me', 'Mich') ?></label>
                        </div>

                        <div class="custom-radio ml-10">
                            <input type="radio" name="highlight" id="highlight-aoi" value="aoi">
                            <label for="highlight-aoi"><?= $Settings->get('affiliation') ?><?= lang(' Authors', '-Autoren') ?></label>
                        </div>

                        <div class="custom-radio ml-10">
                            <input type="radio" name="highlight" id="highlight-none" value="">
                            <label for="highlight-none"><?= lang('None', 'Nichts') ?></label>
                        </div>

                    </div>


                    <div class="form-group">

                        <?= lang('File format:', 'Dateiformat:') ?>

                        <div class="custom-radio ml-10">
                            <input type="radio" name="format" id="format-word" value="word" checked="checked">
                            <label for="format-word">Word</label>
                        </div>

                        <div class="custom-radio ml-10">
                            <input type="radio" name="format" id="format-bibtex" value="bibtex">
                            <label for="format-bibtex">BibTex</label>
                        </div>

                    </div>
                    <button class="btn primary">Download</button>
                </form>
            </div>
        </div>
    </div>


    <h2><?= lang('Other activities', 'Andere Aktivitäten') ?></h2>

    <div class="mt-20 w-full">
        <table class="table dataTable responsive" id="activities-table">
            <thead>
                <tr>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Activity', 'Aktivität') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>

</section>


<section id="raw" style="display:none">

    <h2 class="title">
        <?= lang('Raw data', 'Rohdaten') ?>
    </h2>

    <?= lang('Raw data as they are stored in the database.', 'Die Rohdaten, wie sie in der Datenbank gespeichert werden.') ?>

    <div class="box overflow-x-scroll">
        <?php
        dump($project, true);
        ?>
    </div>

</section>