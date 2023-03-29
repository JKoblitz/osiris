<?php

class Settings
{
    public $settings = array();
    public $affiliation = null;
    public $affiliation_details = array();
    public $startyear = 2017;
    public $departments = array();
    public $activities = array();
    public $apis = array();

    function __construct()
    {
        $json = file_get_contents(BASEPATH . "/settings.json");
        $this->settings = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        if (!empty(json_last_error())) {
            echo "You have an error in your <code>settings.json</code>: ";
            die(json_last_error_msg() . PHP_EOL);
        }
        if (empty($this->settings['affiliation'] ?? null) || empty($this->settings['affiliation']['id'] ?? null)) {
            die('Error in Settings: affiliation cannot be empty.');
        }
        $this->affiliation = $this->settings['affiliation']['id'];
        $this->affiliation_details = $this->settings['affiliation'];

        $this->startyear = $this->settings['startyear'] ?? 2017;

        if (empty($this->settings['departments'] ?? null)) {
            die('Error in Settings: departments cannot be empty.');
        }
        foreach ($this->settings['departments'] as $val) {
            if (!isset($val['id'])) die('Error in settings: departments needs an ID.');
            $this->departments[$val['id']] = $val;
        }

        if (empty($this->settings['activities'] ?? null)) {
            die('Error in Settings: activities cannot be empty.');
        }
        foreach ($this->settings['activities'] as $val) {
            if (!isset($val['id'])) die('Error in settings: activities needs an ID.');
            $this->activities[$val['id']] = $val;
        }

        $this->apis = $this->settings['api'] ?? [];

    }


    function getActivities($type = null)
    {
        if ($type === null)
            return $this->activities;

        return $this->activities[$type] ?? [
            'name' => $type,
            'color' => '#cccccc',
            'icon' => 'notdef'
        ];
    }


    function getDepartments($dept = null)
    {
        if ($dept === null) return $this->departments;
        return $this->departments[$dept] ?? [
            "color" => '#cccccc',
            'name' => $dept
        ];
    }

    function generateStyleSheet(){
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
