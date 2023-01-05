<?php
include_once BASEPATH . "/php/_lom.php";

class Achievement
{
    private $osiris;
    public $userac = [];
    public $achievements = [];
    private $username = null;
    private $userdata = [];
    public $new = [];
    public $lang = 'en';
    public $lang_g = 'en';
    private $self = false;


    function __construct($osiris)
    {
        $this->osiris = $osiris;
        $achievements = $this->osiris->achievements->find()->toArray();
        foreach ($achievements as $ac) {
            $this->achievements[$ac['id']] = $ac;
        }
        $this->lang = lang('en', 'de');
        $this->lang_g = $this->lang;
    }

    function initUser($username)
    {
        $this->username = $username;
        if ($username == ($_SESSION['username'] ?? '')) {
            $this->self = true;
        }
        $this->userdata = $this->osiris->users->findOne(['_id' => $username]);

        $achieved = $this->userdata['achievements'] ?? [];

        foreach ($achieved as $ac) {
            if (!isset($ac['id']) || !array_key_exists($ac['id'], $this->achievements)) continue;
            $ac['achievement'] = $this->achievements[$ac['id']];
            $this->userac[$ac['id']] = $ac;
        }
        if (($this->userdata['gender'] ?? 'n') == 'f') {
            $this->lang_g .= "_f";
        }
    }

    // function getLom(){
    //     if ($this->username === null) return false;
    //     $LOM = new LOM($this->username, $this->osiris);
    //     $lom = [
    //         'pub' => 0,
    //         'year' => 0,

    //     ];
    // }

    function checkAchievements()
    {
        if ($this->username === null) return false;
        $activities = $this->osiris->activities->find(['authors.user' => $this->username])->toArray();

        if (empty($activities)) return false;

        $types = array_column($activities, 'type');
        $types = array_count_values($types);

        $LOM = new LOM($this->username, $this->osiris);
        foreach ($activities as $i => $doc) {
            $l =  $LOM->lom($doc);
            $activities[$i]['coins'] = $l['lom'] ?? 0;
        }

        foreach ($this->achievements as $key => $achievement) {
            $user_ac = $this->userac[$key] ?? [];
            $user_lvl = $user_ac['level'] ?? 0;
            $new_lvl = $user_lvl;
            $max_lvl = $achievement['maxlvl'];

            $value = 0;
            if ($user_lvl < $max_lvl) {
                switch ($key) {
                    case 'create':
                        $value = $this->osiris->activities->count(['created_by' => $this->username]);
                        break;
                    case 'versatile':
                        $created = $this->osiris->activities->find(['created_by' => $this->username])->toArray();
                        $created_types = array_column($created, 'type');
                        $value = count(array_unique($created_types));
                        break;
                    case 'publications':
                        $value = $types['publication'] ?? 0;
                        break;
                    case 'lectures':
                        $value = $types['lecture'] ?? 0;
                        break;
                    case 'posters':
                        $value = $types['poster'] ?? 0;
                        break;
                    case 'reviews':
                        $value = $types['review'] ?? 0;
                        break;
                    case 'software':
                        $value = $types['software'] ?? 0;
                        break;
                    case 'profile-edit':
                        if (isset($this->userdata['updated_by']) && $this->userdata['updated_by'] == $this->username)
                            $value = 1;
                        break;
                    case 'coauthors':
                        $value = $this->osiris->activities->count([
                            'authors' => [
                                '$elemMatch' => [
                                    'user' => $this->username,
                                    'approved' => ['$in' => [true, 1, "1"]]
                                ]
                            ],
                            'created_by' => ['$ne' => $this->username]
                        ]);
                        break;

                    case 'theses':
                        foreach ($activities as $a) {
                            if ($a['type'] == "students" && isset($a['status']) && $a['status'] == 'completed') $value++;
                        }
                        break;
                    case 'network':
                        $authors = [];
                        foreach ($activities as $a) {
                            if (isset($a['authors'])){
                                foreach ($a['authors'] as $au) {
                                    if (!isset($au['first']) || !isset($au['last']) ||($au['user'] ?? '') == $this->username) continue;
                                    $name = strtolower($au['first']." ".$au['last']);
                                    if (!in_array($name, $authors)){
                                        $authors[] = $name;
                                        $value ++;
                                    }
                                }
                            }
                        }
                        break;
    
                    case 'year-coins':
                        $lom_years = [];
                        foreach ($activities as $a) {
                            if (!isset($lom_years[$a['year']])) $lom_years[$a['year']] = 0;
                            $lom_years[$a['year']] += $a['coins'];
                        }
                        $value = max($lom_years);
                        break;
                    case 'pub-impact':
                        $arr = array_column($activities, 'impact');
                        if (!empty($arr)) $value = max($arr);
                        break;
                    case 'pub-coins':
                        $array = array_filter($activities, function ($a) {
                            return $a['type'] == 'publication';
                        });
                        $value = max(array_column($array, 'coins'));
                        break;
                    case 'coins':
                        $value = array_sum(array_column($activities, 'coins'));
                        break;
                    case 'approved':
                        $approved = [];
                        if (!isset($this->userdata['approved'])) {
                            break;
                        }
                        foreach ($this->userdata['approved'] ?? array() as $a) {
                            $a = explode('Q', $a);
                            $y = $a[0];
                            if (!isset($approved[$y])) $approved[$y] = 0;
                            $approved[$y] += 1;
                        }
                        $value = max($approved);
                        break;
                    default:
                        $value = 0;
                        break;
                }
                // dump([$key, $value], true);

                // calculate new level
                $new_lvl = $user_lvl;
                foreach ($achievement['levels'] as $lvl) {
                    if ($lvl['level'] <= $new_lvl) continue;
                    if ($value < $lvl['step']) break;
                    $new_lvl = $lvl['level'];
                }
            }
            if ($new_lvl > $user_lvl) {
                $this->userac[$key] = [
                    'id' => $key, "level" => $new_lvl, "achieved" => date("d.m.Y")
                ];
                $this->new[] = $this->userac[$key];
            }
        }

        return true;
    }

