<script src="<?=ROOTPATH?>/js/chartjs-plugin-datalabels.min.js"></script>
<script>
    // Chart.register(ChartDataLabels);
</script>
<?php
$q = intval(CURRENTQUARTER);
$y = intval(CURRENTYEAR);
$quarters = [];
$years = [$y];

for ($i = 0; $i < 4; $i++) {
    $quarters["${y}Q$q"] = [
        'year' => $y,
        'quarter' => $q,
        'activities' => array(
            "publication" => 0,
            "poster" => 0,
            "lecture" => 0,
            "review" => 0,
            "misc" => 0,
            "students" => 0,
        ),
        'impacts' => []
    ];
    if ($q == 1) {
        $q = 4;
        $y--;
        $years[] = $y;
    } else {
        $q--;
    }
}
asort($quarters);

$stats = array(
    "publication" => [],
    "poster" => [],
    "lecture" => [],
    "review" => [],
    "misc" => [],
    "students" => []
);


$impacts = [];
$journals = [];

$authors = ["firstorlast" => 0, 'middle' => 0];

$filter = [];
$options = ['sort' => ["type" => -1]];
$cursor = $osiris->activities->find($filter, $options);

foreach ($cursor as $doc) {
    if (!isset($doc['type']) || !isset($doc['year'])) continue;
    if ($doc['year'] < 2017) continue;
    $type = $doc['type'];
    $year = strval($doc['year']);
    $issue = false;

    if (!isset($stats[$type])) $stats[$type] = [];
    if (!isset($stats[$type][$year])) $stats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];
    $stats[$type][$year]['good'] += 1;



    if (in_array($year, $years)) {
        $q = getQuarter($doc);
        if (empty($q)) continue;
        $yq = "${year}Q$q";
        if (!isset($quarters[$yq])) continue;

        $quarters[$yq]['activities'][$type]++;


        if ($type == 'publication') {

            if (isset($doc['journal'])) {
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
                    $impacts[] = [$yq, $if];
                    $journals[] = $doc['journal'];
                }
            }

            $firstorlast = false;
            foreach ($doc['authors'] as $a) {
                if (($a['aoi'] ?? false) && isset($a['position']) && ($a['position'] == 'first' || $a['position'] == 'last')) {
                    $firstorlast = true;
                    break;
                }
            }
            if ($firstorlast) $authors['firstorlast']++;
            else $authors['middle']++;
        }
    }
}

// get maximum value for the y axis
$max = 0;
foreach ($quarters as $q => $value) {
    $m = max(array_values($value['activities']));
    if ($m > $max) $max = $m;
}
$max_quarter_act = ceil($max / 10) * 10;
if ($max_quarter_act - $max > 5) $max_quarter_act = $max_quarter_act - 5;

$max_impact = 0;
foreach ($impacts as $vals) {
    $yq = $vals[0];
    $imp = ceil($vals[1]);

    if (!isset($quarters[$yq]['impacts'][$imp])) $quarters[$yq]['impacts'][$imp] = 0;
    $quarters[$yq]['impacts'][$imp]++;

    if ($imp > $max_impact) $max_impact = $imp;
}
?>


<div class="content">

    <h1 class="m-0">Controlling Dashboard</h1>

    <h2><?= lang('Overview on the past four quarters', 'Überblick über die letzten vier Quartale') ?></h2>

</div>


