<?php if (($currentuser || $Settings->featureEnabled('user-metrics')) && $showcoins) { ?>
    <div class="profile-widget col-md-6 col-lg-3">
        <div class="box h-full">
            <div class="chart content">
                <h4 class="title">
                    <?= lang('Coins per Year', 'Coins pro Jahr') ?>
                </h4>
                <canvas id="chart-coins" style="max-height: 30rem;"></canvas>
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
<?php } ?>