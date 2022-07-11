
<?php if ($user == $_SESSION['username']) { ?>

    <h2><?= lang('Welcome', 'Willkommen') ?>, <?= $name ?></h2>

    <p class="row-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p>

    <button class="btn btn-success">
        <i class="fas fa-check mr-5"></i>
        <?= lang('Approve current quarter', 'Bestätige aktuelles Quartal') ?>
    </button>


<?php } else { ?>
    <h2><?= $name ?></h2>
<?php } ?>


<div class="box box-primary">
    <div class="content">

        <h3 class="title"><i class="far fa-books mr-5"></i> <?= lang('Publications', 'Publikationen') ?></h3>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $publication = new Publication;

            $stmt = $db->prepare(
                "SELECT publication_id, q_id FROM `authors` 
                    INNER JOIN publication USING (publication_id) 
                    WHERE user LIKE ? ORDER BY q_id DESC"
            );
            $stmt->execute([$user]);
            $pubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($pubs)) {
                echo "<div class='content'>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</div>";
            } else foreach ($pubs as $pub) {
                $selected = ($pub['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td>
                    <td>
                        <?php $publication->print($pub['publication_id']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


</div>


<div class="box box-signal">
    <div class="content">

        <h3 class="title"><i class="far fa-presentation-screen mr-5"></i> Poster</h3>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $poster = new Poster;
            $stmt = $db->prepare(
                "SELECT poster_id, q_id FROM `authors` 
                    INNER JOIN poster USING (poster_id) 
                    WHERE user LIKE ? ORDER BY q_id DESC"
            );
            $stmt->execute([$user]);
            $poster = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($poster)) {
                echo "<div class='content'>" . lang('No posters found.', 'Keine Publikationen gefunden.') . "</div>";
            } else foreach ($poster as $pub) {
                $selected = ($pub['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td>
                    <td>
                        <?php $poster->print($pub['poster_id']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<div class="box box-success">
    <div class="content">

        <h3 class="title"><i class="far fa-book mr-5"></i> <?= lang('Reviews &amp; Editorial boards') ?></h3>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $stmt = $db->prepare(
                "SELECT * FROM `review`
                    LEFT JOIN journal USING (journal_id)
                    WHERE user LIKE ? ORDER BY q_id DESC, `type` DESC"
            );
            $stmt->execute([$user]);
            $review = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($review)) {
                echo "<div class='content'>" . lang('No review found.', 'Keine Aktivitäten gefunden.') . "</div>";
            } else foreach ($review as $pub) {
                $selected = ($pub['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td>
                    <td>
                        <?= $pub['type'] == 'editor' ? 'Member of the Editorial Board of ' : 'Reviewer for ' ?>
                        <?= $pub['journal'] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


</div>