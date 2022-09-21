<?php 

function get_dept($unit){
    $translate = [
        "Bioinformatik und Datenbanken" => "BI",
        "Bioinformatik" => "BI",
        "IT Systemadministrator" => "IT",
        "Mios" => "MIOS",
        "Mutz" => "MuTZ",
        "MUTZ" => "MuTZ",
        "Verwaltung - Administrative Geschäftsführung" => "Verwaltung",
        "Moed" => "MÖD",
        "MOED" => "MÖD",
        "AG-Sproer" => "BI",
        "MIOS-AG Pester" => "MIOS",
        "BUG Phagen" => "BUG",
        "BUG Pilze" => "BUG",
        "Direktor" => "MÖD",
        "Scientific Computing" => "IT",
        "NWG Virusinteract" => "NFG",
        "NWG VirusInteract" => "NFG",
        "NFG Biotechnologie" => "NFG",
        "NFG Bakterielle Metabolomik" => "NFG",
        "NWG Mikrobielle Biotechnologie"=> "NFG",
        "Pflanzenviren" => "PFVI",
        "ROSEO" => "MÖD",
        "Plant" => "PFVI",
        "MOED - Direktor" => "MÖD",
        "MIOS - Abteilungsleitung" => "MIOS",
        "Services - Abteilungsleitung" => "Services",
        "Plantvirus" => "PFVI",
        "JKI" => "PFVI",
        "Pflanzenviren" => "PFVI",
        "PLV" => "PFVI",
        "Compliance " => "Verwaltung",
        "Qualitätsmanagement" => "Verwaltung",
        "Bi & DB" => "BI & DB"
        // "SeM" => "BI"
    ];
    $allowed = [
        "BI & DB", "IT", "Services", "MIG", "Verwaltung", "MIOS", "BUG", "MuTZ", "Patente", "PFVI", "MÖD", "Presse und Kommunikation"
    ];
    // $depts = array();
    
    $dept = explode("/",$unit)[0];
    $dept = $translate[$dept] ?? $dept;
    $dept = str_replace(" - Abteilungsleitung", "", $dept);
    if (!in_array($dept, $allowed))$dept = "";
    // $depts[$dept][] = $doc['_id'];
    return $dept;
}

Route::get('/userman', function () {
    include_once BASEPATH . "/php/_login.php";
    include_once BASEPATH . "/php/_db.php";
    $collection = $osiris->users;
    $collection->deleteMany(array());
    $data = getUsers();
    // $user = array();
    $keys = [
        "_id" => "samaccountname",
        "username" => "samaccountname",
        "first" => "givenname",
        "last" => "sn",
        "displayname" => "displayname",
        "formalname" => "cn",
        "department" => "department",
        "unit" => "description",
        "telephone" => "telephonenumber",
        "mail" => "mail"
    ];
    $invalid = [
        "sequenzer",
        "pvnano",
        "pacbio",
        "hplce35",
        "oxadmin",
        "guestmi22",
        "admin-mas19",
        "femto",
        "mi03",
        "robo20",
        "dsmzbug",
        "test20",
        "lagerk16",
        "hpetestuser",
        "services",
        "admin-maa21",
        "admin-vig21",
        "bug-hplc",
        "dsmzmebo",
        "mutz-prakt",
        "test202",
        "test22",
        "gramnegative",
        "test-mas19",
        "pre19",
        "xcu"
    ];
    foreach ($data as $id => $value) {
        if (empty($value) || !is_array($value)) continue;
        $user = array();
        foreach ($keys as $key => $name) {
            // dump($value, true);
            $user[$key] = $value[$name][0] ?? null;
        }
        if (empty($user['last']) || in_array(strtolower(trim($user['_id'])), $invalid) || str_contains($user['unit'], "Allgemeiner Account")) continue;
        $user['_id'] = strtolower(trim($user['_id']));
        $user['is_admin'] = $user['username'] == 'juk20';
        $user['dept'] = get_dept($user['unit']);
        $user['academic_title'] = null;
        $user['orcid'] = null;
        $user['is_controlling'] = $user['department'] == "Controller";
        $user['is_scientist'] = in_array($user['dept'], [
            "BI & DB", "Services", "MIG","MIOS", "BUG", "MuTZ", "Patente", "PFVI", "MÖD"
        ]);
        $user['is_leader'] = str_contains($user['unit'], "leitung");
        $user['is_active'] = !str_contains($user['unit'], "verlassen");
        dump($user);
        $collection->insertOne($user);
    }
    // dump($user, true);
    // dump($data, true);
});

Route::get('/userman/([A-Za-z0-9\-]*)', function ($username) {
    include_once BASEPATH . "/php/_login.php";
    $data = getUser($username);
    // $user = array();
    
    foreach ($data as $id => $value) {
        if (empty($value) || !is_array($value)) continue;
            dump($value, true);
        
    }
});