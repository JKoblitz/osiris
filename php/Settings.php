<?php

class Settings
{
    public $settings = array();
    public $affiliation = null;
    public $affiliation_details = array();
    public $startyear = 2017;
    public $departments = array();
    public $activities = array();
    public $errors = array();

    function __construct()
    {

        $file_name = BASEPATH . "/settings.json";
        if (!file_exists($file_name)) {
            $this->construct_default();
        } else {
            $this->construct($file_name);
        }
    }

    function construct_default()
    {
        $this->construct(BASEPATH . "/settings.default.json");
    }

    function construct($file_name)
    {
        $json = file_get_contents($file_name);

        $this->settings = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        if (!empty(json_last_error())) {
            $this->errors[] = "You have an error in your <code>settings.json</code>: ";
            $this->errors[] = (json_last_error_msg() . PHP_EOL);
            $this->construct_default();
            return;
        }
        if (empty($this->settings['affiliation'] ?? null) || empty($this->settings['affiliation']['id'] ?? null)) {
            $this->errors[] = ('Affiliation cannot be empty.');
            $this->construct_default();
            return;
        }
        $this->affiliation = $this->settings['affiliation']['id'];
        $this->affiliation_details = $this->settings['affiliation'];

        $this->startyear = intval($this->settings['startyear'] ?? 2017);

        if (empty($this->settings['departments'] ?? null)) {
            $this->errors[] = ('Departments cannot be empty.');
            $this->construct_default();
            return;
        }
        foreach ($this->settings['departments'] as $val) {
            if (!isset($val['id'])) {
                $this->errors[] = ('Each department need an ID.');
                $this->construct_default();
                return;
            }
            $this->departments[$val['id']] = $val;
        }

        if (empty($this->settings['activities'] ?? null)) {
            $this->errors[] = ('Activities cannot be empty.');
            $this->construct_default();
            return;
        }
        foreach ($this->settings['activities'] as $val) {
            if (!($val['display'] ?? true)) continue;
            if (!isset($val['id'])) {
                $this->errors[] = ('Each activitiy need an ID.');
                $this->construct_default();
                return;
            }
            if (!isset($val['subtypes'])) {
                $this->errors[] = ('Each activitiy need at least one subtype.');
                $this->construct_default();
                return;
            }
            $this->activities[$val['id']] = $val;
        }
    }

    function getActivities($type = null)
    {
        if ($type === null)
            return $this->activities;

        return $this->activities[$type] ?? [
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
        if ($dept === null) return $this->departments;
        return $this->departments[$dept] ?? [
            "color" => '#cccccc',
            'name' => $dept
        ];
    }

    function generateStyleSheet()
    {
        $style = "";

        foreach ($this->departments as $val) {
            $style .= "
            .text-$val[id] {
                color: $val[color];
            }
            .row-$val[id] {
                border-left: 3px solid $val[color];
            }
            .badge-$val[id] {
                color:  $val[color];
                border-color:  $val[color];
            }
            ";
        }
        foreach ($this->activities as $val) {
            $style .= "
            .text-$val[id] {
                color: $val[color];
            }
            .box-$val[id] {
                border-left: 4px solid  $val[color];
            }
            .badge-$val[id] {
                color:  $val[color];
                border-color:  $val[color];
            }
            ";
        }

        return "<style>$style</style>";
    }
}
