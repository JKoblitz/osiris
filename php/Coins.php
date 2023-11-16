<?php
include_once 'DB.php';
include_once 'Settings.php';

class Coins
{

    public $matrix = array();
    private $db = null;


    function __construct($user = null)
    {
        $db = new DB;
        $this->db = $db->db;
        $Settings = new Settings;

        $activities = $Settings->getActivities();

        foreach ($activities as $type => $typeArr) {
            foreach ($typeArr['subtypes'] as $subtype => $subArr) {
                if (is_numeric($subArr['coins']))
                    $subArr['coins'] = floatval($subArr['coins']);
                $this->matrix[$subtype] = $subArr['coins'];
            }
        }

    }

    function getCoins($user, $year = null)
    {
        $total = 0;
        foreach ($this->matrix as $subtype => $coins) {
            $filter = [
                'subtype' => $subtype,
                'authors' => ['$elemMatch' => ['user' => $user, 'aoi' => ['$in' => [true, 1, '1']]]]
            ];
            if ($year !== null)
                $filter['year'] = $year;
            if (is_numeric($coins)) {
                // just count the numbers and multiply
                $N = $this->db->activities->count($filter);
                if ($N == 0) continue;

                // check for middle authorships
                $middle = $this->db->activities->count([
                    'subtype' => $subtype,
                    'authors' => ['$elemMatch' => ['user' => $user, 'aoi' => true, 'position' => 'middle']]
                ]);
                $total += (($N - $middle) * $coins) + ($middle * $coins / 2);
            } else if (preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)\{(if|sws)\}/', $coins, $matches) !== FALSE) {
                $coins = $matches[1];
                $operator = $matches[2];
                $thingy = $matches[3];

                if ($thingy == 'if') {
                    $docs = $this->db->activities->aggregate([
                        ['$match' => $filter],
                        ['$project' => ['impact' => 1, 'authors' => 1]],
                        ['$unwind' => '$authors'],
                        ['$match' => ['authors.user' => $user]],
                        ['$project' => ['impact' => 1, 'authors.position' => 1]],
                        [
                            '$group' => [
                                '_id' => ['$toLower' => '$authors.position'],
                                'sum' => ['$sum' => '$impact']
                            ]
                        ]
                    ]);
                } else {
                    $docs = $this->db->activities->aggregate([
                        ['$match' => $filter],
                        ['$project' => ['authors' => 1]],
                        ['$unwind' => '$authors'],
                        ['$match' => ['authors.user' => $user]],
                        [
                            '$group' => [
                                '_id' => ['$toLower' => '$authors.user'],
                                'sum' => ['$sum' => ['$toDouble' => '$authors.sws']]
                            ]
                        ]
                    ]);
                }
                foreach ($docs as $val) {
                    $c = ($coins) * $val['sum'];
                    if ($val['_id'] == 'middle') $c /= 2;
                    $total += $c;
                }
            }
        }
        return round($total);
    }
}
