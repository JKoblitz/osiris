<?php

$currentuser = $user == $_SESSION['username'];
$q = SELECTEDYEAR . "Q" . SELECTEDQUARTER;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

// gravatar
$email = $scientist['mail'] ;#. "@dsmz.de";
$default = ROOTPATH . "/img/person.jpg";
$size = 140;

$gravatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size;
?>



<div class="modal modal-lg" id="coins" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content w-400 mw-full">
            <a href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h2 class="modal-title">
                <i class="fad fa-lg fa-coin text-signal"></i>
                Coins
            </h2>

            <h5>
                <?= lang('What are coins?', 'Was sind Coins?') ?>
            </h5>
            <p class="">
                <?= lang(
                    "To put it simply, coins are a currency that currently doesn\'t earn you anything and doesn\'t really interest anyone. Unless you like to collect, then you\'re welcome.",
                    'Um es kurz zu sagen, Coins sind eine Währung, die dir im Moment überhaupt nichts bringt und auch eigentlich niemanden interessiert. Außer du sammelst gern, dann gern geschehen.'
                ) ?>
            </p>

            <h5>
                <?= lang('How do I get them?', 'Wie bekomme ich sie?') ?>
            </h5>

            <p>
                <?= lang(
                    'Very simple: you add scientific activities to OSIRIS. Whenever you publish, present a poster, give a talk, or complete a review, OSIRIS gives you coins for it (as long as you were an author of the DSMZ). If you want to find out how exactly the points are calculated, you hover over the coins of an activity. A tooltip will show you more information. For a publication, for example, it matters where you are in the list of authors (first/last or middle author) and how high the impact factor of the journal is.',
                    'Ganz einfach: du fügst wissenschaftliche Aktivitäten zu OSIRIS hinzu. Wann immer du publizierst, ein Poster präsentierst, einen Vortrag hältst, oder ein Review abschließt, bekommst du von OSIRIS dafür Coins (solange du dabei Autor der DSMZ warst). Wenn du herausfinden möchstest, wie genau sich die Punkte berechnen, kannst du mit dem Cursor auf die Coins einer Aktivität gehen. Ein Tooltip zeigt dir dann mehr Informationen. Bei einer Publikation spielt beispielsweise eine Rolle, an welcher Stelle du in der Autorenliste stehst (Erst/Letzt oder Mittelautor) und wie hoch der Impact Factor des Journals ist.'
                ) ?>
            </p>

        </div>
    </div>
</div>


<div class="row align-items-center">
    <div class="col flex-grow-0">
        <div class="position-relative">
        <img src="<?= $gravatar ?>" alt="">
        
        <?php if ($currentuser) { ?>
        <a class="position-absolute bottom-0 right-0 m-5 mb-10 border-0 badge badge-pill" href="https://de.gravatar.com/" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-edit "></i>
        </a>
        <?php } ?>

        </div>

    </div>
    <div class="col ml-20">
        <h1 class="mb-0"><?= $name ?></h1>
        
        <h3 class="m-0 text-<?=$scientist['dept']?>">
            <?php
                echo deptInfo($scientist['dept'])['name'];
            ?>
            
        </h3>
        <p class="lead mt-0">
            <i class="fad fa-lg fa-coin text-signal"></i>
            <b id="lom-points"></b>
            Coins
            <a href='#coins' class="text-muted">
                <i class="far fa-question-circle text-muted"></i>
            </a>

            <?php if (isset($scientist['achievements'])) {
                $achievements = $scientist['achievements']->bsonSerialize();
                $achievement = end($achievements);
            ?>
                <br>

                <i class="fad fa-lg fa-trophy text-signal"></i>
                <span class="">
                    <?= achievementText($achievement['title']) ?>
                    <small class="text-muted">am <?= $achievement['achieved'] ?></small>
                </span>
            <?php
            } ?>

        </p>
    </div>
    <div class="col text-right">
        <a class="btn" href="<?= ROOTPATH ?>/visualize?scientist=<?= $user ?>"><i class="fas fa-chart-network"></i>
            <?= lang('View coauthor network', 'Zeige Ko-Autoren-Netzwerk') ?>
        </a>


        <?php if ($currentuser) { ?>
            <br>
            <a class="btn mt-5" href="<?= ROOTPATH ?>/edit/user/<?= $user ?>"><i class="fas fa-user-pen"></i>
                <?= lang('Edit user profile', 'Bearbeite Profil') ?>
            </a>
        <?php } ?>
    </div>
</div>






