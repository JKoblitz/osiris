<style>
    .lom {
        font-weight: bold;
        text-align: right !important;
    }

    .lom::before {
        content: '\f85c';
        font-family: "Font Awesome 6 Pro";
        font-weight: 900;
        color: var(--signal-color);
        margin-right: .5rem;
    }
</style>

<?php

$currentuser = $user == $_SESSION['username'];
$q = SELECTEDYEAR . "Q" . SELECTEDQUARTER;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

if ($currentuser) {

    $approved = isset($USER['approved']) && in_array($q, $USER['approved']->bsonSerialize());
    $approval_needed = array();
?>



    <h1>
        <?= lang('Welcome', 'Willkommen') ?>, <?= $name ?>
    </h1>

    <p class="row-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p>
    <?php if ($approved) { ?>
        <a href="#" class="btn disabled">
            <i class="fas fa-check mr-5"></i>
            <?= lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') ?>
        </a>
    <?php } else { ?>
        <a class="btn btn-success" href="#approve">
            <i class="fas fa-question mr-5"></i>
            <?= lang('Approve current quarter', 'Bestätige aktuelles Quartal') ?>
        </a>
    <?php } ?>


<?php } else { ?>
    <h1><?= $name ?></h1>
<?php } ?>

<div class="lead my-20">
    <?= lang('In ' . SELECTEDYEAR . ' achieved LOM points: ', 'Im Jahr ' . SELECTEDYEAR . ' erreichte LOM-Punkte: ') ?>
    <i class="fad fa-lg fa-coin text-signal"></i>
    <b id="lom-points"></b>
</div>



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
            $options = ['sort' => ["year" => -1, "month" => -1]];
            $cursor = $collection->find(
                ['$or' => [['authors.user' => $user], ['editors.user' => $user]], 'year' => SELECTEDYEAR], $options);
            // dump($cursor);
            foreach ($cursor as $document) {
                $l = $LOM->publication($document);
                $_lom += $l['lom'];

                $a = is_approved($document, $user);
                if (!$a) {
                    $approval_needed[] = array(
                        'type' => 'publication',
                        'id' => $document['_id'],
                        'title' => $document['title']
                    );
                }

                $q = getQuarter($document['month']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>";
                echo format_publication($document);
                if (!$a) { ?>
                    <div class='alert alert-danger'>
                        <?= lang('Are you an author of this activity?', 'Bist du Autor dieser Aktivität?') ?>
                        <br>
                        <button class="btn btn-sm">
                            <i class="fas fa-check"></i>
                            <?=lang('Yes, this is me.', 'Ja, das bin ich.')?>
                        </button>
                        <button class="btn btn-sm">
                            <i class="fas fa-handshake-slash"></i>
                            <?=lang('Yes, but I was not affiliated to the '.AFFILIATION, 'Ja, aber meine Affiliation ist nicht die '.AFFILIATION)?>
                        </button>
                        <button class="btn btn-sm">
                            <i class="fas fa-xmark"></i>
                            <?=lang('No, this is not me.', 'Nein, das bin ich nicht.')?>
                        </button>
                    </div>

                <?php }
                echo "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/publication" class="btn text-primary">
                <i class="far fa-book-bookmark mr-5"></i> <?= lang('My publications', 'Meine Publikationen') ?>
            </a>
        <?php } ?>
        <a href="<?= ROOTPATH ?>/publication/add" class="btn"><i class="fas fa-plus"></i></a>
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
                $l = $LOM->poster($document);
                $_lom += $l['lom'];
                $q = getQuarter($document['start']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>" . format_poster($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/poster" class="btn text-danger">
                <i class="far fa-presentation-screen mr-5"></i> <?= lang('My posters', 'Meine Poster') ?>
            </a>
        <?php } ?>
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
                $l = $LOM->lecture($document);
                $_lom += $l['lom'];
                $q = getQuarter($document['start']);
                $in_quarter = $q == SELECTEDQUARTER;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td class='quarter'>Q$q</td>
                    <td>" . format_lecture($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/lecture" class="btn text-signal">
                <i class="far fa-keynote mr-5"></i> <?= lang('My lectures', 'Meine Vorträge') ?>
            </a>
        <?php } ?>
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
                $l = $LOM->review($document);
                $_lom += $l['lom'];
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_editorial($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
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
                $l = $LOM->review($document);
                $_lom += $l['lom'];
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_review($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/review" class="btn text-success">
                <i class="far fa-book-open-cover mr-5"></i> <?= lang('My reviews &amp; editorials', 'Meine Reviews &amp; Editorials') ?>
            </a>
        <?php } ?>

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
        <h4 class="title"><i class="far fa-icons mr-5"></i> <?= lang('Other activities', 'Sonstige Aktivitäten') ?></h4>
    </div>
    <table class="table table-simple">
        <tbody>
            <?php
            $collection = $osiris->miscs;
            $cursor = $collection->find([
                'authors.user' => $user,
                "dates.start.year" => array('$lte' => SELECTEDYEAR),
                '$or' => array(
                    ['dates.end.year' => array('$gte' => SELECTEDYEAR)],
                    ['dates.end' => null]
                )
            ]);
            // dump($cursor);
            foreach ($cursor as $document) {
                $l = $LOM->misc($document);
                $_lom += $l['lom'];
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_misc($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>

    </table>


    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/misc" class="btn text-muted">
                <i class="far fa-icons mr-5"></i> <?= lang('My other research activities', 'Meine anderen Forschungsaktivitäten') ?>
            </a>
        <?php } ?>
        <a href="#add-misc" class="btn" role="button"><i class="fas fa-plus"></i></a>
    </div>
</div>
<div class="modal" id="add-misc" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Add activity', 'Füge Aktivität hinzu') ?></h5>
            <?php
            include BASEPATH . "/components/form-misc.php"
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
                $l = $LOM->teaching($document);
                $_lom += $l['lom'];
                $in_quarter = true;
                echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "'>
                    <td>" . format_teaching($document) . "</td>
                    <td class='lom' >$l[lom]</td>
                    <!-- data-toggle='tooltip' data-title='$l[points]'-->
                </tr>";
            }
            ?>
        </tbody>

    </table>


    <div class="content mt-0">
        <?php if ($currentuser) { ?>
            <a href="<?= ROOTPATH ?>/teaching" class="btn text-muted">
                <i class="far fa-people mr-5"></i> <?= lang('My teaching &amp; guests', 'Meine Abschlussarbeiten und Gäste') ?>
            </a>
        <?php } ?>
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


<?php if ($currentuser) { ?>


    <div class="modal modal-lg" id="approve" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-400 mw-full">
                <a href="#" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h5 class="modal-title"><?= lang('Approve', 'Bestätigen') ?></h5>

                <?php
                dump($approval_needed);
                ?>


                <!-- <p>
                    Hier sollen Wissenschaftler die Möglichkeit bekommen, das aktuelle Quartal noch einmal zu reviewen.
                    Sie werden auf Fehler oder mögliche Probleme hingewiesen und können anschließend bestätigen, dass alles korrekt ist.
                    Dies wird dann als Haken in der Übersicht des Controllings hinzugefügt.
                </p> -->

                <?php if ($approved) { ?>
                    <?= lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') ?>
                <?php } else { ?>
                    <form action="<?= ROOTPATH ?>/approve" method="post">
                        <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                        <button class="btn"><?= lang('Approve') ?></button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>


<script>
    $('#lom-points').html('<?= $_lom ?>')
</script>