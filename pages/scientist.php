

<?php if ($user == $_SESSION['username']) { ?>


    <div class="modal modal-lg" id="approve" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-400 mw-full">
                <a href="#" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h5 class="modal-title"><?= lang('Approve', 'Bestätigen') ?></h5>
                <p>
                    Hier sollen Wissenschaftler die Möglichkeit bekommen, das aktuelle Quartal noch einmal zu reviewen.
                    Sie werden auf Fehler oder mögliche Probleme hingewiesen und können anschließend bestätigen, dass alles korrekt ist.
                    Dies wird dann als Haken in der Übersicht des Controllings hinzugefügt.
                </p>
                <!-- 
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">User name: </label>
                        <input class="form-control" id="username" type="text" name="username" placeholder="abc21" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password: </label>
                        <input class="form-control" id="password" type="password" name="password" placeholder="your windows password" required />
                    </div>
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit" />
                </form> -->
            </div>
        </div>
    </div>


    <h1><?= lang('Welcome', 'Willkommen') ?>, <?= $name ?></h1>

    <p class="row-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p>

    <a class="btn btn-success" href="#approve">
        <i class="fas fa-check mr-5"></i>
        <?= lang('Approve current quarter', 'Bestätige aktuelles Quartal') ?>
    </a>


<?php } else { ?>
    <h1><?= $name ?></h1>
<?php } ?>

<h3>
    <?php
    echo lang('Research activities in ', 'Forschungsaktivitäten in '). SELECTEDYEAR;
    ?>
</h3>

