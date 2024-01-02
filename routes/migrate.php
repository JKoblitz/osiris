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

        echo "Update Activity schema";
        $osiris->adminCategories->deleteMany([]);
        $osiris->adminTypes->deleteMany([]);
        foreach ($Settings->getActivities() as $t => $type) {
            $cat = [
                "id" => $type['id'],
                "icon" => $type['icon'],
                "color" => $type['color'],
                "name" => $type['name'],
                "name_de" => $type['name_de'],
                // "children" => $type['subtypes']
            ];
            dump($cat, true);
            $osiris->adminCategories->insertOne($cat);
            foreach ($type['subtypes'] as $s => $subtype) {   
                $subtype['parent'] = $t;             
                dump($subtype, true);
                $osiris->adminTypes->insertOne($subtype);
            }
        }
    }

    echo "<p>Done.</p>";
    $insertOneResult  = $osiris->system->updateOne(
        ['key' => 'version'],
        ['$set' => ['value' => OSIRIS_VERSION]]
    );
    include BASEPATH . "/footer.php";
});