<div class="row row-eq-spacing mb-0">

    <?php

    foreach ($quarters as $q => $d) {
    ?>
        <div class="col-md-6 col-lg-3">
            <div class="box">
                <div class="chart content">
                    <h5 class="title text-center"><?= $q ?></h5>

                    <canvas id="overview-<?= $q ?>"></canvas>
                    <!-- <div class="text-right mt-5">
                        <button class="btn btn-sm" onclick="loadModal('components/controlling-approved', {q: '<?= $d['quarter'] ?>', y: '<?= $d['year'] ?>'})">
                            <i class="fas fa-search-plus"></i> <?= lang('Activities') ?>
                        </button>
                    </div> -->

                    <script>
                        var ctx = document.getElementById('overview-<?= $q ?>')
                        var raw_data = JSON.parse('<?= json_encode($d['activities']) ?>')
                        console.log(raw_data);
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                // labels: ['<?= lang("Approved", "Bestätigt") ?>', '<?= lang("Approval missing", "Bestätigung fehlt") ?>'],
                                labels: Object.keys(raw_data),
                                datasets: [{
                                    data: Object.values(raw_data),
                                    backgroundColor: [
                                        "#006EB795",
                                        "#B61F2995",
                                        "#ECAF0095",
                                        "#1FA13895",
                                        "#b3b3b395",
                                        "#57575695",
                                    ],
                                    borderColor: '#464646', //'',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }, ]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        stacked: true,
                                    },
                                    y: {
                                        stacked: true,
                                        min: 0,
                                        max: <?= $max_quarter_act ?>,
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        display: false,
                                    },
                                    title: {
                                        display: false,
                                        text: 'Scientists approvation'
                                    }
                                }
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    <?php }


    foreach ($quarters as $q => $d) {

        $n_scientists = $osiris->users->count(["is_scientist" => true, "is_active" => true]);
        $n_approved = $osiris->users->count(["is_scientist" => true, "is_active" => true, "approved" => $d['year'] . "Q" . $d['quarter']]);

    ?>
        <div class="col-md-3">
            <div class="box">
                <div class="chart content">
                    <h5 class="title text-center"><?= $q ?></h5>

                    <canvas id="approved-<?= $q ?>"></canvas>
                    <div class="text-right mt-5">
                        <button class="btn btn-sm" onclick="loadModal('components/controlling-approved', {q: '<?= $d['quarter'] ?>', y: '<?= $d['year'] ?>'})">
                            <i class="fas fa-search-plus"></i> <?= lang('Details') ?>
                        </button>
                    </div>

                    <script>
                        var ctx = document.getElementById('approved-<?= $q ?>')
                        var myChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['<?= lang("Approved", "Bestätigt") ?>', '<?= lang("Approval missing", "Bestätigung fehlt") ?>'],
                                datasets: [{
                                    label: '# of Scientists',
                                    data: [<?= $n_approved ?>, <?= $n_scientists - $n_approved ?>],
                                    backgroundColor: [
                                        '#ECAF0095',
                                        '#B61F2995',
                                    ],
                                    borderColor: '#464646', //'',
                                    borderWidth: 1,
                                }]
                            },
                            plugins: [ChartDataLabels],
                            options: {
                                responsive: true,
                                plugins: {
                                    datalabels: {
                                        color: 'black',
                                        // anchor: 'end',
                                        // align: 'end',
                                        // offset: 10,
                                        font: {
                                            size: 20
                                        }
                                    },
                                    legend: {
                                        position: 'bottom',
                                        display: false,
                                    },
                                    title: {
                                        display: false,
                                        text: 'Scientists approvation'
                                    }
                                }
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    <?php }
    ?>
</div>


<div class="content">
    <h2>
        <?= lang('Publications in this time frame', 'Publikationen in diesem Zeitrahmen') ?>
    </h2>
</div>

