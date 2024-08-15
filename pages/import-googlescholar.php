<?php
$Format = new Document();
include(BASEPATH . '/php/GoogleScholar.php');
$google = new GoogleScholar($scholar_id);

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
                            if ($sim >= 98) $alert = 'duplicate';
                            else $alert = 'signal';
                        ?>

                            <div class="alert <?= $alert ?>">
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

                        <?php if ($sim < 98) { ?>
                            <button class="btn mt-5" onclick='addGoogleActivity("<?= $scholar_id ?>", "<?= $pub_id ?>")'>
                                <i class="ph ph-plus"></i>
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
                    btn.addClass('btn sm')
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
                    var alert = $('<div class="alert success">')
                    alert.append('<p class="my-0">' + lang('Publication successfully added. Please review the result carefully.', 'Publikation wurde hinzugefügt. Bitte überprüfe das Ergebnis sorgfältig!') + '</p>')
                    alert.append(response.formatted)
                    alert.append('<br><a class="btn mt-5" href="' + ROOTPATH + '/activities/view/' + response.id + '" target="_blank">Review</a>')
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