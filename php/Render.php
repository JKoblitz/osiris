<?php
include_once 'init.php';
function renderActivities($filter = [])
    {
        global $Groups;
        $Format = new Document(true);
        $DB = new DB;
        $cursor = $DB->db->activities->find($filter);
        $rendered = [
            'print' => '',
            'web' => '',
            'depts' => '',
            'icon' => '',
            'type' => '',
            'start' => null,
            'end' => null,
        ];
        foreach ($cursor as $doc) {
            $id = $doc['_id'];
            $Format->setDocument($doc);
            $f = $Format->format();
            $rendered = [
                'print' => $f,
                'plain' => strip_tags($f),
                'web' => $Format->formatShort(),
                'depts' => $Groups->getDeptFromAuthors($doc['authors']),
                'icon' => trim($Format->activity_icon()),
                'type' => $Format->activity_type(),
                'subtype' => $Format->activity_subtype(),
                'start' => valueFromDateArray($doc['start'] ?? $doc),
                'end' => valueFromDateArray($doc['end'] ?? $doc['start'] ?? $doc),
                'title'=> $Format->getTitle(),
                'authors'=> $Format->getAuthors('authors')
            ];
            $values = ['rendered' => $rendered];

            if ($doc['type'] == 'publication' && isset($doc['journal'])) {
                // update impact if necessary
                $if = $DB->get_impact($doc);
                if (!empty($if) && (!isset($doc['impact']) || $if != $doc['impact'])) {
                    $values['impact'] = $if;
                }
            }

            $DB->db->activities->updateOne(
                ['_id' => $id],
                ['$set' => $values]
            );
        }
        // return last element in case that only one id has been rendered
        return $rendered;
    }