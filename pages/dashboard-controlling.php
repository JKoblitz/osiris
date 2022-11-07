
    <div class="content">

<h2 class="mb-0"><?= lang('Welcome', 'Willkommen') ?>, <?= $USER['name'] ?></h2>

<h4 class="text-muted font-weight-normal mt-0">Controlling Dashboard</h4>


</div>


<div class="row row-eq-spacing mb-0">

<?php
$q = intval(SELECTEDQUARTER);
$y = intval(SELECTEDYEAR);
$quarters = ["${y}Q$q" => ['year' => $y, 'quarter' => $q]];

for ($i = 0; $i < 3; $i++) {
    if ($q == 1) {
        $q = 4;
        $y--;
    } else {
        $q--;
    }
    $quarters["${y}Q$q"] = ['year' => $y, 'quarter' => $q];
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
                            labels: ['<?=lang("Approved", "Bestätigt")?>', '<?=lang("Approval missing", "Bestätigung fehlt")?>'],
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
                                    display: false,
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
<?php }

$stats = array(
    "publication" => [],
    "poster" => [],
    "lecture" => [],
    "review" => [],
    "misc" => [],
    "students" => []
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
                backgroundColor: 'rgba(236, 175, 0, 1)',
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
<a href="<?= ROOTPATH ?>/export/reports" class="btn btn-select bg-white">
    <i class="far fa-file-chart-column text-success"></i>
    <?= lang('Generate report', 'Erstelle Report') ?>
</a>
</div>