
<div class="content">
    <h2>
        <?= lang('Publications in this time frame', 'Publikationen in diesem Zeitrahmen') ?>
    </h2>
</div>

<div class="row row-eq-spacing mx-0 mb-0">
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

<div class="row row-eq-spacing mx-0 mb-0">
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
</div>