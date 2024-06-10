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


$formaction = ROOTPATH ;
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
    if ($val instanceof MongoDB\Model\BSONArray){
        return implode(',', DB::doc2Arr($val));
    }


    return $val;
}

function sel($index, $value)
{
    return val($index) == $value ? 'selected' : '';
}
?>
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>

<style>
</style>

<h3 class="title">
    <?= lang('Add new project', 'Neues Projekt') ?>
</h3>

<form action="<?= $formaction ?>" method="post" id="project-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <div class="row row-eq-spacing">
        <div class="col-sm-6">
            <label for="project" class="required element-other">
                <?= lang('Short title', 'Kurztitel') ?>
            </label>
            <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>" maxlength="30">
        </div>
        <div class="col-sm-6">
            <label class="required" for="type">
                <?= lang('Type', 'Typ') ?>
            </label>
            <select class="form-control" id="type" name="values[type]" required autocomplete="off">
                <option value="Drittmittel" <?= sel('type', 'Drittmittel') ?>><?= lang('Third-party funded', 'Drittmittel-finanziert') ?></option>
                <option value="Eigenfinanziert" <?= sel('type', 'Eigenfinanziert') ?>><?= lang('Self-funded', 'Eigenfinanziert') ?></option>
                <option value="Sonstiges" <?= sel('type', 'Sonstiges') ?>><?= lang('Other', 'Sonstiges') ?></option>
            </select>
        </div>
    </div>

    <div class="form-group">

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

    <div class="row row-eq-spacing">
        <div class="col-sm-6">
            <label class="required element-author" for="username">
                <?= lang('Contact person', 'Ansprechpartner:in') ?>
            </label>
            <select class="form-control" id="username" name="values[contact]" required autocomplete="off">
                <?php
                $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                foreach ($userlist as $j) { ?>
                    <option value="<?= $j['username'] ?>" <?= $j['username'] == ($form['contact'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-6">
            <label class="required" for="status">
                <?= lang('Status', 'Status') ?>
            </label>
            <select class="form-control" id="status" name="values[status]" required autocomplete="off">
                <option value="applied" <?= sel('status', 'applied') ?>><?= lang('applied', 'beantragt') ?></option>
                <option value="approved" <?= sel('status', 'approved') ?>><?= lang('approved', 'bewilligt') ?></option>
                <option value="rejected" <?= sel('status', 'rejected') ?>><?= lang('rejected', 'abgelehnt') ?></option>
                <option value="finished" <?= sel('status', 'abgeschlossen') ?>><?= lang('finished', 'abgeschlossen') ?></option>
            </select>
        </div>

    </div>

    <div class="row row-eq-spacing">
        <div class="col-sm-4">
            <label for="funder" class="required">
                <?= lang('Third-party funder', 'Drittmittelgeber') ?>
            </label>
            <select class="form-control" name="values[funder]" value="<?= val('funder') ?>" required id="funder">
                <option <?= sel('funder', 'Eigenmittel') ?>>Eigenmittel</option>
                <option <?= sel('funder', 'DFG') ?>>DFG</option>
                <option <?= sel('funder', 'Bund') ?>>Bund</option>
                <option <?= sel('funder', 'Bundesländer') ?>>Bundesländer</option>
                <option <?= sel('funder', 'Wirtschaft') ?>>Wirtschaft</option>
                <option <?= sel('funder', 'EU') ?>>EU</option>
                <option <?= sel('funder', 'Stiftungen') ?>>Stiftungen</option>
                <option <?= sel('funder', 'Leibniz Wettbewerb') ?>>Leibniz Wettbewerb</option>
                <option <?= sel('funder', 'Sonstige Drittmittelgeber') ?>>Sonstige Drittmittelgeber</option>
                <!-- <option>Sonstige öffentliche internationale Förderorganisationen</option> -->
                <!-- <option>Nicht erklärt (Private Mittelgeber)</option>
                <option>Nicht erklärt (Öffentliche Mittelgeber)</option> -->
            </select>
        </div>
        <div class="col-sm-4">
            <label for="funding_organization" class="required">
                <?= lang('Funding organization', 'Förderorganisation / Zuwendungsgeber') ?>
            </label>
            <input type="text" class="form-control" name="values[funding_organization]" value="<?= val('funding_organization') ?>" id="funding_organization" required>
        </div>
        <div class="col-sm-4">
            <label for="funding_number">
                <?= lang('Funding reference number', 'Förderkennzeichen') ?>
            </label>
            <input type="text" class="form-control" name="values[funding_number]" value="<?= val('funding_number') ?>" id="funding_number">
            <span class="text-muted"><?=lang('Multiple seperated by comma', 'Mehrere durch Komma getrennt')?></span>
        </div>
    </div>


    <div class="row row-eq-spacing">
        <div class="col-sm-4">
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
        <div class="col-sm-4">
            <label for="role">
                <?= lang('Role of', 'Rolle von') ?> <?= $Settings->get('affiliation') ?>
            </label>
            <select class="form-control" name="values[role]" id="role">
                <option value="coordinator" <?= sel('role', 'coordinator') ?>><?= lang('Coordinator', 'Koordinator') ?></option>
                <option value="partner" <?= sel('role', 'partner') ?>><?= lang('Partner') ?></option>
            </select>
        </div>
        <div class="col-sm-4">
            <label for="coordinator">
                <?= lang('Coordinator facility', 'Koordinator-Einrichtung') ?>
            </label>
            <input type="text" class="form-control" name="values[coordinator]" id="coordinator" value="<?= val('coordinator', $Settings->get('affiliation')) ?>">
        </div>
    </div>

    <div class=" row row-eq-spacing align-items-end">
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

    
    <div class="row row-eq-spacing">
        <div class="col-sm-6">
            <label for="grant_sum">
                <?= lang('Grant amount', 'Bewilligungssumme') ?> [Euro]
            </label>
            <input type="number" step="1" class="form-control" name="values[grant_sum]" id="grant_sum" value="<?=val('grant_sum')?>">
        </div>
        <div class="col-sm-6">
            <label for="grant_income">
                <?= lang('Funding income', 'Drittmitteleinnahmen') ?> [Euro]
            </label>
            <input type="number" step="1" class="form-control" name="values[grant_income]" id="grant_income" value="<?=val('grant_income')?>">
        </div>
    </div>

    <div class="form-group">
        <label for="personal" class="">
            <?= lang('Personnel measures planned', 'Geplante Personalmaßnahmen') ?>
        </label>
        <textarea name="values[personal]" id="personal" cols="30" rows="2" class="form-control"><?= val('personal') ?></textarea>
        <small class="text-muted">
            Einstellungen/Verlängerungen in Personenmonaten & Kategorie
        </small>
    </div>

    <div class="form-group">
        <label for="website">
            <?= lang('Project website', 'Webseite des Projects') ?>
        </label>
        <input type="text" class="form-control" name="values[website]" id="website" value="<?= val('website') ?>">
        <small class="text-muted">
            <?= lang('Please enter full ULR (incl. http...)', 'Bitte vollständige URL angeben (inkl. http...)') ?>
        </small>
    </div>

    <div class="form-group">
        <label for="abstract" class="">
            <?= lang('Abstract', 'Kurzbeschreibung') ?>
        </label>
        <textarea name="values[abstract]" id="abstract" cols="30" rows="5" class="form-control"><?= val('abstract') ?></textarea>
    </div>

    <div class="form-group">
        <div class="custom-checkbox">
            <input type="checkbox" id="public-check" <?= val('public', false) ? 'checked' : '' ?> name="values[public]">
            <label for="public-check">
                Zustimmung zur Internetpräsentation des bewilligten Vorhaben
            </label>
        </div>
    </div>

    <button class="btn primary" type="submit" id="submit-btn">
        <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
    </button>

</form>