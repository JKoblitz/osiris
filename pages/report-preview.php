<?php

/**
 * This is the preview for the report builder
 */
// markdown support
require_once BASEPATH . "/php/Report.php";

$Report = new Report($report);

$year = $_GET['year'] ?? CURRENTYEAR - 1;

echo "<h1>" . lang('Preview: ', 'Vorschau: ') . $report['title'] . "</h1>";
echo "<p>" . lang('For start year ', 'Für das Startjahr ') . $year . "</p>";
echo "<hr/ >";

$Report->setYear($year);

// echo $Report->formatChart();

echo "<div class='box'>";
echo "<div class='content'>";
echo $Report->getReport();
echo "</div>";
echo "</div>";
