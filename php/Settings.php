<?php

require_once "DB.php";
include_once "Groups.php";

class Settings
{
    public $settings = array();
    // private $user = array();
    public $roles = array();
    private $osiris = null;
    private $features = array();

    public const FEATURES = ['coins', 'achievements', 'user-metrics', 'projects', 'guests'];

    function __construct($user = array())
    {
        // construct database object 
        $DB = new DB;
        $this->osiris = $DB->db;

        // set user roles
        if (isset($user['roles'])) {
            $this->roles = DB::doc2Arr($user['roles']);
        } else {
            foreach (['editor', 'admin', 'leader', 'controlling', 'scientist'] as $key) {
                if ($user['is_' . $key] ?? false) $this->roles[] = $key;
            }
        }
        // everyone is a user
        $this->roles[] = 'user';

        // get default settings from file
        $json = file_get_contents(BASEPATH . "/settings.default.json");
        $default = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        $this->settings = $default;

        // get custom settings
        $file_name = BASEPATH . "/settings.json";
        if (file_exists($file_name)) {
            $json = file_get_contents($file_name);
            $set = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
            // replace existing keys with new ones
            $this->settings = array_merge($this->settings, $set);
        }

        // init Features
        $featList = $this->osiris->adminFeatures->find([]);
        foreach ($featList as $f) {
            $this->features[$f['feature']] = boolval($f['enabled']);
        }
    }

    function get($key)
    {
        $s = $this->settings;
        switch ($key) {
            case 'affiliation':
            case 'affiliation_details':
                // return $s['affiliation']['id'] ?? '';
                $req = $this->osiris->adminGeneral->findOne(['key'=>'affiliation']);
                if ($key == 'affiliation') return $req['value']['id'] ?? '';
                return DB::doc2Arr($req['value'] ?? array());
            case 'startyear':
                $req = $this->osiris->adminGeneral->findOne(['key'=>'startyear']);
                return intval($req['value'] ?? 2020);
            case 'departments':
                dump("DEPARTMENTS sollten nicht mehr hierüber abgefragt werden.");
                return '';
            case 'activities':
                return $this->getActivities();
            // case 'general':
            //     return $s['general'];
            case 'features':
                return $this->features;
            default:
                $req = $this->osiris->adminGeneral->findOne(['key'=>$key]);
                if (!empty($req)) return $req['value'];
                return '';
                break;
        }
    }

    function printLogo($class=""){
        $logo = $this->osiris->adminGeneral->findOne(['key'=>'logo']);
        if (empty($logo)) return '';
        if ($logo['ext'] == 'svg'){ $logo['ext'] = 'svg+xml';}
            // return '<img src="data:svg;'.base64_encode($logo['value']).' " class="'.$class.'" />';

        // } else {
            return '<img src="data:image/'.$logo['ext'].';base64,'.base64_encode($logo['value']).' " class="'.$class.'" />';

        // }
    }
    /**
     * Checks if current user has a permission
     *
     * @param string $right
     * @return boolean
     */
    function hasPermission(string $right)
    {
        $permission = $this->osiris->adminRights->findOne([
            'role'=>['$in'=>$this->roles],
            'right'=>$right,
            'value'=>true
        ]);
        return !empty($permission);
    }

    /**
     * Check if feature is active
     *
     * @param string $feature
     * @return boolean
     */
    function featureEnabled($feature){
        return $this->features[$feature] ?? false;
        // $active = $this->osiris->adminFeatures->findOne([
        //     'feature'=>$feature
        // ]);
        // return boolval($active['enabled'] ?? false);
    }

    /**
     * Get Activity categories
     *
     * @param $type
     * @return array
     */
    function getActivities($type = null)
    {
        if ($type === null)
            return $this->osiris->adminCategories->find()->toArray();

        $arr = $this->osiris->adminCategories->findOne(['id'=>$type]);
        if (!empty($arr)) return DB::doc2Arr($arr);
        // default
        return [
            'name' => $type,
            'name_de' => $type,
            'color' => '#cccccc',
            'icon' => 'placeholder'
        ];
    }

    /**
     * Get Activity settings for cat and type
     *
     * @param string $cat
     * @param string $type
     * @return array
     */
    function getActivity($cat, $type = null){
        if ($type === null){
            $act = $this->osiris->adminCategories->findOne(['id'=>$cat]);
            return DB::doc2Arr($act);
        }

        $act = $this->osiris->adminTypes->findOne(['id'=>$type]);
        return DB::doc2Arr($act);
        
    }

    /**
     * Helper function to get the label of an activity type
     *
     * @param [type] $cat
     * @param [type] $type
     * @return string
     */
    function title($cat, $type = null)
    {
            $act = $this->getActivity($cat, $type);
            if (empty($act)) return 'unknown';
            return lang($act['name'], $act['name_de'] ?? $act['name']);        
    }

    /**
     * Helper function to get the icon of an activity type
     *
     * @param [type] $cat
     * @param [type] $type
     * @return string
     */
    function icon($cat, $type = null, $tooltip = true)
    {
        $act = $this->getActivity($cat, $type);
        $icon = $act['icon'] ?? 'placeholder';

        $icon = "<i class='ph text-$cat ph-$icon'></i>";
        if ($tooltip) {
            $name = $this->title($cat);
            return "<span data-toggle='tooltip' data-title='$name'>
                $icon
            </span>";
        }
        return $icon;
    }


    function generateStyleSheet()
    {
        $style = "";

        // foreach ($this->settings['departments'] as $val) {
        //     $style .= "
        //     .text-$val[id] {
        //         color: $val[color] !important;
        //     }
        //     .row-$val[id] {
        //         border-left: 3px solid $val[color] !important;
        //     }
        //     .badge-$val[id] {
        //         color:  $val[color] !important;
        //         background-color:  $val[color]20 !important;
        //     }
        //     ";
        // }
        foreach ($this->getActivities() as $val) {
            $style .= "
            .text-$val[id] {
                color: $val[color] !important;
            }
            .box-$val[id] {
                border-left: 4px solid $val[color] !important;
            }
            .badge-$val[id] {
                color:  $val[color] !important;
                border-color:  $val[color] !important;
            }
            ";
        }
        $style = preg_replace('/\s+/', ' ', $style);
        return "<style>$style</style>";
    }
}
