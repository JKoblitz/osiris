<?php

/**
 * Page to see details on one activity
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /activities/view/<activity_id>
 *
 * @package     OSIRIS
 * @since       1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

include_once BASEPATH . "/php/Modules.php";

// check if this is an ongoing activity type
$ongoing = false;
$M = $Format->subtypeArr['modules'] ?? array();
foreach ($M as $m) {
    if (str_ends_with($m, '*')) $m = str_replace('*', '', $m);
    if ($m == 'date-range-ongoing') $ongoing = true;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'add-success') { ?>
    <div class="alert signal mb-20">
        <h3 class="title">
            <?= lang('For the good practice: ', 'Für die gute Praxis:') ?>
        </h3>
        <?= lang(
            'Upload now all relevant files for this activity (e.g. as PDF) to have them available for documentation and exchange.',
            'Lade jetzt die relevanten Dateien (z.B. PDF) hoch, um sie für die Dokumentation parat zu haben.'
        ) ?>
        <i class="ph ph-smiley"></i>
        <b><?= lang('Thank you!', 'Danke!') ?></b>
        <br>
        <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn primary">
            <i class="ph ph-upload"></i>
            <?= lang('Upload files', 'Dateien hochladen') ?>
        </a>
    </div>
<?php }
?>

<style>
    .key {
        /* min-width: 16rem; */
        text-align: left;
        padding-right: 2rem !important;
    }

    #detail-table td,
    #detail-table th {
        padding: 0.5rem 0;
    }


    .table tbody tr[class^="row-"]:hover {
        border-left-width: 5px;
    }

    [class^="col-"] .box {
        margin: 0;
        /* height: 100%; */
    }

    .btn-toolbar {
        margin: 0 0 1rem;
    }

    .filelink {
        display: block;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        color: inherit !important;
        padding: 0 2rem;
        margin: 0 0 1rem;
    }

    .filelink:hover {
        text-decoration: none;
        background-color: rgba(0, 110, 183, 0.1);
    }

    .show-on-hover:hover .invisible {
        visibility: visible !important;
    }
</style>


<?php if ($doc['locked'] ?? false) { ?>
    <div class="mb-10 show-on-hover">
        <span class="badge danger font-size-16" data-toggle="tooltip" data-title="<?= lang('This activity has been locked.', 'Diese Aktivität wurde gesperrt.') ?>">
            <i class="ph ph-lock text-danger"></i>
            <?= lang('Locked', 'Gesperrt') ?>
        </span>
        <span class="invisible text-danger">
            <?php if ($Settings->hasPermission('edit-locked') || $Settings->hasPermission('delete-locked')) { ?>
                <?= lang('You have permission to edit and delete', 'Du hast Rechte zum Editieren und Löschen') ?>
            <?php } else if ($Settings->hasPermission('edit-locked')) { ?>
                <?= lang('You have permission to edit', 'Du hast Rechte zum Editieren') ?>
            <?php } else if ($Settings->hasPermission('delete-locked')) { ?>
                <?= lang('You have permission to delete', 'Du hast Rechte zum Löschen') ?>
            <?php } else { ?>
                <?= lang('You have no permission to edit or delete this activity', 'Du hast keine Rechte zum Barbeiten oder Löschen dieser Aktivität') ?>
            <?php } ?>

        </span>
    </div>

<?php } ?>

