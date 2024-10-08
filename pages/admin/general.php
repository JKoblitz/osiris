<?php

/**
 * Page for admin dashboard for general settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link /admin/general
 *
 * @package OSIRIS
 * @since 1.1.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

$affiliation = $Settings->get('affiliation_details');

// transform activities to new format
$Format = new Document();

$N = 0;
$activities = $osiris->activities->find(['subtype' => ['$exists' => false]]);
foreach ($activities as $doc) {
    if (isset($doc['subtype'])) continue;

    $Format->setDocument($doc);
    $subtype = $Format->subtypeArr['id'];

    $updateResult = $osiris->activities->updateOne(
        ['_id' => $doc['_id']],
        ['$set' => ["subtype" => $subtype]]
    );
    $N += $updateResult->getModifiedCount();
}

if ($N > 0) {
    echo "$N activities were transformed into the new format.";
}
?>


<form action="<?= ROOTPATH ?>/crud/admin/general" method="post" id="modules-form">


    <div class="box primary">
        <h2 class="header"><?= lang('General Settings', 'Allgemeine Einstellungen') ?></h2>

        <div class="content">
            <div class="form-group">
                <label for="name" class="required "><?= lang('Start year', 'Startjahr') ?></label>
                <input type="year" class="form-control" name="general[startyear]" required value="<?= $Settings->get('startyear') ?? '2022' ?>">
                <span class="text-muted">
                    <?= lang(
                        'The start year defines the beginning of many charts in OSIRIS. It is possible to add activities that occured befor that year though.',
                        'Das Startjahr bestimmt den Anfang vieler Abbildungen in OSIRIS. Man kann jedoch auch Aktivitäten hinzufügen, die vor dem Startjahr geschehen sind.'
                    ) ?>
                </span>
            </div>
            <div class="form-group">
                <label for="apikey"><?= lang('API-Key') ?></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="general[apikey]" id="apikey" value="<?= $Settings->get('apikey') ?>">

                    <div class="input-group-append">
                        <button type="button" class="btn" onclick="generateAPIkey()"><i class="ph ph-arrows-clockwise"></i> Generate</button>
                    </div>
                </div>
                <span class="text-muted">
                    <?= lang(
                        'If you do not provide an API key, the REST-API will be open to anyone.',
                        'Falls kein API-Key angegeben wird, ist die REST-API für jeden offen.'
                    ) ?>
                </span>

            </div>
            <script>
                function generateAPIkey() {
                    let length = 50;
                    let result = '';
                    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    const charactersLength = characters.length;
                    let counter = 0;
                    while (counter < length) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                        counter += 1;
                    }
                    $('#apikey').val(result)
                }
            </script>
            <button class="btn primary">
                <i class="ph ph-floppy-disk"></i>
                Save
            </button>

        </div>
    </div>


    <div class="box signal">
        <h2 class="header">
            Institut
        </h2>

        <div class="content">

            <div class="row row-eq-spacing">
                <div class="col-sm-2">
                    <label for="icon" class="required">ID</label>
                    <input type="text" class="form-control" name="general[affiliation][id]" required value="<?= $affiliation['id'] ?>">
                </div>
                <div class="col-sm">
                    <label for="name" class="required ">Name</label>
                    <input type="text" class="form-control" name="general[affiliation][name]" required value="<?= $affiliation['name'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="link" class="required ">Link</label>
                    <input type="text" class="form-control" name="general[affiliation][link]" required value="<?= $affiliation['link'] ?? '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="openalex-id">
                    OpenAlex ID
                </label>
                <input type="text" class="form-control" name="general[affiliation][openalex]" value="<?= $affiliation['openalex'] ?? '' ?>">
                <small class="text-primary">
                    <?=lang('Needed for OpenAlex imports!', 'Diese ID ist notwendig um OpenAlex-Importe zu ermöglichen!')?>
                </small>
            </div>
            <div class="row row-eq-spacing">
                <div class="col-sm-2">
                    <label for="ror">ROR (inkl. URL)</label>
                    <input type="text" class="form-control" name="general[affiliation][ror]" value="<?= $affiliation['ror'] ?? 'https://ror.org/' ?>">
                </div>
                <div class="col-sm">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" name="general[affiliation][location]" value="<?= $affiliation['location'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="country">Country Code (2lttr)</label>
                    <input type="text" class="form-control" name="general[affiliation][country]" value="<?= $affiliation['country'] ?? 'DE' ?>">
                </div>
            </div>
            <div class="row row-eq-spacing">
                <div class="col-sm">
                    <label for="lat">Latitude</label>
                    <input type="number" class="form-control" name="general[affiliation][lat]" value="<?= $affiliation['lat'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="lng">Longitude</label>
                    <input type="number" class="form-control" name="general[affiliation][lng]" value="<?= $affiliation['lng'] ?? '' ?>">
                </div>
            </div>

            <button class="btn signal">
                <i class="ph ph-floppy-disk"></i>
                Save
            </button>
        </div>


    </div>
</form>


<form action="<?= ROOTPATH ?>/crud/admin/general" method="post" id="modules-form" enctype="multipart/form-data">


    <div class="box signal">
        <h2 class="header">
            Logo
        </h2>

        <div class="content">
            <div class="row">
                <div class="col-sm">
                    <b><?= lang('Current Logo', 'Derzeitiges Logo') ?>: <br></b>
                    <?= $Settings->printLogo("img-fluid w-300 mw-full mb-20") ?>
                </div>
                <div class="col-sm text-right">
                    <div class="custom-file mb-20" id="file-input-div">
                        <input type="file" id="file-input" name="logo" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                        <label for="file-input"><?= lang('Upload a new logo', 'Lade ein neues Logo hoch') ?></label>
                        <br><small class="text-danger">Max. 2 MB.</small>
                    </div>

                </div>
            </div>

            <button class="btn signal">
                <i class="ph ph-floppy-disk"></i>
                Save
            </button>
        </div>
    </div>
</form>

<!-- 
<div class="box danger">
    <h2 class="header">
        <?= lang('Export/Import Settings', 'Exportiere und importiere Einstellungen') ?>
    </h2>
    <div class="content">
        <a href="<?= ROOTPATH ?>/settings.json" download='settings.json' class="btn"><?= lang('Download current settings', 'Lade aktuelle Einstellungen herunter') ?></a>
    </div>
    <hr>
    <div class="content">
        <form action="<?= ROOTPATH ?>/crud/admin/reset-settings" method="post" id="modules-form" enctype="multipart/form-data">
            <div class="custom-file mb-20" id="settings-input-div">
                <input type="file" id="settings-input" name="settings" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                <label for="settings-input"><?= lang('Upload settings (as JSON)', 'Lade Einstellungen hoch (als JSON)') ?></label>
            </div>
            <button class="btn danger">Upload & Replace</button>
        </form>
    </div>
    <hr>
    <div class="content">
        <form action="<?= ROOTPATH ?>/crud/admin/reset-settings" method="post">
            <button class="btn danger">
                <?= lang('Reset all settings to the default value.', 'Setze alle Einstellungen auf den Standardwert zurück.') ?>
            </button>
        </form>
    </div>

</div> -->