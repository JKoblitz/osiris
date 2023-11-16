<?php

/**
 * Routing file for controlling pages
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


Route::get('/controlling', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('lock-activities')) die('You have no permission to be here.');
    $breadcrumb = [
        ['name' => lang("Controlling")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/controlling.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/controlling', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('lock-activities')) die('You have no permission to be here.');

    $breadcrumb = [
        ['name' => lang("Controlling")]
    ];

    include BASEPATH . "/header.php";

    $changes = 0;
    if (isset($_POST['action']) && isset($_POST['start']) && isset($_POST['end'])) {

        $lock = ($_POST['action'] == 'lock');
        // dump($lock);

        $cursor = $DB->get_reportable_activities($_POST['start'], $_POST['end']);
        foreach ($cursor as $doc) {
            // dump($doc['title'] ?? 'REVIEW');

            if ($lock) {
                // in progress stuff is not locked
                if (
                    (
                        ($doc['type'] == 'misc' && $doc['iteration'] == 'annual') ||
                        ($doc['type'] == 'review' && in_array($doc['role'], ['Editor', 'editorial']))
                    ) && is_null($doc['end'])
                ) {
                    continue;
                }
                if ($doc['type'] == "students" && isset($doc['status']) && $doc['status'] == 'in progress') {
                    continue;
                }
            }

            $updateResult = $osiris->activities->updateOne(
                ['_id' => $doc['_id']],
                ['$set' => ['locked' => $lock]]
            );

            $changes += $updateResult->getModifiedCount();
        }
        // construct output message
        $header = $lock ? lang('Locked activities.', 'Aktivitäten gesperrt.') : lang('Unlocked activities.', 'Aktivitäten entsperrt.');
        $text = lang(
            "Successfully changed the status of $changes activities.",
            "Es wurde erfolgreich der Status von $changes Aktivitäten geändert."
        );
        printMsg($text, 'success', $header);
    } else {
        echo 'Nothing to do.';
    }

    include BASEPATH . "/pages/controlling.php";
    include BASEPATH . "/footer.php";
}, 'login');