<div class="box box-primary">
    <div class="content">

        <h4 class="title"><i class="far fa-book-bookmark mr-5"></i> <?= lang('Publications', 'Publikationen') ?></h4>

        
    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $activity = new Publication;

            $stmt = $db->prepare(
                "SELECT publication_id, quarter.* FROM `authors` 
                    INNER JOIN publication USING (publication_id) 
                    LEFT JOIN quarter USING (q_id)
                    WHERE user LIKE ? AND quarter.year = ?"
            );
            $stmt->execute([$user, SELECTEDYEAR]);
            $pubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($pubs)) {
                echo "<div class='content'>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</div>";
            } else foreach ($pubs as $pub) {
                $selected = ($pub['quarter'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        Q<?= $pub['quarter'] ?>
                    </td>
                    <td>
                        <?php $activity->print($pub['publication_id']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php $activity->printMsg(); ?>
        <a href="<?= ROOTPATH ?>/my-publication" class="btn text-primary">
            <i class="far fa-book-bookmark mr-5"></i> <?= lang('My publications', 'Meine Publikationen') ?>
        </a>

    </div>

</div>


<div class="box box-danger">
    <div class="content">

        <h4 class="title"><i class="far fa-presentation-screen mr-5"></i> Poster</h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $activity = new Poster;
            $stmt = $db->prepare(
                "SELECT poster_id, q_id FROM `authors` 
                    INNER JOIN poster USING (poster_id) 
                    LEFT JOIN quarter USING (q_id)
                    WHERE user LIKE ?  AND quarter.year = ?"
            );
            $stmt->execute([$user, SELECTEDYEAR]);
            $poster = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($poster)) {
                echo "<div class='content'>" . lang('No posters found.', 'Keine Publikationen gefunden.') . "</div>";
            } else foreach ($poster as $pub) {
                $selected = true; //($pub['q_id'] == $quarter);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <!-- <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td> -->
                    <td>
                        <?php $activity->print($pub['poster_id']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php $activity->printMsg(); ?>
        <a href="<?= ROOTPATH ?>/my-poster" class="btn text-danger">
            <i class="far fa-presentation-screen mr-5"></i> <?= lang('My posters', 'Meine Poster') ?>
        </a>

    </div>
</div>


<div class="box box-signal">
    <div class="content">

        <h4 class="title"><i class="far fa-keynote mr-5"></i> <?= lang('Lectures', 'Vorträge') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $activity = new Lecture;
            $stmt = $db->prepare(
                "SELECT lecture_id, q_id FROM `authors` 
                    INNER JOIN lecture USING (lecture_id) 
                    LEFT JOIN quarter USING (q_id)
                    WHERE user LIKE ? AND quarter.year = ?"
            );
            $stmt->execute([$user, SELECTEDYEAR]);
            $lecture = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($lecture)) {
                echo "<div class='content'>" . lang('No lectures found.', 'Keine Vorträge gefunden.') . "</div>";
            } else foreach ($lecture as $pub) {
                $selected = true; //($pub['q_id'] == $quarter);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <!-- <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td> -->
                    <td>
                        <?php $activity->print($pub['lecture_id']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php $activity->printMsg(); ?>
        <a href="<?= ROOTPATH ?>/my-lecture" class="btn text-signal">
            <i class="far fa-keynote mr-5"></i> <?= lang('My lectures', 'Meine Vorträge') ?>
        </a>

    </div>
</div>

<div class="box box-success">
    <div class="content">

        <h4 class="title"><i class="far fa-book-open-cover mr-5"></i> <?= lang('Reviews &amp; Editorial boards') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $stmt = $db->prepare(
                "SELECT * FROM `review`
                    LEFT JOIN journal USING (journal_id)
                    LEFT JOIN quarter USING (q_id)
                    WHERE user LIKE ?  AND quarter.year = ? 
                    ORDER BY `type` DESC"
            );
            $stmt->execute([$user, SELECTEDYEAR]);
            $review = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($review)) {
                echo "<div class='content'>" . lang('No review found.', 'Keine Reviewer-Aktivität gefunden.') . "</div>";
            } else foreach ($review as $pub) {
                $selected = true; //($pub['q_id'] == $quarter);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <!-- <td class="quarter">
                        <?= str_replace('Q', ' Q', $pub['q_id']) ?>
                    </td> -->
                    <td>
                        <b><?php if (isset($userArr)) {
                                echo Database::abbreviateAuthor($userArr['last_name'], $userArr['first_name']);
                            } else {
                                echo $userClass->name('abbreviated');
                            } ?></b>


                        <?= $pub['type'] == 'editor' ? 'Member of the Editorial Board of ' : 'Reviewer for ' ?>
                        <?= $pub['journal'] ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-review" class="btn text-success">
            <i class="far fa-book-open-cover mr-5"></i> <?= lang('My reviews &amp; editorials', 'Meine Reviews &amp; Editorials') ?>
        </a>

    </div>



</div>
<div class="box box-muted">
    <div class="content">

        <h4 class="title"><i class="far fa-people mr-5"></i> <?= lang('Teaching &amp; Guests', 'Abschlussarbeiten und Gäste') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            include_once BASEPATH . "/php/Teaching.php";
            $stmt = $db->prepare(
                "SELECT teaching_id
                FROM `authors`
                INNER JOIN teaching USING (teaching_id) 
                WHERE `user` LIKE ? AND YEAR(date_start) <= ? and YEAR(date_end) >= ?;
                ORDER BY date_start
                "
            );
            $stmt->execute([$user, SELECTEDYEAR, SELECTEDYEAR]);
            $teaching = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (empty($teaching)) {
                echo "<div class='content'>" . lang('No teachings found.', 'Keine Publikationen gefunden.') . "</div>";
            } else foreach ($teaching as $id) {

                $activity = new Teaching($id);
                $selected = $activity->inSelectedQuarter();
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td><?= $activity->print() ?></td>
                </tr>
            <?php } ?>
        </tbody>

    </table>


    <div class="content mt-0">
        <?php $activity->printMsg(); ?>
        <a href="<?= ROOTPATH ?>/my-teaching" class="btn text-muted">
            <i class="far fa-book-open-cover mr-5"></i> <?= lang('My teaching &amp; guests', 'Meine Abschlussarbeiten und Gäste') ?>
        </a>

    </div>
</div>