<?php
require_once "init.php";

use Amenadiel\JpGraph\Graph;
use Amenadiel\JpGraph\Plot;

require_once "MyParsedown.php";

class Report
{
    public $report = array();
    public $steps = array();
    private $timefilter = ['year' => CURRENTYEAR - 1];
    private $startmonth = 1;
    private $endmonth = 12;
    private $startyear = CURRENTYEAR - 1;
    private $endyear = CURRENTYEAR - 1;

    public function __construct($report)
    {
        $this->report = $report;
        $this->steps = $report['steps'] ?? array();
    }

    public function setYear($year)
    {
        $startyear = $year;
        $endyear = $year;
        $startmonth = $this->report['start'] ?? 1;
        $duration = $this->report['duration'] ?? 12;
        $endmonth = $startmonth + $duration - 1;
        if ($endmonth > 12) {
            $endmonth -= 12;
            $endyear++;
        }

        $this->setTime($startyear, $endyear, $startmonth, $endmonth);
    }

    public function setTime($startyear, $endyear, $startmonth, $endmonth)
    {
        $this->startmonth = $startmonth;
        $this->endmonth = $endmonth;
        $this->startyear = $startyear;
        $this->endyear = $endyear;

        if ($this->startyear == $this->endyear) {
            $this->timefilter = [
                '$and' => [
                    ['year' => ['$eq' => $this->startyear]],
                    ['month' => ['$gte' => $this->startmonth]],
                    ['month' => ['$lte' => $this->endmonth]]
                ]
            ];
        } else {
            $this->timefilter = [
                '$or' => [
                    [
                        '$and' => [
                            ['year' => ['$eq' => $this->startyear]],
                            ['month' => ['$gte' => $this->startmonth]]
                        ]
                    ],
                    [
                        '$and' => [
                            ['year' => ['$eq' => $this->endyear]],
                            ['month' => ['$lte' => $this->endmonth]]
                        ]
                    ]
                ]
            ];
        }
    }

    public function getReport()
    {
        $html = "";
        $steps = $this->report['steps'] ?? array();
        foreach ($steps as $step) {
            $html .= $this->format($step);
        }
        return $html;
    }


    public function format($item)
    {
        switch ($item['type']) {
            case 'text':
                return $this->formatText($item);
            case 'activities':
                return $this->formatActivities($item);
            case 'table':
                return $this->formatTable($item);
            case 'line':
                return $this->formatLine($item);
            default:
                return '';
        }
    }

    /**
     * Retreive translated text elements
     *
     * @param array $item
     * @return string Formatted paragraph
     */
    public function getText($item)
    {
        $text = $item['text'] ?? '';
        if (empty($text)) return '';
        $Parsedown = new Parsedown();
        return $Parsedown->line($text);
    }

    /**
     * Format Text for HTML output.
     *
     * @param array $item
     * @return string formatted HTML
     */
    private function formatText($item)
    {
        $level = $item['level'] ?? 'p';
        $text = $this->getText($item);
        return "<$level>" . $text . "</$level>";
    }

    private function formatLine()
    {
        return '<hr />';
    }

    public function getActivities($item)
    {
        $filter = json_decode($item['filter'], true);
        $timelimit = $item['timelimit'] ?? false;

        // add time limit filter
        if ($timelimit)
            $filter = array_merge_recursive($this->timefilter, $filter);

        // sort by type, year, month
        $options = ['sort' => ["type" => 1, "year" => 1, "month" => 1]];

        $DB = new DB();
        $data = $DB->db->activities->find($filter, $options);

        return array_map(function ($item) {
            return strip_tags($item['rendered']['print']);
        }, $data->toArray());
    }

    private function formatActivities($item)
    {
        $data = $this->getActivities($item);
        $html = "";
        foreach ($data as $activity) {
            $html .= "<p>" . $activity . "</p>";
        }
        return $html;
    }

    /**
     * Get all data for the table based on step
     *
     * @param array $item
     * @return array Table rows as array with head being index 0
     */
    public function getTable($item)
    {
        $filter = json_decode($item['filter'], true);
        $timelimit = $item['timelimit'] ?? false;
        $group = $item['aggregate'] ?? '';
        $group2 = $item['aggregate2'] ?? null;

        if ($timelimit)
            $filter = array_merge_recursive($this->timefilter, $filter);

        $DB = new DB();
        $aggregate = [
            ['$match' => $filter],
        ];
        if (strpos($group, 'authors') !== false) {
            $aggregate[] = ['$unwind' => '$authors'];
        }
        if (empty($group2)) {
            $aggregate[] =
                ['$group' => ['_id' => '$' . $group, 'count' => ['$sum' => 1]]];
        } else {
            $aggregate[] =
                ['$group' => ['_id' => ['$' . $group, '$' . $group2], 'count' => ['$sum' => 1]]];
        }
        $aggregate[] = ['$sort' => ['count' => -1]];
        $aggregate[] = ['$project' => ['_id' => 0, 'activity' => '$_id', 'count' => 1]];
        $aggregate[] = ['$sort' => ['count' => -1]];
        $aggregate[] = ['$project' => ['_id' => 0, 'activity' => 1, 'count' => 1]];

        $data = $DB->db->activities->aggregate(
            $aggregate
        )->toArray();

        $table = [];

        if (empty($group2)) {
            $table[] = ['Activity', 'Count'];
            foreach ($data as $row) {
                $table[] = [$row['activity'], $row['count']];
            }
        } else {
            $activities = [];
            $header = [];
            foreach ($data as $row) {
                $g1 = $row['activity'][0];
                $g2 = $row['activity'][1];
                $activities[$g1][$g2] = $row['count'];
                if (!in_array($g2, $header)) {
                    $header[] = $g2;
                }
            }
            sort($header);
            ksort($activities);

            $table[] = array_merge(['Activity'], $header);
            
            foreach ($activities as $activity => $counts) {
                $row = [$activity];
                foreach ($header as $h) {
                    $row[] = $counts[$h] ?? 0;
                }
                $table[] = $row;
            }
        }
        return $table;
    }

    private function formatTable($item)
    {
        $result = $this->getTable($item);

        $html = "";
        if (count($result) > 0) {
            $html .= "<table class='table'>";
            $html .= "<thead><tr>";
            foreach ($result[0] as $h) {
                $html .= "<th>" . $h . "</th>";
            }
            $html .= "</tr></thead>";
            $html .= "<tbody>";
            foreach (array_slice($result, 1) as $row) {
                $html .= "<tr>";
                foreach ($row as $cell) {
                    $html .= "<td>" . $cell . "</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</tbody>";
            $html .= "</table>";
        }
        return $html;
    }

    public function formatChart()
    {
        // Create the Pie Graph.
        $graph = new Graph\PieGraph(350, 250);
        $graph->title->Set("A Simple Pie Plot");
        $graph->SetBox(true);

        $data = array(40, 21, 17, 14, 23);
        $p1   = new Plot\PiePlot($data);
        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetSliceColors(array('#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C', '#BA55D3'));

        $graph->Add($p1);
        $graph->Stroke();
    }
}