    function icon($uac)
    {
        $ac = $this->achievements[$uac['id']] ?? array();

        $title = $ac['title'][$this->lang_g];

        $lvl = $uac['level'] ?? 1;
        if ($lvl == $ac['maxlvl']) $lvl = "max";
        $title .= " (Level&nbsp;$lvl)";

?>
        <a class="achievement lvl<?= $uac['level'] ?> max-<?= $ac['maxlvl'] ?? 4 ?>" href="<?= ROOTPATH ?>/achievements/<?= $this->username ?>#<?= $uac['id'] ?>">

            <span class="thumb-sm" data-toggle="tooltip" data-title="<?= $title ?>">
                <?php
                include BASEPATH . "/img/achievements/ac_$ac[icon].svg";
                ?>
            </span>

        </a>
    <?php
    }

    function snack($uac)
    {
        $ac = $this->achievements[$uac['id']] ?? array();
    ?>
        <div class="alert achievement mb-10 lvl<?= $uac['level'] ?> max-<?= $ac['maxlvl'] ?? 4 ?>">
            <button class="close" data-dismiss="alert" type="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="row">
                <div class="col flex-grow-0 thumb">
                    <?php
                    include BASEPATH . "/img/achievements/ac_$ac[icon].svg";
                    ?>
                </div>
                <div class="col">
                    <h3 class="">
                        <a class="link colorless" href="<?= ROOTPATH ?>/achievements/<?= $this->username ?>#<?= $uac['id'] ?>">
                            <?= $ac['title'][$this->lang_g] ?>
                        </a>
                    </h3>
                    <h5 class="d-inline-block">Level
                        <?php
                        $lvl = $uac['level'] ?? 1;
                        if ($lvl == $ac['maxlvl']) $lvl = "max";
                        echo $lvl;
                        ?>
                    </h5>
                    <small class="text-muted"><?= lang('achieved at', 'erlangt am') ?> <?= $uac['achieved'] ?? '-' ?></small>
                    <p class="m-0">
                        <?php
                        $descr = $ac['levels'][$uac['level'] - 1][$this->lang];
                        if ($this->self) {
                            $descr = str_replace(lang('have', 'hat'), lang('has', 'hast'), $descr);
                            echo str_replace('*', 'Du', $descr);
                        } else {
                            echo str_replace('*', $this->userdata['first'], $descr);
                        }
                        ?>
                    </p>

                </div>
            </div>
        </div>
<?php
    }

    function save()
    {
        // check if there is something to save
        if (!$this->self || empty($this->new) || empty($this->userac)) return;
        $values = array_values($this->userac);

        $this->osiris->users->updateOne(
            ['_id' => $this->username],
            ['$set' => ['achievements' => $values]]
        );
    }
}
