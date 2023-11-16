<?php

/**
 * Routes for OSIRIS Portal
 * Preview and API
 */

Route::get('/preview/(activity|person|project|group)/(.*)', function ($type, $id) {
    // display correct breadcrumb
    switch ($type) {
        case 'activity':
            $breadcrumb = [
                ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
                ['name' => $id, 'path' => "/activities/view/$id"],
            ];
            break;

        case 'person':
            $breadcrumb = [
                ['name' => lang('User', 'Nutzer:in'), 'path' => "/user/browse"],
                ['name' => $id, 'path' => "/profile/$id"],
            ];
            break;

        case 'project':
            $breadcrumb = [
                ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
                ['name' => $id, 'path' => "/projects/view/$id"],
            ];
            break;

        case 'group':
            $breadcrumb = [
                ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
                ['name' => $id, 'path' => "/projects/view/$id"],
            ];
            break;
        default:
            # code...
            break;
    }
    $breadcrumb[] = ['name' => lang("Preview", "Vorschau")];

    // important: NO database connection
    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/portal/preview.php";
    include BASEPATH . "/footer.php";
});



Route::get('/portal/activity/(.*)', function ($id) {
    include BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";
    $doc = $DB->getActivity($id);
    if (empty($doc)) {
        echo "Activity does not exist.";
        die;
    }
    include BASEPATH . "/addons/portal/activity.php";
});


Route::get('/portal/group/(.*)', function ($id) {
    include BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";
    if (DB::is_ObjectID($id)) {
        $mongo_id = $DB->to_ObjectID($id);
        $group = $osiris->groups->findOne(['_id' => $mongo_id]);
        $id = $group['id'];
    } else {
        $group = $osiris->groups->findOne(['id' => $id]);
    }
    if (empty($group)) {
        echo "Group does not exist.";
        die;
    }
    include BASEPATH . "/addons/portal/group.php";
});

Route::get('/portal/person/(.*)', function ($user) {
    include BASEPATH . "/php/init.php";

    $scientist = $DB->getPerson($user);
    if (empty($scientist)) {
        echo "Person does not exist.";
        die;
    }
    if (file_exists(BASEPATH . "/img/users/$user.jpg")) {
        $img = ROOTPATH . "/img/users/$user.jpg";
    } else {
        // standard picture
        $img = ROOTPATH . "/img/person.jpg";
    }
    include BASEPATH . "/addons/portal/person.php";
});

Route::get('/portal/project/(.*)', function ($id) {
    include BASEPATH . "/php/init.php";
    $mongo_id = $DB->to_ObjectID($id);
    $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    if (empty($project)) {
        echo "Project does not exist.";
        die;
    }
    if (!($project['public'] ?? true)) {
        die('Project is private.');
    }
    include BASEPATH . "/addons/portal/project.php";
});