<div class="box mt-0">
    <div class="content">

        <div class="float-sm-right">
            <div class="btn-group">
                <button class="btn primary" onclick="addToCart(this, '<?= $id ?>')">
                    <i class="<?= (in_array($id, $cart)) ? 'ph ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-shopping-cart ph-shopping-cart-plus' ?>"></i>
                    <?= lang('Add to cart', 'Für Download sammeln') ?>
                </button>
                <div class=" dropdown with-arrow btn-group ">
                    <button class="btn primary" data-toggle="dropdown" type="button" id="download-btn" aria-haspopup="true" aria-expanded="false">
                        <i class="ph ph-download"></i> Download
                        <i class="ph ph-fill ph-angle-down ml-5" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="download-btn">
                        <div class="content">
                            <form action="<?= ROOTPATH ?>/download" method="post">

                                <input type="hidden" name="filter[id]" value="<?= $id ?>">

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
            </div>

        </div>

        <h4 class="title m-0" style="line-height: 1;">
            <span class='mr-10'><?= $Format->activity_icon(false) ?></span>
            <?= $Format->activity_title() ?>
        </h4>

        <p class="lead">
            <?= $Format->formatShort($link = false) ?>
        </p>
    </div>
    <hr>
    <div class="content">
        <span class="float-right badge font-size-12"><?= lang('Formatted entry', 'Formatierter Eintrag') ?></span>

        <p>
            <?php
            echo $Format->format();
            ?>
        </p>
    </div>
</div>




