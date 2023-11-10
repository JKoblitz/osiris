<?php

class Settings
{
    public $settings = array();
    // private $user = array();
    public $roles = array();

    function __construct($user = array())
    {
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


        // dump($this->roles);
        // get default settings
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
    }

    function get($key)
    {
        $s = $this->settings;
        switch ($key) {
            case 'affiliation':
                return $s['affiliation']['id'] ?? '';
            case 'affiliation_details':
                return $s['affiliation'];
            case 'startyear':
                return intval($s['general']['startyear'] ?? 2020);
            case 'departments':
                return $s['departments'];
            case 'activities':
                return $s['activities'];
            case 'general':
                return $s['general'];
            case 'roles':
                return $s['roles']['roles'];
            case 'rights':
                return $s['roles']['rights'];
            case 'features':
                return $s['features'];

            default:
                return '';
                break;
        }
    }

    function hasPermission($right)
    {
        $rights = $this->settings['roles']['rights'][$right] ?? array();
        foreach ($this->roles as $role) {
            $index = array_search($role, $this->settings['roles']['roles']);
            if ($index === false) continue;
            if ($rights[$index] ?? false) return true;
        }
        return false;
    }

    function hasFeatureDisabled($feature)
    {
        return ($this->settings['general']['disable-' . $feature] ?? 'false') == 'true';
    }

    function featureActive($name){
        $f = $this->settings['features'][$name] ?? array();
        if (($f['active'] ?? 'false') == 'true') return true;
        return false;
    }

    function getActivities($type = null)
    {
        if ($type === null)
            return $this->settings['activities'];

        return $this->settings['activities'][$type] ?? [
            'name' => $type,
            'name_de' => $type,
            'color' => '#cccccc',
            'icon' => 'placeholder'
        ];
    }

    function title($type, $subtype = null)
    {
        $act = $this->getActivities($type);
        if ($subtype === null)
            return lang($act['name'], $act['name_de'] ?? $act['name']);

        foreach ($act['subtypes'] as $st) {
            if ($st['id'] == $subtype) {
                return lang($st['name'], $st['name_de'] ?? $st['name']);
            }
        }
    }

    function icon($type, $subtype = null, $tooltip = true)
    {
        $act = $this->getActivities($type);
        $icon = $act['icon'] ?? 'placeholder';

        if ($subtype !== null) {
            foreach ($act['subtypes'] as $st) {
                if ($st['id'] == $subtype) {
                    $icon = $st['icon'] ?? $icon;
                    break;
                }
            }
        }

        $icon = "<i class='ph text-$type ph-$icon'></i>";
        if ($tooltip) {
            $name = $this->title($type);
            return "<span data-toggle='tooltip' data-title='$name'>
                $icon
            </span>";
        }

        return $icon;
    }


    function getDepartments($dept = null)
    {
        if ($dept === null) return $this->settings['departments'];
        foreach ($this->settings['departments'] as $d) {
            if ($d['id'] == $dept) return $d;
        }
        return $this->settings['departments'][$dept] ?? [
            "color" => '#cccccc',
            'name' => $dept
        ];
    }

    function generateStyleSheet()
    {
        $style = "";

        foreach ($this->settings['departments'] as $val) {
            $style .= "
            .text-$val[id] {
                color: $val[color] !important;
            }
            .row-$val[id] {
                border-left: 3px solid $val[color] !important;
            }
            .badge-$val[id] {
                color:  $val[color] !important;
                border-color:  $val[color] !important;
            }
            ";
        }
        foreach ($this->settings['activities'] as $val) {
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
