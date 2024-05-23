<?php

/**
 * Routing file for the database migration
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

// TODO: Add the following routes to the routes/migrate.php file
Route::get('/migrate/test', function () {
    include_once BASEPATH . "/php/init.php";
    $cursor = $osiris->activities->find(['history' => ['$exists' => false]]);

    foreach ($cursor as $doc) {
        if (isset($doc['history'])) continue;
        $id = $doc['_id'];
        echo "$id<br>";
        $values = ['history' => []];
        if (isset($doc['created_by'])) {
            $values['history'][] = [
                'date' => $doc['created'],
                'user' => $doc['created_by'],
                'type' => 'created',
                'changes' => []
            ];
        }
        if (isset($doc['edited_by'])) {
            $values['history'][] = [
                'date' => $doc['edited'],
                'user' => $doc['edited_by'],
                'type' => 'edited',
                'changes' => []
            ];
        }

        if (empty($values['history']) ) continue;
        // $values['history'][count($values['history']) - 1]['current'] = $doc['rendered']['print'] ?? 'unknown';

        $osiris->activities->updateOne(
            ['_id' => $id],
            ['$set' => $values]
        );
        // remove old fields
        // $osiris->activities->updateOne(
        //     ['_id' => $id],
        //     ['$unset' => ['edited_by' => '', 'edited' => '']]
        // );
    }

    echo "Done";

});


Route::get('/install', function () {
    // include_once BASEPATH . "/php/init.php";
    // include BASEPATH . "/header.php";

    include_once BASEPATH . "/php/DB.php";

    // Database connection
    global $DB;
    $DB = new DB;

    global $osiris;
    $osiris = $DB->db;

    echo "<h1>Willkommen bei OSIRIS</h1>";

    // check version
    $version = $osiris->system->findOne(['key' => 'version']);
    if (!empty($version) && !isset($_GET['force'])) {
        echo "<p>Es sieht so aus, als wäre OSIRIS bereits initialisiert. Falls du eine Neu-Initialisierung erzwingen möchtest, klicke bitte <a href='?force'>hier</a>.</p>";
        include BASEPATH . "/footer.php";
        die;
    }

    echo "<p>Ich initialisiere die Datenbank für dich und werde erst mal die Standardeinstellungen übernehmen. Du kannst alles Weitere später anpassen.</p>";

    $json = file_get_contents(BASEPATH . "/settings.default.json");
    $settings = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
    $file_name = BASEPATH . "/settings.json";
    if (file_exists($file_name)) {
        echo "<p>Ich habe bereits vorhandene Einstellungen in <code>settings.json</code> gefunden. Ich werde versuchen, diese zu übernehmen.</p>";
        $json = file_get_contents($file_name);
        $set = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        // replace existing keys with new ones
        $settings = array_merge($settings, $set);
    }

    // echo "<h3>Generelle Einstellungen</h3>";
    $osiris->adminGeneral->deleteMany([]);
    $affiliation = $settings['affiliation'];
    $osiris->adminGeneral->insertOne([
        'key' => 'affiliation',
        'value' => $affiliation
    ]);

    $osiris->adminGeneral->insertOne([
        'key' => 'startyear',
        'value' => date('Y')
    ]);
    $roles = $settings['roles']['roles'];
    $osiris->adminGeneral->insertOne([
        'key' => 'roles',
        'value' => $roles
    ]);
    echo "<p>";
    echo "Ich habe die generellen Einstellungen vorgenommen. ";


    // echo "<h3>Features</h3>";
    // $osiris->adminFeatures->deleteMany([]);
    // foreach (["coins", "achievements", "user-metrics"] as $key) {
    //     $osiris->adminFeatures->insertOne([
    //         'feature' => $key,
    //         'enabled' => boolval(!$settings['general']['disable-' . $key])
    //     ]);
    // }


    // echo "<h3>Rechte und Rollen</h3>";

    $json = file_get_contents(BASEPATH . "/roles.json");
    $rights = json_decode($json, true, 512, JSON_NUMERIC_CHECK);

    $osiris->adminRights->deleteMany([]);
    $rights = $settings['roles']['rights'];
    foreach ($rights as $right => $perm) {
        foreach ($roles as $n => $role) {
            $r = [
                'role' => $role,
                'right' => $right,
                'value' => $perm[$n]
            ];
            $osiris->adminRights->insertOne($r);
        }
    }
    echo "Ich habe Rechte und Rollen etabliert. ";

    // echo "<h3>Aktivitäten</h3>";
    $osiris->adminCategories->deleteMany([]);
    $osiris->adminTypes->deleteMany([]);
    foreach ($settings['activities'] as $type) {
        $t = $type['id'];
        $cat = [
            "id" => $type['id'],
            "icon" => $type['icon'],
            "color" => $type['color'],
            "name" => $type['name'],
            "name_de" => $type['name_de']
        ];
        $osiris->adminCategories->insertOne($cat);
        foreach ($type['subtypes'] as $s => $subtype) {
            $subtype['parent'] = $t;
            $osiris->adminTypes->insertOne($subtype);
        }
    }

    // set up indices
    $indexNames = $osiris->adminCategories->createIndexes([
        ['key' => ['id' => 1], 'unique' => true],
    ]);
    $indexNames = $osiris->adminTypes->createIndexes([
        ['key' => ['id' => 1], 'unique' => true],
    ]);

    echo "Ich habe die Standard-Aktivitäten hinzugefügt. ";


    // echo "<h3>Organisationseinheiten</h3>";
    $osiris->groups->deleteMany([]);

    // add institute as root level
    $dept = [
        'id' => $affiliation['id'],
        'color' => '#000000',
        'name' => $affiliation['name'],
        'parent' => null,
        'level' => 0,
        'unit' => 'Institute',
    ];
    $osiris->groups->insertOne($dept);
    echo "Ich die Organisationseinheiten initialisiert, indem ich eine übergeordnete Einheit hinzugefügt habe. 
        Bitte bearbeite diese und füge weitere Einheiten hinzu. ";


    $json = file_get_contents(BASEPATH . "/achievements.json");
    $achievements = json_decode($json, true, 512, JSON_NUMERIC_CHECK);

    $osiris->achievements->deleteMany([]);
    $osiris->achievements->insertMany($achievements);
    $osiris->achievements->createIndexes([
        ['key' => ['id' => 1], 'unique' => true],
    ]);
    echo "Zu guter Letzt habe ich die Achievements initialisiert. ";

    echo "</p>";

    // last step: write Version number to database
    $osiris->system->deleteMany(['key' => 'version']);
    $osiris->system->insertOne(
        ['key' => 'version', 'value' => OSIRIS_VERSION]
    );

    echo "<h3>Fertig</h3>";
    echo "<p>
        Ich habe alle Einstellungen gespeichert und OSIRIS erfolgreich initialisiert.
        Am besten gehst du als nächstes zum <a href='" . ROOTPATH . "/admin/general'>Admin-Dashboard</a> und nimmst dort die wichtigsten Einstellungen vor.
    </p>";

    if (strtoupper(USER_MANAGEMENT) == 'AUTH') {
        echo '<b style="color:#e95709;">Wichtig:</b> Wie ich sehe benutzt du das Auth-Addon für die Nutzer-Verwaltung. Wenn du deinen Account anlegst, achte bitte darauf, dass der Nutzername mit dem vorkonfigurierten Admin-Namen (in <code>CONFIG.php</code>)  exakt übereinstimmt. Nur der vorkonfigurierte Admin kann die Ersteinstellung übernehmen und weiteren Personen diese Rolle übertragen.';
    }
});

Route::get('/migrate', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";

    set_time_limit(6000);
    $DBversion = $osiris->system->findOne(['key' => 'version']);
    if (empty($DBversion)) {
        $DBversion = "1.0.0";
        $osiris->system->insertOne([
            'key' => 'version',
            'value' => $DBversion
        ]);
    } else {
        $DBversion = $DBversion['value'];
    }

    // $V = explode('.', $version);

    if (version_compare($DBversion, '1.2.0', '<')) {
        echo "<h1>Migrate to Version 1.2.X</h1>";
        $osiris->teachings->drop();
        $osiris->miscs->drop();
        $osiris->posters->drop();
        $osiris->publications->drop();
        $osiris->lectures->drop();
        $osiris->reviews->drop();
        $osiris->lecture->drop();

        $users = $osiris->users->find([])->toArray();

        $person_keys = [
            "first",
            "last",
            "academic_title",
            "displayname",
            "formalname",
            "names",
            "first_abbr",
            "department",
            "unit",
            "telephone",
            "mail",
            "dept",
            "orcid",
            "gender",
            "google_scholar",
            "researchgate",
            "twitter",
            "webpage",
            "expertise",
            "updated",
            "updated_by",
        ];

        $account_keys = [
            // "is_admin",
            // "is_controlling",
            // "is_scientist",
            // "is_leader",
            "is_active",
            "maintenance",
            "hide_achievements",
            "hide_coins",
            "display_activities",
            "lastlogin",
            "created",
            "approved",
        ];

        $osiris->persons->deleteMany([]);
        $osiris->accounts->deleteMany([]);
        $osiris->achieved->deleteMany([]);

        foreach ($users as $user) {
            $user = iterator_to_array($user);
            $username = strtolower($user['username']);

            $person = ["username" => $username];
            foreach ($person_keys as $key) {
                if (!array_key_exists($key, $user)) continue;
                $person[$key] = $user[$key];
                unset($user[$key]);
            }
            $osiris->persons->insertOne($person);

            $account = ["username" => $username];
            foreach ($account_keys as $key) {
                if (!array_key_exists($key, $user)) continue;
                if ($key)
                    $account[$key] = $user[$key];
                unset($user[$key]);
            }
            $roles = [];
            foreach (['editor', 'admin', 'leader', 'controlling', 'scientist'] as $role) {
                if ($user['is_' . $role] ?? false) {
                    if ($role == 'controlling') $role = 'editor';
                    $roles[] = $role;
                }
            }
            $account['roles'] = $roles;

            $osiris->accounts->insertOne($account);

            if (isset($user['achievements'])) {
                foreach ($user['achievements'] as $ac) {
                    $ac['username'] = $username;
                    $osiris->achieved->insertOne($ac);
                }
                unset($user['achievements']);
            }
        }
        echo "Migrated " . count($users) . " users into a new format.<br> Migration successful. You might close this window now.";
    }

    // if ($V[1] < 2 || ($V[1] == 2 && $V[2] < 1)) {
    if (version_compare($DBversion, '1.2.1', '<')){
        echo "<p>Migrating persons into new version.</p>";
        $migrated = 0;

        $accounts = $osiris->accounts->find([])->toArray();
        foreach ($accounts as $account) {
            $user = $account['username'];
            // check if user exists
            $person = $osiris->persons->findOne(['username' => $user]);
            if (empty($person)) {
                echo $user;
            } else {
                unset($account['_id']);
                $updated = $osiris->persons->updateOne(
                    ['username' => $user],
                    ['$set' => $account]
                );
                $migrated += $updated->getModifiedCount();
            }
        }

        echo "<p>Migrated $migrated users.</p>";
    }

    if (version_compare($DBversion, '1.3.0', '<')) {
        echo "<h1>Migrate to Version 1.3.X</h1>";

        $json = file_get_contents(BASEPATH . "/settings.default.json");
        $settings = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
        // get custom settings
        $file_name = BASEPATH . "/settings.json";
        if (file_exists($file_name)) {
            $json = file_get_contents($file_name);
            $set = json_decode($json, true, 512, JSON_NUMERIC_CHECK);
            // replace existing keys with new ones
            $settings = array_merge($settings, $set);
        }
        // dump($settings, true);


        echo "<p>Update general settings</p>";
        $osiris->adminGeneral->deleteMany([]);

        $osiris->adminGeneral->insertOne([
            'key' => 'affiliation',
            'value' => $settings['affiliation']
        ]);

        $osiris->adminGeneral->insertOne([
            'key' => 'startyear',
            'value' => $settings['general']['startyear']
        ]);
        $roles = $settings['roles']['roles'];
        $osiris->adminGeneral->insertOne([
            'key' => 'roles',
            'value' => $roles
        ]);


        echo "<p>Update Features</p>";
        $osiris->adminFeatures->deleteMany([]);
        foreach (["coins", "achievements", "user-metrics"] as $key) {
            $osiris->adminFeatures->insertOne([
                'feature' => $key,
                'enabled' => boolval(!$settings['general']['disable-' . $key])
            ]);
        }


        echo "<p>Update Rights and Roles</p>";


        $osiris->adminRights->deleteMany([]);
        $rights = $settings['roles']['rights'];
        foreach ($rights as $right => $perm) {
            foreach ($roles as $n => $role) {
                $r = [
                    'role' => $role,
                    'right' => $right,
                    'value' => $perm[$n]
                ];
                $osiris->adminRights->insertOne($r);
            }
        }

        echo "<p>Update Activity schema</p>";
        $osiris->adminCategories->deleteMany([]);
        $osiris->adminTypes->deleteMany([]);
        foreach ($settings['activities'] as $type) {
            $t = $type['id'];
            $cat = [
                "id" => $type['id'],
                "icon" => $type['icon'],
                "color" => $type['color'],
                "name" => $type['name'],
                "name_de" => $type['name_de'],
                // "children" => $type['subtypes']
            ];
            $osiris->adminCategories->insertOne($cat);
            foreach ($type['subtypes'] as $s => $subtype) {
                $subtype['parent'] = $t;
                // dump($subtype, true);
                $osiris->adminTypes->insertOne($subtype);
            }
        }

        // set up indices
        $indexNames = $osiris->adminCategories->createIndexes([
            ['key' => ['id' => 1], 'unique' => true],
        ]);


        $osiris->groups->deleteMany([]);

        // add institute as root level
        $affiliation = $settings['affiliation'];
        $dept = [
            'id' => $affiliation['id'],
            'color' => '#000000',
            'name' => $affiliation['name'],
            'parent' => null,
            'level' => 0,
            'unit' => 'Institute',
        ];
        $osiris->groups->insertOne($dept);

        // add departments as children
        $depts = $settings['departments'];
        foreach ($depts as $dept) {
            if ($dept['id'] == 'BIDB') $dept['id'] = 'BID';
            $dept['parent'] = $affiliation['id'];
            $dept['level'] = 1;
            $dept['unit'] = 'Department';
            $osiris->groups->insertOne($dept);
        }

        // migrate person affiliation
        $persons = $osiris->persons->find([])->toArray();
        foreach ($persons as $person) {
            // dump($person, true);
            // $dept = [$affiliation['id']];
            $depts = [];
            if (isset($person['dept']) && !empty($person['dept'])) {
                if ($person['dept'] === 'BIDB') $person['dept'] = 'BID';
                $depts[] = $person['dept'];
            }
            dump($depts);
            // die;
            $updated = $osiris->persons->updateOne(
                ['_id' => $person['_id']],
                ['$set' => ['depts' => $depts]]
            );
        }
    }

    if (version_compare($DBversion, '1.3.3', '<')) {
        // migrate old documents, convert old history (created_by, edited_by) to new history format
        $cursor = $osiris->activities->find(['history' => ['$exists' => false], '$or' => [['created_by' => ['$exists' => true]], ['edited_by' => ['$exists' => true]]]]);
        foreach ($cursor as $doc) {
            if (isset($doc['history'])) continue;
            $id = $doc['_id'];
            $values = ['history' => []];
            if (isset($doc['created_by'])) {
                $values['history'][] = [
                    'date' => $doc['created'],
                    'user' => $doc['created_by'],
                    'type' => 'created',
                    'changes' => []
                ];
            }
            if (isset($doc['edited_by'])) {
                $values['history'][] = [
                    'date' => $doc['edited'],
                    'user' => $doc['edited_by'],
                    'type' => 'edited',
                    'changes' => []
                ];
            }

            // $values['history'][count($values['history']) - 1]['current'] = $doc['rendered']['print'] ?? 'unknown';

            $osiris->activities->updateOne(
                ['_id' => $id],
                ['$set' => $values]
            );
            // remove old fields
            $osiris->activities->updateOne(
                ['_id' => $id],
                ['$unset' => ['edited_by' => '', 'created' => '', 'edited' => '']]
            );
        }
    }
    if (version_compare($DBversion, '1.3.4', '<')) {
        $osiris->activities->createIndex(['rendered.plain' => 'text']);
    }
    
    echo "<p>Rerender activities</p>";
    include_once BASEPATH . "/php/Render.php";
    renderActivities();

    echo "<p>Done.</p>";
    $insertOneResult  = $osiris->system->updateOne(
        ['key' => 'version'],
        ['$set' => ['value' => OSIRIS_VERSION]]
    );
    include BASEPATH . "/footer.php";
});
