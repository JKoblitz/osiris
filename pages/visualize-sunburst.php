<?php

/**
 * Page to visualize activities of users in a sunburst
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /visualize/sunburst
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$filter = [];
$filter_dept = $_GET['dept'] ?? '';
if (!empty($filter_dept) && $filter_dept != 'undefined') {

    $users = [];
    $cursor = $osiris->persons->find(['dept' => $filter_dept], ['projection' => ['username' => 1]]);

    foreach ($cursor as $u) {
        if (empty($u['username'] ?? '')) continue;
        $users[] = strtolower($u['username']);
    }
    $filter['authors.user'] = ['$in' => $users]; //, ['editors.user' => ['$in'=>$users]]];
}

$links = array();

$time = $_GET['time'] ?? '';
// 12,2017,12,2024
if (!empty($time) && $time != 'undefined') {
    if (!is_array($time)) {
        $time = explode(',', $time);
    }
    if (count($time) !== 4 || array_sum($time) <= 0) {
        // echo "Time is invalid.";
        $time = ['', '', '', ''];
    } else {
        $startyear = intval($time[1]);
        $endyear = intval($time[3]);
        $filter['year'] = ['$gte' => $startyear, '$lte' => $endyear];

        $links[] = 'time=' . implode(',', $time);
    }
} else {
    $time = ['', '', '', ''];
}

if (!isset($_GET['epub'])) {
    $filter['epub'] = ['$ne' => true];
}

$filter_year = intval($_GET['year'] ?? 0);
// if (!empty($filter_year) && $filter_year != 'undefined') {
//     $filter['year'] = $filter_year;
// }
$filter_type = $_GET['type'] ?? 'publication';
if (!empty($filter_type) && $filter_type != 'undefined') {
    $filter['type'] = $filter_type;
    $links[] = 'type=' . $filter_type;
}


$temp = $osiris->persons->find([], ['sort' => ["last" => 1]]);
$users = [];
foreach ($temp as $row) {
    $users[strval($row['username'])] = $row['dept'];
}

// generate graph json

$departments = [];
$flare = [];

$cursor = $osiris->activities->find($filter);

$activities = [];
if (count($time) === 4 && array_sum($time) > 0) {

    // get date and time
    $start = $time[1] . '-' . $time[0] . '-1';
    $end = $time[3] . '-' . $time[2] . '-1';
    $starttime = getDateTime($start . ' 00:00:00');
    $endtime = getDateTime(date("Y-m-t", strtotime($end)) . ' 23:59:59');
    // t returns the number of days in the month of a given date

    foreach ($cursor as $doc) {
        // check if time of activity ist in the correct time range
        $ds = getDateTime($doc['start'] ?? $doc);
        if (isset($doc['end']) && !empty($doc['end'])) $de = getDateTime($doc['end'] ?? $doc);
        else $de = $ds;
        if (($ds <= $starttime && $starttime <= $de) || ($starttime <= $ds && $ds <= $endtime)) {
        } else {
            continue;
        }
        $activities[] = $doc;
    }
} else {
    $activities = $cursor->toArray();
}


$index = 0;
$i = 0;
$departments = [];
foreach ($Settings->getDepartments() as $val) {
    $dept_id = $val['id'];
    $flare[$i] = [
        'name' => $dept_id,
        'abbr' => $dept_id,
        'children' => [],
    ];
    $departments[$dept_id] = $val;
    $departments[$dept_id]['index'] = $i++;
}


$N = 0;
$links = implode('&', $links);

foreach ($activities as $doc) {
    $count = false;
    foreach ($doc['authors'] as $aut) {
        if (!($aut['aoi'] ?? false) || empty($aut['user']) || !array_key_exists($aut['user'], $users)) continue;

        $username = $aut['user'];
        $dept = $users[$username];

        if (!empty($dept)) {
            $deptindex = $departments[$dept]['index'] ?? null;
            if (!isset($flare[$deptindex])) continue;
            $userindex = array_search($username, array_column($flare[$deptindex]['children'], 'abbr'));
            if ($userindex === false) {
                $flare[$deptindex]['children'][] = [
                    'name' => $aut['last'] . ", " . $aut['first'],
                    'abbr' => $username,
                    'value' => 0,
                    'link' => ROOTPATH . '/my-activities?user=' . $username . '#' . $links
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
    <i class="ph ph-graph" aria-hidden="true"></i>
    <?= lang('Department overview', 'Abteilungs-Übersicht') ?>
</h1>

<div class="dropdown">
    <button class="btn primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
        <i class="ph ph-funnel"></i>
        Filter
        <i class="ph ph-caret-down ml-5" aria-hidden="true"></i>
    </button>
    <div class="dropdown-menu w-400 " aria-labelledby="dropdown-1">
        <form action="" method="get" class="content">


            <div class="form-group">
                <label for="type-select"><?= lang('Time range', 'Zeitspanne') ?></label>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= lang('From', 'Von') ?></span>
                    </div>
                    <input type="number" name="time[]" class="form-control" placeholder="month" min="1" max="12" step="1" id="from-month" value="<?= $time[0] ?? '' ?>">
                    <input type="number" name="time[]" class="form-control" placeholder="year" min="2000" max="<?= CURRENTYEAR + 1 ?>" step="1" id="from-year" value="<?= $time[1] ?? '' ?>">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= lang('to', 'bis') ?></span>
                    </div>
                    <input type="number" name="time[]" class="form-control" placeholder="month" min="1" max="12" step="1" id="to-month" value="<?= $time[2] ?? '' ?>">
                    <input type="number" name="time[]" class="form-control" placeholder="year" min="2000" max="<?= CURRENTYEAR + 1 ?>" step="1" id="to-year" value="<?= $time[3] ?? '' ?>">

                    <div class="input-group-append">
                        <a class="btn" type="button" href="<?= currentGET(['time']) ?>">&times;</a>
                    </div>
                </div>

            </div>
            <div class="form-group">
                <label for="type-select"><?= lang('Activities', 'Aktivitäten') ?></label>
                <select name="type" id="type-select" class="form-control ">
                    <option value=""><?= lang('All types', 'Alle Arten') ?></option>
                    <?php foreach ($Settings->get('activities') as $type => $a) { ?>
                        <option value="<?= $type ?>" <?= $type == $filter_type ? 'selected' : '' ?>><?= lang($a['name'], $a['name_de'] ?? $a['name']) ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <div class="custom-checkbox">
                    <input type="checkbox" id="epub" value="1" name="epub" <?=isset($_GET['epub']) ? 'checked': '' ?>>
                    <label for="epub"><?= lang('Include online ahead of print', 'Inklusive Online ahead of print') ?></label>
                </div>
            </div>

            <button class="btn primary " type="submit">Select</button>
        </form>
    </div>
</div>


<!-- </div> -->

<p>
    <?= $N ?> <?= lang('results', 'Ergebnisse') ?>
</p>

<div id="flare" class="d-flex" style="max-width: 80rem"></div>
<!-- <svg id="legend" height=300 width=450></svg> -->
<!-- <div id="flare-info-div" class="tile h-auto" style="display: none;"></div> -->


<!-- <script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script> -->
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="<?= ROOTPATH ?>/js/popover.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-sunburst.js?v=2"></script>
<script>
    var DEPTS = JSON.parse('<?= json_encode($departments) ?>')
    // var depts = {}
    // DEPTS.forEach((d)=>{
    //     depts[d.id] = d;
    // })
    var flare = '<?= json_encode($flare) ?>'
    data = {
        name: AFFILIATION,
        children: JSON.parse(flare)
    };

    chart('#flare', data);
</script>