<?php
if ($currentuser) {

    $approved = isset($USER['approved']) && in_array($q, $USER['approved']->bsonSerialize());
    $approval_needed = array();

    $q_end = new DateTime(SELECTEDYEAR . '-' . (3 * SELECTEDQUARTER) . '-' . (SELECTEDQUARTER == 1 || SELECTEDQUARTER == 4 ? 31 : 30) . ' 23:59:59');
    $quarter_in_past = new DateTime() > $q_end;

?>
    <p class="row-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p>
    <?php if (!$quarter_in_past) { ?>
        <a href="#" class="btn disabled">
            <i class="fas fa-check mr-5"></i>
            <?= lang('Selected quarter is not over yet.', 'Gewähltes Quartal ist noch nicht zu Ende.') ?>
        </a>
    <?php

    } elseif ($approved) { ?>
        <a href="#" class="btn disabled">
            <i class="fas fa-check mr-5"></i>
            <?= lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') ?>
        </a>
    <?php } else { ?>
        <a class="btn btn-success" href="#approve">
            <i class="fas fa-question mr-5"></i>
            <?= lang('Approve current quarter', 'Aktuelles Quartal freigeben') ?>
        </a>
    <?php } ?>
    <span data-toggle="tooltip" data-title=" <?= lang('The quarter can be selected in the menu at the top-right corner.', 'Das Quartal kann im Menü oben rechts ausgewählt werden.') ?>">
        <i class="far fa-question-circle text-muted"></i>
    </span>

<?php
} ?>
<!-- 
<div class="lead my-20">
    <?= lang('In ' . SELECTEDYEAR . ' achieved LOM points: ', 'Im Jahr ' . SELECTEDYEAR . ' erreichte LOM-Punkte: ') ?>
    <i class="fad fa-lg fa-coin text-signal"></i>
    <b id="lom-points"></b>
</div> -->



<h3>
    <?php
    echo lang('Research activities in ', 'Forschungsaktivitäten in ') . SELECTEDYEAR;
    ?>
    <span data-toggle="tooltip" data-title=" <?= lang('The year can be selected in the menu at the top-right corner.', 'Das Jahr kann im Menü oben rechts ausgewählt werden.') ?>">
        <i class="far fa-question-circle text-muted"></i>
    </span>
</h3>

<?php

$queries = array(
    "publication" => [
        "filter" => ['$or' => [['authors.user' => $user], ['editors.user' => $user]], 'year' => SELECTEDYEAR],
        "options" => ['sort' => ["year" => -1, "month" => -1]],
        "title" => lang('Publications', 'Publikationen'),
        "icon" => 'book-bookmark',
        "color" => "primary",
        "show-quarter" => true
    ],
    "poster" => [
        "filter" => [
            'authors.user' => $user,
            "start.year" => SELECTEDYEAR
        ],
        "options" => array(),
        "title" => lang('Poster'),
        "icon" => 'presentation-screen',
        "color" => "danger",
        "show-quarter" => true
    ],
    "lecture" => [
        "filter" =>  [
            'authors.user' => $user,
            "start.year" => SELECTEDYEAR
        ],
        "options" => array(),
        "title" => lang('Lectures', 'Vorträge'),
        "icon" => 'keynote',
        "color" => "signal",
        "show-quarter" => true
    ],
    "review" => [
        "filter" => [
            'user' => $user,
            // "role" => "Editor",
            // "start.year" => array('$lte' => SELECTEDYEAR),
            '$or' => array(
                ['end.year' => array('$gte' => SELECTEDYEAR)],
                ['end' => null],
                ['dates.year' => SELECTEDYEAR]
            )
        ],
        "options" => array(),
        "title" => lang('Reviews &amp; Editorial boards'),
        "icon" => 'book-open-cover',
        "color" => "success",
        "show-quarter" => false
    ],
    "misc" => [
        "filter" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => SELECTEDYEAR),
            '$or' => array(
                ['end.year' => array('$gte' => SELECTEDYEAR)],
                ['end' => null]
            )
        ],
        "options" => array(),
        "title" => lang('Other activities', 'Sonstige Aktivitäten'),
        "icon" => 'icons',
        "color" => "none",
        "show-quarter" => false
    ],
    "students" => [
        "filter" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => SELECTEDYEAR),
            '$or' => array(
                ['end.year' => array('$gte' => SELECTEDYEAR)],
                ['end' => null]
            )
        ],
        "options" => array(),
        "title" => lang('Students &amp; Guests', 'Studierende &amp; Gäste'),
        "icon" => 'people',
        "color" => "none",
        "show-quarter" => false
    ]
);

