<?php

// $yearstart = mongo_date(SELECTEDYEAR . "-01-01");
// $yearend = mongo_date(SELECTEDYEAR . "-12-31");

?>


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
    echo lang('Research activities in ', 'Forschungsaktivitäten in ') . SELECTEDYEAR;
    ?>
</h3>

<div class="box box-primary">
    <div class="content">
        <h4 class="title"><i class="far fa-book-bookmark mr-5"></i> <?= lang('Publications', 'Publikationen') ?></h4>
    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $collection = $osiris->publications;
            // $cursor = $collection->find(['authors.user' => $user, 'year' => SELECTEDYEAR]);
            
            $options = ['sort' => ["year" => -1, "month"=> -1]];
            $cursor = $collection->find(['$or'=> [['authors.user' => $user], ['editors.user' => $user]], 'year' => SELECTEDYEAR], $options);
            // dump($cursor);
            foreach ($cursor as $document) {
                $q = getQuarter($document['month']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>" . format_publication($document) . "</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-publication" class="btn text-primary">
            <i class="far fa-book-bookmark mr-5"></i> <?= lang('My publications', 'Meine Publikationen') ?>
            
        <a href="<?= ROOTPATH ?>/my-publication/add" class="btn"><i class="fas fa-plus"></i></a>
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
            $collection = $osiris->posters;
            $cursor = $collection->find([
                'authors.user' => $user,
                "start.year" => SELECTEDYEAR
            ]);
            // dump($cursor);
            foreach ($cursor as $document) {
                $q = getQuarter($document['start']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>" . format_poster($document) . "</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-poster" class="btn text-danger">
            <i class="far fa-presentation-screen mr-5"></i> <?= lang('My posters', 'Meine Poster') ?>
        </a>
        <a href="#add-poster" class="btn" role="button"><i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="modal" id="add-poster" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Add poster', 'Poster hinzufügen') ?></h5>
            <?php
            include BASEPATH . "/components/form-poster.php"
            ?>

        </div>
    </div>
</div>


<div class="box box-signal">
    <div class="content">

        <h4 class="title"><i class="far fa-keynote mr-5"></i> <?= lang('Lectures', 'Vorträge') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $collection = $osiris->lectures;
            $cursor = $collection->find([
                'authors.user' => $user,
                "start.year" => SELECTEDYEAR
            ]);
            // dump($cursor);
            foreach ($cursor as $document) {
                $q = getQuarter($document['start']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>" . format_lecture($document) . "</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-lecture" class="btn text-signal">
            <i class="far fa-keynote mr-5"></i> <?= lang('My lectures', 'Meine Vorträge') ?>
        </a>
        <a href="#add-lecture" class="btn" role="button"><i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="modal" id="add-lecture" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Add lecture', 'Vortrag hinzufügen') ?></h5>
            <?php
            include BASEPATH . "/components/form-lecture.php"
            ?>
        </div>
    </div>
</div>


<div class="box box-success">
    <div class="content">

        <h4 class="title"><i class="far fa-book-open-cover mr-5"></i> <?= lang('Reviews &amp; Editorial boards') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $collection = $osiris->reviews;
            // first editorials
            $filter = [
                'user' => $user,
                "role" => "Editor",
                "start.year" => array('$lte' => SELECTEDYEAR),
                '$or' => array(
                    ['end.year' => array('$gte' => SELECTEDYEAR)],
                    ['end' => null]
                )
            ];
            $cursor = $collection->find($filter);
            foreach ($cursor as $document) {
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_editorial($document) . "</td>
                </tr>";
            }

            // next reviews
            $filter = [
                'user' => $user,
                "role" => "Reviewer",
                "dates.year" => SELECTEDYEAR
            ];
            $cursor = $collection->find($filter);
            foreach ($cursor as $document) {
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_review($document) . "</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-review" class="btn text-success">
            <i class="far fa-book-open-cover mr-5"></i> <?= lang('My reviews &amp; editorials', 'Meine Reviews &amp; Editorials') ?>
        </a>

        <a href="#add-review" class="btn" role="button"><i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="modal" id="add-review" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Add Review/Editorial', 'Review/Editorial hinzufügen') ?></h5>
            <?php
            include BASEPATH . "/components/form-review.php"
            ?>

        </div>
    </div>
</div>

<div class="box box-muted">
    <div class="content">

        <h4 class="title"><i class="far fa-people mr-5"></i> <?= lang('Teaching &amp; Guests', 'Abschlussarbeiten und Gäste') ?></h4>

    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $collection = $osiris->teachings;
            $cursor = $collection->find([
                'authors.user' => $user,
                "start.year" => array('$lte' => SELECTEDYEAR),
                '$or' => array(
                    ['end.year' => array('$gte' => SELECTEDYEAR)],
                    ['end' => null]
                )
            ]);
            // dump($cursor);
            foreach ($cursor as $document) {
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_teaching($document) . "</td>
                </tr>";
            }
            ?>
        </tbody>

    </table>


    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/my-teaching" class="btn text-muted">
            <i class="far fa-book-open-cover mr-5"></i> <?= lang('My teaching &amp; guests', 'Meine Abschlussarbeiten und Gäste') ?>
        </a>
        <a href="#add-teaching" class="btn" role="button"><i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="modal" id="add-teaching" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Add teaching &amp; guests', 'Abschlussarbeiten oder Gäste hinzufügen') ?></h5>
            <?php
            include BASEPATH . "/components/form-teaching.php"
            ?>

        </div>
    </div>
</div>