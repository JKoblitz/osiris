<?php
include_once BASEPATH . "/php/Modules.php";


if (isset($_GET['msg']) && $_GET['msg'] == 'add-success') { ?>
    <div class="alert alert-signal">
        <h3 class="title">
            <?=lang('For the good practice: ', 'Für die gute Praxis:')?>
        </h3>
            <?=lang('Upload now all relevant files for this activity (e.g. as PDF) to have them available for documentation and exchange.', 
            'Lade jetzt die relevanten Dateien (z.B. PDF) hoch, um sie für die Dokumentation parat zu haben.')?>
            <i class="ph ph-smiley"></i>
            <b><?=lang('Thank you!', 'Danke!')?></b>
            <br>
        <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn">
            <i class="ph ph-regular ph-upload"></i>
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

    .btn-toolbar .btn {
        color: var(--color-warm-orange);
        border-color: var(--color-warm-orange);
    }

    .btn-toolbar .btn:hover {
        /* color: white;
        background-color: var(--color-warm-orange); */
        background-color: var(--signal-color-very-light);
    }

    .table tbody tr[class^="row-"]:hover {
        border-left-width: 5px;
    }
</style>

<div class="content">

    <div class="float-md-right">
        <div class="btn-group">
            <button class="btn " onclick="addToCart(this, '<?= $id ?>')">
                <i class="<?= (in_array($id, $cart)) ? 'ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-regular ph-shopping-cart ph-shopping-cart-plus' ?>"></i>
                <?= lang('Add to cart', 'Für Download sammeln') ?>
            </button>
            <div class=" dropdown with-arrow btn-group ">
                <button class="btn" data-toggle="dropdown" type="button" id="download-btn" aria-haspopup="true" aria-expanded="false">
                    <i class="ph ph-regular ph-download"></i> Download
                    <i class="ph-fill ph-angle-down ml-5" aria-hidden="true"></i>
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
                                    <label for="highlight-aoi"><?= $Settings->affiliation ?><?= lang(' Authors', '-Autoren') ?></label>
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
                            <button class="btn btn-osiris">Download</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <h2>
        <span class='mr-10'><?= $Format->activity_icon(false) ?></span>
        <?= $Format->activity_title() ?>
    </h2>

    <p class="lead">
        <?= $Format->formatShort($link = false) ?>
    </p>

    <h4><?= lang('Formatted entry', 'Formatierter Eintrag') ?></h4>

    <p>
        <?php
        echo $Format->format();
        ?>
    </p>
</div>

<div class="row row-eq-spacing-lg">
    <div class="col-lg-7">

        <h2>Details</h2>

        <div class="btn-toolbar mb-10">
            <?php if (($user_activity && !$locked) || $USER['is_controlling'] || $USER['is_admin']) { ?>
                <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn mr-5">
                    <i class="ph ph-regular ph-pencil-simple-line"></i>
                    <?= lang('Edit activity', 'Aktivität bearbeiten') ?>
                </a>
            <?php } ?>


            <?php if (!in_array($doc['type'], ['publication'])) {
                echo '<a href="' . ROOTPATH . '/activities/copy/' . $id . '" class="btn mr-5">
        <i class="ph ph-regular ph-copy"></i>
        ' . lang("Add a copy", "Kopie anlegen") .
                    '</a>';
            }
            ?>


            <?php if (($user_activity && !$locked) || $USER['is_controlling'] || $USER['is_admin']) { ?>
                <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn mr-5">
                    <i class="ph ph-regular ph-upload"></i>
                    <?= lang('Upload files', 'Dateien hochladen') ?>
                </a>
            <?php } ?>


        </div>

        <div class="box mt-0">
            <div class="content">
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
                            $module = getConnected('teaching', $doc['module_id']);
                        ?>
                            <tr>
                                <th class="key"><?= lang('Module', 'Modul') ?>:</th>
                                <td>
                                    <a class="module" href="<?= ROOTPATH ?>/activities/teaching#<?= $doc['module_id'] ?>">

                                        <h5 class="m-0"><span class="highlight-text"><?= $module['module'] ?></span> <?= $module['title'] ?></h5>
                                        <span class="text-muted"><?= $module['affiliation'] ?></span>

                                    </a>
                                </td>
                            </tr>



                        <?php elseif ($module == 'journal' && isset($doc['journal_id'])) :
                            $journal = getConnected('journal', $doc['journal_id']);
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


                    <?php if (in_array($doc['type'], ['publication', 'poster', 'lecture', 'misc'])) : ?>
                        <tr>
                            <th class="key">Files:</th>
                            <td>
                                <?php if (!empty($doc['files'])) : ?>
                                    <?php foreach ($doc['files'] as $file) : ?>
                                        <a href="<?= $file['filepath'] ?>" target="_blank" class="mr-10">
                                            <?= $file['filename'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <span class="text-signal"><?= lang('No files attached', 'Noch keine Dateien hochgeladen') ?></span>
                                <?php endif; ?>
                                <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn btn-sm">
                                    <i class="ph ph-upload"></i>
                                </a>
                            </td>
                        </tr>

                    <?php endif; ?>


                    <?php if (($user_activity || $USER['is_controlling'] || $USER['is_admin']) && isset($doc['comment'])) : ?>
                        <tr class="text-muted">
                            <th class="key" style="text-decoration: 1px dotted underline;" data-toggle="tooltip" data-title="<?= lang('Only visible for authors and controlling staff.', 'Nur sichtbar für Autoren und Controlling-MA.') ?>"><?= lang('Comment', 'Kommentar') ?>:</th>
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
                                <?= getUserFromId($doc['created_by'])['displayname'] ?> (<?= $doc['created'] ?>)
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($doc['updated_by'])) : ?>
                        <tr class="text-muted">
                            <th class="key">Last updated:</th>
                            <td>
                                <?= getUserFromId($doc['updated_by'])['displayname'] ?> (<?= $doc['updated'] ?>)
                            </td>
                        </tr>
                    <?php endif; ?>



                </table>
            </div>

        </div>
        <?php

        // $in_quarter = inCurrentQuarter($doc['year'], $doc['month']);
        $is_controlling = $USER['is_controlling'] || $USER['is_admin'] ?? false;
        if ($is_controlling) :
        ?>
            <div class="alert alert-danger mt-20 py-20">
                <p class="mt-0">
                    <?= lang('Admins can delete any time:', 'Admins können jederzeit löschen:') ?>
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn text-danger">
                        <i class="ph ph-regular ph-trash"></i>
                        <?= lang('Delete activity', 'Lösche Aktivität') ?>
                    </button>
                </form>
            </div>
        <?php elseif (false) : ?>
            <!-- <div class="alert alert-danger mt-20 py-20">

                <p class="mt-0">
                    <?= lang(
                        'This activity is not in the current quarter, i.e. it may have already been reported. Deleting it is therefore no longer possible. But you can write to Controlling why the entry should be deleted and they will take care of it.',
                        'Diese Aktivität liegt nicht im aktuellen Quartal, d.h. sie ist unter Umständen bereits reportet worden. Löschen ist daher nicht mehr einfach möglich. Du kannst aber dem Controlling schreiben, warum der Eintrag gelöscht werden soll und die kümmern sich darum:'
                    ) ?>
                </p>
                <?php
                $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                ?>
                <a class="btn text-danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Löschung&body=<?= $body ?>">
                    <i class="ph ph-regular ph-envelope" aria-hidden="true"></i>
                    <?= lang('Delete activity', 'Löschen beantragen') ?>
                </a>
            </div> -->
        <?php elseif (!$user_activity) : ?>
            <div class="alert alert-danger mt-20 py-20">

                <p class="mt-0">
                    <?= lang(
                        'This is not your own activity. If for any reason you want it changed or deleted, please contact the creator of the activity or the controlling.',
                        'Dies ist nicht deine Aktivität. Wenn du aus irgendwelchen Gründen willst, dass sie verändert oder gelöscht wird, kontaktiere bitte den Urheber der Aktivität oder das Controlling.'
                    ) ?>
                </p>
                <?php
                $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag bearbeiten/löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                ?>
                <a class="btn text-danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Änderung&body=<?= $body ?>">
                    <i class="ph ph-regular ph-envelope" aria-hidden="true"></i>
                    <?= lang('Contact controlling', 'Controlling kontaktieren') ?>
                </a>
                <?php if (isset($doc['created_by'])) { ?>

                    <a class="btn text-danger" href="mailto:<?= $doc['created_by'] ?>@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf 'Änderung'&body=<?= $body ?>">
                        <i class="ph ph-regular ph-envelope" aria-hidden="true"></i>
                        <?= lang('Contact creator', 'Urheber kontaktieren') ?>
                    </a>
                <?php } ?>

            </div>
        <?php elseif ($locked) : ?>
            <div class="alert alert-danger mt-20 py-20">

                <p class="mt-0">
                    <?= lang(
                        'This activity was locked because it was already used by Controlling in a report. Due to the documentation and verification obligation, activities may not be easily changed or deleted after the report. However, if a change is necessary, please contact the responsible persons:',
                        'Diese Aktivität wurde gesperrt, da sie bereits vom Controlling in einem Report verwendet wurde. Wegen der Dokumentations- und Nachweispflicht dürfen Aktivitäten nach dem Report nicht mehr so einfach verändert oder gelöscht werden. Sollte dennoch eine Änderung notwenig sein, meldet euch bitte bei den Verantwortlichen:'
                    ) ?>
                </p>
                <?php
                $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag bearbeiten/löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                ?>
                <a class="btn text-danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Änderung&body=<?= $body ?>">
                    <i class="ph ph-regular ph-envelope" aria-hidden="true"></i>
                    <?= lang('Contact controlling', 'Controlling kontaktieren') ?>
                </a>

            </div>
        <?php else : ?>
            <div class="alert alert-danger mt-20 py-20">
                <p class="mt-0">
                    <!-- <b>Info:</b> -->
                    <!-- <?= lang(
                                'This activity is not in the current quarter, i.e. it may have already been reported. Deleting it is therefore no longer possible. But you can write to Controlling why the entry should be deleted and they will take care of it.',
                                'Aktivitäten können problemlos gelöscht werden, solange sie sich im aktuellen Quartal befinden. Am Ende des Quartals werden alle Aktivitäten reportet, danach ist löschen nicht mehr einfach möglich.'
                            ) ?> -->
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn text-danger">
                        <i class="ph ph-regular ph-trash"></i>
                        <?= lang('Delete activity', 'Lösche Aktivität') ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>


    <div class="col-lg-5">
        <?php foreach (['authors', 'editors'] as $role) { ?>
            <?php if (isset($activity[$role])) { ?>

                <h2><?= ucfirst($role) ?></h2>

                <?php
                // if (($user_activity && !$locked) || $USER['is_controlling'] || $USER['is_admin']) { 
                ?>
                <div class="btn-toolbar mb-10">
                    <?php if ($role == 'authors') {
                        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/authors" class="btn">
                                <i class="ph ph-regular ph-user-list"></i>
                                ' . lang("Edit authors", "Autorenliste bearbeiten") .
                            '</a>';
                    } else {
                        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/editors" class="btn">
                                    <i class="ph ph-regular ph-user-list"></i>
                                    ' . lang("Edit editors", "Editorenliste bearbeiten") .
                            '</a>';
                    } ?>
                </div>
                <?php
                //  } 
                ?>
                <table class="table">
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
                                $u = getUserFromId($author['user']);
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
            <?php } ?>
        <?php } ?>

    </div>
</div>


<?php if ($USER['is_admin'] || isset($_GET['verbose'])) { ?>
    <section class="section">

        <div class="content">

            <h2>
                Raw data
            </h2>

            <?= lang('Raw data as they are stored in the database - for development only.', 'Die Rohdaten, wie sie in der Datenbank gespeichert werden - nur für die Entwicklung.') ?>

            <?php
            dump($doc, true);
            ?>


        </div>
    </section>
<?php } ?>