<div class="row row-eq-spacing-lg">
    <div class="col-lg-6">


        <div class="box h-full">
            <div class="content">

                <div class="btn-toolbar float-sm-right">
                    <?php if (($user_activity || $Settings->hasPermission('edit-activities')) && (!$locked || $Settings->hasPermission('edit-locked'))) { ?>
                        <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn primary">
                            <i class="ph ph-upload"></i>
                            <?= lang('Upload', 'Hochladen') ?>
                        </a>
                    <?php } ?>
                </div>

                <h2 class="title"><?= lang('Files', 'Dateien') ?></h2>

                <?php if (!empty($doc['files'])) : ?>
                    <?php foreach ($doc['files'] as $file) : ?>
                        <a href="<?= $file['filepath'] ?>" target="_blank" class="filelink">
                            <i class="ph ph-<?= getFileIcon($file['filetype']) ?> mr-10 ph-2x text-osiris"></i>

                            <?= $file['filename'] ?>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span class="text-signal"><?= lang('No files attached', 'Noch keine Dateien hochgeladen') ?></span>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div class="box h-full">
            <div class="content">

                <div class="btn-toolbar float-sm-right">
                    <a href="#connect" class="btn primary mr-5">
                        <i class="ph ph-circles-three-plus"></i>
                        <?= lang("Connect", "Verknüpfen") ?>
                    </a>
                </div>

                <h2 class="title">
                    <?= lang('Research data', 'Forschungsdaten') ?>
                </h2>

                <?php if (!empty($doc['connections'])) { ?>
                    <?php foreach ($doc['connections'] as $con) { ?>
                        <p>
                            <b class="mr-10"><?= $con['entity'] ?>:</b>
                            <?php if (!empty($con['link'])) { ?>
                                <a href="<?= $con['link'] ?>" class="badge " target="_blank">
                                    <i class="ph ph-link text-blue" style="line-height: 0;"></i>
                                    <?= $con['name'] ? $con['name'] : $con['link'] ?>
                                </a>
                            <?php } else { ?>
                                <span class="badge "><?= $con['name'] ?></span>
                            <?php } ?>
                        </p>
                    <?php } ?>

                <?php } else { ?>
                    <?= lang('No research data connected.', 'Noch keine Forschungsdaten verknüpft.') ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>


<div class="modal" id="connect" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="btn float-right" role="button" aria-label="Close" href="#close-modal">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="modal-title">
                <?= lang('Connect research data', 'Forschungsdaten verknüpfen') ?>
            </h5>
            <div>
                <?php
                include BASEPATH . "/components/connect-research-data.php";
                ?>

            </div>
        </div>
    </div>
</div>


<div class="row row-eq-spacing-lg">
    <div class="col-lg-7">


        <div class="box ">
            <div class="content">


                <div class="btn-toolbar float-sm-right">
                    <?php if (($user_activity || $Settings->hasPermission('edit-activities')) && (!$locked || $Settings->hasPermission('edit-locked'))) { ?>
                        <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn primary">
                            <i class="ph ph-pencil-simple-line"></i>
                            <?= lang('Edit', 'Bearbeiten') ?>
                        </a>
                    <?php } ?>


                    <?php if (!in_array($doc['type'], ['publication'])) { ?>
                        <a href="<?= ROOTPATH ?>/activities/copy/<?= $id ?>" class="btn primary">
                            <i class="ph ph-copy"></i>
                            <?= lang("Add a copy", "Kopie anlegen") ?>
                        </a>
                    <?php } ?>


                    <?php if ($user_activity && $locked && empty($doc['end'] ?? null) && $ongoing) { ?>
                        <!-- End user activity even if activity is locked -->
                        <div class="dropdown">
                            <button class="btn primary" data-toggle="dropdown" type="button" id="update-end-date" aria-haspopup="true" aria-expanded="false">
                                <i class="ph ph-calendar-check"></i>
                                <?= lang('End activity', 'Beenden') ?> <i class="ph ph-caret-down ml-5" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-center w-200" aria-labelledby="update-end-date">
                                <form action="<?= ROOTPATH . "/update/" . $id ?>" method="POST" class="content">
                                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                    <div class="form-group">
                                        <label for="date_end"><?= lang('Activity ended at:', 'Aktivität beendet am:') ?></label>
                                        <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray($doc['end'] ?? null) ?>" required>
                                    </div>
                                    <button class="btn btn-block" type="submit"><?= lang('Save', 'Speichern') ?></button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>

                </div>

                <h2 class="title">Details</h2>
                <div class="mb-10">
                    <?= $Format->activity_badge() ?>
                </div>

                <table class="w-full" id="detail-table">

                    <?php
                    $selected = $Format->subtypeArr['modules'] ?? array();
                    $Modules = new Modules($doc);
                    $Format->usecase = "list";

                    foreach ($selected as $module) {
                        if (str_ends_with($module, '*')) $module = str_replace('*', '', $module);
                        if (in_array($module, ['authors', "editors", "semester-select"])) continue;
                    ?>
                        <?php if ($module == 'teaching-course' && isset($doc['module_id'])) :
                            $module = $DB->getConnected('teaching', $doc['module_id']);
                        ?>
                            <tr>
                                <th class="key"><?= lang('Module', 'Modul') ?>:</th>
                                <td>
                                    <a class="module" href="<?= ROOTPATH ?>/teaching#<?= $doc['module_id'] ?>">

                                        <h5 class="m-0"><span class="highlight-text"><?= $module['module'] ?></span> <?= $module['title'] ?></h5>
                                        <span class="text-muted"><?= $module['affiliation'] ?></span>

                                    </a>
                                </td>
                            </tr>



                        <?php elseif ($module == 'journal' && isset($doc['journal_id'])) :
                            $journal = $DB->getConnected('journal', $doc['journal_id']);
                        ?>

                            <tr>
                                <th class="key"><?= lang('Journal') ?>:</th>
                                <td>
                                    <a class="module" href="<?= ROOTPATH ?>/journal/view/<?= $doc['journal_id'] ?>">

                                        <h5 class="m-0"><?= $journal['journal'] ?></h5>
                                        <span class="float-right text-muted"><?= $journal['publisher'] ?></span>
                                        <span class="text-muted">
                                            ISSN: <?= print_list($journal['issn']) ?>
                                            <br>
                                            Impact:
                                            <?= $doc['impact'] ?? 'unknown' ?>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        <?php else : ?>

                            <tr>
                                <th class="key"><?= $Modules->get_name($module) ?>:</th>
                                <td><?= $Format->get_field($module) ?></td>
                            </tr>

                        <?php endif; ?>
                    <?php } ?>



                    <?php if (($user_activity || $Settings->hasPermission('edit-activities')) && isset($doc['comment'])) : ?>
                        <tr class="text-muted">
                            <th class="key" style="text-decoration: 1px dotted underline;" data-toggle="tooltip" data-title="<?= lang('Only visible for authors and editors.', 'Nur sichtbar für Autoren und Editor-MA.') ?>">
                                <?= lang('Comment', 'Kommentar') ?>:
                            </th>
                            <td>
                                <?= $doc['comment'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if (isset($doc['created_by'])) :
                        if ($user == $doc['created_by']) $user_activity = true;
                    ?>
                        <tr class="text-muted">
                            <th class="key">Created by:</th>
                            <td>
                                <?= $DB->getNameFromId($doc['created_by']) ?> (<?= $doc['created'] ?>)
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($doc['updated_by'])) : ?>
                        <tr class="text-muted">
                            <th class="key">Last updated:</th>
                            <td>
                                <?= $DB->getNameFromId($doc['updated_by']) ?> (<?= $doc['updated'] ?>)
                            </td>
                        </tr>
                    <?php endif; ?>



                </table>
            </div>

        </div>


        <div class="alert danger mt-20 py-20">
            <h2 class="title">
                <?= lang('Delete', 'Löschen') ?>
            </h2>
            <?php

            // $in_quarter = inCurrentQuarter($doc['year'], $doc['month']);
            if ($locked && !$Settings->hasPermission('delete-locked')) : ?>
                <p class="mt-0">
                    <?= lang(
                        'This activity has been locked because it was already used by Controlling in a report. Due to the documentation and verification obligation, activities may not be easily changed or deleted after the report. However, if a change is necessary, please contact the responsible persons.',
                        'Diese Aktivität wurde gesperrt, da sie bereits vom Controlling in einem Report verwendet wurde. Wegen der Dokumentations- und Nachweispflicht dürfen Aktivitäten nach dem Report nicht mehr so einfach verändert oder gelöscht werden. Sollte dennoch eine Änderung notwenig sein, meldet euch bitte bei den Verantwortlichen.'
                    ) ?>
                </p>
                <?php
                $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag bearbeiten/löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                ?>
                <!-- <a class="btn danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Änderung&body=<?= $body ?>">
                    <i class="ph ph-envelope" aria-hidden="true"></i>
                    <?= lang('Contact controlling', 'Controlling kontaktieren') ?>
                </a> -->
            <?php
            elseif ($Settings->hasPermission('delete-activities')) :
            ?>
                <p class="mt-0">
                    <?= lang('You have permission to delete this activity:', 'Du hast die nötigen Rechte, um diese Aktivität zu löschen:') ?>
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn danger">
                        <i class="ph ph-trash"></i>
                        <?= lang('Delete activity', 'Lösche Aktivität') ?>
                    </button>
                </form>
            <?php elseif (!$user_activity && ($doc['created_by'] ?? '') !== $user) : ?>

                <p class="mt-0">
                    <?= lang(
                        'This is not your own activity. If for any reason you want it changed or deleted, please contact the creator of the activity or the controlling.',
                        'Dies ist nicht deine Aktivität. Wenn du aus irgendwelchen Gründen willst, dass sie verändert oder gelöscht wird, kontaktiere bitte den Urheber der Aktivität oder das Controlling.'
                    ) ?>
                </p>
                <!-- <?php
                        $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag bearbeiten/löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                        ?>
                <a class="btn danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Änderung&body=<?= $body ?>">
                    <i class="ph ph-envelope" aria-hidden="true"></i>
                    <?= lang('Contact controlling', 'Controlling kontaktieren') ?>
                </a>
                <?php if (isset($doc['created_by'])) { ?>

                    <a class="btn danger" href="mailto:<?= $doc['created_by'] ?>@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf 'Änderung'&body=<?= $body ?>">
                        <i class="ph ph-envelope" aria-hidden="true"></i>
                        <?= lang('Contact creator', 'Urheber kontaktieren') ?>
                    </a>
                <?php } ?> -->

            <?php else : ?>
                <p class="mt-0">
                    <b>Info:</b>
                    <?= lang(
                        'This is your own activity and it has not been locked yet. You can delete it.',
                        'Dies ist deine eigene Aktivität und sie ist noch nicht gesperrt worden. Du kannst sie also löschen.'
                    ) ?>
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn danger">
                        <i class="ph ph-trash"></i>
                        <?= lang('Delete activity', 'Lösche Aktivität') ?>
                    </button>
                    <br>
                    <small class="text-danger">
                        <?= lang('Cannot be made undone.', 'Kann nicht rückgängig gemacht werden.') ?>
                    </small>
                </form>
            <?php endif; ?>

        </div>
    </div>


    <div class="col-lg-5">
        <?php foreach (['authors', 'editors'] as $role) { ?>
            <?php if (isset($activity[$role])) { ?>

                <div class="box">
                    <div class="content">

                        <div class="btn-toolbar mb-10 float-sm-right">
                            <?php if (($user_activity || $Settings->hasPermission('edit-activities')) && (!$locked || $Settings->hasPermission('edit-locked'))) { ?>
                                <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>/<?= $role ?>" class="btn primary">
                                    <i class="ph ph-pencil-simple-line"></i>
                                    <?= lang("Edit", "Bearbeiten") ?>
                                </a>
                            <?php } ?>
                        </div>

                        <h2 class="title">
                            <?php if ($role == 'authors') {
                                echo lang('Authors', 'Autoren');
                            } else {
                                echo lang('Editors', 'Editoren');
                            } ?>
                        </h2>


                    </div>
                    <table class="table simple">
                        <thead>
                            <tr>
                                <th>Last name</th>
                                <th>First name</th>
                                <?php if ($doc['type'] == 'publication' && $role == 'authors') : ?>
                                    <th>Position</th>
                                <?php endif; ?>
                                <?php if ($doc['type'] == 'teaching' && $role == 'authors') : ?>
                                    <th>SWS</th>
                                <?php endif; ?>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody id="<?= $role ?>">
                            <?php foreach ($activity[$role] as $i => $author) {
                                $cls = "";
                                $dept = "";
                                if (isset($author['user']) && !empty($author['user'])) {
                                    $u = $DB->getPerson($author['user']);
                                    $dept = $u['dept'] ?? 'unknown';
                                    $cls = "row-" . ($u['dept'] ?? 'muted');
                                }
                                // row-MIOS 
                            ?>
                                <tr class="<?= $cls ?>" data-dept="<?= $dept ?>">
                                    <td class="<?= (($author['aoi'] ?? 0) == '1' ? 'font-weight-bold' : '') ?>">
                                        <?php if (!empty($dept)) { ?>
                                            <span data-toggle="tooltip" data-title="<?= $dept ?>"><?= $author['last'] ?? '' ?></span>
                                        <?php } else { ?>
                                            <?= $author['last'] ?? '' ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?= $author['first'] ?? '' ?>
                                    </td>
                                    <?php if ($doc['type'] == 'publication' && $role == 'authors') : ?>
                                        <td>
                                            <?= $author['position'] ?? '' ?>
                                        </td>
                                    <?php endif; ?>
                                    <?php if ($doc['type'] == 'teaching' && $role == 'authors') : ?>
                                        <td>
                                            <?= $author['sws'] ?? 0 ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if (isset($author['user']) && !empty($author['user'])) {
                                        ?>
                                            <a href="<?= ROOTPATH ?>/profile/<?= $author['user'] ?>"><?= $author['user'] ?></a>
                                            <span data-toggle="tooltip" data-title="<?= lang('Author approved activity?', 'Autor hat die Aktivität bestätigt?') ?>">
                                                <?= bool_icon($author['approved'] ?? 0) ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        <?php } ?>

    </div>
</div>


<?php if ($Settings->hasPermission('see-raw-data') || isset($_GET['verbose'])) { ?>
    <h2 class="title">
        <?= lang('Raw data', 'Rohdaten') ?>
    </h2>

    <?= lang('Raw data as they are stored in the database - for admins only.', 'Die Rohdaten, wie sie in der Datenbank gespeichert werden - nur für Admins.') ?>

    <div class="box overflow-x-scroll">
        <?php
        dump($doc, true);
        ?>
    </div>
<?php } ?>