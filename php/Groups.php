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
                    'color' => $data[$i]['color'] ?? '#000000',
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
        if ($unit !== null) 
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
        $color = $this->getGroup($id)['color'] ?? '#000000';
        return "style=\"--highlight-color: $color;\"";
    }

    public function personDept($depts, $level = false)
    {
        $result = ['level' => 0, 'name' => '', 'id' => ''];
        foreach ($depts as $d) {
            foreach ($this->getParents($d) as $id) {
                $dept = $this->getGroup($id);
                if (!isset($dept['level'])) $dept['level'] = $this->getLevel($id);
                if ($dept['level'] === $level) return $dept;
                if ($dept['level'] > $result['level'])
                    $result = $dept;
            }
        }
        return $result;
    }
    public function personDepts($depts)
    {
        $result = [];
        foreach ($depts as $d) {
            $p = $this->getParents($d);
            if ($p && $p[0] && !in_array($p[0], $result)) {
                $result[] = $p[0];
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

    public function getHierarchy()
    {
        $groups = array_values($this->groups);
        return Groups::hierarchyList($groups);
    }

    static function hierarchyList($datas, $parent = 0, $depth = 0)
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
                $tree .= Groups::hierarchyList($datas, $datas[$i]['id'], $depth + 1);
                $tree .= '</li>';
            }
        }
        $tree .= '</ul>';
        return $tree;
    }

    public function getHierarchyTree()
    {
        $groups = array_values($this->groups);
        return Groups::hierarchyTree($groups);
    }

    static function hierarchyTree($datas, $parent = 0, $depth = 0)
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
                $tree = array_merge($tree, Groups::hierarchyTree($datas, $datas[$i]['id'], $depth + 1));
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




     /**
     * Get the hierarchy tree for a given list of person units
     *
     * @param array $personUnits Liste der Einheiten, denen eine Person angehört
     * @return array Hierarchiebaum der Einheiten
     */
    public function getPersonHierarchyTree($personUnits) {
        $result = [];

        foreach ($personUnits as $unit) {
            $path = $this->findUnitPath($unit, $this->tree);
            if ($path) {
                $this->mergePaths($result, $path);
            }
        }

        return $result;
    }

    /**
     * Find the path to a specific unit within the hierarchy
     *
     * @param string $unit Die zu findende Einheit
     * @param array $hierarchy Der aktuelle Hierarchieknoten
     * @param array $currentPath Der bisherige Pfad
     * @return array|null Pfad zur Einheit oder null, wenn nicht gefunden
     */
    private function findUnitPath($unit, $hierarchy, $currentPath = []) {
        $newPath = array_merge($currentPath, [$hierarchy['id']]);

        if ($hierarchy['id'] === $unit) {
            return $newPath;
        }

        if (!empty($hierarchy['children'])) {
            foreach ($hierarchy['children'] as $child) {
                $path = $this->findUnitPath($unit, $child, $newPath);
                if ($path) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * Merge a path into the result tree
     *
     * @param array $result Referenz auf den Ergebnisbaum
     * @param array $path Der zu mergende Pfad
     */
    private function mergePaths(&$result, $path) {
        $current = &$result;
        foreach ($path as $node) {
            if (!isset($current[$node])) {
                $current[$node] = [];
            }
            $current = &$current[$node];
        }
    }

    /**
     * Print the hierarchy tree
     *
     * @param array $tree Der Hierarchiebaum
     * @param int $indent Die aktuelle Einrückungsebene
     */
    public function printPersonHierarchyTree($tree, $indent = 0) {
        foreach ($tree as $key => $subTree) {
            echo str_repeat("  ", $indent) . ($indent > 0 ? str_repeat(">", $indent) . " " : "") . "$key\n";
            if (!empty($subTree)) {
                $this->printPersonHierarchyTree($subTree, $indent + 1);
            }
        }
    }
     public function readableHierarchy($tree, $indent = 0) {
        $result = [];
        foreach ($tree as $key => $subTree) {
            $group = $this->getGroup($key);
            $unit = $this->getUnit($group['unit'] ?? null);
            $result[] = [
                'id' => $key, 
                'name_en' => $group['name'], 
                'name_de'=>($group['name_de']??null), 
                'unit_en' => $unit['name'],
                'unit_de' => $unit['name_de'],
                'indent' => $indent,
                'hasChildren' => !empty($subTree) ? true : false
            ];
            // $result[] = str_repeat("  ", $indent) . "$key <br>";
            if (!empty($subTree)) {
                $result = array_merge($result, $this->readableHierarchy($subTree, $indent + 1));
            }
        }
        return $result;
    }

    /**
     * Display the hierarchy tree for a person
     *
     * @param array $personUnits Liste der Einheiten, denen eine Person angehört
     */
    public function displayPersonHierarchy($personUnits) {
        $tree = $this->getPersonHierarchyTree($personUnits);
        $this->printPersonHierarchyTree($tree);
    }
}
