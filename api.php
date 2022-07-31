<?php
function return_rest($data, $count = 0, $status = 200)
{
    $result = array();
    $limit = intval($_GET['limit'] ?? 0);

    if (!empty($limit) && $count > $limit && is_array($data)) {
        $offset = intval($_GET['offset'] ?? 0) || 0;
        $data = array_slice($data, $offset, min($limit, $count-$offset));
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

/**
 * @apiDefine error404 Error 404
 */

/**
 * @apiDefine Medium Media endpoints
 *
 * The following endpoints consider media information.
 * You can request a list of all media, the whole medium recipe containing
 * all solutions, the molecular composition of a medium, or all strains
 * that grow on the medium.
 */

/**
 * @api {get} /media All media
 * @apiName GetAllMedia
 * @apiGroup Medium
 * 
 * @apiParam {Integer} [limit] Max. number of results
 * @apiParam {Integer} [offset] Offset of results
 *
 * @apiSampleRequest https://mediadive.dsmz.de/download/publications
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
 * [
    {
        "id": "119a",
        "name": "METHANOBREVIBACTER MEDIUM",
        "complex_medium": 1,
        "source": "DSMZ",
        "link": "https://www.dsmz.de/microorganisms/medium/pdf/DSMZ_Medium119a.pdf",
        "min_pH": 6.8,
        "max_pH": 7,
        "reference": null,
        "description": null
    },
    ...
]
 */
Route::get('/download/publications', function () {
    require_once BASEPATH . "/php/_config.php";
    $sql = "SELECT publication_id AS `id`, `title`, `journal`, `journal_abbr`, `year`, `month`, `day`, `issue`, `pages`, `volume`, `doi`, `pubmed`, `type`, `open_access`, `epub`, `correction`, `book`, `edition`, `city`, p.`publisher`
            FROM publication p
            LEFT JOIN journal USING (journal_id)
            ORDER BY q_id ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT publication_id, last_name, first_name, position, aoi, is_editor, user
            FROM authors
            WHERE publication_id IS NOT NULL";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $authors = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

    foreach ($result as $i => $res) {
        if (!isset($authors[$res['id']])) continue;
        $result[$i]['authors'] = $authors[$res['id']];
    }

    echo return_rest($result, count($result));
});

// Route::get('/download/users', function () {
//     require_once BASEPATH . "/php/_config.php";
//     $sql = "SELECT user AS `_id`, user AS `username`, first_name, last_name, academic_title, dept, orcid, is_admin, is_controlling, is_leader, is_scientist, is_active
//             FROM users
//             ORDER BY username ASC";
//     $stmt = $db->prepare($sql);
//     $stmt->execute();
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     $sql = "SELECT user, publication_id, position, is_editor
//             FROM authors
//             WHERE user IS NOT NULL AND publication_id IS NOT NULL";
//     $stmt = $db->prepare($sql);
//     $stmt->execute();
//     $authorships = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

//     foreach ($result as $i => $res) {
//         if (!isset($authorships[$res['username']])) continue;
//         $result[$i]['publications'] = $authorships[$res['username']];
//     }

//     echo return_rest($result, count($result));
// });

Route::get('/download/users/?([a-z0-9]*)', function ($id) {
    if (empty($id)) $id = "%";
    require_once BASEPATH . "/php/_config.php";
    $sql = "SELECT user AS `_id`, user AS `username`, first_name, last_name, academic_title, dept, orcid, is_admin, is_controlling, is_leader, is_scientist, is_active
            FROM users
            WHERE user LIKE ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT user, publication_id, poster_id, lecture_id, misc_id, teaching_id
            FROM authors
            WHERE user IS NOT NULL";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $authorships = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

    foreach ($result as $i => $res) {
        // if (!isset($authorships[$res['username']])) continue;
        $au = $authorships[$res['username']] ?? array();
        $result[$i]['publications'] = array_values(array_filter(array_column($au, 'publication_id')));
        $result[$i]['posters'] = array_values(array_filter(array_column($au, 'poster_id')));
        $result[$i]['lectures'] = array_values(array_filter(array_column($au, 'lecture_id')));
        $result[$i]['miscs'] = array_values(array_filter(array_column($au, 'misc_id')));
        $result[$i]['teachings'] = array_values(array_filter(array_column($au, 'teaching_id')));
    }

    echo return_rest($result, count($result));
});
