<?php

$Format = new Format(true);
$doc = json_decode(json_encode($activity->getArrayCopy()), true);

$user_activity = isUserActivity($doc, $user);
?>

<style>
    .key {
        min-width: 10rem;
        text-align: left;
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
</style>

<div class="content">

    <h2><?= lang('Formatted entry', 'Formatierter Eintrag') ?></h2>

    <p>
        <?= $Format->format($doc) ?>
    </p>
</div>

<div class="row row-eq-spacing-lg">
    <div class="col-lg-7">

        <h2>Details</h2>

        <div class="btn-toolbar mb-10">
            <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn mr-10">
                <i class="icon-activity-pen"></i>
                <?= lang('Edit activity', 'Aktivität bearbeiten') ?>
            </a>

            <?php if (in_array($doc['type'], ['poster', 'lecture', 'review', 'misc', 'students'])) {
                echo '<a href="' . ROOTPATH . '/activities/copy/' . $id . '" class="btn mr-10">
        <i class="fas fa-book-copy"></i>
        ' . lang("Add a copy", "Eine Kopie anlegen") .
                    '</a>';
            }
            ?>

            <?php if (in_array($doc['type'], ['publication', 'poster', 'lecture', 'misc'])) { ?>
                <a href="<?= ROOTPATH ?>/activities/files/<?= $id ?>" class="btn mr-10">
                    <i class="fas fa-upload"></i>
                    <?= lang('Upload files', 'Dateien hochladen') ?>
                </a>
            <?php } ?>
        </div>

        <div class="box mt-0">
            <div class="content">
                <?php if (isset($doc['title'])) : ?>
                    <p class="lead">
                        <span class="mr-10"><?= activity_icon($doc) ?></span>
                        <?= $doc['title'] ?>
                    </p>
                <?php elseif ($doc['type'] == "review") : ?>
                    <p class="lead">
                        <span class="mr-10"><?= activity_icon($doc) ?></span>
                        <?php
                        switch (strtolower($doc['role'] ?? '')) {
                            case 'editorial':
                            case 'editor':
                                echo "Editorial board";
                                break;
                            case 'grant-rev':
                                echo "Grant proposal";
                                break;
                            case 'thesis-rev':
                                echo "Thesis review";
                                break;
                            default:
                                echo "Journal Review";
                                break;
                        }
                        ?>
                    </p>
                <?php endif; ?>

                <table class="w-full" id="detail-table">

                    <?php if (isset($doc['journal'])) : ?>
                        <tr>
                            <th class="key">Journal:</th>
                            <td>
                                <em>
                                    <?php if (isset($doc['journal_id'])) { ?>
                                        <a href="<?= ROOTPATH ?>/journal/view/<?= $doc['journal_id'] ?>">
                                        <?php } else { ?>
                                            <a href="<?= ROOTPATH ?>/journal/browse?q=<?= $doc['journal'] ?>">
                                            <?php } ?>
                                            <?= $doc['journal'] ?>
                                            </a>
                                </em>
                                <span class="text-muted">
                                    <?php
                                    $issn = "";
                                    if (!empty($doc['issn'] ?? null)) {
                                        $issn = $doc['issn'];
                                        try {
                                            $issn = $issn->bsonSerialize();
                                        } catch (\Throwable $th) {
                                        }
                                        if (is_array($issn)) $issn = implode(', ', $issn);
                                        echo " (ISSN: $issn)";
                                    } ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['magazine'])) : ?>
                        <tr>
                            <th class="key">Journal:</th>
                            <td>
                                <em><?= $doc['magazine'] ?></em>
                            </td>
                        </tr>
                    <?php endif; ?>



                    <?php if ($doc['type'] == 'lecture' && isset($doc['lecture_type'])) : ?>
                        <tr>
                            <th class="key"><?= lang('Type', 'Art') ?>:</th>
                            <td>
                                <?= $doc['lecture_type'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if ($doc['type'] == 'review' && isset($doc['editor_type'])) : ?>
                        <tr>
                            <th class="key"><?= lang('Details', 'Details') ?>:</th>
                            <td>
                                <?= $doc['editor_type'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if (isset($doc['start'])) : ?>
                        <tr>
                            <th class="key"><?= lang('Time frame', 'Zeitraum') ?>:</th>
                            <td>
                                <?= format_date($doc['start']) ?>
                                <?php if (!empty($doc['end'] ?? null)) {
                                    echo '-' . format_date($doc['end']);
                                } ?>

                            </td>
                        </tr>
                    <?php elseif (isset($doc['year'])) : ?>
                        <tr>
                            <th class="key"><?= lang('Date', 'Datum') ?>:</th>
                            <td>
                                <?= format_date($doc) ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if (isset($doc['doi']) && !empty($doc['doi'])) : ?>
                        <tr>
                            <th class="key">DOI:</th>
                            <td>
                                <a target='_blank' href='https://doi.org/<?= $doc['doi'] ?>'><?= $doc['doi'] ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['pubmed'])) : ?>
                        <tr>
                            <th class="key">PubMed:</th>
                            <td>
                                <a target='_blank' href='https://pubmed.ncbi.nlm.nih.gov/<?= $doc['pubmed'] ?>'><?= $doc['pubmed'] ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['link'])) : ?>
                        <tr>
                            <th class="key">Link:</th>
                            <td>
                                <a target='_blank' href='<?= $doc['link'] ?>'><?= $doc['link'] ?></a>
                            </td>
                        </tr>
                    <?php endif; ?>




                    <?php if ($doc['type'] == 'publication') : ?>

                        <?php if ($doc['pubtype'] == 'article') : ?>
                            <tr>
                                <th class="key">Issue:</th>
                                <td>
                                    <?= $doc['issue'] ?? '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="key">Volume:</th>
                                <td>
                                    <?= $doc['volume'] ?? '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="key">Pages:</th>
                                <td>
                                    <?= $doc['pages'] ?? '' ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="key">Impact factor:</th>
                                <td>
                                    <?= $doc['impact'] ?? '' ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if (isset($doc['book'])) : ?>
                            <tr>
                                <th class="key">Book title:</th>
                                <td>
                                    <?= $doc['book'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($doc['publisher'])) : ?>
                            <tr>
                                <th class="key">Publisher:</th>
                                <td>
                                    <?= $doc['publisher'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($doc['city'])) : ?>
                            <tr>
                                <th class="key">Location:</th>
                                <td>
                                    <?= $doc['city'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($doc['edition'])) : ?>
                            <tr>
                                <th class="key">Edition:</th>
                                <td>
                                    <?= $doc['edition'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($doc['isbn'])) : ?>
                            <tr>
                                <th class="key">ISBN:</th>
                                <td>
                                    <?= $doc['isbn'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($doc['doc_type'])) : ?>
                            <tr>
                                <th class="key">Document type:</th>
                                <td>
                                    <?= $doc['doc_type'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>






                    <?php if ($doc['type'] == 'misc') : ?>
                        <tr>
                            <th class="key">Iteration:</th>
                            <td>
                                <?= $doc['iteration'] ?? '' ?>
                            </td>
                        </tr>
                    <?php endif; ?>







                    <?php if ($doc['type'] == 'software') : ?>
                        <tr>
                            <th class="key">Software Type:</th>
                            <td>
                                <?= $doc['software_type'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Publication venue:</th>
                            <td>
                                <?= $doc['software_venue'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Version:</th>
                            <td>
                                <?= $doc['version'] ?? '' ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if ($doc['type'] == 'teaching') : ?>
                        <tr>
                            <th class="key">Einrichtung:</th>
                            <td>
                                <?= $doc['affiliation'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">SWS (Semesterwochenstunden):</th>
                            <td>
                                <?= $doc['sws'] ?? '' ?>
                            </td>
                        </tr>
                    <?php endif; ?>


                    <?php if ($doc['type'] == 'students') : ?>
                        <tr>
                            <th class="key">Category:</th>
                            <td>
                                <?= $doc['category'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Status:</th>
                            <td>
                                <?= $doc['status'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Name:</th>
                            <td>
                                <?= $doc['name'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Academic Title:</th>
                            <td>
                                <?= $doc['academic_title'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Affiliation:</th>
                            <td>
                                <?= $doc['affiliation'] ?? '' ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="key">Details:</th>
                            <td>
                                <?= $doc['details'] ?? '' ?>
                            </td>
                        </tr>

                    <?php endif; ?>



                    <?php if (isset($doc['conference'])) : ?>
                        <tr>
                            <th class="key">Conference:</th>
                            <td>
                                <?= $doc['conference'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['location'])) : ?>
                        <tr>
                            <th class="key">Location:</th>
                            <td>
                                <?= $doc['location'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>



                    <?php if (isset($doc['open_access'])) : ?>
                        <tr>
                            <th class="key">Open access:</th>
                            <td>
                                <?php if ($doc['open_access']) : ?>
                                    <i class="icon-open-access text-success" title="Open Access"></i>
                                    <?= lang('Yes', 'Ja') ?>
                                    <!-- icon-open-access -->
                                <?php else : ?>
                                    <i class="icon-closed-access text-danger" title="Closed Access"></i>
                                    <?= lang('No', 'Nein') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['epub'])) : ?>
                        <tr>
                            <th class="key">Epub ahead of print:</th>
                            <td>
                                <?= bool_icon($doc['epub']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['correction'])) : ?>
                        <tr>
                            <th class="key">Correction:</th>
                            <td>
                                <?= bool_icon($doc['correction']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($doc['invited_lecture'])) : ?>
                        <tr>
                            <th class="key">Invited Lecture:</th>
                            <td>
                                <?= bool_icon($doc['invited_lecture']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>




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
                                    <i class="fas fa-upload"></i>
                                </a>
                            </td>
                        </tr>

                    <?php endif; ?>


                    <?php if (($user_activity || $USER['is_controlling'] || $USER['is_controlling']) && isset($doc['comment'])) : ?>
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

        $in_quarter = inCurrentQuarter($doc['year'], $doc['month']);
        $is_controlling = $USER['is_controlling'] ?? false;
        if ($is_controlling) :
        ?>
            <div class="alert alert-danger mt-20 py-20">
                <p class="mt-0">
                    <?= lang('Controlling staff can delete any time:', 'Controlling-Mitarbeiter können jederzeit löschen:') ?>
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn text-danger">
                        <i class="icon-activity-slash"></i>
                        <?= lang('Delete activity', 'Lösche Aktivität') ?>
                    </button>
                </form>
            </div>
        <?php elseif (!$in_quarter) : ?>
            <div class="alert alert-danger mt-20 py-20">

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
                    <i class="far fa-envelope" aria-hidden="true"></i>
                    <?= lang('Delete activity', 'Löschen beantragen') ?>
                </a>
            </div>
        <?php elseif (!$user_activity) : ?>
            <div class="alert alert-danger mt-20 py-20">

                <p class="mt-0">
                    <?= lang(
                        'This is not your own activity. If for any reason you want it deleted, please contact the creator of the activity or the controlling.',
                        'Dies ist nicht deine Aktivität. Wenn du aus irgendwelchen Gründen willst, dass sie gelöscht wird, kontaktiere bitte den Urheber der Aktivität oder das Controlling.'
                    ) ?>
                </p>
                <?php
                $body = $USER['displayname'] . " möchte folgenden OSIRIS-Eintrag löschen: $name%0D%0A%0D%0ABegründung/Reason:%0D%0A%0D%0Ahttp://osiris.int.dsmz.de/activities/view/$id";
                ?>
                <a class="btn text-danger" href="mailto:dominic.koblitz@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Löschung&body=<?= $body ?>">
                    <i class="far fa-envelope" aria-hidden="true"></i>
                    <?= lang('Contact controlling', 'Controlling kontaktieren') ?>
                </a>
                <?php if (isset($doc['created_by'])) { ?>

                    <a class="btn text-danger" href="mailto:<?= $doc['created_by'] ?>@dsmz.de?cc=julia.koblitz@dsmz.de&subject=[OSIRIS] Antrag auf Löschung&body=<?= $body ?>">
                        <i class="far fa-envelope" aria-hidden="true"></i>
                        <?= lang('Contact creator', 'Urheber kontaktieren') ?>
                    </a>
                <?php } ?>

            </div>
        <?php else : ?>
            <div class="alert alert-danger mt-20 py-20">
                <p class="mt-0">
                    <b>Info:</b>
                    <?= lang(
                        'This activity is not in the current quarter, i.e. it may have already been reported. Deleting it is therefore no longer possible. But you can write to Controlling why the entry should be deleted and they will take care of it.',
                        'Aktivitäten können problemlos gelöscht werden, solange sie sich im aktuellen Quartal befinden. Am Ende des Quartals werden alle Aktivitäten reportet, danach ist löschen nicht mehr einfach möglich.'
                    ) ?>
                </p>
                <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post" class="d-inline-block ml-auto">
                    <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
                    <button type="submit" class="btn text-danger">
                        <i class="icon-activity-slash"></i>
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
                <div class="btn-toolbar mb-10">
                    <?php if ($role == 'authors') {
                        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/authors" class="btn">
                                <i class="fa-regular fa-user-pen"></i>
                                ' . lang("Edit authors", "Autorenliste bearbeiten") .
                            '</a>';
                    } else {
                        echo '<a href="' . ROOTPATH . '/activities/edit/' . $id . '/editors" class="btn">
                                    <i class="fa-regular fa-user-pen"></i>
                                    ' . lang("Edit editors", "Editorenliste bearbeiten") .
                            '</a>';
                    } ?>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Last name</th>
                            <th>First name</th>
                            <?php if ($doc['type'] == 'publication' && $role == 'authors') : ?>
                                <th>Position</th>
                            <?php endif; ?>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody id="<?= $role ?>">
                        <?php foreach ($activity[$role] as $i => $author) { ?>
                            <tr>
                                <td class="<?= (($author['aoi'] ?? 0) == '1' ? 'font-weight-bold' : '') ?>">
                                    <?= $author['last'] ?? '' ?>
                                </td>
                                <td>
                                    <?= $author['first'] ?? '' ?>
                                </td>
                                <?php if ($doc['type'] == 'publication' && $role == 'authors') : ?>
                                    <td>
                                        <?= $author['position'] ?? '' ?>
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


<?php if ($USER['is_admin']) { ?>
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