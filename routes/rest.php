<?php

/**
 * Routing for the Rest-API
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

function rest($data, $count = 0, $status = 200)
{
    $result = array();
    $limit = intval($_GET['limit'] ?? 0);

    if (!empty($limit) && $count > $limit && is_array($data)) {
        $offset = intval($_GET['offset'] ?? 0) || 0;
        $data = array_slice($data, $offset, min($limit, $count - $offset));
        $result += array(
            'limit' => $limit,
            'offset' => $offset
        );
    }
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
    if ($status == 200) {
        $result += array(
            'status' => 200,
            'count' => $count,
            'data' => $data
        );
    } elseif ($status == 400) {
        $result += array(
            'status' => 400,
            'count' => 0,
            'error' => 'WrongCall',
            'msg' => $data
        );
    } else {
        $result += array(
            'status' => $status,
            'count' => 0,
            'error' => 'DataNotFound',
            'msg' => $data
        );
    }
    return json_encode($result, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}


Route::get('/data/users', function () {
    $result = [];
    echo rest($result, count($result));
});

/**
 * @api {get} /media All media
 * @apiName GetAllMedia
 * @apiGroup Medium
 * 
 * @apiParam {String} apikey Your API key
 * @apiParam {String} [filter] Filter string from the advanced search
 *
 * @apiSampleRequest /download/publications
 * 
 * @apiSuccess {String} id Unique ID of the medium.
 * @apiSuccess {String} name  Name of the medium.
 * @apiSuccess {Boolean} complex_medium True if the medium is complex
 * @apiSuccess {String} source Collection where the medium originates from 
 * @apiSuccess {String} link Original URL
 * @apiSuccess {Float} min_pH Min. final pH
 * @apiSuccess {Float} max_pH Max final pH
 * @apiSuccess {String} reference URL for original reference (if available)
 * @apiSuccess {String} description Description or additional information (if available)
 * @apiSuccessExample {json} Example data:
 * {
        "id": "1",
        "name": "TEST"
    }
 */
Route::get('/data/activities', function () {
    include(BASEPATH . '/php/init.php');
    
    $filter = [];
    if (isset($_GET['filter'])){
        $filter = json_decode($_GET['filter'], true);
    }
    $options = ['sort'=>['year'=> -1, 'month'=> -1, 'day'=> -1]];

    $formatted = $_GET['formatted'] ?? true;
    if ($formatted){
        $options['projection'] = ['html'=> '$rendered.print', '_id'=> 0];
    }

    $result = $osiris->activities->find($filter, $options)->toArray();
    // if (isset($_GET['formatted']) && $_GET['formatted']) {
    //     include_once BASEPATH . "/php/Document.php";
    //     $table = [];
    //     $Format = new Document(true, 'web');

    //     foreach ($result as $doc) {
    //         $Format->setDocument($doc);
    //         $table[] = [
    //             'id' => strval($doc['_id']),
    //             'activity' => $Format->format(),
    //             'icon' => $Format->activity_icon()
    //         ];
    //     }

    //     $result = $table;
    // }
    echo rest($result, count($result));
});