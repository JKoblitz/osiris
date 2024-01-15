<?php

/**
 * Routing file for the database migration
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */


Route::get('/install', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";

    echo "<h1>Willkommen bei OSIRIS</h>";

    // check version
    $version = $osiris->system->findOne(['key' => 'version']);
    if (!empty($version) && !isset($_GET['force'])) {
        echo "<p>Es sieht so aus, als wäre OSIRIS bereits initialisiert. Falls du eine Neu-Initialisierung erzwingen möchtest, klicke bitte <a href='?force'>hier</a>.</p>";
        include BASEPATH . "/footer.php";
        die;
    }

    echo "<p>Ich initialisiere die Datenbank für Euch und werde erst mal die Standardeinstellungen übernehmen. Du kannst alles Weitere später anpassen.</p>";

    $json = file_get_contents(BASEPATH . "/settings.default.json");
    $default = json_decode($json, true, 512, JSON_NUMERIC_CHECK);


    // last step: write Version number to database

    include BASEPATH . "/footer.php";
});

Route::get('/migrate', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    $version = $osiris->system->findOne(['key' => 'version']);
    if (empty($version)) {
        $version = "1.0.0";
        $osiris->system->insertOne([
            'key' => 'version',
            'value' => $version
        ]);
    } else
        $version = $version['value'];
    $V = explode('.', $version);
    if ($V[1] < 2) {
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
            // TODO: create graphic schema of the new structure
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
    if ($V[1] < 2 || ($V[1] == 2 && $V[2] < 1)) {
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

    if ($V[1] < 3) {
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
                'enabled' => boolval(!$settings['general']['disable-'.$key])
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

        die;

        // $osiris->groups->deleteMany([]);

        // // add institute as root level
        // $affiliation = $Settings->get('affiliation_details');
        // $dept = [
        //     'id' => $affiliation['id'],
        //     'color' => '#000000',
        //     'name' => $affiliation['name'],
        //     'parent' => null,
        //     'level' => 0,
        //     'unit' => 'Institute',
        // ];
        // $osiris->groups->insertOne($dept);

        // // add departments as children
        // $depts = $Settings->get('departments');
        // foreach ($depts as $dept) {
        //     if ($dept['id'] == 'BIDB') $dept['id'] = 'BID';
        //     $dept['parent'] = $affiliation['id'];
        //     $dept['level'] = 1;
        //     $dept['unit'] = 'Department';
        //     $osiris->groups->insertOne($dept);
        // }

        // // migrate person affiliation
        // $persons = $osiris->persons->find([])->toArray();
        // foreach ($persons as $person) {
        //     // dump($person, true);
        //     // $dept = [$affiliation['id']];
        //     $depts = [];
        //     if (isset($person['dept']) && !empty($person['dept'])) {
        //         if ($person['dept'] === 'BIDB') $person['dept'] = 'BID';
        //         $depts[] = $person['dept'];
        //     }
        //     dump($depts);
        //     // die;
        //     $updated = $osiris->persons->updateOne(
        //         ['_id' => $person['_id']],
        //         ['$set' => ['depts' => $depts]]
        //     );
        // }

      

    }

    echo "<p>Done.</p>";
    $insertOneResult  = $osiris->system->updateOne(
        ['key' => 'version'],
        ['$set' => ['value' => OSIRIS_VERSION]]
    );
    include BASEPATH . "/footer.php";
});
