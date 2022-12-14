<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
<script src="<?= ROOTPATH ?>/js/chartjs-plugin-datalabels.min.js"></script>
<style>
    .box.h-full {
        height: calc(100% - 4rem) !important;
    }
</style>
<?php

$currentuser = $user == $_SESSION['username'];

include_once BASEPATH . "/php/_lom.php";
$LOM = new LOM($user, $osiris);

$_lom = 0;

$achievements = array();
if (isset($scientist['achievements'])) {
    $achievements = $scientist['achievements']->bsonSerialize();
}
// dump($achievements);

// gravatar
$email = $scientist['mail']; #. "@dsmz.de";
$default = ROOTPATH . "/img/person.jpg";
$size = 140;

$gravatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $size;


$stats = [];
$groups = [
    'publication' => 0,
    'poster' => 0,
    'lecture' => 0,
    'review' => 0,
    "teaching" => 0,
    "students" => 0,
    "software" => 0,
    "misc" => 0,
];

$authors = ["firstorlast" => 0, 'middle' => 0, 'editor' => 0];

$issues = 0;

$years = [];
$lom_years = [];
for ($i = 2017; $i <= CURRENTYEAR; $i++) {
    $years[] = strval($i);
    $lom_years[$i] = 0;
}

$impacts = [];
$journals = [];


$filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]]];
$options = ['sort' => ["year" => -1, "month" => -1, "day" => -1]];
$cursor = $osiris->activities->find($filter, $options);
$cursor = $cursor->toArray();

foreach ($cursor as $doc) {
    if (!isset($doc['type']) || !isset($doc['year'])) continue;
    if ($doc['year'] < 2017) continue;

    $type = $doc['type'];

    $l = $LOM->lom($doc);
    $_lom += $l['lom'];
    $lom_years[$doc['year']] +=  $l['lom'];

    if ($type == 'publication' && isset($doc['journal'])) {
        // dump([get_impact($doc['journal'], $doc['year'] - 1), $doc['year'], $doc['journal']], true);
        if (!isset($doc['impact'])) {
            $if = get_impact($doc['journal'], $doc['year'] - 1);
            if (!empty($if)) {
                $osiris->activities->updateOne(
                    ['_id' => $doc['_id']],
                    ['$set' => ['impact' => $if]]
                );
            }
        } else {
            $if = $doc['impact'];
        }
        if (!empty($if)) {
            $impacts[] = $if;
            // $impacts[] = [$year, $if];
            $journals[] = $doc['journal'];
        }
    }
    if ($type == 'publication') {
        foreach ($doc['authors'] ?? array() as $a) {
            if (($a['user'] ?? '') == $user) {
                if (isset($a['position']) && (in_array($a['position'], ['first', 'last', 'corresponding']))) {
                    $authors['firstorlast']++;
                } else {
                    $authors['middle']++;
                }
                break;
            }
        }

        foreach ($doc['editors'] ?? array() as $a) {
            if (($a['user'] ?? '') == $user) {
                $authors['editor']++;
                break;
            }
        }
    }

    if (!array_key_exists($type, $groups)) continue;

    $year = strval($doc['year']);


    if (!isset($stats)) $stats = [];
    if (!isset($stats[$year])) $stats[$year] = array_merge(["x" => $year], $groups);

    $stats[$year][$type] += 1;

    if ($currentuser) {
        if (has_issues($doc)) {
            $issues++;
        }
    }
}

