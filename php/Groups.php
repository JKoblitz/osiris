<?php

/**
 * Class for all project associated methods.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @package OSIRIS
 * @since 1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

require_once "DB.php";
// require_once "Country.php";

class Groups
{
    public $groups = array();

    function __construct()
    {
        $osiris = new DB;

        $groups = $osiris->db->groups->find([])->toArray();
        foreach ($groups as $g) {
            $this->groups[$g['id']] = $g;
        }
    
    }

    public function getGroup($id){
        return $this->groups[$id] ?? [
            'id'=> '',
            'name' => 'Unknown Unit',
            'color'=> '#000000',
            'level' => -1,
            'unit' => 'Unknown'
        ];
    }
    public function getName($id){
        return $this->getGroup($id)['name'];
    }

    public function cssVar($id){
        $color = $this->getGroup($id)['color'];
        return "style=\"--department-color: $color;\"";
    }

    public function personDept($depts, $level = false){
        $result = ['level'=>0, 'name'=>'', 'id'=> ''];
        foreach ($depts as $id) {
            $dept = $this->getGroup($id);
            if ($dept['level'] === $level) return $dept;
            if ($dept['level'] > $result['level'])
                $result = $dept;  
        }
        return $result;
    }

    public function getHirarchy(){
        $groups = array_values($this->groups);
        return Groups::hirarchy($groups);
    }

    static function hirarchy($datas, $parent = 0, $depth=0){
        $ni=count($datas);
        if($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
        $tree = '<ul>';
        for($i=0; $i < $ni; $i++){
            if($datas[$i]['parent'] == $parent){
                $tree .= '<li>';
                $tree .= $datas[$i]['name'];
                $tree .= Groups::hirarchy($datas, $datas[$i]['id'], $depth+1);
                $tree .= '</li>';
            }
        }
        $tree .= '</ul>';
        return $tree;
    }
    
    public function getHirarchyTree(){
        $groups = array_values($this->groups);
        return Groups::hirarchyTree($groups);
    }

    static function hirarchyTree($datas, $parent = 0, $depth=0){
        $ni=count($datas);
        $tree = [];
        if($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
        for($i=0; $i < $ni; $i++){
            if($datas[$i]['parent'] == $parent){
                $element = $datas[$i]['name'];
                if ($depth > 0){
                    $element = str_repeat('-', $depth) . ' '. $element;
                }
                $tree[$datas[$i]['id']] = $element;
                $tree = array_merge($tree, Groups::hirarchyTree($datas, $datas[$i]['id'], $depth+1));
            }
        }
        return $tree;
    }
    
    
    public function getGroupList($id){
        $groups = [$id];
        $el = $this->getGroup($id);
        $i = 0;
        while (!empty($el['parent'] )) {
            $el = $this->getGroup($el['parent']);
            $groups[] = $el['id'];
            if ($i++ > 9) break;
        }
        return $groups;
    }
}