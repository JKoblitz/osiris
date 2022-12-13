<script src="<?= ROOTPATH ?>/js/chart.min.js"></script>
<script src="<?= ROOTPATH ?>/js/chartjs-plugin-datalabels.min.js"></script>
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
            "teaching" => 0,
            "students" => 0,
            "software" => 0,
            "misc" => 0,
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
    "teaching" => [],
    "students" => [],
    "software" => [],
    "misc" => [],
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
                if (($a['aoi'] ?? false) && isset($a['position']) && ($a['position'] == 'first' || $a['position'] == 'last' || $a['position'] == 'corresponding')) {
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

<style>
    .row .box {
        margin-top: 0;
    }
</style>

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

<?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
    <div class="content">

        <h1 class="m-0">Controlling Dashboard</h1>
    </div>
<?php
    include BASEPATH . "/pages/dashboard-controlling.php";
    include BASEPATH . "/pages/dashboard-scientist.php";
} else { ?>
    <div class="content">

        <h1 class="m-0"><?=lang('Scientist', 'Wissenschaftler')?> Dashboard</h1>

    </div>
<?php

    include BASEPATH . "/pages/dashboard-scientist.php";
 } ?>