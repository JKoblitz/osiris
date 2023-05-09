<?php
$affiliation = $Settings->affiliation_details;

?>

<form action="#" method="post" id="modules-form" enctype="multipart/form-data">


    <div class="box box-success">
        <h2 class="header"><?= lang('General Settings', 'Allgemeine Einstellungen') ?></h2>
        <div class="content">
            <div class="form-group">
                <label for="name" class="required "><?= lang('Start year', 'Startjahr') ?></label>
                <input type="year" class="form-control" name="startyear" required value="<?= $Settings->startyear ?? '2022' ?>">
                <span class="text-muted">
                    <?= lang(
                        'The start year defines the beginning of many charts in OSIRIS. It is possible to add activities that occur befor that year though.',
                        'Das Startjahr bestimmt den Anfang vieler Abbildungen in OSIRIS. Man kann jedoch auch Aktivitäten hinzufügen, die vor dem Startjahr geschehen sind.'
                    ) ?>
                </span>
            </div>
        </div>
        <h2 class="header border-top">
            Affiliation
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

        <h2 class="header border-top">
            Logo
        </h2>
        <div class="content">
            <b><?= lang('Current Logo', 'Derzeitiges Logo') ?>: <br></b>
            <img src="<?= ROOTPATH . '/img/' . $affiliation['logo'] ?>" alt="No logo available" class="img-fluid w-300 mw-full mb-20">
            <div class="custom-file mb-20" id="file-input-div">
                <input type="file" id="file-input" name="logo" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                <label for="file-input"><?= lang('Upload a new logo', 'Lade ein neues Logo hoch') ?></label>
                <br><small class="text-danger">Max. 2 MB.</small>
            </div>

    <button class="btn btn-success">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>
        </div>

        

    </div>



</form>


<div class="box box-danger">
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
            <button class="btn btn-danger">Upload & Replace</button>
        </form>
    </div>
    <hr>
    <div class="content">
        <form action="<?= ROOTPATH ?>/reset-settings" method="post">
            <button class="btn btn-danger">
                <?= lang('Reset all settings to the default value.', 'Setze alle Einstellungen auf den Standardwert zurück.') ?>
            </button>
        </form>
    </div>

</div>