
        <div class="box h-full">
            <div class="chart content">
                <h5 class="title text-center"><?= lang('Role of ' . $Settings->get('affiliation') . ' authors', 'Rolle der ' . $Settings->get('affiliation') . '-Autoren') ?></h5>
                <canvas id="chart-authors" style="max-height: 30rem;"></canvas>
            </div>

            <?php
                $data = $osiris->activities->aggregate([
                    ['$match' => ['authors.user' => $user]],
                    ['$project' => ['authors' => 1]],
                    ['$unwind' => '$authors'],
                    ['$match' => ['authors.user' => $user]],
                    ['$match' => ['authors.aoi' => true]],
                    [
                        '$group' => [
                            '_id' => '$authors.position',
                            'count' => ['$sum' => 1],
                            // 'doc' => ['$push' => '$$ROOT']
                        ]
                    ],
                    ['$sort' => ['count' => -1]],
                    [ '$limit' => 100 ]
                ]);
                foreach ($data as $a) {
                    dump($a, true);
                }
            ?>
            
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