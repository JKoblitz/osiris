<?php

/**
 * Page to add new projects
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /projects/new
 *
 * @package     OSIRIS
 * @since       1.2.2
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

$Format = new Document(true);
$form = $form ?? array();


$formaction = ROOTPATH;
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "/crud/projects/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/projects/view/" . $form['_id'];
} else {
    $formaction .= "/crud/projects/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/projects/view/*";
}

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    if ($val instanceof MongoDB\Model\BSONArray) {
        return implode(',', DB::doc2Arr($val));
    }
    return $val;
}

function sel($index, $value)
{
    return val($index) == $value ? 'selected' : '';
}

$type = $_GET['type'] ?? 'Drittmittel';

$all_fields = [
    'name',
    'title',
    'status',
    'purpose',
    'role',
    'coordinator',
    'time',
    'grant_sum',
    'grant_income',
    'grant_sum_proposed',
    'grant_income_proposed',
    'funder',
    'funding_organization',
    'funding_number',
    'contact',
    'personnel',
    'website',
    'abstract',
    'public',
    'ressources',
    'internal_number'
];

$fields = [
    'name',
    'title',
    'status',
    'time',
    'abstract',
    'public',
    'internal_number',
    'website'
];

if ($type == 'Drittmittel') {
    // fields for third-party funded projects
    $fields[] = 'funder';
    $fields[] = 'funding_organization';
    $fields[] = 'funding_number';

    $fields[] = 'grant_sum_proposed';
    $fields[] = 'grant_income_proposed';

    $fields[] = 'grant_sum';
    $fields[] = 'grant_income';

    $fields[] = 'personnel';
    $fields[] = 'ressources';
    $fields[] = 'contact';

    $fields[] = 'purpose';
    $fields[] = 'role';
    $fields[] = 'coordinator';
} elseif ($type == 'Stipendium') {
    // fields for scholarships
    $fields[] = 'supervisor';
    $fields[] = 'scholar';

    $fields[] = 'scholarship';
    $fields[] = 'university';
} else {
    // fields for self-funded projects
    $fields[] = 'personnel';
    $fields[] = 'ressources';
    $fields[] = 'contact';
}




?>
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>


<?php if (empty($form)) { ?>
    <h3 class="title">
        <?= lang('Add new project', 'Neues Projekt') ?>
    </h3>
<?php } else { ?>
    <h3 class="title">
        <?= lang('Edit project', 'Projekt bearbeiten') ?>
    </h3>
<?php } ?>


<!-- only new projects can be changed -->
<?php if (empty($form)) { ?><?php } ?>

<div class="select-btns">
    <a href="<?= ROOTPATH ?>/projects/new?type=Drittmittel" class="btn select text-danger <?= $type == 'Drittmittel' ? 'active' : '' ?>">
        <i class="ph ph-hand-coins"></i>
        <?= lang('Third-party funded', 'Drittmittelprojekt') ?>
    </a>
    <!-- <a href="<?= ROOTPATH ?>/projects/new?type=Eigenfinanziert" class="btn select text-signal <?= $type == 'Eigenfinanziert' ? 'active' : '' ?>">
            <i class="ph ph-piggy-bank"></i>
            <?= lang('Self-funded', 'Eigenfinanziert') ?>
        </a> -->
    <a href="<?= ROOTPATH ?>/projects/new?type=Stipendium" class="btn select text-success <?= $type == 'Stipendium' ? 'active' : '' ?>">
        <i class="ph ph-tip-jar"></i>
        <?= lang('Scholarship', 'Stipendium') ?>
    </a>
</div>
<!-- <small class="text-muted">
        <i class="ph ph-warning"></i>
        <?= lang('Please note that the type of funding cannot be changed after the project has been created.', 'Bitte beachten Sie, dass der Förderungstyp nach Erstellung des Projekts nicht mehr geändert werden kann.') ?>
    </small> -->



<form action="<?= $formaction ?>" method="post" id="project-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">
    <input type="hidden" class="hidden" name="values[type]" value="<?= $type ?>">

    <div class="row row-eq-spacing" id="data-modules">
        <?php if (in_array('name', $fields)) { ?>
            <div class="data-module col-sm-6">
                <label for="project" class="required element-other">
                    <?= lang('Short title', 'Kurztitel') ?>
                </label>
                <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>" maxlength="30">
            </div>
        <?php } ?>

        <?php if (in_array('status', $fields)) { ?>
            <div class="data-module col-sm-6">
                <label class="required" for="status">
                    <?= lang('Status', 'Status') ?>
                </label>
                <select class="form-control" id="status" name="values[status]" required autocomplete="off">
                    <option value="applied" <?= sel('status', 'applied') ?>><?= lang('applied', 'beantragt') ?></option>
                    <option value="approved" <?= sel('status', 'approved') ?>><?= lang('approved', 'bewilligt') ?></option>
                    <option value="rejected" <?= sel('status', 'rejected') ?>><?= lang('rejected', 'abgelehnt') ?></option>
                    <option value="finished" <?= sel('status', 'abgeschlossen') ?>><?= lang('finished', 'abgeschlossen') ?></option>
                </select>
            <?php } ?>
            </div>

            <?php if (in_array('title', $fields)) { ?>
                <div class="data-module col-12">
                    <div class=" lang-<?= lang('en', 'de') ?>">
                        <label for="title" class="required element-title">
                            <?= lang('Full title of the project', 'Voller Titel des Projekts') ?>
                        </label>

                        <div class="form-group title-editor" id="title-editor"><?= $form['title'] ?? '' ?></div>
                        <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= val('title') ?>">
                    </div>

                    <script>
                        initQuill(document.getElementById('title-editor'));
                    </script>
                </div>
            <?php } ?>

            <?php if (in_array('contact', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label class="required element-author" for="username">
                        <?= lang('Applicant', 'Antragstellende Person') ?>
                    </label>
                    <select class="form-control" id="username" name="values[contact]" required autocomplete="off">
                        <?php
                        $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                        foreach ($userlist as $j) { ?>
                            <option value="<?= $j['username'] ?>" <?= $j['username'] == ($form['contact'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>


            <?php if (in_array('scholar', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label class="required element-author" for="username">
                        <?= lang('Scholar', 'Stipendiat:in') ?>
                    </label>
                    <select class="form-control" id="username" name="values[scholar]" required autocomplete="off">
                        <?php
                        $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                        foreach ($userlist as $j) { ?>
                            <option value="<?= $j['username'] ?>" <?= $j['username'] == ($form['scholar'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>


            <?php if (in_array('supervisor', $fields)) {

                $selected = '';
                if (empty($form)) {
                    include_once BASEPATH . "/php/Groups.php";
                    // default: head of group
                    $dept = $USER['depts'] ?? [];
                    if (!empty($dept)) {
                        $Groups = new Groups();
                        $heads = $Groups->getGroup($dept[0])['head'] ?? array();
                        $selected = $heads[0] ?? '';
                    }
                } else {
                    $selected = $form['supervisor'] ?? '';
                }

            ?>
                <div class="data-module col-sm-6">
                    <label class="required element-author" for="username">
                        <?= lang('Supervisor', 'Betreuende Person') ?>
                    </label>
                    <select class="form-control" id="username" name="values[supervisor]" required autocomplete="off">
                        <?php
                        $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                        foreach ($userlist as $j) { ?>
                            <option value="<?= $j['username'] ?>" <?= $j['username'] == $selected ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>


            <?php if (in_array('funder', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="funder" class="required">
                        <?= lang('Third-party funder', 'Drittmittelgeber') ?>
                    </label>
                    <select class="form-control" name="values[funder]" value="<?= val('funder') ?>" required id="funder">
                        <!-- <option <?= sel('funder', 'Eigenmittel') ?>>Eigenmittel</option> -->
                        <option <?= sel('funder', 'DFG') ?>>DFG</option>
                        <option <?= sel('funder', 'Bund') ?>>Bund</option>
                        <option <?= sel('funder', 'Bundesländer') ?>>Bundesländer</option>
                        <option <?= sel('funder', 'Wirtschaft') ?>>Wirtschaft</option>
                        <option <?= sel('funder', 'EU') ?>>EU</option>
                        <option <?= sel('funder', 'Stiftungen') ?>>Stiftungen</option>
                        <option <?= sel('funder', 'Leibniz Wettbewerb') ?>>Leibniz Wettbewerb</option>
                        <option <?= sel('funder', 'Sonstige Drittmittelgeber') ?>>Sonstige Drittmittelgeber</option>
                        <!-- <option>Sonstige öffentliche internationale Förderorganisationen</option>
                <option>Nicht erklärt (Private Mittelgeber)</option>
                <option>Nicht erklärt (Öffentliche Mittelgeber)</option> -->
                    </select>
                </div>
            <?php } ?>

            <?php if (in_array('funding_organization', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="funding_organization" class="required">
                        <?= lang('Funding organization', 'Zuwendungsgeber') ?>
                        <!-- Förderorganisation laut KDSF -->
                    </label>
                    <input type="text" class="form-control" name="values[funding_organization]" value="<?= val('funding_organization') ?>" id="funding_organization" required>

                </div>
            <?php } ?>

            <?php if (in_array('funding_number', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="funding_number">
                        <?= lang('Funding reference number', 'Förderkennzeichen') ?>
                    </label>
                    <input type="text" class="form-control" name="values[funding_number]" value="<?= val('funding_number') ?>" id="funding_number">
                    <span class="text-muted"><?= lang('Multiple seperated by comma', 'Mehrere durch Komma getrennt') ?></span>
                </div>
            <?php } ?>


            <?php if (in_array('purpose', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="purpose">
                        <?= lang('Purpose of the project', 'Zwecks des Projekts') ?>
                    </label>
                    <select class="form-control" name="values[purpose]" id="purpose">
                        <option value="research" <?= sel('purpose', 'research') ?>><?= lang('Research', 'Forschung') ?></option>
                        <option value="teaching" <?= sel('purpose', 'teaching') ?>><?= lang('Teaching', 'Lehre') ?></option>
                        <option value="promotion" <?= sel('purpose', 'promotion') ?>><?= lang('Promotion of young scientists', 'Förderung des wissenschaftlichen Nachwuchs') ?></option>
                        <option value="transfer" <?= sel('purpose', 'transfer') ?>><?= lang('Transfer', 'Transfer') ?></option>
                        <option value="others" <?= sel('purpose', 'others') ?>><?= lang('Other purpose', 'Sonstiger Zweck') ?></option>
                    </select>
                </div>
            <?php } ?>
            <?php if (in_array('role', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="role">
                        <?= lang('Role of', 'Rolle von') ?> <?= $Settings->get('affiliation') ?>
                    </label>
                    <select class="form-control" name="values[role]" id="role">
                        <option value="coordinator" <?= sel('role', 'coordinator') ?>><?= lang('Coordinator', 'Koordinator') ?></option>
                        <option value="partner" <?= sel('role', 'partner') ?>><?= lang('Partner') ?></option>
                    </select>
                </div>
            <?php } ?>
            <?php if (in_array('coordinator', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="coordinator">
                        <?= lang('Coordinator facility', 'Koordinator-Einrichtung') ?>
                    </label>
                    <input type="text" class="form-control" name="values[coordinator]" id="coordinator" value="<?= val('coordinator', $Settings->get('affiliation')) ?>">
                </div>
            <?php } ?>

            <?php if (in_array('time', $fields)) { ?>
                <div class="col-12 data-module">
                    <div class="row row-eq-spacing align-items-end my-0">
                        <div class="col-sm-4">
                            <label for="start" class="required">
                                Projektbeginn
                            </label>
                            <input type="date" class="form-control" name="values[start]" value="<?= valueFromDateArray(val('start')) ?>" id="start" required>
                        </div>
                        <div class="col-sm-4">
                            <label for="">
                                <?= lang('Shortcut Length', 'Schnell-Auswahl Laufzeit') ?>
                            </label>
                            <div class="btn-group w-full">
                                <div class="btn" onclick="timeframe(36)"><?= lang('3 yr', '3 J') ?></div>
                                <!-- <div class="btn"><?= lang('2 yr', '2 J') ?></div> -->
                                <div class="btn" onclick="timeframe(12)"><?= lang('1 yr', '1 J') ?></div>
                                <div class="btn" onclick="timeframe(6)"><?= lang('6 mo', '6 Mo') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="end" class="required">
                                Projektende
                            </label>
                            <input type="date" class="form-control" name="values[end]" value="<?= valueFromDateArray(val('end')) ?>" id="end" required>
                        </div>
                    </div>

                </div>
            <?php } ?>


            <script>
                function timeframe(month) {
                    let startField = document.querySelector('#start');
                    let start = startField.valueAsDate;
                    if (start == '' || start === null) {
                        toastError(lang('Please select a start date first.', 'Bitte wähle zuerst ein Startdatum.'))
                        return;
                    }

                    let end = new Date(start.setMonth(start.getMonth() + month));
                    end.setDate(end.getDate() - 1);
                    let endField = document.querySelector('#end');
                    endField.valueAsDate = end;
                }
            </script>



            <?php if (in_array('scholarship', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label for="scholarship">
                        <?= lang('Scholarship institution', 'Stipendiengeber') ?>
                    </label>
                    <input type="number" class="form-control" name="values[scholarship]" id="scholarship" value="<?= val('scholarship') ?>">
                </div>
            <?php } ?>

            <?php if (in_array('university', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label for="university">
                        <?= lang('Partner University', 'Partner-Universität') ?>
                    </label>
                    <input type="number" class="form-control" name="values[university]" id="university" value="<?= val('university') ?>">
                </div>
            <?php } ?>


            <?php if (in_array('grant_sum', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="grant_sum">
                        <?= lang('Grant amount', 'Bewilligungssumme') ?> [Euro]
                    </label>
                    <input type="number" step="1" class="form-control" name="values[grant_sum]" id="grant_sum" value="<?= val('grant_sum') ?>">
                </div>
            <?php } ?>
            <?php if (in_array('grant_income', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="grant_income">
                        <?= lang('Funding income', 'Drittmitteleinnahmen') ?> [Euro]
                    </label>
                    <input type="number" step="1" class="form-control" name="values[grant_income]" id="grant_income" value="<?= val('grant_income') ?>">
                </div>
            <?php } ?>

            <?php if (in_array('internal_number', $fields)) { ?>
                <div class="data-module col-sm-4">
                    <label for="internal_number">
                        <?= lang('Kostenträger') ?>
                    </label>
                    <input type="number" class="form-control" name="values[internal_number]" id="internal_number" value="<?= val('internal_number') ?>">
                </div>
            <?php } ?>


            <?php if (in_array('grant_sum_proposed', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label for="grant_sum_proposed">
                        <?= lang('Proposed grant amount (total)', 'Beantragte Fördersumme (gesamt)') ?> [Euro]
                    </label>
                    <input type="number" step="1" class="form-control" name="values[grant_sum_proposed]" id="grant_sum_proposed" value="<?= val('grant_sum_proposed') ?>">
                </div>
            <?php } ?>
            <?php if (in_array('grant_income_proposed', $fields)) { ?>
                <div class="data-module col-sm-6">
                    <label for="grant_income_proposed">
                        <?= lang('Proposed grant amount (institute)', 'Beantragte Fördersumme (Institut)') ?> [Euro]
                    </label>
                    <input type="number" step="1" class="form-control" name="values[grant_income_proposed]" id="grant_income_proposed" value="<?= val('grant_income_proposed') ?>">
                </div>
            <?php } ?>


            <?php if (in_array('personnel', $fields)) { ?>
                <div class="data-module col-12">
                    <label for="personnel" class="">
                        <?= lang('Personnel measures planned', 'Geplante Personalmaßnahmen') ?>
                    </label>
                    <textarea name="values[personnel]" id="personnel" cols="30" rows="2" class="form-control"><?= val('personnel') ?></textarea>
                    <small class="text-muted">
                        Einstellungen/Verlängerungen in Personenmonaten & Kategorie
                    </small>
                </div>
                <div class="data-module col-12">
                    <label for="in-kind" class="">
                        <?= lang('In-kind personnel', 'Umfang des geplanten eigenen Personaleinsatzes') ?>
                    </label>
                    <textarea name="values[in-kind]" id="in-kind" cols="30" rows="2" class="form-control"><?= val('in-kind') ?></textarea>
                    <small class="text-muted">
                        Nachrichtliche Angaben in % unter Nennung der mitarbeitenden Personen (z.B. Antragsteller 10%, ABC 15%, etc.)
                    </small>
                </div>
            <?php } ?>

            <?php if (in_array('website', $fields)) { ?>
                <div class="data-module col-6">
                    <label for="website">
                        <?= lang('Project website', 'Webseite des Projekts') ?>
                    </label>
                    <input type="text" class="form-control" name="values[website]" id="website" value="<?= val('website') ?>">
                    <small class="text-muted">
                        <?= lang('Please enter full ULR (incl. http...)', 'Bitte vollständige URL angeben (inkl. http...)') ?>
                    </small>
                </div>
            <?php } ?>

            <?php if (in_array('abstract', $fields)) { ?>
                <div class="data-module col-12">
                    <label for="abstract" class="">
                        <?= lang('Abstract', 'Kurzbeschreibung') ?>
                    </label>
                    <textarea name="values[abstract]" id="abstract" cols="30" rows="5" class="form-control"><?= val('abstract') ?></textarea>
                </div>
            <?php } ?>

            <?php if (in_array('public', $fields)) { ?>
                <div class="data-module col-12">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="public-check" <?= val('public', false) ? 'checked' : '' ?> name="values[public]">
                        <label for="public-check">
                            Zustimmung zur Internetpräsentation des bewilligten Vorhabens
                        </label>
                    </div>
                </div>
            <?php } ?>

            <?php if (in_array('ressources', $fields)) {
                $res = $form['ressources'] ?? [];
            ?>
                <!-- fieldset -->
                <div class="data-module col-12">
                    <fieldset>
                        <legend>
                            <?= lang('Ressources', 'Ressourcen') ?>
                        </legend>

                        <!-- each: Sachmittel, Personalmittel, Raumkapitäten, sonstige Ressourcen -->
                        <div class="ressources">
                            <div class="form-group">
                                <label for="ressource1">
                                    <?= lang('Additional material resources', 'Zusätzliche Sachmittel') ?>
                                </label>
                                <div>
                                    <input type="radio" name="values[ressources][material]" id="material-yes" value="yes" <?= ($res['material'] ?? false) ? 'checked' : '' ?>>
                                    <label for="material-yes">Yes</label>
                                    <input type="radio" name="values[ressources][material]" id="material-no" value="no" <?= ($res['material'] ?? false) ? '' : 'checked' ?>>
                                    <label for="material-no">No</label>
                                </div>

                                <textarea type="text" class="form-control" name="values[ressources][material_details]" id="ressource-material" style="display: <?= ($res['material'] ?? false) ? 'block' : 'none' ?>;" placeholder="Details"><?= $res['material_details'] ?? '' ?></textarea>
                                <script>
                                    document.getElementById('material-yes').addEventListener('change', function() {
                                        document.getElementById('ressource-material').style.display = 'block';
                                    });
                                    document.getElementById('material-no').addEventListener('change', function() {
                                        document.getElementById('ressource-material').style.display = 'none';
                                    });
                                </script>
                            </div>

                            <div class="form-group">
                                <label for="ressource2">
                                    <?= lang('Additional personnel resources', 'Zusätzliche Personalmittel') ?>
                                </label>
                                <div>
                                    <input type="radio" name="values[ressources][personnel]" id="personnel-yes" value="yes" <?= ($res['personnel'] ?? false) ? 'checked' : '' ?>>
                                    <label for="personnel-yes">Yes</label>
                                    <input type="radio" name="values[ressources][personnel]" id="personnel-no" value="no" <?= ($res['personnel'] ?? false) ? '' : 'checked' ?>>
                                    <label for="personnel-no">No</label>
                                </div>

                                <textarea type="text" class="form-control" name="values[ressources][personnel_details]" id="ressource-personnel" style="display: <?= ($res['personnel'] ?? false) ? 'block' : 'none' ?>;" placeholder="Details"><?= $res['personnel_details'] ?? '' ?></textarea>
                                <script>
                                    document.getElementById('personnel-yes').addEventListener('change', function() {
                                        document.getElementById('ressource-personnel').style.display = 'block';
                                    });
                                    document.getElementById('personnel-no').addEventListener('change', function() {
                                        document.getElementById('ressource-personnel').style.display = 'none';
                                    });
                                </script>
                            </div>
                            <div class="form-group">
                                <label for="ressource3">
                                    <?= lang('Additional room capacities', 'Zusätzliche Raumkapazitäten') ?>
                                </label>
                                <div>
                                    <input type="radio" name="values[ressources][room]" id="room-yes" value="yes" <?= ($res['room'] ?? false) ? 'checked' : '' ?>>
                                    <label for="room-yes">Yes</label>
                                    <input type="radio" name="values[ressources][room]" id="room-no" value="no" <?= ($res['room'] ?? false) ? '' : 'checked' ?>>
                                    <label for="room-no">No</label>
                                </div>

                                <textarea type="text" class="form-control" name="values[ressources][room_details]" id="ressource-room" style="display: <?= ($res['room'] ?? false) ? 'block' : 'none' ?>;" placeholder="Details"><?= $res['room_details'] ?? '' ?></textarea>
                                <script>
                                    document.getElementById('room-yes').addEventListener('change', function() {
                                        document.getElementById('ressource-room').style.display = 'block';
                                    });
                                    document.getElementById('room-no').addEventListener('change', function() {
                                        document.getElementById('ressource-room').style.display = 'none';
                                    });
                                </script>

                            </div>
                            <div class="form-group">
                                <label for="ressource4">
                                    <?= lang('Other resources', 'Sonstige Ressourcen') ?>
                                </label>
                                <div>
                                    <input type="radio" name="values[ressources][other]" id="other-yes" value="yes" <?= ($res['other'] ?? false) ? 'checked' : '' ?>>
                                    <label for="other-yes">Yes</label>
                                    <input type="radio" name="values[ressources][other]" id="other-no" value="no" <?= ($res['other'] ?? false) ? '' : 'checked' ?>>
                                    <label for="other-no">No</label>
                                </div>

                                <textarea type="text" class="form-control" name="values[ressources][other_details]" id="ressource-other" style="display: <?= ($res['other'] ?? false) ? 'block' : 'none' ?>;" placeholder="Details"><?= $res['other_details'] ?? '' ?></textarea>
                                <script>
                                    document.getElementById('other-yes').addEventListener('change', function() {
                                        document.getElementById('ressource-other').style.display = 'block';
                                    });
                                    document.getElementById('other-no').addEventListener('change', function() {
                                        document.getElementById('ressource-other').style.display = 'none';
                                    });
                                </script>
                            </div>
                    </fieldset>
                </div>

            <?php } ?>

    </div>
    <button class="btn secondary" type="submit" id="submit-btn">
        <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
    </button>

</form>