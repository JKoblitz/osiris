<?php
/**
 * OSIRIS Weekly Digest
 * 
 * This script is used to send a weekly digest to all users who have not opted out to receive it.
 * 
 * @package OSIRIS
 * @since 1.3.7
 * 
 */ 

require_once 'CONFIG.php';
require_once 'CONFIG.fallback.php';
// var_dump($_SERVER);
// define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);
define('BASEPATH', $_SERVER['PWD']);

require_once BASEPATH. '/php/init.php';

// get all users who have not opted out
$users = $osiris->persons->find(['digest' => true]);

// // get all new publications from the last week
// $lastweek = strtotime('-1 week');
// $publications = $osiris->publications->find(['created' => ['$gt' => $lastweek]]);
// $publications = DB::doc2Arr($publications);

// // get all new projects from the last week
// $projects = $osiris->projects->find(['created' => ['$gt' => $lastweek]]);
// $projects = DB::doc2Arr($projects);

// new osiris updates?

// $osiris->system->updateOne(
//     ['key' => 'last_update'],
//     ['$set' => ['value' => date('Y-m-d')]],
//     ['upsert' => true]
// );
$last_update = $osiris->system->findOne(['key' => 'last_update']);
$last_update = $last_update['value'] ?? 'never';

// send digest to all users
foreach ($users as $user) {
    $email = $user['mail'];
    $name = $user['first'] . ' ' . $user['last'];
    $username = $user['username'];

    // get user's language
    // $language = $user['language'] ?? 'en';

    // get user's digest settings
    // $digest = $user['digest'] ?? [];

    // get user's last digest
    $last_digest = $user['last_digest'] ?? 0;

    // check if user has opted out
    // if ($digest['optout'] === true) {
    //     continue;
    // }

    // check if user has received a digest in the last week
    if ($last_digest > $lastweek) {
        continue;
    }

    // send digest
    $subject = 'OSIRIS Weekly Digest';
    $message = "Hello $name,\n\n";
    $message .= "Here is your weekly digest from OSIRIS.\n\n";
    $message .= "New publications:\n";
    foreach ($publications as $publication) {
        $message .= "- " . $publication['title'] . "\n";
    }
    $message .= "\nNew projects:\n";
    foreach ($projects as $project) {
        $message .= "- " . $project['title'] . "\n";
    }
    $message .= "\nLast OSIRIS update: $last_update\n\n";
    $message .= "You can change your digest settings in your profile.\n\n";
    $message .= "Best regards,\nOSIRIS Team";

    // send email
    $headers = "From: OSIRIS <osiris-app.de>\r\n";
    $headers .= "Reply-To:
    $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($email, $subject, $message, $headers);

    // update user's last digest
    $osiris->persons->updateOne(
        ['username' => $username],
        ['$set' => ['last_digest' => time()]]
    );
}



?>
