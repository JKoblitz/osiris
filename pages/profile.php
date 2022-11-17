<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
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
    'misc' => 0,
    'students' => 0
];

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

// $detailstats = array(
//     "publication" => [],
//     "poster" => [],
//     "lecture" => [],
//     "review" => [],
//     "misc" => [],
//     "students" => []
// );


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
            $journals[] = $doc['journal'];
        }
    }

    if (!array_key_exists($type, $groups)) continue;

    $year = strval($doc['year']);


    if (!isset($stats)) $stats = [];
    if (!isset($stats[$year])) $stats[$year] = array_merge(["x" => $year], $groups);

    $stats[$year][$type] += 1;

    if ($currentuser) {
        // if (!isset($detailstats[$type])) $detailstats[$type] = [];
        // if (!isset($detailstats[$type][$year])) $detailstats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];

        if (has_issues($doc)) {
            // $detailstats[$type][$year]['bad'] += 1;
            $issues++;
        }
        // else $detailstats[$type][$year]['good'] += 1;
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
            <a class="btn" href="<?= ROOTPATH ?>/scientist/<?= $user ?>"><i class="far fa-calendar"></i>
                <?= lang('The year of ', 'Das Jahr von ') . $name ?>
            </a><br>

            <a class="btn text-signal bg-white mt-5" href="<?= ROOTPATH ?>/visualize?scientist=<?= $user ?>"><i class="far fa-chart-network"></i>
                <?= lang('View coauthor network', 'Zeige Koautoren-Netzwerk') ?>
            </a>

        </div>
    </div>

    <?php if ($currentuser) { ?>


        <div class="box row-<?= $scientist['dept'] ?>" style="border-left-width:5px">
            <div class="content">

                <p class="lead">
                    <?= lang('This is your personal profile page.', 'Dies ist deine persönliche Profilseite.') ?>
                </p>
                <?php if ($issues !== 0) { ?>
                    <p>
                        <a class="link text-danger" href='<?= ROOTPATH ?>/issues'>
                            <?= lang(
                                "You have $issues unresolved " . ($issues == 1 ? 'issue' : 'issues') . " with your activities.",
                                "Du hast $issues " . ($issues == 1 ? 'ungelöstes Problem' : 'ungelöste Probleme') . " mit deinen Aktivitäten."
                            ) ?>
                        </a>
                    </p>
                <?php } ?>

                <div class="">
                    <a class="btn" href="<?= ROOTPATH ?>/activities/new"><i class="far fa-plus text-signal"></i>
                        <?= lang('Add activity', 'Aktivität hinzufügen') ?>
                    </a>

                    <a class="btn" href="<?= ROOTPATH ?>/edit/user/<?= $user ?>"><i class="far fa-user-pen text-primary"></i>
                        <?= lang('Edit user profile', 'Bearbeite Profil') ?>
                    </a>

                    <a class="btn" href="<?= ROOTPATH ?>/scientist/<?= $user ?>"><i class="far fa-calendar text-success"></i>
                        <?= lang('My Year', 'Mein Jahr') ?>
                    </a>
                    <!-- 
                <a class="btn" href="<?= ROOTPATH ?>/visualize?scientist=<?= $user ?>"><i class="far fa-chart-network text-signal"></i>
                    <?= lang('My coauthor network', 'Mein Koautoren-Netzwerk') ?>
                </a> -->
                <a href="<?= ROOTPATH ?>/my-activities" class="btn">
                    <i class="far fa-book-bookmark text-danger"></i> <?= lang('My activities', 'Meine Aktivitäten ') ?>
                </a>
                </div>


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
                        <td>Email</td>
                        <td><?= $scientist['mail'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td><?= lang('Telephone', 'Telefon') ?></td>
                        <td><?= $scientist['telephone'] ?? '' ?></td>
                    </tr>
                    <tr>
                        <td>ORCID</td>
                        <td>
                            <?php if (isset($scientist['orcid'])) { ?>
                                <a href="http://orcid.org/<?= $scientist['orcid'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['orcid'] ?></a>
                            <?php } ?>
                        </td>
                    </tr>
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
                <h4 class="title"><?= lang('Latest activities', 'Neueste Aktivitäten') ?></h4>
            </div>
            <table class="table table-simple">
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($cursor as $doc) {
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
                                    <i class="fa-regular fa-search"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="content mt-0">
                <a href="<?= ROOTPATH ?>/my-activities?user=<?= $user ?>" class="btn">
                    <i class="far fa-book-bookmark mr-5"></i> <?= lang('All activities', 'Alle Aktivitäten ') ?>
                </a>
            </div>

        </div>
    </div>
</div>


<div class="row row-eq-spacing my-0">
    <div class="col-md-12 col-lg-6">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('All activities', 'Alle Aktivitäten') ?></h5>
                <canvas id="chart-activities" style="max-height: 30rem;"></canvas>

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
                            text: 'Chart'
                        },
                        legend: {
                            display: true,
                        }
                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
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
                            }
                        }
                    },
                    // events: ['click'],
                    onClick: (e) => {
                        const canvasPosition = Chart.helpers.getRelativePosition(e, activityChart);

                        // Substitute the appropriate scale IDs
                        const dataX = activityChart.scales.x.getValueForPixel(canvasPosition.x);
                        const dataY = activityChart.scales.y.getValueForPixel(canvasPosition.y);
                        console.log(years[dataX], dataY);
                        window.location = ROOTPATH + "/scientist/<?= $user ?>?year=" + years[dataX]
                    }
                    // onClick(e) {
                    //     const activePoints = myChart.getElementsAtEventForMode(e, 'nearest', {
                    //         intersect: true
                    //     }, false)
                    //     const [{
                    //         index
                    //     }] = activePoints;
                    //     console.log(datasets[0].data[index]);
                    // }
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

    <div class="col-md-6 col-lg-3">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('Impact factors', 'Impact Factors') ?></h5>
                <canvas id="chart-impact" style="max-height: 30rem;"></canvas>
            </div>
        </div>

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



    <div class="col-md-6 col-lg-3">
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
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: (tooltipItem, data) => {
                                const v = data.datasets[0].data[tooltipItem.index];
                                return Array.isArray(v) ? v[1] - v[0] : v;
                            }
                        }
                    },
                }
            }


            console.log(data);
            var myChart = new Chart(ctx, data);
        </script>
    </div>
</div>