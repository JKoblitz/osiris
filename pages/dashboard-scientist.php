
<div class="content">
    <div class="alert">
        <div class="title">Systemnachricht</div>
        <p>
            Die bereits eingepflegten Publikationen stammen aus EndNote und enthalten aus irgendeinem Grund keine Umlaute o.ä. An einer Lösung arbeite ich zurzeit noch.
        </p>
    </div>
</div>

    <div class="content">
        <h2><?= lang('Welcome', 'Willkommen') ?>, <?= $USER['name'] ?></h2>

        <p class="lead">
            <?= lang(
                'This is your personal dashboard.',
                'Dies ist dein persönliches Dashboard.'
            ) ?>
        </p>
    </div>

    <?php

    $detailstats = array(
        "publication" => [],
        "poster" => [],
        "lecture" => [],
        "review" => [],
        "misc" => [],
        "students" => []
    );

    $user = $_SESSION['username'];
    $filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]]];
    $options = ['sort' => ["year" => -1, "month" => -1]];
    $cursor = $osiris->activities->find($filter, $options);

    foreach ($cursor as $doc) {
        if (!isset($doc['type']) || !isset($doc['year'])) continue;
        if ($doc['year'] < 2017) continue;
        $type = $doc['type'];
        
        $year = strval($doc['year']);


        if (!isset($detailstats[$type])) $detailstats[$type] = [];
        if (!isset($detailstats[$type][$year])) $detailstats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];

        if (has_issues($doc)) $detailstats[$type][$year]['bad'] += 1;
        else $detailstats[$type][$year]['good'] += 1;
    }


    ?>
    <div class="row row-eq-spacing mb-0">

        <?php foreach ($detailstats as $type => $vals) {

            $years = [];
            for ($i = 2017; $i <= CURRENTYEAR; $i++) {
                $years[] = strval($i);
            }
            $has_issues = array_sum(array_column($vals, 'bad')) != 0;
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
                            <?php if ($has_issues) { ?>
                                <a href="<?= ROOTPATH ?>/issues" class="btn btn-sm btn-danger"><?= lang('Resolve issues', 'Zeige Probleme') ?></a>
                            <?php } ?>
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
                            backgroundColor: 'rgba(236, 175, 0, 1)',
                        },
                        <?php if ($has_issues) { ?> {
                                label: 'Activities with issues',
                                data: raw_data,
                                parsing: {
                                    yAxisKey: 'bad'
                                },
                                backgroundColor: 'rgba(182, 31, 41, 1)',
                            }
                        <?php } ?>
                    ]
                }


                console.log(data);
                var myChart = new Chart(ctx, data);
            </script>
        <?php } ?>


    </div>

    <div class="content mt-0">
        <a href="<?= ROOTPATH ?>/activities" class="btn btn-select bg-white">
            <i class="far fa-book-bookmark text-danger"></i>
            <?= lang('View all activites', 'Zeige alle Aktivitäten') ?>
        </a>

    </div>
