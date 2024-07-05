<?php

/**
 * Class for all project associated methods.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @package OSIRIS
 * @since 1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

require_once "DB.php";

// require_once "Country.php";

class Groups
{
    public $groups = array();
    public $tree = array();
    private $osiris;
    private $UNITS = [
        'institute' => [
            'name' => 'Institute',
            'name_de' => 'Institut',
            'head' => 'Directorate',
            'head_de' => 'Direktorat',
        ],
        'department' => [
            'name' => 'Department',
            'name_de' => 'Abteilung',
            'head' => 'Head of Department',
            'head_de' => 'Abteilungsleitung',
        ],
        'group' => [
            'name' => 'Group',
            'name_de' => 'Gruppe',
            'head' => 'Head of Group',
            'head_de' => 'Arbeitsgruppenleitung',
        ],
        'unit' => [
            'name' => 'Unit',
            'name_de' => 'Einheit',
            'head' => 'Head of Unit',
            'head_de' => 'Leitung der Organisationseinheit',
        ]
    ];

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

    public function getUnit($unit = null, $key = null)
    {
        $unit = strtolower($unit);
        if (isset($this->UNITS[$unit])) {
            $info = $this->UNITS[$unit];
        } else {
            $info = $this->UNITS['unit'];
        }
        if ($key === null) return $info;

        if ($key == 'name')
            return lang($info['name'], $info['name_de']);
        if ($key == 'head')
            return lang($info['head'], $info['head_de']);
        return $info[$key] ?? '';
    }

    public function cssVar($id)
    {
        $color = $this->getGroup($id)['color'];
        return "style=\"--highlight-color: $color;\"";
    }

    public function personDept($depts, $level = false)
    {
        $result = ['level' => 0, 'name' => '', 'id' => ''];
        foreach ($depts as $d) {
            foreach ($this->getParents($d) as $id) {
                # code...
                $dept = $this->getGroup($id);
                if (!isset($dept['level'])) $dept['level'] = $this->getLevel($id);
                if ($dept['level'] === $level) return $dept;
                if ($dept['level'] > $result['level'])
                    $result = $dept;
            }
        }
        return $result;
    }

    public function editPermission($id, $user = null)
    {
        if ($user === null) $user = $_SESSION['username'];
        $edit_perm = false;
        // get all parent units
        $parents = $this->getParents($id);
        foreach ($parents as $p) {
            if ($p == $id) continue;
            $g = $this->getGroup($p);
            if (isset($g) && isset($g['head'])) {
                $head = $g['head'];
                if (is_string($head)) $head = [$head];
                else $head = DB::doc2Arr($head);
                if (in_array($_SESSION['username'], $head)) {
                    $edit_perm = true;
                    break;
                }
            }
        }
        return $edit_perm;
    }

    public function getDeptFromAuthors($authors)
    {
        $result = [];
        $authors = DB::doc2Arr($authors);
        if (empty($authors)) return [];
        $users = array_filter(array_column($authors, 'user'));
        foreach ($users as $user) {
            $user = $this->osiris->getPerson($user);
            if (empty($user) || empty($user['depts'])) continue;
            $dept = $this->personDept($user['depts'], 1)['id'];
            if (in_array($dept, $result)) continue;
            $result[] = $dept;
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


    public function getParents($id, $to0 = false)
    {
        $groups = [$id];
        $el = $this->getGroup($id);
        $i = 0;
        while (!empty($el['parent'])) {
            $el = $this->getGroup($el['parent']);
            if (!$to0 && $el['level'] == 0) break; // do not show institute
            $groups[] = $el['id'];
            if ($i++ > 9) break;
        }
        $groups = array_reverse($groups);
        return $groups;
    }

    public function getChildren($id, $only_id = true)
    {
        $el = Groups::findTreeNode($this->tree, $id);
        if (!$only_id)
            return $el;

        $ids = [];
        if ($el == null) return [];
        array_walk_recursive($el, function ($v, $k) use (&$ids) {
            if ($k == 'id') $ids[] = $v;
        });
        return $ids;
    }

    public function getLevel($id)
    {
        $group = $this->getGroup($id);
        $level = $group['level'] ?? null;
        if ($level === null) {
            $parents = $this->getParents($id);
            $level = count($parents);
        }
        return $level;
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
