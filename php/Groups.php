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
    public $tree = array();
    private $osiris;

    function __construct()
    {
        $this->osiris = new DB;

        $groups = $this->osiris->db->groups->find([])->toArray();
        foreach ($groups as $g) {
            $this->groups[$g['id']] = $g;
        }

        $g = array_values($this->groups);
        $this->tree = $this->tree($g)[0];
    }


    private function tree($data, $parent = 0, $depth = 0)
    {
        $ni = count($data);
        if ($ni === 0 || $depth > 100) return ''; // Make sure not to have an endless recursion
        $tree = [];
        for ($i = 0; $i < $ni; $i++) {
            if ($data[$i]['parent'] == $parent) {
                $tree[] = [
                    'id' => $data[$i]['id'],
                    'name' => $data[$i]['name'],
                    'unit' => $data[$i]['unit'],
                    'color' => $data[$i]['color'],
                    'level' => $depth,
                    // 'head' => $v,
                    'children' => $this->tree($data, $data[$i]['id'], $depth + 1)
                ];
            }
        }
        return $tree;
    }


    public function getGroup($id)
    {
        $group = $this->groups[$id] ?? [
            'id' => '',
            'name' => 'Unknown Unit',
            'color' => '#000000',
            'level' => -1,
            'unit' => 'Unknown',
            'head' => []
        ];
        if (isset($group['head'])) {
            $head = $group['head'];
            if (is_string($head)) $group['head'] = [$head];
            else $group['head'] = DB::doc2Arr($head);
        }

        return $group;
    }


    public function getName($id)
    {
        return $this->getGroup($id)['name'];
    }

    public function cssVar($id)
    {
        $color = $this->getGroup($id)['color'];
        return "style=\"--department-color: $color;\"";
    }

    public function personDept($depts, $level = false)
    {
        $result = ['level' => 0, 'name' => '', 'id' => ''];
        foreach ($depts as $id) {
            $dept = $this->getGroup($id);
            if ($dept['level'] === $level) return $dept;
            if ($dept['level'] > $result['level'])
                $result = $dept;
        }
        return $result;
    }

    public function getHirarchy()
    {
        $groups = array_values($this->groups);
        return Groups::hirarchyList($groups);
    }

    static function hirarchyList($datas, $parent = 0, $depth = 0)
    {
        $ni = count($datas);
        if ($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
        $tree = '<ul class="list">';
        for ($i = 0; $i < $ni; $i++) {
            if ($datas[$i]['parent'] == $parent) {
                $tree .= '<li>';
                $tree .= "<a class='colorless' href='" . ROOTPATH . "/groups/view/" . $datas[$i]['id'] . "' >";
                $tree .= $datas[$i]['name'];
                $tree .= "</a>";
                $tree .= Groups::hirarchyList($datas, $datas[$i]['id'], $depth + 1);
                $tree .= '</li>';
            }
        }
        $tree .= '</ul>';
        return $tree;
    }

    public function getHirarchyTree()
    {
        $groups = array_values($this->groups);
        return Groups::hirarchyTree($groups);
    }

    static function hirarchyTree($datas, $parent = 0, $depth = 0)
    {
        $ni = count($datas);
        $tree = [];
        if ($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
        for ($i = 0; $i < $ni; $i++) {
            if ($datas[$i]['parent'] == $parent) {
                $element = $datas[$i]['name'];
                if ($depth > 0) {
                    $element = str_repeat('-', $depth) . ' ' . $element;
                }
                $tree[$datas[$i]['id']] = $element;
                $tree = array_merge($tree, Groups::hirarchyTree($datas, $datas[$i]['id'], $depth + 1));
            }
        }
        return $tree;
    }


    public function getParents($id)
    {
        $groups = [$id];
        $el = $this->getGroup($id);
        $i = 0;
        while (!empty($el['parent'])) {
            $el = $this->getGroup($el['parent']);
            $groups[] = $el['id'];
            if ($i++ > 9) break;
        }
        return $groups;
    }

    public function getChildren($id, $only_id = true)
    {
        $el = Groups::findTreeNode($this->tree, $id);
        if (!$only_id)
            return $el;

        $ids = [];
        array_walk_recursive($el, function ($v, $k) use (&$ids) {
            if ($k == 'id') $ids[] = $v;
        });
        return $ids;
    }

    private function findTreeNode($array, $find)
    {
        if ($array['id'] == $find) {
            return $array;
        }

        if (empty($array['children'])) {
            return null;
        }

        foreach ($array['children'] as $child) {
            $result = $this->findTreeNode($child, $find);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }
}
