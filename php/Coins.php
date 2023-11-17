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

    function activityCoins($doc, $user)
    {
        if (isset($doc['epub']) && $doc['epub']) return [
            'coins' => 0, 'comment' => 'Online ahead of print'
        ];

        $subtype = $doc['subtype'];
        $coins = $this->matrix[$subtype];

        $authors = $doc['authors']->bsonSerialize();
        $author = array_filter($authors, function ($author) use ($user) {
            return $author['user'] == $user;
        });

        if (empty($author)) return [
            'coins' => 0, 'comment' => 'User not author'
        ];
        $author = reset($author);
        $position = ($author['position'] ?? '');
        
        if (!($author['aoi']?? false)) return [
            'coins' => 0, 'comment' => 'User not affiliated'
        ];

        if (is_numeric($coins)) {
            $comment = "$coins for $subtype";
            if ($position == 'middle') {
                $coins /= 2;
                $comment .= " (middle author)";
            }
            return [
                'coins' => $coins, 'comment' => $comment
            ];
        }
        if (preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)\{(if|sws)\}/', $coins, $matches) !== FALSE) {
            $val = 0;
            $coins = $matches[1];
            $operator = $matches[2];
            $thingy = strtoupper($matches[3]);
            if ($thingy == 'IF'){
                $val = max($doc['impact'] ?? 0, 1);
            }
            if ($thingy == 'SWS'){
                $val = $author['sws'] ?? 0;
            }
            if ($position == 'middle') $coins /= 2;
            $c = ($coins) * $val;
            return [
                'coins' => $c, 'comment' => "$coins &times; $val ($thingy) "
            ];
        }
        return [
            'coins' => 0, 'comment' => 'Undefined coins'
        ];
    }

    function getCoins($user, $year = null)
    {
        $total = 0;
        foreach ($this->matrix as $subtype => $coins) {
            $filter = [
                'subtype' => $subtype,
                'authors' => ['$elemMatch' => ['user' => $user, 'aoi' => ['$in' => [true, 1, '1']]]],
                'epub' => ['$ne'=>true]
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
