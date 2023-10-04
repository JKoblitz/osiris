<?php

/**
 * Page for admin dashboard for general settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /admin/general
 *
 * @package OSIRIS
 * @since 1.1.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$affiliation = $Settings->get('affiliation_details');

// transform activities to new format
$Format = new Document();

$N = 0;
$activities = $osiris->activities->find([]);
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


<?php
    include BASEPATH . "/components/admin-nav.php";
?>


<form action="#" method="post" id="modules-form" enctype="multipart/form-data">


    <div class="box success">
        <h2 class="header"><?= lang('General Settings', 'Allgemeine Einstellungen') ?></h2>

        <div class="content">
            <div class="form-group">
                <label for="name" class="required "><?= lang('Start year', 'Startjahr') ?></label>
                <input type="year" class="form-control" name="general[startyear]" required value="<?= $Settings->get('startyear') ?? '2022' ?>">
                <span class="text-muted">
                    <?= lang(
                        'The start year defines the beginning of many charts in OSIRIS. It is possible to add activities that occur befor that year though.',
                        'Das Startjahr bestimmt den Anfang vieler Abbildungen in OSIRIS. Man kann jedoch auch Aktivitäten hinzufügen, die vor dem Startjahr geschehen sind.'
                    ) ?>
                </span>
            </div>


            <div class="form-group">
                <span>
                <?= lang('Disable coins globally', 'Coins global ausschalten') ?>
                </span>
                <?php
                    $disable_coins = $Settings->hasFeatureDisabled('coins');
                ?>
                
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-coins-true" value="true" name="general[disable-coins]" <?= $disable_coins ? 'checked' : '' ?>>
                    <label for="disable-coins-true">ja</label>
                </div>
                
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-coins-false" value="false" name="general[disable-coins]" <?= $disable_coins ? '' : 'checked' ?>>
                    <label for="disable-coins-false">nein</label>
                </div>
            </div>
            
            
            <div class="form-group">
                <span>
                <?= lang('Disable achievements globally', 'Errungenschaften global ausschalten') ?>
                </span>
                <?php
                    $disable_achievements = $Settings->hasFeatureDisabled('achievements');
                ?>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-achievements-true" value="true" name="general[disable-achievements]" <?= $disable_achievements ? 'checked' : '' ?>>
                    <label for="disable-achievements-true">ja</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-achievements-false" value="false" name="general[disable-achievements]" <?= $disable_achievements ? '' : 'checked' ?>>
                    <label for="disable-achievements-false">nein</label>
                </div>
            </div>

            
            <div class="form-group">
                <span>
                <?= lang('Disable user profile metrics globally', 'Metriken im Nutzerprofil global ausschalten') ?>
                </span>
                <?php
                    $disable_user_metrics = $Settings->hasFeatureDisabled('user-metrics');
                ?>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-user-metrics-true" value="true" name="general[disable-user-metrics]" <?= $disable_user_metrics ? 'checked' : '' ?>>
                    <label for="disable-user-metrics-true">ja</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" id="disable-user-metrics-false" value="false" name="general[disable-user-metrics]" <?= $disable_user_metrics ? '' : 'checked' ?>>
                    <label for="disable-user-metrics-false">nein</label>
                </div>

                <span class="font-size-12 d-block text-muted">
                    Wenn diese Funktion ausgeschaltet wird, sind Nutzermetriken (Graphen) nur noch auf der eigenen Profilseite sichtbar.
                </span>
            </div>

        </div>
        <hr>

        <div class="content">
            <h2 class="title">
                Institut
            </h2>

            <div class="row row-eq-spacing">
                <div class="col-sm-2">
                    <label for="icon" class="required">ID</label>
                    <input type="text" class="form-control" name="affiliation[id]" required value="<?= $affiliation['id'] ?>">
                </div>
                <div class="col-sm">
                    <label for="name" class="required ">Name</label>
                    <input type="text" class="form-control" name="affiliation[name]" required value="<?= $affiliation['name'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="name" class="required ">Link</label>
                    <input type="text" class="form-control" name="affiliation[link]" required value="<?= $affiliation['link'] ?? '' ?>">
                </div>
            </div>
        </div>
        <hr>
        <div class="content">
            <h2 class="title">
                Logo
            </h2>

            <div class="row">
                <div class="col-sm">
                    <b><?= lang('Current Logo', 'Derzeitiges Logo') ?>: <br></b>
                    <img src="<?= ROOTPATH . '/img/' . $affiliation['logo'] ?>" alt="No logo available" class="img-fluid w-300 mw-full mb-20">

                </div>
                <div class="col-sm text-right">
                    <div class="custom-file mb-20" id="file-input-div">
                        <input type="file" id="file-input" name="logo" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                        <label for="file-input"><?= lang('Upload a new logo', 'Lade ein neues Logo hoch') ?></label>
                        <br><small class="text-danger">Max. 2 MB.</small>
                    </div>

                </div>
            </div>

            <button class="btn success">
                <i class="ph ph-floppy-disk"></i>
                Save
            </button>
        </div>



    </div>



</form>


<div class="box danger">
    <h2 class="header">
        <?= lang('Export/Import Settings', 'Exportiere und importiere Einstellungen') ?>
    </h2>
    <div class="content">
        <a href="<?= ROOTPATH ?>/settings.json" download='settings.json' class="btn"><?= lang('Download current settings', 'Lade aktuelle Einstellungen herunter') ?></a>
    </div>
    <hr>
    <div class="content">
        <form action="<?= ROOTPATH ?>/reset-settings" method="post" id="modules-form" enctype="multipart/form-data">
            <div class="custom-file mb-20" id="settings-input-div">
                <input type="file" id="settings-input" name="settings" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                <label for="settings-input"><?= lang('Upload settings (as JSON)', 'Lade Einstellungen hoch (als JSON)') ?></label>
            </div>
            <button class="btn danger">Upload & Replace</button>
        </form>
    </div>
    <hr>
    <div class="content">
        <form action="<?= ROOTPATH ?>/reset-settings" method="post">
            <button class="btn danger">
                <?= lang('Reset all settings to the default value.', 'Setze alle Einstellungen auf den Standardwert zurück.') ?>
            </button>
        </form>
    </div>

</div>