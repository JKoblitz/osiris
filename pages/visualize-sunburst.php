<?php
$filter = [];
$filter_dept = $_GET['dept'] ?? '';
if (!empty($filter_dept) && $filter_dept != 'undefined') {

    $users = [];
    $cursor = $osiris->users->find(['dept' => $filter_dept], ['projection' => ['username' => 1]]);

    foreach ($cursor as $u) {
        if (empty($u['username'] ?? '')) continue;
        $users[] = strtolower($u['username']);
    }
    $filter['authors.user'] = ['$in' => $users]; //, ['editors.user' => ['$in'=>$users]]];

}

$filter_year = intval($_GET['year'] ?? 0);
if (!empty($filter_year) && $filter_year != 'undefined') {
    $filter['year'] = $filter_year;
}
$filter_type = $_GET['type'] ?? 'publication';
if (!empty($filter_type) && $filter_type != 'undefined') {
    $filter['type'] = $filter_type;
}


$temp = $osiris->users->find([], ['sort' => ["last" => 1]]);
$users = [];
foreach ($temp as $row) {
    $users[$row['_id']] = $row['dept'];
}

// generate graph json

$departments = [];
$flare = [];

$activities = $osiris->activities->find($filter);
$activities = $activities->toArray();

$index = 0;
$departments = $Settings->getDepartments();
$i = 0;
foreach ($departments as $dept => $val) {
    $flare[$i] = [
        'name' => $dept,
        'abbr' => $dept,
        'children' => [],
        // 'color' => $departments[$dept]['color']
    ];
    $departments[$dept]['index'] = $i++;
}

// foreach ($departments as $key => $val) {
//     $departments[$key]['index'] = $i++;
//     $departments[$key]['count'] = 0;
// }

$N = 0; //count($activities);
foreach ($activities as $doc) {
    // dump($doc['authors']);
    $count = false;
    foreach ($doc['authors'] as $aut) {
        if (!($aut['aoi'] ?? false) || empty($aut['user']) || !array_key_exists($aut['user'], $users)) continue;

        $username = $aut['user'];
        $dept = $users[$username];

        if (!empty($dept)) {
            $deptindex = $departments[$dept]['index'];
            // dump([$username, $deptindex]);
            // $departments[$dept]['count']++;
            $userindex = array_search($username, array_column($flare[$deptindex]['children'], 'abbr'));
            if ($userindex === false) {
                $flare[$deptindex]['children'][] = [
                    'name' => $aut['last'] . ", " . $aut['first'],
                    'abbr' => $username,
                    'value' => 0,
                    // 'color' => $departments[$dept]['color']
                ];
                $userindex = count($flare[$deptindex]['children']) - 1;
            }
            $flare[$deptindex]['children'][$userindex]['value']++;
            $count = true;
        }
    }
    if ($count) $N++;
}

?>

<h1>
    <i class="far fa-chart-network" aria-hidden="true"></i>
    <?= lang('Department overview', 'Abteilungs-Übersicht') ?>
</h1>

<form action="" method="get" class="form-row">
    <!-- <div class="input-group"> -->
    <!-- <div class="col-sm">
        <label for="dept-select"><?= lang('Department', 'Abteilung') ?></label>
        <select name="dept" id="dept-select" class="form-control ">
            <option value=""><?= lang('All departments', 'Alle Abteilungen') ?></option>
            <?php foreach ($departments as $dept => $vals) { ?>
                <option value="<?= $dept ?>" <?= $dept == $filter_dept ? 'selected' : '' ?>><?= $vals['name'] ?></option>
            <?php } ?>
        </select>
    </div> -->
    <div class="col-sm" style="max-width: 40rem;">
        <label for="year-input"><?= lang('Year', 'Jahr') ?></label>
        <input type="number" name="year" id="year-input" class="form-control " value="<?= empty($filter_year) ? '' : $filter_year ?>">
    </div>
    <div class="col-sm ml-sm-10" style="max-width: 40rem;">
        <label for="type-select"><?= lang('Activities', 'Aktivitäten') ?></label>
        <select name="type" id="type-select" class="form-control ">
            <option value=""><?= lang('All types', 'Alle Arten') ?></option>
            <?php foreach ([
                'publication',
                'poster',
                'lecture',
                'review',
                'misc',
                'students',
                'teaching',
                'software',
            ] as $type) { ?>
                <option value="<?= $type ?>" <?= $type == $filter_type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-sm ml-sm-10 align-self-end">

        <button class="btn btn-primary " type="submit">Select</button>
    </div>
    <!-- </div> -->
</form>

<p>
    <?= $N ?> <?= lang('results', 'Ergebnisse') ?>
</p>

<div id="flare" class="d-flex" style="max-width: 80rem"></div>
<!-- <svg id="legend" height=300 width=450></svg> -->
<!-- <div id="flare-info-div" class="tile h-auto" style="display: none;"></div> -->


<!-- <script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script> -->
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="<?= ROOTPATH ?>/js/popover.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-sunburst.js"></script>
<script>
    var DEPTS = JSON.parse('<?= json_encode($departments) ?>')
    var flare = '<?= json_encode($flare) ?>'
    data = {
        name: AFFILIATION,
        children: JSON.parse(flare)
    };

    chart('#flare', data);
</script>