<div class="row row-eq-spacing mb-0">
    <div class="col-lg-8">

        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('Impact factors', 'Impact Factors') ?></h5>
                <canvas id="chart-impact" style="max-height: 30rem;"></canvas>
            </div>
        </div>

        <?php
        $x = [];
        for ($i = 1; $i < $max_impact; $i++) {
            $x[] = $i;
        }
        ?>

        <script>
            var ctx = document.getElementById('chart-impact')
            var labels = JSON.parse('<?= json_encode($x) ?>');
            var colors = [
                '#83D0F595',
                '#006EB795',
                '#13357A95',
                '#00162595'
            ]
            var i = 0

            console.log(labels);
            var data = {
                type: 'bar',
                options: {
                    plugins: {
                        legend: {
                            display: true,
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
                                    const min = x - 1;
                                    const max = x;
                                    return `IF: ${min} - ${max}`;
                                }
                            }
                        }
                    },
                    responsive: true,
                    x: {
                        type: 'linear',
                        offset: false,
                        grid: {
                            offset: false
                        },
                        ticks: {
                            stepSize: 1
                        },
                        stacked: true,
                    },
                    y: {
                        // beginAtZero: true
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Y axis title'
                        }
                    },
                },
                data: {
                    labels: labels,
                    datasets: [

                        <?php foreach ($quarters as $q => $data) {
                            $imp = [];
                            for ($i = 1; $i < $max_impact; $i++) {
                                $imp[] = $data['impacts'][$i] ?? 0;
                            }
                        ?> {
                                label: '<?= $q ?>',
                                data: JSON.parse('<?= json_encode($imp) ?>'),
                                backgroundColor: colors[i++],
                                borderWidth: 1,
                                borderColor: '#464646',
                                borderRadius: 4
                            },
                        <?php } ?>

                    ],
                }
            }


            console.log(data);
            var myChart = new Chart(ctx, data);
        </script>
    </div>

    <div class="col-lg-4">
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('Role of DSMZ authors', 'Rolle der DSMZ-Autoren') ?></h5>
                <canvas id="chart-authors" style="max-height: 30rem;"></canvas>
            </div>
            <script>
                var ctx = document.getElementById('chart-authors')
                var myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['<?= lang("First or last author", "Erst- oder Letztautor") ?>', '<?= lang("Middle authors", "Mittelautor") ?>'],
                        datasets: [{
                            label: '# of Scientists',
                            data: [<?= $authors['firstorlast'] ?>, <?= $authors['middle'] ?>],
                            backgroundColor: [
                                '#006EB795',
                                '#83D0F595',
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

<div class="content">
    <h2>
        <?= lang('Development of activities by type', 'Entwicklung der Aktivitäten nach Art') ?>
    </h2>
</div>

<div class="row row-eq-spacing mb-0">
    <?php foreach ($stats as $type => $vals) {

        $years = [];
        for ($i = 2017; $i <= CURRENTYEAR; $i++) {
            $years[] = strval($i);
        }
    ?>
        <div class="col-lg-4 col-md-6">

            <div class="box">
                <div class="chart content">
                    <h5 class="title text-center"><?= type2title($type) ?></h5>
                    <canvas id="chart-<?= $type ?>"></canvas>

                    <div class="mt-5 text-right">
                        <a href="<?= ROOTPATH ?>/activities/new?type=<?= $type ?>" class="btn btn-sm">
                            <i class="fas fa-plus"></i>
                            <?= lang('Add new', 'Neu anlegen') ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>


        <script>
            var ctx = document.getElementById('chart-<?= $type ?>')
            var data = Object.assign({}, barChartConfig)
            var raw_data = Object.values(<?= json_encode($vals) ?>);
            data.data = {
                labels: <?= json_encode($years) ?>,
                datasets: [{
                    label: 'Activities',
                    data: raw_data,
                    parsing: {
                        yAxisKey: 'good'
                    },
                    backgroundColor: '<?= typeInfo($type)['color'] ?>95',
                    borderColor: '#464646',
                    borderWidth: 1,
                    borderRadius: 4
                }, ]
            }

            var myChart = new Chart(ctx, data);
        </script>
    <?php } ?>


</div>

<div class="content mt-0">
    <a href="<?= ROOTPATH ?>/activities" class="btn btn-select bg-white mr-20">
        <i class="far fa-book-bookmark text-danger"></i>
        <?= lang('View all activites', 'Zeige alle Aktivitäten') ?>
    </a>
    <a href="<?= ROOTPATH ?>/reports" class="btn btn-select bg-white">
        <i class="far fa-file-chart-column text-success"></i>
        <?= lang('Generate report', 'Erstelle Report') ?>
    </a>
</div>