if (isset($_GET['verbose'])) {
    dump($scientist, true);
}


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
<div class="content my-0">


    <div class="row align-items-center my-0">
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
                <b id="lom-points"><?= $_lom ?></b>
                Coins
                <a href='#coins' class="text-muted">
                    <i class="far fa-question-circle text-muted"></i>
                </a>

                <?php if (!empty($achievements)) {
                    $last_achievement = end($achievements);
                ?>
                    <br>

                    <i class="fad fa-lg fa-trophy text-signal"></i>
                    <span class="">
                        <?= achievementText($last_achievement['title'], $scientist['first']) ?>
                        <small class="text-muted">am <?= $last_achievement['achieved'] ?></small>
                    </span>
                <?php
                } ?>

            </p>
        </div>
        <?php if (!$currentuser) { ?>

            <div class="col text-right">
                <a class="btn btn-osiris" href="<?= ROOTPATH ?>/scientist/<?= $user ?>"><i class="far fa-calendar"></i>
                    <?= lang('The year of ', 'Das Jahr von ') . $name ?>
                </a><br>

                <a class="btn btn-osiris mt-5" href="<?= ROOTPATH ?>/visualize/coauthors?scientist=<?= $user ?>"><i class="far fa-chart-network"></i>
                    <?= lang('View coauthor network', 'Zeige Koautoren-Netzwerk') ?>
                </a>
                <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>
                    <br>
                    <a class="btn btn-osiris mt-5" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>"><i class="fas fa-user-pen"></i>
                        <?= lang('Edit user profile', 'Bearbeite Profil') ?>
                    </a>
                <?php } ?>

            </div>
        <?php } ?>

    </div>

    <?php if ($currentuser) { ?>

        <div class="box p-5 row-<?= $scientist['dept'] ?>" style="border-left-width:5px">
            <!-- <div class="content"> -->

            <!-- <p class="mt-0">
                   
                </p> -->
            <div class="m-10">
                <h5 class="title font-size-16">
                    <?= lang('This is your personal profile page.', 'Dies ist deine persönliche Profilseite.') ?>
                </h5>

                <div class="btn-group btn-group-lg">
                    <a class="btn" href="<?= ROOTPATH ?>/activities/new" data-toggle="tooltip" data-title="<?= lang('Add activity', 'Aktivität hinzufügen') ?>">
                        <i class="icon-activity-plus text-orange fa-fw"></i>
                        <!-- <?= lang('Add activity', 'Aktivität hinzufügen') ?> -->
                    </a>
                    <a class="btn" href="<?= ROOTPATH ?>/scientist/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('My Year', 'Mein Jahr') ?>">
                        <i class="far fa-calendar text-success fa-fw"></i>
                        <!-- <?= lang('My Year', 'Mein Jahr') ?> -->
                    </a>
                    <a href="<?= ROOTPATH ?>/my-activities" class="btn" data-toggle="tooltip" data-title="<?= lang('My activities', 'Meine Aktivitäten ') ?>">
                        <i class="icon-activity-user text-primary fa-fw"></i>
                        <!-- <?= lang('My activities', 'Meine Aktivitäten ') ?> -->
                    </a>

                    <a class="btn" href="<?= ROOTPATH ?>/user/edit/<?= $user ?>" data-toggle="tooltip" data-title="<?= lang('Edit user profile', 'Bearbeite Profil') ?>">
                        <i class="far fa-user-pen text-muted fa-fw"></i>
                        <!-- <?= lang('Edit user profile', 'Bearbeite Profil') ?> -->
                    </a>
                </div>


                <?php if ($issues !== 0) { ?>
                    <div class="alert alert-danger mt-20">
                        <a class="link text-danger" href='<?= ROOTPATH ?>/issues'>
                            <?= lang(
                                "You have $issues unresolved " . ($issues == 1 ? 'issue' : 'issues') . " with your activities.",
                                "Du hast $issues " . ($issues == 1 ? 'ungelöstes Problem' : 'ungelöste Probleme') . " mit deinen Aktivitäten."
                            ) ?>
                        </a>
                    </div>
                <?php } ?>

                <?php
                $approvedQ = array();
                if (isset($scientist['approved'])) {
                    $approvedQ = $scientist['approved']->bsonSerialize();
                }

                // TODO: for testing
                // $Q = 1 - 1;
                // $Y = 2023;
                $Q = CURRENTQUARTER - 1;
                $Y = CURRENTYEAR;
                if ($Q < 1) {
                    $Q = 4;
                    $Y -= 1;
                }
                $lastquarter = "${Y}Q$Q";

                if (!in_array($lastquarter, $approvedQ)) { ?>
                    <div class="alert alert-muted mt-20">

                        <div class="title">
                            <?= lang("The past quarter ($lastquarter) has not been approved yet.", "Das vergangene Quartal ($lastquarter) wurde von dir noch nicht freigegeben.") ?>
                        </div>

                        <p>
                            <?= lang('
                            For the quarterly controlling, you need to confirm that all activities from the previous quarter are stored in OSIRIS and saved correctly.
                            To do this, go to your year and check your activities. Afterwards you can release the quarter via the green button.
                            ', '
                            Für das Quartalscontrolling musst du bestätigen, dass alle Aktivitäten aus dem vergangenen Quartal in OSIRIS hinterlegt und korrekt gespeichert sind.
                            Gehe dazu in dein Jahr und überprüfe deine Aktivitäten. Danach kannst du über den grünen Button das Quartal freigeben.
                            ') ?>
                        </p>

                        <a class="btn btn-success" href="<?= ROOTPATH ?>/scientist/<?= $user ?>?year=<?= $Y ?>&quarter=<?= $Q ?>">
                            <?= lang('Review & Approve', 'Überprüfen & Freigeben') ?>
                        </a>
                    </div>
                <?php } ?>


            </div>

        </div>
            <?php } ?>


</div>

<div class="row row-eq-spacing my-0">
    <div class="col-md-4">
        <div class="box h-full">
            <table class="table table-simple">
                <tbody>

                    <tr>
                        <td><?= lang('Last name', 'Nachname') ?></td>
                        <td><?= $scientist['last'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('First name', 'Vorname') ?></td>
                        <td><?= $scientist['first'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Academic title', 'Akademischer Titel') ?></td>
                        <td><?= $scientist['academic_title'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Gender', 'Geschlecht') ?></td>
                        <td><?php
                            $genders = [
                                'm' => lang('male', 'männlich'),
                                'f' => lang('female', 'weiblich'),
                                'd' => lang('non-binary', 'divers'),
                                'n' => lang('not specified', 'nicht angegeben'),
                            ];
                            echo $genders[$scientist['gender'] ?? 'n'];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?= $scientist['mail'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Telephone', 'Telefon') ?></td>
                        <td><?= $scientist['telephone'] ?? '' ?></td>
                    </tr>
                    <?php if (!empty($scientist['twitter'] ?? null)) { ?>
                        <tr>
                            <td>Twitter</td>
                            <td>
                                <a href="https://twitter.com/<?= $scientist['twitter'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['twitter'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['orcid'] ?? null)) { ?>
                        <tr>
                            <td>ORCID</td>
                            <td>
                                <a href="http://orcid.org/<?= $scientist['orcid'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['orcid'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['researchgate'] ?? null)) { ?>
                        <tr>
                            <td>ResearchGate</td>
                            <td>
                                <a href="https://www.researchgate.net/profile/<?= $scientist['researchgate'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['researchgate'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['google_scholar'] ?? null)) { ?>
                        <tr>
                            <td>Google Scholar</td>
                            <td>
                                <a href="https://scholar.google.com/citations?user=<?= $scientist['google_scholar'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['google_scholar'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['webpage'] ?? null)) {
                        $web = preg_replace('/^https?:\/\//', '', $scientist['webpage']);
                    ?>
                        <tr>
                            <td>Personal web page</td>
                            <td>
                                <a href="https://<?= $web ?>" target="_blank" rel="noopener noreferrer"><?= $web ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><?= lang('Department', 'Abteilung') ?></td>
                        <td><?= deptInfo($scientist['dept'])['name'] ?></td>
                    </tr>
                    <!-- <tr>
                        <td><?= lang('Achievements', 'Erfolge') ?></td>
                        <td>
                            <?php
                            if (isset($scientist['achievements'])) {
                                echo count($scientist['achievements']->bsonSerialize());
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                    </tr> -->
                    <tr>
                        <td><?= lang('Scientist', 'Wissenschaftler:in') ?></td>
                        <td><?= bool_icon($scientist['is_scientist'] ?? false) ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Department head', 'Abteilungsleiter:in') ?></td>
                        <td><?= bool_icon($scientist['is_leader'] ?? false) ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Active', 'Aktiv') ?></td>
                        <td><?= bool_icon($scientist['is_active'] ?? false) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="v-spacer d-md-none"></div>

    <div class="col-md-8">
        <div class="box h-full">
            <div class="content">
                <h4 class="title"><?= lang('Latest publications', 'Neueste Publikationen') ?></h4>
            </div>
            <table class="table table-simple">
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($cursor as $doc) {
                        if ($doc['type'] != 'publication') continue;
                        if ($i++ >= 5) break;
                        $id = $doc['_id'];

                    ?>
                        <tr id='tr-<?= $id ?>'>
                            <td class="w-50"><?= activity_icon($doc); ?></td>
                            <td>
                                <?= $Format->format($doc) ?>
                            </td>
                            <td class="unbreakable w-25">
                                <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                    <i class="icon-activity-search"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="content mt-0">
                <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>#type=publication" class="btn btn-osiris">
                    <i class="far fa-book-bookmark mr-5"></i> <?= lang('All publications of ', 'Alle Publikationen von ') . $name ?>
                </a>
            </div>

        </div>
    </div>
</div>

<div class="row row-eq-spacing-md my-0">

    <!-- <div class="col-md-4">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('Impact factors', 'Impact Factors') ?></h5>
                <canvas id="chart-impact" style="max-height: 30rem;"></canvas>

                <script>
                    var ctx = document.getElementById('chart-impact')
                    var raw_data = Object.values(<?= json_encode($impacts) ?>);
                    console.log(raw_data);
                    var data = {
                        type: 'polarArea',
                        options: {
                            plugins: {
                                title: {
                                    display: false,
                                    text: 'Chart'
                                },
                                legend: {
                                    display: false,
                                }
                            },
                            responsive: true,
                        },
                        data: {
                            labels: <?= json_encode($journals) ?>,
                            datasets: [{
                                data: raw_data,
                                // backgroundColor: '#006EB795',
                                // borderColor: '#006EB7',
                                borderWidth: 1,
                                backgroundColor: [
                                    '#83D0F595',
                                    '#006EB795',
                                    '#13357A95',
                                ],
                                borderColor: '#464646'
                            }],

                        }
                    }


                    console.log(data);
                    var myChart = new Chart(ctx, data);
                </script>
            </div>
        </div>
    </div> -->
    <div class="col-md-6">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center">
                    <i class="fad fa-file-lines text-primary"></i>
                    <?= lang('Impact factor histogram', 'Impact Factor Histogramm') ?>
                </h5>
                <canvas id="chart-impact" style="max-height: 30rem;"></canvas>
                <?php
                $x = [];
                $y = [];
                if (!empty($impacts)) {
                    $max_impact = ceil(max($impacts));


                    for ($i = 0; $i <= $max_impact; $i++) {
                        $x[] = $i;
                    }
                    $y = array_fill(0, $max_impact, 0);

                    foreach ($impacts as $val) {
                        $imp = ceil($val);
                        $y[$val]++;
                    }
                }
                ?>

                <script>
                    var ctx = document.getElementById('chart-impact')
                    var labels = JSON.parse('<?= json_encode($x) ?>');
                    var colors = [
                        // '#83D0F595',
                        '#006EB795',
                        // '#13357A95',
                        // '#00162595'
                    ]
                    var i = 0

                    console.log(labels);
                    var data = {
                        type: 'bar',
                        options: {
                            plugins: {
                                legend: {
                                    display: false,
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        title: (items) => {
                                            if (!items.length) {
                                                return '';
                                            }
                                            const item = items[0];
                                            const x = item.parsed.x;
                                            const min = x;
                                            const max = x + 1;
                                            return `IF: ${min} - ${max}`;
                                        }
                                    }
                                }
                            },
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    type: 'linear',
                                    ticks: {
                                        stepSize: 1
                                    },
                                    stacked: true,
                                    title: {
                                        display: true,
                                        text: lang('Impact factor', 'Impact factor')
                                    },
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: lang('Number of publications', 'Anzahl Publikationen')
                                    },
                                    ticks: {
                                        callback: function(value, index, ticks) {
                                            // only show full numbers
                                            if (Number.isInteger(value)) {
                                                return value
                                            }
                                            return "";
                                        }
                                    }
                                }
                            },
                        },
                        data: {
                            labels: JSON.parse('<?= json_encode($x) ?>'),
                            datasets: [

                                <?php
                                // foreach ($quarters as $q => $data) {
                                //     $imp = [];
                                //     for ($i = 1; $i < $max_impact; $i++) {
                                //         $imp[] = $data['impacts'][$i] ?? 0;
                                //     }
                                ?> {
                                    data: JSON.parse('<?= json_encode($y) ?>'),
                                    backgroundColor: colors[i++],
                                    borderWidth: 1,
                                    borderColor: '#464646',
                                    borderRadius: 4
                                },
                                <?php
                                //  } 
                                ?>

                            ],
                        }
                    }


                    console.log(data);
                    var myChart = new Chart(ctx, data);
                </script>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center">
                    <i class="fad fa-book-user text-primary"></i>
                    <?= lang('Role of', 'Rolle von') ?> <?= $scientist['first'] ?> <?= lang('in publications', 'in Publikationen') ?>
                </h5>
                <canvas id="chart-authors" style="max-height: 30rem;"></canvas>

                <script>
                    var ctx = document.getElementById('chart-authors')
                    var myChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: [
                                '<?= lang("First or last author", "Erst- oder Letztautor") ?>',
                                '<?= lang("Middle authors", "Mittelautor") ?>',
                                <?= $authors['editor'] !== 0 ? lang("'Editorship'", "'Editorenschaft'") : '' ?>
                            ],
                            datasets: [{
                                label: '# of Scientists',
                                data: [<?= $authors['firstorlast'] ?>, <?= $authors['middle'] ?>, <?= $authors['editor'] !== 0 ? $authors['editor'] : '' ?>],
                                backgroundColor: [
                                    '#006EB795',
                                    '#83D0F595',
                                    '#13357A95'
                                ],
                                borderColor: '#464646', //'',
                                borderWidth: 1,
                            }]
                        },
                        plugins: [ChartDataLabels],
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    display: true,
                                },
                                title: {
                                    display: false,
                                    text: 'Scientists approvation'
                                },
                                datalabels: {
                                    color: 'black',
                                    // anchor: 'end',
                                    // align: 'end',
                                    // offset: 10,
                                    font: {
                                        size: 20
                                    }
                                }
                            },
                        }
                    });
                </script>
            </div>

        </div>
    </div>
    <div class="col-md-3">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center">
                    <i class="fad fa-lg fa-coin text-signal"></i>
                    <?= lang('Coins per Year', 'Coins pro Jahr') ?>
                </h5>
                <canvas id="chart-coins" style="max-height: 30rem;"></canvas>
            </div>
        </div>

        <?php
        $data = [];
        $lastval = 0;
        $labels = [];
        foreach ($lom_years as $year => $val) {
            $labels[] = $year;
            $data[] = [$lastval, $val + $lastval];
            $lastval = $val + $lastval;
        }
        // $labels[] = 'total';
        // $data[] = $lastval;
        ?>

        <script>
            var ctx = document.getElementById('chart-coins')
            var raw_data = JSON.parse('<?= json_encode($data) ?>');
            var labels = JSON.parse('<?= json_encode($labels) ?>');
            console.log(raw_data);
            var colors = new Array(labels.length - 1);
            colors.fill('#ECAF0095')
            // colors[colors.length] = '#ECAF00'
            console.log(colors);
            var data = {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'coins',
                        data: raw_data,
                        backgroundColor: colors,
                        borderWidth: 1,
                        borderColor: '#464646',
                        borderSkipped: false,
                        // barPercentage: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (data) => {
                                    return data.parsed.y - data.parsed.x;
                                }
                            }
                        },
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: lang('Years', 'Jahre')
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: lang('Coins (accumulated)', 'Coins (akkumuliert)')
                            }
                        }
                    }
                }
            }


            console.log(data);
            var myChart = new Chart(ctx, data);
        </script>
    </div>
