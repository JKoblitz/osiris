<div class="content">
    <h2><?= lang('Overview on the past four quarters', 'Überblick über die letzten vier Quartale') ?></h2>

</div>

<div class="row row-eq-spacing mb-0">

    <?php
    foreach ($quarters as $q => $d) {
    ?>
        <div class="col-md-6 col-lg-3">
            <div class="box">
                <div class="chart content h-250">
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
                                        "#00000095",
                                        "#5F272A95",
                                        "#9a499c95",
                                        "#57575695",
                                    ],
                                    borderColor: '#464646', //'',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }, ]
                            },
                            options: {
                                maintainAspectRatio: false,
                                layout: {
                                    padding: {
                                        bottom: 30
                                    }
                                },
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

