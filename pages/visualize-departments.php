<?php

/**
 * Page to visualize department network
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /visualize/departments
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

function combinations($array)
{
    $results = array();
    foreach ($array as $a)
        foreach ($array as $b) {
            $t = [$a, $b];
            sort($t);
            if ($a == $b || in_array($t, $results)) continue;
            $results[] = $t;
        }
    return $results;
}

// select activities from database
$activities = $osiris->activities->find(['type' => 'publication']);
$activities = $activities->toArray();

// generate user dept array
$temp = $osiris->persons->find([], ['sort' => ["last" => 1]]);
$users = [];
foreach ($temp as $row) {
    $users[$row['username']] = $row['dept'];
}

// generate graph json
$combinations = [];
$labels = $Settings->getDepartments();

foreach ($labels as $key => $val) {
    $labels[$key]['count'] = 0;
    $labels[$key]['id'] = $key;
}

foreach ($activities as $doc) {
    $authors = [];
    foreach ($doc['authors'] as $aut) {
        if (!($aut['aoi'] ?? false) || empty($aut['user']) || !array_key_exists($aut['user'], $users)) continue;

        $id = $aut['user'];
        $dept = $users[$id];

        if (!empty($dept) && !in_array($dept, $authors)) {
            $labels[$dept]['count']++;
            $authors[] = $dept;
        }
    }
    if (count($authors) == 1)
        $combinations = array_merge($combinations, [[$authors[0], $authors[0]]]);
    else
        $combinations = array_merge($combinations, combinations($authors));
}

// remove depts without publications
$labels = array_filter($labels, function ($d) {
    return $d['count'] !== 0;
});

// add index (needed for following steps)
$i = 0;
foreach ($labels as $key => $val) {
    $labels[$key]['index'] = $i++;
}

// init matrix of n x n
$matrix = array_fill(0, count($labels), 0);
$matrix = array_fill(0, count($labels), $matrix);

// fill matrix based on all combinations
foreach ($combinations as $c) {
    $a = $labels[$c[0]]['index'];
    $b = $labels[$c[1]]['index'];

    $matrix[$a][$b] += 1;
    if ($a != $b)
        $matrix[$b][$a] += 1;
}
?>

<h1>
    <i class="ph ph-regular ph-graph" aria-hidden="true"></i>
    <?= lang('Department network', 'Abteilungs-Netzwerk') ?>
</h1>

<div id="chart" class="d-flex " style="max-width: 80rem"></div>

<script src="<?= ROOTPATH ?>/js/popover.js"></script>
<script src="<?= ROOTPATH ?>/js/d3.v4.min.js"></script>
<script src="<?= ROOTPATH ?>/js/d3-chords.js"></script>

<script>
    var matrix = JSON.parse('<?= json_encode($matrix) ?>')
    var labels = JSON.parse('<?= json_encode(array_column($labels, 'id')) ?>')
    var data = JSON.parse('<?= json_encode(array_values($labels)) ?>')
    var colors = JSON.parse('<?= json_encode(array_column($labels, 'color')) ?>')

    Chords('#chart', matrix, labels, colors, data, links=false, useGradient=true, highlightFirst=false);
</script>