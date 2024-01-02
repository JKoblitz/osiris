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

class Categories
{
    public $all = array();
    public $categories = array();
    public $types = array();
    public $tree = array();
    private $osiris;

    function __construct()
    {
        $this->osiris = new DB;

        $categories = $this->osiris->db->categories->find()->toArray();
        foreach ($categories as $c) {
            $this->categories[$c['id']] = $c;
        }
        // $this->types = (DB::doc2Arr(array_column($this->categories, 'children')));

        // $g = ($this->all);
        // $this->tree = $this->categories; //$this->tree($g);
    }


    private function tree($data, $parent = 0, $depth = 0)
    {
        $ni = count($data);
        if ($ni === 0 || $depth > 100) return ''; // Make sure not to have an endless recursion
        $tree = [];
        for ($i = 0; $i < $ni; $i++) {
            if ($data[$i]['parent'] == $parent) {
                $el = DB::doc2Arr($data[$i]);
                $tree[] = array_merge($el,[
                    'level' => $depth,
                    // 'head' => $v,
                    'children' => $this->tree($data, $data[$i]['id'], $depth + 1)
                ]);
            }
        }
        return $tree;
    }


    public function getCategory($id)
    {
        $category = $this->categories[$id] ?? [
            'id' => '',
            'name' => 'Unknown Unit',
            'color' => '#000000',
            'level' => -1,
            'unit' => 'Unknown',
            'head' => []
        ];
        if (isset($category['head'])) {
            $head = $category['head'];
            if (is_string($head)) $category['head'] = [$head];
            else $category['head'] = DB::doc2Arr($head);
        }

        return $category;
    }


    public function getName($id)
    {
        return $this->getCategory($id)['name'];
    }

    public function cssVar($id)
    {
        $color = $this->getCategory($id)['color'];
        return "style=\"--highlight-color: $color;--highlight-color-20: ".$color."20;\"";
    }

    // public function personDept($depts, $level = false)
    // {
    //     $result = ['level' => 0, 'name' => '', 'id' => ''];
    //     foreach ($depts as $d) {
    //         foreach ($this->getParents($d) as $id) {
    //             # code...
    //             $dept = $this->getCategory($id);
    //             if (!isset($dept['level'])) $dept['level'] = $this->getLevel($id);
    //             if ($dept['level'] === $level) return $dept;
    //             if ($dept['level'] > $result['level'])
    //                 $result = $dept;
    //         }
    //     }
    //     return $result;
    // }


    // public function getDeptFromAuthors($authors)
    // {
    //     $result = [];
    //     $authors = DB::doc2Arr($authors);
    //     if (empty($authors)) return [];
    //     $users = array_filter(array_column($authors, 'user'));
    //     foreach ($users as $user) {
    //         $user = $this->osiris->getPerson($user);
    //         if (empty($user) || empty($user['depts'])) continue;
    //         $dept = $this->personDept($user['depts'], 1)['id'];
    //         if (in_array($dept, $result)) continue;
    //         $result[] = $dept;
    //     }
    //     return $result;
    // }

    // public function getHirarchy()
    // {
    //     $categories = array_values($this->categories);
    //     return Categories::hirarchyList($categories);
    // }

    // static function hirarchyList($datas, $parent = 0, $depth = 0)
    // {
    //     $ni = count($datas);
    //     if ($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
    //     $tree = '<ul class="list">';
    //     for ($i = 0; $i < $ni; $i++) {
    //         if ($datas[$i]['parent'] == $parent) {
    //             $tree .= '<li>';
    //             $tree .= "<a class='colorless' href='" . ROOTPATH . "/categories/view/" . $datas[$i]['id'] . "' >";
    //             $tree .= $datas[$i]['name'];
    //             $tree .= "</a>";
    //             $tree .= Categories::hirarchyList($datas, $datas[$i]['id'], $depth + 1);
    //             $tree .= '</li>';
    //         }
    //     }
    //     $tree .= '</ul>';
    //     return $tree;
    // }

    // public function getHirarchyTree()
    // {
    //     $categories = array_values($this->categories);
    //     return Categories::hirarchyTree($categories);
    // }

    // static function hirarchyTree($datas, $parent = 0, $depth = 0)
    // {
    //     $ni = count($datas);
    //     $tree = [];
    //     if ($ni === 0 || $depth > 1000) return ''; // Make sure not to have an endless recursion
    //     for ($i = 0; $i < $ni; $i++) {
    //         if ($datas[$i]['parent'] == $parent) {
    //             $element = $datas[$i]['name'];
    //             if ($depth > 0) {
    //                 $element = str_repeat('-', $depth) . ' ' . $element;
    //             }
    //             $tree[$datas[$i]['id']] = $element;
    //             $tree = array_merge($tree, Categories::hirarchyTree($datas, $datas[$i]['id'], $depth + 1));
    //         }
    //     }
    //     return $tree;
    // }


    public function getParents($id, $to0 = false)
    {
        $categories = [$id];
        $el = $this->getCategory($id);
        $i = 0;
        while (!empty($el['parent'])) {
            $el = $this->getCategory($el['parent']);
            if (!$to0 && $el['level'] == 0) break; // do not show institute
            $categories[] = $el['id'];
            if ($i++ > 9) break;
        }
        $categories = array_reverse($categories);
        return $categories;
    }

    public function getChildren($id, $only_id = true)
    {
        $el = Categories::findTreeNode($this->tree, $id);
        if (!$only_id)
            return $el;

        $ids = [];
        array_walk_recursive($el, function ($v, $k) use (&$ids) {
            if ($k == 'id') $ids[] = $v;
        });
        return $ids;
    }

    public function getLevel($id)
    {
        $category = $this->getCategory($id);
        $level = $category['level'] ?? null;
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
