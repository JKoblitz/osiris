
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

    $stats = array(
        "publication" => [],
        "poster" => [],
        "lecture" => [],
        "review" => [],
        "misc" => [],
        "teaching" => []
    );

    $user = $_SESSION['username'];
    $filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]]];
    $options = ['sort' => ["year" => -1, "month" => -1]];
    $cursor = $osiris->activities->find($filter, $options);

    foreach ($cursor as $doc) {
        if (!isset($doc['type']) || !isset($doc['year'])) continue;
        $type = $doc['type'];
        $year = strval($doc['year']);

        // check if there are any issues
        $approval = !is_approved($doc, $user);
        $epub = ($doc['epub'] ?? false);
        // $doc['epub-delay'] = "2022-09-01";
        if ($epub && isset($doc['epub-delay'])) {
            $startTimeStamp = strtotime($doc['epub-delay']);
            $endTimeStamp = strtotime(date('Y-m-d'));
            $timeDiff = abs($endTimeStamp - $startTimeStamp);
            $numberDays = intval($timeDiff / 86400);  // 86400 seconds in one day
            if ($numberDays < 30){
                $epub = false;
            }
        }
        $teaching = ($type == "teaching" && $doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end']));
        
        // if (
        //     !is_approved($doc, $user) ||
        //     ($doc['epub'] ?? false) ||
        //     ($type == "teaching" && $doc['status'] == 'in progress' && new DateTime() > getDateTime($document['end']))
        // ) {
        //     $issue = true;
        // }
        if (!isset($stats[$type])) $stats[$type] = [];
        if (!isset($stats[$type][$year])) $stats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];

        if ($approval || $epub || $teaching) $stats[$type][$year]['bad'] += 1;
        else $stats[$type][$year]['good'] += 1;
    }


    ?>
    <div class="row row-eq-spacing mb-0">

        <?php foreach ($stats as $type => $vals) {

            $years = [];
            for ($i = 2017; $i < CURRENTYEAR; $i++) {
                $years[] = strval($i);
            }
            $has_issues = array_sum(array_column($vals, 'bad')) != 0;
        ?>
            <div class="col-lg-4 col-md-6">

                <div class="box">
                    <div class="chart content">
                        <h5 class="title text-center"><?= ucfirst($type) ?></h5>
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
