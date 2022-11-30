<?php

$currentuser = $user == $_SESSION['username'];

$YEAR = intval($_GET['year'] ?? CURRENTYEAR);
$QUARTER = intval($_GET['quarter'] ?? CURRENTQUARTER);

$q = $YEAR . "Q" . $QUARTER;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

// gravatar
$email = $scientist['mail']; #. "@dsmz.de";
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

<div class="content">

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

            <h3 class="m-0 text-<?= $scientist['dept'] ?>">
                <?php
                echo deptInfo($scientist['dept'])['name'];
                ?>
            </h3>
            <p class="lead mt-0">
                <i class="fad fa-lg fa-coin text-signal"></i>
                <b id="lom-points"></b>
                Coins in <?= $YEAR ?>
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
                        <?= achievementText($achievement['title'], $scientist['first'] ?? null) ?>
                        <small class="text-muted">am <?= $achievement['achieved'] ?></small>
                    </span>
                <?php
                } ?>

            </p>
        </div>
        <div class="col text-right">
            <a class="btn" href="<?= ROOTPATH ?>/profile/<?= $user ?>"><i class="far fa-user-graduate"></i>
                <?= lang('Profile of ', 'Profil von ') . $name ?>
            </a><br>

            <a class="btn mt-5" href="<?= ROOTPATH ?>/visualize/coauthors?scientist=<?= $user ?>"><i class="far fa-chart-network"></i>
                <?= lang('View coauthor network', 'Zeige Koautoren-Netzwerk') ?>
            </a>


            <?php if ($currentuser) { ?>
                <br>
                <a class="btn mt-5" href="<?= ROOTPATH ?>/edit/user/<?= $user ?>"><i class="fas fa-user-pen"></i>
                    <?= lang('Edit user profile', 'Bearbeite Profil') ?>
                </a>
            <?php } ?>
        </div>
    </div>






    <h1>
        <?php
        echo lang('Research activities in ', 'Forschungsaktivitäten in ') . $YEAR;
        ?>
    </h1>

    <form id="" action="" method="get" class="w-400 mw-full">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text" data-toggle="tooltip" data-title="<?= lang('Select quarter', 'Wähle ein Quartal aus') ?>">
                    <i class="fa-regular fa-calendar-day"></i>
                </div>
            </div>
            <select name="year" id="year" class="form-control">
                <?php foreach (range(2017, CURRENTYEAR) as $year) { ?>
                    <option value="<?= $year ?>" <?= $YEAR == $year ? 'selected' : '' ?>><?= $year ?></option>
                <?php } ?>
            </select>
            <select name="quarter" id="quarter" class="form-control">
                <option value="1" <?= $QUARTER == '1' ? 'selected' : '' ?>>Q1</option>
                <option value="2" <?= $QUARTER == '2' ? 'selected' : '' ?>>Q2</option>
                <option value="3" <?= $QUARTER == '3' ? 'selected' : '' ?>>Q3</option>
                <option value="4" <?= $QUARTER == '4' ? 'selected' : '' ?>>Q4</option>
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary"><i class="fas fa-check"></i></button>
            </div>
        </div>
    </form>

    <p class="text-muted font-size-12 mt-0">
        <?= lang('The entire year is shown here. Activities outside the selected quarter are grayed out. ', 'Das gesamte Jahr ist hier gezeigt. Aktivitäten außerhalb des gewählten Quartals sind ausgegraut.') ?>
    </p>

    <?php
    if ($currentuser) {

        $approved = isset($USER['approved']) && in_array($q, $USER['approved']->bsonSerialize());
        $approval_needed = array();

        $q_end = new DateTime($YEAR . '-' . (3 * $QUARTER) . '-' . ($QUARTER == 1 || $QUARTER == 4 ? 31 : 30) . ' 23:59:59');
        $quarter_in_past = new DateTime() > $q_end;

    ?>
        <!-- <p class="row-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p> -->
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

    <?php
    } ?>


    <?php
    $options = ['sort' => ["year" => -1, "month" => -1]];
    $queries = array(
        "publication" => [
            '$or' => [
                ['authors.user' => $user],
                ['editors.user' => $user]
            ],
            'year' => $YEAR
        ],
        "poster" => [
            'authors.user' => $user,
            "year" => $YEAR
        ],
        "lecture" =>  [
            'authors.user' => $user,
            "year" => $YEAR
        ],
        "review" => [
            'authors.user' => $user,
            '$or' => array(
                [
                    "role" => "editorial",
                    "start.year" => array('$lte' => $YEAR),
                    '$or' => array(
                        ['end.year' => array('$gte' => $YEAR)],
                        ['end' => null]
                    )
                ],
                ['year' => $YEAR]
            )
        ],
        "teaching" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => $YEAR),
            '$or' => array(
                ['end.year' => array('$gte' => $YEAR)],
                ['end' => null]
            )
        ],
        "students" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => $YEAR),
            '$or' => array(
                ['end.year' => array('$gte' => $YEAR)],
                ['end' => null]
            )
        ],
        "software" => [
            'authors.user' => $user,
            "year" => $YEAR,
        ],
        "misc" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => $YEAR),
            '$or' => array(
                ['end.year' => array('$gte' => $YEAR)],
                ['end' => null]
            )
        ],
    );

    foreach ($queries as $col => $filter) {
        // $collection = get_collection($col);
        $collection = $osiris->activities;

    ?>


        <div class="box box-<?= $col ?>" id="<?= $col ?>">
            <div class="content">
                <h4 class="title text-<?= $col ?>"><i class="far fa-fw fa-<?= typeInfo($col)['icon'] ?> mr-5"></i> <?= typeInfo($col)['name'] ?></h4>
            </div>
            <table class="table table-simple">
                <tbody>
                    <?php
                    $filter['type'] = $col;
                    $cursor = $collection->find($filter, $options);
                    // dump($cursor);
                    foreach ($cursor as $doc) {
                        $id = $doc['_id'];
                        $l = $LOM->lom($doc);
                        $_lom += $l['lom'];

                        if ($doc['year'] == $YEAR) {
                            $q = getQuarter($doc);
                            $in_quarter = $q == $QUARTER;
                        } else {
                            $q = '';
                            $in_quarter = false;
                        }


                        echo "<tr class='" . (!$in_quarter ? 'row-muted' : '') . "' id='tr-$col-$id'>";
                        // echo "<td class='w-25'>";
                        // echo activity_icon($doc);
                        // echo "</td>";
                        echo "<td class='quarter'>";
                        if (!empty($q)) echo "Q$q";
                        echo "</td>";
                        echo "<td>";
                        echo $Format->format($doc);

                        // show error messages, warnings and todos
                        $has_issues = has_issues($doc);
                        if ($currentuser && !empty($has_issues)) {
                            $approval_needed[] = array(
                                'type' => $col,
                                'id' => $doc['_id'],
                                'title' => $doc['title'] ?? $doc['journal'] ?? ''
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
                    <a href="<?= ROOTPATH ?>/my-activities?type=<?= $col ?>" class="btn text-<?= typeInfo($col)['color'] ?>">
                        <i class="far fa-book-bookmark mr-5"></i> <?= lang('My ', 'Meine ') ?><?= typeInfo($col)['name'] ?>
                    </a>
                    <a href="<?= ROOTPATH . "/activities/new?type=" . $t ?>" class="btn"><i class="fas fa-plus"></i></a>

                <?php } ?>

            </div>

        </div>

    <?php } ?>




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
                            <input type="hidden" name="quarter" class="hidden" value="<?= $YEAR . "Q" . $QUARTER ?>">
                            <button class="btn"><?= lang('Approve', 'Freigeben') ?></button>
                        </form>
                    <?php } ?>

                </div>
            </div>
        </div>
    <?php } ?>

</div>
<script>
    $('#lom-points').html('<?= round($_lom) ?>');
</script>