</div>






<div class="row row-eq-spacing my-0">
    <div class="col-md-12 col-lg-6">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('All activities', 'Alle Aktivitäten') ?></h5>
                <canvas id="chart-activities" style="max-height: 35rem;"></canvas>

            </div>
        </div>

        <script>
            var ctx = document.getElementById('chart-activities')
            var raw_data = Object.values(<?= json_encode($stats) ?>);
            var years = JSON.parse('<?= json_encode($years) ?>')
            console.log(raw_data);
            var data = {
                type: 'bar',
                options: {
                    plugins: {
                        title: {
                            display: false,
                            text: 'All activities'
                        },
                        legend: {
                            display: true,
                        }
                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: lang('Years', 'Jahre')
                            }
                        },
                        y: {
                            stacked: true,
                            ticks: {
                                callback: function(value, index, ticks) {
                                    // only show full numbers
                                    if (Number.isInteger(value)) {
                                        return value
                                    }
                                    return "";
                                }
                            },
                            title: {
                                display: true,
                                text: lang('Number of activities', 'Anzahl der Aktivitäten')
                            }
                        }
                    },
                    maintainAspectRatio: false,
                    onClick: (e) => {
                        const canvasPosition = Chart.helpers.getRelativePosition(e, activityChart);

                        // Substitute the appropriate scale IDs
                        const dataX = activityChart.scales.x.getValueForPixel(canvasPosition.x);
                        const dataY = activityChart.scales.y.getValueForPixel(canvasPosition.y);
                        console.log(years[dataX], dataY);
                        window.location = ROOTPATH + "/scientist/<?= $user ?>?year=" + years[dataX]
                    }
                },
                data: {
                    labels: years,
                    datasets: [
                        <?php $n = 3;
                        foreach ($groups as $group => $i) {
                            $color = typeInfo($group)['color'];
                            // $color = adjustBrightness($color, ($n--)*10);
                        ?> {
                                label: '<?= typeInfo($group)['name'] ?>',
                                data: raw_data,
                                parsing: {
                                    yAxisKey: '<?= $group ?>'
                                },
                                backgroundColor: '<?= $color ?>95',
                                borderColor: '#464646', //'<?= $color ?>',
                                borderWidth: 1
                            },
                        <?php } ?>
                    ]
                }
            }


            console.log(data);
            var activityChart = new Chart(ctx, data);
        </script>
    </div>

    <div class="v-spacer d-lg-none"></div>
    <div class="col-md-12 col-lg-6">
        <div class="box h-full">
            <div class="content">
                <h4 class="title"><?= lang('Latest activities', 'Neueste Aktivitäten') ?></h4>
            </div>
            <table class="table table-simple">
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($cursor as $doc) {
                        if ($doc['type'] == 'publication') continue;
                        if ($i++ > 5) break;
                        $id = $doc['_id'];

                    ?>
                        <tr id='tr-<?= $id ?>'>
                            <td class="w-50"><?= activity_icon($doc); ?></td>
                            <td>
                                <?= $Format->format($doc) ?>
                            </td>
                            <td class="unbreakable w-25">
                                <a class="btn btn-link btn-square" href="<?= ROOTPATH . "/activities/view/" . $id ?>">
                                    <i class="icon-activity-search"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="content mt-0">
                <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>" class="btn btn-osiris">
                    <i class="far fa-book-bookmark mr-5"></i> <?= lang('All activities', 'Alle Aktivitäten ') ?>
                </a>
            </div>

        </div>
    </div>

</div>