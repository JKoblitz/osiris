<?php

$currentuser = $user == $_SESSION['username'];
$q = SELECTEDYEAR . "Q" . SELECTEDQUARTER;

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

// gravatar
$email = $user . "@dsmz.de";
$default = ROOTPATH . "/img/person.jpg";
$size = 100;

$gravatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size;
?>

<div class="row align-items-center">
    <div class="col flex-grow-0">
        <img src="<?= $gravatar ?>" alt="">
    </div>
    <div class="col ml-20">
        <h1><?= $name ?></h1>
        <p class="lead">
            <i class="fad fa-lg fa-coin text-signal"></i>
            <b id="lom-points"></b>
            Credits
        </p>
    </div>
</div>


<?php
if ($currentuser) {

    $approved = isset($USER['approved']) && in_array($q, $USER['approved']->bsonSerialize());
    $approval_needed = array();
?>


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
            <?= lang('Approve current quarter', 'Aktuelles Quartal freigeben') ?>
        </a>
    <?php } ?>

<?php } ?>
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
            "dates.start.year" => array('$lte' => SELECTEDYEAR),
            '$or' => array(
                ['dates.end.year' => array('$gte' => SELECTEDYEAR)],
                ['dates.end' => null]
            )
        ],
        "options" => array(),
        "title" => lang('Other activities', 'Sonstige Aktivitäten'),
        "icon" => 'icons',
        "color" => "none",
        "show-quarter" => false
    ],
    "teaching" => [
        "filter" => [
            'authors.user' => $user,
            "start.year" => array('$lte' => SELECTEDYEAR),
            '$or' => array(
                ['end.year' => array('$gte' => SELECTEDYEAR)],
                ['end' => null]
            )
        ],
        "options" => array(),
        "title" => lang('Teaching &amp; Guests', 'Abschlussarbeiten und Gäste'),
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
                    echo format($col, $doc);

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
                        <?=lang('This activity has unresolved warnings.', 'Diese Aktivität hat ungelöste Warnungen.')?>
                        <a href="<?=ROOTPATH?>/issues" class="link">Review</a>
                    </b>
                    <?php
                    }
                    ?>

                    </td>
                    <td class="unbreakable">
                        <!-- <button class="btn btn-sm text-success" onclick="toggleEditForm('<?= $doc['type'] ?>', '<?= $id ?>')">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </button> -->
                        <a class="btn btn-sm text-success" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                            <i class="fa-regular fa-lg fa-search"></i>
                        </a>
                        <a class="btn btn-sm text-success" href="<?= ROOTPATH . "/activities/edit/" . $id ?>">
                            <i class="fa-regular fa-lg fa-edit"></i>
                        </a>
                    </td>
                    <td class='lom'><span data-toggle='tooltip' data-title='<?=$l['points']?>'><?= $l["lom"] ?></span></td>
                    <!---->
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="content mt-0">
            <?php if ($currentuser) { ?>
                <a href="<?= ROOTPATH ?>/<?= $col ?>" class="btn text-<?= $val['color'] ?>">
                    <i class="far fa-book-bookmark mr-5"></i> <?= lang('My ', 'Meine ') ?><?= $val['title'] ?>
                </a>
            <?php } ?>

            <?php if ($col == "publication") {
                $link = ROOTPATH . "/activities/new?type=article";
            } else {
                $link = ROOTPATH . "/activities/new?type=" . $col;
            } ?>

            <a href="<?= $link ?>" class="btn"><i class="fas fa-plus"></i></a>
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
                        "The following activities have unresolved warnings. Please <a href='".ROOTPATH."/issues' class='link'>review all issues</a> before approving the current quarter.",
                        "Die folgenden Aktivitäten haben ungelöste Warnungen. Bitte <a href='".ROOTPATH."/issues' class='link'>klären sie alle Probleme</a> bevor sie das aktuelle Quartal freigeben können."
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