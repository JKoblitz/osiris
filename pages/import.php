<?php
/**
 * Page to import activities
 * 
 * e.g. from file or Google Scholar
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /expertise
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<h1>
    <i class="ph ph-regular ph-upload text-osiris"></i>
    Import
</h1>


<?php
$Format = new Document();

if (!empty($USER['google_scholar'] ?? null)) { ?>

    <?php if (isset($_GET['googlescholar'])) { ?>
        <?php
        include(BASEPATH . '/php/GoogleScholar.php');
        $user = $_GET["googlescholar"];
        $google = new GoogleScholar($user);

        $result = $google->getAllUserEntries();

        if (empty($result) || empty($result['publications'])) { ?>
            <p class="text-danger">
                <?= lang('We could not find any articles from your Google Scholar Account. Sorry.', 'Wir haben keine Artikel auf deinem Google Scholar Account gefunden. Sorry.') ?>
            </p>
        <?php
        } else {
            include(BASEPATH . '/php/Levenshtein.php');
            $levenshtein = new Levenshtein($osiris);

            $pubs = array();
            foreach ($result['publications'] as $i => $pub) {
                $l = $levenshtein->findDuplicate($pub['title']);
                $id = $l[0];
                $sim = round($l[2], 1);
                if ($sim < 50) $sim = 0;
                $years_ago = 100;
                if (is_numeric($pub['year'])) $years_ago = CURRENTYEAR - intval($pub['year']);
                $pubs[] = [$sim, $years_ago, $id, $pub];
            }

            sort($pubs);
        ?>
            <p class="text-success">
                <?= lang('We found the following articles from your Google Scholar Account:', 'Wir haben die folgenden Artikel auf deinem Google Scholar Account gefunden:') ?>
            </p>

            <table class="table">
                <tbody>
                    <?php foreach ($pubs as $i => $array) {
                        $sim = $array[0];
                        $id = $array[2];
                        $pub = $array[3];
                        $pub_id = $pub['link'];
                    ?>

                        <tr class="<?php if ($sim >= 98) echo "row-muted"; ?>" id="<?= $pub_id ?>">
                            <td>
                                <a class="title colorless" href="<?= $google->googleDocLink($pub_id) ?>" target="_blank">
                                    <?= $pub['title'] ?>
                                </a>
                                <small class="text-muted d-block">
                                    <?= $pub['authors'] ?>
                                </small>
                                <small class="text-muted d-block mb-10">
                                    <?= $pub['venue'] ?>
                                </small>

                                <?php if (!empty($id) && $sim > 50) {
                                    $activity = $DB->getActivity($id);

                                    $Format->setDocument($activity);
                                    $dupl = $Format->formatShort();
                                    if ($sim >= 98) $alert = 'danger';
                                    else $alert = 'signal';
                                ?>

                                    <div class="alert alert-<?= $alert ?>">
                                        <p class="mt-0">
                                            <?php if ($sim >= 98) { ?>
                                                <?= lang('This is a 100% duplicate of the follwing publication:', 'Dies ist ein 100%iges Duplikat der folgenden Publikation:') ?>
                                            <?php } else { ?>
                                                <?= lang('This might be a duplicate of the follwing publication', 'Dies könnte ein Duplikat der folgenden Publikation sein') ?>
                                                (<b><?= $sim ?>&nbsp;%</b>):
                                            <?php } ?>
                                        </p>
                                        <?= $dupl ?>
                                    </div>
                                <?php } ?>

                                <?php if ($sim >= 98) { ?>
                                    <!-- <button class="btn" disabled>
                                    <i class="ph ph-regular ph-plus"></i>
                                    <?= lang('Adding duplicates is impossible.', 'de') ?>
                                </button> -->
                                <?php } else { ?>
                                    <button class="btn mt-5" onclick='addGoogleActivity("<?= $user ?>", "<?= $pub_id ?>")'>
                                        <i class="ph ph-regular ph-plus"></i>
                                        <?= lang('Add to database', 'Zur DB hinzufügen') ?>
                                    </button>
                                <?php } ?>

                            </td>

                        </tr>

                    <?php
                    }
                    ?>

                </tbody>
            </table>
        <?php
        }
        ?>

        <script>
            function googleScholar(user) {
                $('.loader').addClass('show')
                $.ajax({
                    type: "GET",
                    data: {
                        user: user
                    },
                    dataType: "json",
                    url: ROOTPATH + '/api/google',
                    success: function(response) {
                        console.log(response);
                        $('.loader').removeClass('show')

                        var table = $('#result');

                        response.publications.forEach(pub => {
                            var tr = $('<tr id="' + pub.link + '">')
                            var td = $('<td>')
                            td.append(pub.title)
                            td.append('<br />')
                            td.append(`<small class="text-muted d-block">${pub.authors}</small>`)
                            td.append(`<small class="text-muted d-block">${pub.venue}</small>`)
                            tr.append(td)

                            var btn = $('<button>')
                            btn.addClass('btn btn-sm')
                                .html('Import')
                            btn.on('click', googleScholarDetails(user, pub.link))
                            tr.append($('<td>').append(btn))
                            table.append(tr)
                        });

                        // $('#result').html(JSON.stringify(response))
                    },
                    error: function(response) {
                        $('.loader').removeClass('show')
                        toastError(response.responseText)
                    }
                })
            }

            function googleScholarDetails(user, doc) {
                $('.loader').addClass('show')
                $.ajax({
                    type: "GET",
                    data: {
                        user: user,
                        doc: doc
                    },
                    dataType: "json",
                    url: ROOTPATH + '/api/google',
                    success: function(response) {
                        console.log(response);
                        $('.loader').removeClass('show')


                    },
                    error: function(response) {
                        $('.loader').removeClass('show')
                        toastError(response.responseText)
                    }
                })
            }

            function addGoogleActivity(user, doc) {
                $('.loader').addClass('show')
                $.ajax({
                    type: "POST",
                    data: {
                        user: user,
                        doc: doc
                    },
                    dataType: "json",
                    url: ROOTPATH + '/import/google',
                    success: function(response) {
                        console.log(response);
                        if (response.inserted > 0) {
                            var td = $('tr#' + doc).find('td:first')
                            td.find('.alert,.btn').remove()
                            var alert = $('<div class="alert alert-success">')
                            alert.append('<p class="mt-0">' + lang('Publication successfully added. Please review the result carefully.', 'Publikation wurde hinzugefügt. Bitte überprüfe das Ergebnis sorgfältig!') + '</p>')
                            alert.append(response.formatted)
                            alert.append('<a class="btn mt-5" href="' + ROOTPATH + '/activities/view/' + response.id + '" target="_blank">Review</a>')
                            td.append(alert)
                        }
                        $('.loader').removeClass('show')
                    },
                    error: function(response) {
                        $('.loader').removeClass('show')
                        toastError(response.responseText)
                        console.log(response.responseText);
                    }
                })
            }
        </script>

    <?php } else { ?>


        <div class="box box-primary">
            <div class="content">
                <h2 class="title">Google Scholar Import</h2>
                <p>
                    <?= lang(
                        'You can import data from your Google scholar account',
                        'Du kannst Publikationen von deinem Google Scholar-Account importieren'
                    ) ?>:
                </p>
                <p class="mt-0 font-size-16 font-weight-bold">
                    Account-ID: <a href="https://scholar.google.com/citations?user=<?= $USER['google_scholar'] ?>"><?= $USER['google_scholar'] ?></a>
                </p>

                <p class="font-size-12 text-muted">
                    <?= lang('Please note that only the 100 latest entries can be imported.', 'Bitte beachte, dass nur die 100 neusten Einträge importiert werden können.') ?>
                </p>

                <a href="?googlescholar=<?= $USER['google_scholar'] ?>" class="btn">Import</a>

            </div>
        </div>

    <?php } ?>

<?php } else { ?><!-- if empty(USER[googlescholar]) -->
    <div class="box box-primary">
        <div class="content">
            <h2 class="title">Google Scholar Import</h2>
            <p>
                <?= lang(
                    'You must connect a google scholar account to your profile to use this feature.',
                    'Du musst einen Google Scholar-Account in deinem Profil hinterlegen, um dieses Feature zu nutzen.'
                ) ?>
            </p>

            <a href="<?= ROOTPATH ?>/user/edit/<?= $_SESSION['username'] ?>" class="btn"><?= lang('Update Profile', 'Profil bearbeiten') ?></a>

        </div>
    </div>

<?php } ?>





<!-- 

<div class="box box-signal">
    <div class="content">
        <h2 class="title">
            <?= lang('Import activities from file', 'Importiere Aktivitäten aus einer Datei') ?>
        </h2>
        <form action="<?= ROOTPATH ?>/import/file" method="post" enctype="multipart/form-data">
            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
            <div class="custom-file mb-20" id="file-input-div" data-visible="article,preprint,magazine,book,chapter,lecture,poster,misc-once,misc-annual">
                <input type="file" id="file-input" name="file" data-default-value="<?= lang("No file chosen", "Keine Datei ausgewählt") ?>">
                <label for="file-input"><?= lang('Upload a BibTeX file', 'Lade eine BibTeX-Datei hoch') ?></label>
                <br><small class="text-danger">Max. 16 MB.</small>
            </div>

            <div class="form-group">
                <label for="">Format:</label>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="format" id="format-bibtex" value="bibtex" checked="checked" >
                    <label for="format-bibtex">BibTeX</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="format" id="format-nbib" value="nbib">
                    <label for="format-nbib">NBIB (Pubmed)</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="format" id="format-ris" value="ris">
                    <label for="format-ris">RIS</label>
                </div>
            </div>

            <button class="btn btn-primary">
                <i class="ph ph-upload"></i>
                Upload
            </button>
        </form>
    </div>
</div>
 -->