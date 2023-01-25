<?php

class Settings
{

    private $json = null;
    public $affiliation = null;
    public $affiliation_details = array();
    public $startyear = 2017;
    public $departments = array();
    public $activities = array();
    public $apis = array();

    function __construct()
    {
        $json = file_get_contents(BASEPATH . "/settings.json");
        $this->json = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        if (!empty(json_last_error())) {
            echo "You have an error in your <code>settings.json</code>: ";
            die(json_last_error_msg() . PHP_EOL);
        }
        if (empty($this->json['affiliation'] ?? null) || empty($this->json['affiliation']['id'] ?? null)) {
            die('Error in Settings: affiliation cannot be empty.');
        }
        $this->affiliation = $this->json['affiliation']['id'];
        $this->affiliation_details = $this->json['affiliation'];

        $this->startyear = $this->json['startyear'] ?? 2017;

        if (empty($this->json['departments'] ?? null)) {
            die('Error in Settings: departments cannot be empty.');
        }
        foreach ($this->json['departments'] as $val) {
            if (!isset($val['id'])) die('Error in settings: departments needs an ID.');
            $this->departments[$val['id']] = $val;
        }

        if (empty($this->json['activities'] ?? null)) {
            die('Error in Settings: activities cannot be empty.');
        }
        foreach ($this->json['activities'] as $val) {
            if (!isset($val['id'])) die('Error in settings: activities needs an ID.');
            $this->activities[$val['id']] = $val;
        }

        $this->apis = $this->json['apis'] ?? [];

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
