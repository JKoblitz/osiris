<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    var barChartConfig = {
        type: 'bar',
        data: [],
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
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        // Include a dollar sign in the ticks
                        callback: function(value, index, ticks) {
                            // only show full numbers
                            if (Number.isInteger(value)) {
                                return value
                            }
                            return "";
                        }
                    }
                }
            }
        }

    };
</script>

<?php if ($USER['is_controlling'] || $USER['is_admin']) {


?>

    <div class="content">

        <h2><?= lang('Welcome', 'Willkommen') ?>, <?= $USER['name'] ?></h2>

        <h4 class="text-muted font-weight-normal">Controlling</h4>


    </div>


    <div class="row row-eq-spacing">

    <?php
        $q = intval(SELECTEDQUARTER);
        $y = intval(SELECTEDYEAR);
        $quarters = ["${y}Q$q"=>['year'=>$y, 'quarter'=> $q]];

        for ($i=0; $i < 3; $i++) { 
            if ($q == 1){
                $q = 4;
                $y--;
            } else {
                $q--;
            }
            $quarters["${y}Q$q"] = ['year'=>$y, 'quarter'=> $q];
        }

        asort($quarters);
    ?>
    <?php foreach ($quarters as $q => $d) { 
        
    $n_scientists = $osiris->users->count(["is_scientist" => true]);
    $n_approved = $osiris->users->count(["is_scientist" => true, "approved" => $d['year'] . "Q" . $d['quarter']]);
    ?>
    
        <div class="col-md-3">
            <div class="box">
                <div class="chart content">
                    <h5 class="title text-center"><?= $q ?></h5>

                    <canvas id="approved-<?=$q?>"></canvas>
                    
                    <button class="btn mt-20" onclick="loadModal('components/controlling-approved', {q: '<?=$d['quarter']?>', y: '<?=$d['year']?>'})">
                        <i class="fas fa-search-plus"></i> <?= lang('Show details', 'Zeige Details') ?>
                    </button>

                    <script>
                        var ctx = document.getElementById('approved-<?=$q?>')
                        var myChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Approved', 'Missing'],
                                datasets: [{
                                    label: '# of Scientists',
                                    data: [<?= $n_approved ?>, <?= $n_scientists - $n_approved ?>],
                                    backgroundColor: [
                                        'rgba(236, 175, 0, 0.9)',
                                        'rgba(182, 31, 41, 0.9)',
                                    ],
                                    borderColor: [
                                        'rgba(236, 175, 0, 1)',
                                        'rgba(182, 31, 41, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Scientists approvation'
                                    }
                                }
                            }
                        });
                    </script>

                </div>
            </div>
        </div>


        <?php } ?>
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
        $filter = [];
        $options = ['sort' => ["type" => -1]];
        $cursor = $osiris->activities->find($filter, $options);

        foreach ($cursor as $doc) {
            if (!isset($doc['type']) || !isset($doc['year'])) continue;
            $type = $doc['type'];
            $year = strval($doc['year']);
            $issue = false;

            if (!isset($stats[$type])) $stats[$type] = [];
            if (!isset($stats[$type][$year])) $stats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];
            $stats[$type][$year]['good'] += 1;
        }

        foreach ($stats as $type => $vals) {

            $years = [];
            for ($i = 2017; $i < CURRENTYEAR; $i++) {
                $years[] = strval($i);
            }
        ?>
            <div class="col-lg-4 col-md-6">

                <div class="box">
                    <div class="chart content">
                        <h5 class="title text-center"><?= ucfirst($type) ?></h5>
                        <canvas id="chart-<?= $type ?>"></canvas>

                        <div class="mt-5">
                            <a href="<?= ROOTPATH ?>/activities/new?type=<?= $type ?>" class="btn btn-sm">
                                <i class="fas fa-plus"></i>
                                <?= lang('Add new ' . $type, 'Füge Aktivität hinzu') ?>
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
                        backgroundColor: 'rgba(236, 175, 0, 1)',
                    }, ]
                }


                console.log(data);
                var myChart = new Chart(ctx, data);
            </script>
        <?php } ?>


    </div>

    <div class="content">
        <a href="<?= ROOTPATH ?>/activities" class="btn btn-select bg-white mr-20">
            <i class="far fa-book-bookmark text-danger"></i>
            <?= lang('View all activites', 'Zeige alle Aktivitäten') ?>
        </a>
        <a href="<?= ROOTPATH ?>/export/reports" class="btn btn-select bg-white">
            <i class="far fa-file-chart-column text-success"></i>
            <?= lang('Generate report', 'Erstelle Report') ?>
        </a>
    </div>
<?php

} elseif ($USER['is_scientist']) { ?>

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
        $issue = false;
        if (
            !is_approved($doc, $user) ||
            ($doc['epub'] ?? false) ||
            ($type == "teaching" && $doc['status'] == 'in progress' && new DateTime() > getDateTime($document['end']))
        ) {
            $issue = true;
        }
        if (!isset($stats[$type])) $stats[$type] = [];
        if (!isset($stats[$type][$year])) $stats[$type][$year] = ["x" => $year, "good" => 0, "bad" => 0];

        if ($issue) $stats[$type][$year]['bad'] += 1;
        else $stats[$type][$year]['good'] += 1;
    }


    ?>
    <div class="row row-eq-spacing">

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

                        <div class="mt-5">
                            <a href="<?= ROOTPATH ?>/activities/new?type=<?= $type ?>" class="btn btn-sm">
                                <i class="fas fa-plus"></i>
                                <?= lang('Add new ' . $type, 'Füge Aktivität hinzu') ?>
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

        <!-- 
        <div class="col-lg-4 col-md-6">
            <div class="box">
                <div class="content text-center">
                    <a href="<?= ROOTPATH ?>/activities" class="btn btn-select">
                        <i class="far fa-book-medical text-danger"></i>
                        <?= lang('Add new activites', 'Neue Aktivität hinzufügen') ?>
                    </a>

                </div>
            </div>
        </div> -->


    </div>

    <div class="content">
        <a href="<?= ROOTPATH ?>/activities" class="btn btn-select bg-white">
            <i class="far fa-book-bookmark text-danger"></i>
            <?= lang('View all activites', 'Zeige alle Aktivitäten') ?>
        </a>

    </div>



<?php } else { ?>
    <p>
        <?= lang('You are neither scientist nor controlling staff.', 'Du bist weder Wissenschaftler:in noch Controlling.') ?>
    </p>
<?php } ?>