foreach ($queries as $col => $val) {
    // $collection = get_collection($col);
    $collection = $osiris->activities;

?>


    <div class="box box-<?= $val['color'] ?>">
        <div class="content">
            <h4 class="title text-<?= $val['color'] ?>"><i class="far fa-<?= $val['icon'] ?> mr-5"></i> <?= $val['title'] ?></h4>
        </div>
        <table class="table table-simple">
            <tbody>
                <?php
                $filter = $val['filter'];
                $filter['type'] = $col;
                $cursor = $collection->find($filter, $val['options']);
                // dump($cursor);
                foreach ($cursor as $doc) {
                    $id = $doc['_id'];
                    $l = $LOM->lom($col, $doc);
                    $_lom += $l['lom'];

                    if ($val["show-quarter"]) {
                        $q = getQuarter($doc);
                        $in_quarter = $q == SELECTEDQUARTER;
                    } else {
                        $in_quarter = true;
                    }


                    echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "' id='tr-$col-$id'>";
                    if ($val['show-quarter']) echo "<td class='quarter'>Q$q</td>";
                    echo "<td>";
                    echo $Format->format($col, $doc);

                    // show error messages, warnings and todos
                    $has_issues = has_issues($doc);
                    if ($currentuser && !empty($has_issues)) {
                        $approval_needed[] = array(
                            'type' => $col,
                            'id' => $doc['_id'],
                            'title' => $doc['title']
                        );
                ?>
                        <br>
                        <b class="text-danger">
                            <?= lang('This activity has unresolved warnings.', 'Diese Aktivität hat ungelöste Warnungen.') ?>
                            <a href="<?= ROOTPATH ?>/issues" class="link">Review</a>
                        </b>
                    <?php
                    }
                    // if($doc['type'] == 'misc') {
                    //     dump($doc, true);
                    // dump(is_null($doc['end']??''));
                    // }

                    ?>

                    </td>

                    <?php if ($currentuser) { ?>
                        <td class="unbreakable w-50">
                            <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                <i class="fa-regular fa-search"></i>
                            </a>
                            <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/edit/" . $id ?>">
                                <i class="fa-regular fa-edit"></i>
                            </a>
                        </td>
                    <?php } ?>
                    <td class='lom w-50'><span data-toggle='tooltip' data-title='<?= $l['points'] ?>'><?= $l["lom"] ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="content mt-0">
            <?php if ($currentuser) {
                $t = $col;
                if ($col == "publication") $t = "article";
            ?>
                <a href="<?= ROOTPATH ?>/my-activities?type=<?= $col ?>" class="btn text-<?= $val['color'] ?>">
                    <i class="far fa-book-bookmark mr-5"></i> <?= lang('My ', 'Meine ') ?><?= $val['title'] ?>
                </a>
                <a href="<?= ROOTPATH . "/activities/new?type=" . $t ?>" class="btn"><i class="fas fa-plus"></i></a>

            <?php } ?>

        </div>

    </div>

<?php }
?>




<?php if ($currentuser) { ?>


    <div class="modal modal-lg" id="approve" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content w-400 mw-full">
                <a href="#" class="btn float-right" role="button" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h5 class="modal-title"><?= lang('Approve', 'Freigeben') ?></h5>

                <?php
                if ($approved) {
                    echo "<p>" . lang('You have already approved the currently selected quarter.', 'Du hast das aktuelle Quartal bereits bestätigt.') . "</p>";
                } else if (!empty($approval_needed)) {
                    echo "<p>" . lang(
                        "The following activities have unresolved warnings. Please <a href='" . ROOTPATH . "/issues' class='link'>review all issues</a> before approving the current quarter.",
                        "Die folgenden Aktivitäten haben ungelöste Warnungen. Bitte <a href='" . ROOTPATH . "/issues' class='link'>klären sie alle Probleme</a> bevor sie das aktuelle Quartal freigeben können."
                    ) . "</p>";
                    echo "<ul class='list'>";
                    foreach ($approval_needed as $item) {
                        $type = ucfirst($item['type']);
                        echo "<li><b>$type</b>: $item[title]</li>";
                    }
                    echo "</ul>";
                } else { ?>
                    <form action="<?= ROOTPATH ?>/approve" method="post">
                        <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                        <button class="btn"><?= lang('Approve', 'Freigeben') ?></button>
                    </form>
                <?php } ?>

            </div>
        </div>
    </div>
<?php } ?>

<script>
    $('#lom-points').html('<?= round($_lom) ?>');
</script>