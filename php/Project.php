<?php

/**
 * Class for all project associated methods.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @package OSIRIS
 * @since 1.2.2
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

require_once "DB.php";

class Project
{
    public $project = array();

    function __construct($project = null)
    {
        if ($project !== null)
            $this->project = $project;
    }

    public function setProject($project)
    {
        $this->project = $project;
    }

    public function getStatus()
    {
        switch ($this->project['status'] ?? '') {
            case 'applied':
                return "<span class='badge signal'>" . lang('applied', 'beantragt') . "</span>";
            case 'approved':
                if ($this->inPast())
                    return "<span class='badge dark'>" . lang('expired', 'abgelaufen') . "</span>";
                return "<span class='badge success'>" . lang('approved', 'bewilligt') . "</span>";
            case 'rejected':
                return "<span class='badge danger'>" . lang('rejected', 'abgelehnt') . "</span>";
            default:
                return "<span class='badge'>-</span>";
        }
    }

    public function getRole()
    {
        if (($this->project['role'] ?? '') == 'coordinator') {
            return "<span class='badge'>" . '<i class="ph ph-crown text-signal"></i> ' . lang('Coordinator', 'Koordinator') . "</span>";
        }
        return "<span class='badge'>" . '<i class="ph ph-handshake text-muted"></i> ' . lang('Partner') . "</span>";
    }

    /**
     * Convert MongoDB document to array.
     *
     * @param $doc MongoDB Document.
     * @return array Document array.
     */
    public function getDateRange()
    {
        $start = sprintf('%02d', $this->project['start']['month']) . "/" . $this->project['start']['year'];
        $end = sprintf('%02d', $this->project['end']['month']) . "/" . $this->project['end']['year'];
        return "$start - $end";
    }

    function inPast()
    {
        $end = new DateTime();
        $end->setDate(
            $this->project['end']['year'],
            $this->project['end']['month'] ?? 1,
            $this->project['end']['day'] ?? 1
        );
        $today = new DateTime();
        if ($end < $today) return true;
        return false;
    }

    public static function personRole($role, $gender='n')
    {
        switch ($role) {
            case 'PI':
                return lang('Project lead', 'Projektleitung');
            case 'worker':
                return lang('Project member', 'Projektmitarbeiter');
            default:
                return lang('Associate', 'Beteiligte Person');
        }
    }

    public function widgetSmall()
    {
        $widget = '<a class="module '.($this->inPast() ? 'inactive': '').'" href="' . ROOTPATH . '/projects/view/' . $this->project['_id'] . '">';
        $widget .= '<h5 class="m-0">' . $this->project['name'] . '</h5>';
        $widget .= '<small class="d-block text-muted mb-5">' . $this->project['title'] . '</small>';
        $widget .= '<span class="float-right text-muted">' . $this->project['funder'] . '</span>';
        $widget .= '<span class="text-muted">' . $this->getDateRange() . '</span>';
        $widget .= '</a>';
        return $widget;
    }


    public function widgetLarge($user=null)
    {
        $widget = '<a class="module '.($this->inPast() ? 'inactive': '').'" href="' . ROOTPATH . '/projects/view/' . $this->project['_id'] . '">';

        $widget .= '<span class="float-right">' . $this->getDateRange() . '</span>';
        $widget .= '<h5 class="m-0">' . $this->project['name'] . '</h5>';
        $widget .= '<small class="d-block text-muted mb-5">' . $this->project['title'] . '</small>';

        if ($user === null)
            $widget .= '<span class="float-right">' . $this->getRole() . '</span> ';
        else {
            $userrole = '';
            foreach ($this->project['persons'] as $p) {
                if ($p['user'] == $user){
                    $userrole = $p['role'];
                    break;
                }
            }
            $widget .= '<span class="float-right badge">' . $this->personRole($userrole) . '</span> ';
        }
        $widget .= '<span class="mr-10">' . $this->getStatus() . '</span> ';
        $widget .= '<span class="mr-10">' . $this->project['funder'];
        if (!empty($this->project['funding_number'])) {
            $widget .= " (" . $this->project['funding_number'] . ")";
        }
        $widget .=  '</span>';
        $widget .= '</a>';
        return $widget;
    }
}
