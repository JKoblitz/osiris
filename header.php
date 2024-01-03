<?php

/**
 * Header component
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

include_once BASEPATH . "/php/init.php";

$breadcrumb = $breadcrumb ?? [];
$pagetitle = array('OSIRIS');
foreach ($breadcrumb as $crumb) {
    array_push($pagetitle, $crumb['name']);
}
$pagetitle = implode(' | ', array_reverse($pagetitle));

$uri = $_SERVER['REQUEST_URI'];
// $uri = str_replace(ROOTPATH."/", '', $uri, 1);
$uri = substr_replace($uri, '', 0, strlen(ROOTPATH . "/"));
$lasturl = explode("/", $uri);
// dump($lasturl);
$page =  $page ?? $lasturl[0]; //end($lasturl);
$pageactive = function ($p) use ($page, $breadcrumb) {
    if ($page == $p) return "active";
    $uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    if ((ROOTPATH . "/" . $p) == $uri) return 'active';
    // return "";
    // if ( count($breadcrumb) > 1 && $breadcrumb[0]['path'] == ("/" . $p)) return "active";
    return "";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta name="description" content="OSIRIS ist ein modernes Forschungsinformationssystem, das besonderen Schwerpunkt auf Open Source und Nutzerfreundlichkeit legt." />

    <!-- Favicon and title -->
    <link rel="icon" href="img/favicon.png">
    <title><?= $pagetitle ?? 'OSIRIS-App' ?></title>
    <link rel="manifest" href="/manifest.json">

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="OSIRIS - the open, smart and intuitive research information system" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://osiris-app.de" />
    <meta property="og:description" content="OSIRIS ist ein modernes Forschungsinformationssystem, das besonderen Schwerpunkt auf Open Source und Nutzerfreundlichkeit legt.." />
    <meta property="og:image" content="<?= ROOTPATH ?>/img/apple-touch-icon.png" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://osiris-app.de">
    <meta property="twitter:title" content="OSIRIS - the open, smart and intuitive research information system">
    <meta property="twitter:description" content="OSIRIS ist ein modernes Forschungsinformationssystem, das besonderen Schwerpunkt auf Open Source und Nutzerfreundlichkeit legt..">
    <meta property="twitter:image" content="<?= ROOTPATH ?>/img/apple-touch-icon.png">

    <!-- Apple -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ROOTPATH ?>/img/apple-touch-icon.png">
    <link rel="mask-icon" href="<?= ROOTPATH ?>/img/mask-icon.svg" color="#dd590e">

    <!-- Favicon and title -->
    <link rel="icon" href="<?= ROOTPATH ?>/img/favicon.png">
    <title><?= $pagetitle ?? 'OSIRIS' ?></title>

    <style>
        :root {
            --affiliation: "<?= $Settings->get('affiliation') ?>";
        }
    </style>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/fill/style.css" />

    <!-- <link href="<?= ROOTPATH ?>/css/phosphoricons/style.css" rel="stylesheet" /> -->
    <!-- <link href="<?= ROOTPATH ?>/vendor/twbs/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" /> -->
    <!-- <link href="<?= ROOTPATH ?>/css/fontawesome/css/all.css" rel="stylesheet" /> -->
    <link href="<?= ROOTPATH ?>/css/fontello/css/osiris.css?v=2" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/digidive.css?v=3" rel="stylesheet" />
    <!-- Quill (rich-text editor) -->
    <link href="<?= ROOTPATH ?>/css/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/daterangepicker.min.css">

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/style.css?<?= filemtime(BASEPATH . '/css/style.css') ?>">
    <?php
    echo $Settings->generateStyleSheet();
    ?>

    <script>
        const ROOTPATH = "<?= ROOTPATH ?>";
        const AFFILIATION = "<?= $Settings->get('affiliation') ?>";
    </script>

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/shepherd.css" />
    <script src="<?= ROOTPATH ?>/js/digidive.js?v=4"></script>
    <script src="<?= ROOTPATH ?>/js/jquery-3.3.1.min.js"></script>

    <!-- <link rel="stylesheet" href="shepherd.js/dist/css/shepherd.css"/> -->
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.3.1/dist/js/shepherd.min.js"></script>

    <script src="<?= ROOTPATH ?>/js/script.js?<?= filemtime(BASEPATH . '/js/script.js') ?>"></script>
    <script src="<?= ROOTPATH ?>/js/osiris.js?<?= filemtime(BASEPATH . '/js/osiris.js') ?>"></script>


    <?php if (isset($additionalHead)) {
        echo $additionalHead;
    } ?>

</head>

<body>
    <div class="loader">
        <span></span>
    </div>

    <!-- Page wrapper start -->
    <div class="page-wrapper 
        <?= $_COOKIE['D3-accessibility-contrast'] ?? '' ?>
        <?= $_COOKIE['D3-accessibility-transitions'] ?? '' ?>
        <?= $_COOKIE['D3-accessibility-dyslexia'] ?? '' ?>
    ">
        <!-- data-sidebar-hidden="hidden" to hide sidebar on start -->

        <!-- Sticky alerts (toasts), empty container -->
        <div class="sticky-alerts"></div>


        <div class="modal" id="the-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <a data-dismiss="modal" class="btn float-right" role="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                    <h5 class="modal-title" id="modal-title"></h5>
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" onclick="digidive.toggleSidebar()"></div>

        <!-- Navbar start -->
        <div class="navbar navbar-top">
            <a href="<?= ROOTPATH ?>/" class="navbar-brand ml-20">
                <img src="<?= ROOTPATH ?>/img/logo.svg" alt="OSIRIS">
                <span style="position: absolute;bottom: 0;font-size: 1.3rem;color: var(--signal-color);">v<?= OSIRIS_VERSION ?></span>
            </a>

            <a href="<?= $Settings->get('affiliation_details')['link'] ?? '#' ?>" class="navbar-brand ml-auto" target="_blank">
                <img src="<?= ROOTPATH ?>/img/<?= $Settings->get('affiliation_details')['logo'] ?? '#' ?>" alt="<?= $Settings->get('affiliation') ?>">
            </a>
        </div>
        <nav class="navbar navbar-bottom">
            <!-- Button to toggle sidebar -->
            <button class="btn btn-action active" type="button" onclick="digidive.toggleSidebar(this);"></button>
            <ul class="navbar-nav">
                <?php if (false) { ?>
                    <!-- set to true during maintenance -->
                    <div class="alert danger">
                        <b><?= lang('System maintenance', 'Wartungsarbeiten') ?>.</b>
                        <?= lang('Please do not add, edit or remove data. Changes might be overwritten.', 'Bitte keine Daten hinzufügen, bearbeiten oder löschen. Änderungen werden evtl. überschrieben.') ?>
                    </div>
                <?php } else { ?>
                    <nav aria-label="breadcrumbs">
                        <ul class="breadcrumb">
                            <?php
                            $breadcrumb = $breadcrumb ?? [];
                            if (!empty($breadcrumb)) {
                                // array_unshift($breadcrumb , 'Home');
                                echo '<li class="breadcrumb-item"><a href="' . ROOTPATH . '/">Home</a></li>';
                                foreach ($breadcrumb as $crumb) {
                                    if (!isset($crumb['path'])) {
                                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="#">' . $crumb['name'] . '</a></li>';
                                    } else {
                                        echo '<li class="breadcrumb-item"><a href="' . ROOTPATH . $crumb['path'] . '">' . $crumb['name'] . '</a></li>';
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </nav>
                <?php } ?>

            </ul>

            <!-- Accessibility menu -->
            <div class="dropdown">
                <button class="btn accessibility" data-toggle="dropdown" type="button" id="accessibility-menu" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only"><?= lang('Accessibility Options', 'Accessibility-Optionen') ?></span>
                </button>
                <div class="dropdown-menu dropdown-menu-center w-300" aria-labelledby="accessibility-menu">
                    <h6 class="header text-primary">Accessibility</h6>
                    <form action="#" method="get" class="content">
                        <input type="hidden" name="accessibility[check]">

                        <div class="form-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="set-contrast" name="accessibility[contrast]" value="high-contrast" <?= !empty($_COOKIE['D3-accessibility-contrast'] ?? '') ? 'checked' : '' ?>>
                                <label for="set-contrast"><?= lang('High contrast', 'Erhöhter Kontrast') ?></label><br>
                                <small class="text-muted">
                                    <?= lang('Enhance the contrast of the web page for better readability.', 'Erhöht den Kontrast für bessere Lesbarkeit.') ?>
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="set-transitions" name="accessibility[transitions]" value="without-transitions" <?= !empty($_COOKIE['D3-accessibility-transitions'] ?? '') ? 'checked' : '' ?>>
                                <label for="set-transitions"><?= lang('Reduce motion', 'Verringerte Bewegung') ?></label><br>
                                <small class="text-muted">
                                    <?= lang('Reduce motion and animations on the page.', 'Verringert Animationen und Bewegungen auf der Seite.') ?>
                                </small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="set-dyslexia" name="accessibility[dyslexia]" value="dyslexia" <?= !empty($_COOKIE['D3-accessibility-dyslexia'] ?? '') ? 'checked' : '' ?>>
                                <label for="set-dyslexia"><?= lang('Dyslexia mode', 'Dyslexie-Modus') ?></label><br>
                                <small class="text-muted">
                                    <?= lang('Use a special font to increase readability for users with dyslexia.', 'OSIRIS nutzt eine spezielle Schriftart, die von manchen Menschen mit Dyslexie besser gelesen werden kann.') ?>
                                </small>
                            </div>
                        </div>
                        <button class="btn primary">Apply</button>
                    </form>
                </div>
            </div>


            <form id="navbar-search" action="<?= ROOTPATH ?>/activities" method="get" class="nav-search">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" autocomplete="off" placeholder="<?= lang('Search in activities', 'Suche in Aktivitäten') ?>">
                    <div class="input-group-append">
                        <button class="btn primary"><i class="ph ph-magnifying-glass"></i></button>
                    </div>
                </div>
                <!-- <div class="suggestions">
                    <div class="title"></div>
                </div> -->
            </form>

        </nav>
        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">

                <!-- Sidebar links and titles -->
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>

                    <div class="cta">
                        <a href="<?= ROOTPATH ?>/" class="btn osiris" style="border-radius:2rem">
                            <i class="ph ph-sign-in mr-10" aria-hidden="true"></i>
                            <?= lang('Log in') ?>
                        </a>
                    </div>

                <?php } else { ?>

                    <?php
                    $realusername = $_SESSION['realuser'] ?? $_SESSION['username'];
                    $maintain = $osiris->persons->find(['maintenance' => $realusername], ['projection' => ['displayname' => 1, 'username' => 1]])->toArray();
                    if (!empty($maintain)) { ?>
                        <form action="" class="content">
                            <select name="OSIRIS-SELECT-MAINTENANCE-USER" id="osiris-select-maintenance-user" class="form-control" onchange="$(this).parent().submit()">
                                <option value="<?= $realusername ?>"><?= $DB->getNameFromId($realusername) ?></option>
                                <?php
                                foreach ($maintain as $d) { ?>
                                    <option value="<?= $d['username'] ?>" <?= $d['username'] ==  $_SESSION['username'] ? 'selected' : '' ?>><?= $DB->getNameFromId($d['username']) ?></option>
                                <?php } ?>
                            </select>
                        </form>
                    <?php } ?>


                    <div class="title">
                        <!-- <?= lang('User', 'Nutzer') ?> -->
                        <?= $USER["displayname"] ?? 'User' ?>
                    </div>

                    <div class="cta">
                        <a href="<?= ROOTPATH ?>/activities/new" class="btn osiris <?= $pageactive('activities/new') ?>" style="border-radius:2rem">
                            <i class="ph ph-plus-circle mr-10" aria-hidden="true"></i>
                            <?= lang('Add activity', 'Aktivität hinzuf.') ?>
                        </a>
                    </div>
                    <a href="<?= ROOTPATH ?>/profile/<?= $_SESSION['username'] ?>" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('profile/' . $_SESSION['username']) ?>">
                        <i class="ph ph-user" aria-hidden="true"></i>
                        <?= $USER["displayname"] ?? 'User' ?>
                    </a>


                    <?php if ($Settings->hasPermission('complete-dashboard')) { ?>
                        <a href="<?= ROOTPATH ?>/dashboard" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('dashboard') ?>">
                            <i class="ph ph-chart-line" aria-hidden="true"></i>
                            <?= lang('Dashboard') ?>
                        </a>
                    <?php } ?>
                    <?php if ($Settings->hasPermission('complete-queue')) { ?>
                        <?php
                        $n_queue = $osiris->queue->count(['declined' => ['$ne' => true]]);
                        ?>

                        <a href="<?= ROOTPATH ?>/queue/editor" class="sidebar-link with-icon sidebar-link-osiris <?= $pageactive('queue/editor') ?>">
                            <i class="ph ph-queue" aria-hidden="true"></i>
                            <?= lang('Queue', 'Warteschlange') ?>
                            <span class="badge primary badge-pill ml-10" id="cart-counter">
                                <?= $n_queue ?>
                            </span>
                        </a>
                    <?php } ?>


                    <!-- 
                    <a href="<?= ROOTPATH ?>/coins" class="sidebar-link with-icon sidebar-link-osiris <?= $pageactive('lom') ?>">
                        <i class="ph ph-coin" aria-hidden="true"></i>
                        <?= lang('Coins') ?>
                    </a> -->


                    <?php if ($Settings->hasPermission('scientist')) { ?>
                        <a href="<?= ROOTPATH ?>/my-year" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('my-year') ?>">
                            <i class="ph ph-calendar" aria-hidden="true"></i>
                            <?= lang('My year', 'Mein Jahr') ?>
                        </a>
                        <!-- <a href="<?= ROOTPATH ?>/my-activities" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('my-activities') ?>">
                            <i class="ph ph-folder-user" aria-hidden="true"></i>
                            <?= lang('My activities', 'Meine Aktivitäten') ?>
                        </a> -->
                    <?php } ?>

                    <a href="<?= ROOTPATH ?>/user/logout" class="sidebar-link  with-icon">
                        <i class="ph ph-sign-out" aria-hidden="true"></i>
                        Logout
                    </a>

                    <div class="title">
                        <?= lang('Data', 'Daten') ?>
                    </div>


                    <a href="<?= ROOTPATH ?>/activities" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('activities') ?>">
                        <i class="ph ph-folders" aria-hidden="true"></i>
                        <?= lang('All activities', 'Alle Aktivitäten') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/user/browse" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('user/browse') ?>">
                        <i class="ph ph-users" aria-hidden="true"></i>
                        <?= lang('Users', 'Personen') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/groups" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('groups') ?>">
                        <i class="ph ph-users-three" aria-hidden="true"></i>
                        <?= lang('Organisational Units', 'Organisationseinh.') ?>
                    </a>

                    <?php if (GUEST_FORMS) { ?>
                        <a href="<?= ROOTPATH ?>/guests" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('guests') ?>">
                            <i class="ph ph-user-switch" aria-hidden="true"></i>
                            <?= lang('Guests', 'Gäste') ?>
                        </a>
                    <?php } ?>




                    <a href="<?= ROOTPATH ?>/journal" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('journal') ?>">
                        <i class="ph ph-newspaper-clipping" aria-hidden="true"></i>
                        <?= lang('Journals', 'Journale') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/teaching" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('teaching') ?>">
                        <i class="ph ph-chalkboard-simple" aria-hidden="true"></i>
                        <?= lang('Teaching modules', 'Lehrveranstaltungen') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/projects" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('projects') ?>">
                        <i class="ph ph-tree-structure" aria-hidden="true"></i>
                        <?= lang('Projects', 'Projekte') ?>
                        <!-- <span class="badge ml-10">SOON</span> -->
                    </a>


                    <a href="<?= ROOTPATH ?>/research-data" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('research-data') ?>">
                        <i class="ph ph-circles-three-plus" aria-hidden="true"></i>
                        <?= lang('Tags', 'Schlagwörter') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/concepts" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('concepts') ?>">
                        <i class="ph ph-lightbulb" aria-hidden="true"></i>
                        <?= lang('Concepts', 'Konzepte') ?>
                    </a>


                    <div class="title">
                        <?= lang('Tools', 'Werkzeuge') ?>
                    </div>
                    <a href="<?= ROOTPATH ?>/search/activities" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('search') ?>">
                        <i class="ph ph-magnifying-glass-plus" aria-hidden="true"></i>
                        <?= lang('Advanced search', 'Erweiterte Suche') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/dashboard" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('dashboard') ?>">
                        <i class="ph ph-chart-line" aria-hidden="true"></i>
                        <?= lang('Dashboard') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/visualize" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('visualize') ?>">
                        <i class="ph ph-graph" aria-hidden="true"></i>
                        <?= lang('Visualizations', 'Visualisierung') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/expertise" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('expertise') ?>">
                        <i class="ph ph-barbell" aria-hidden="true"></i>
                        <?= lang('Expertise search', 'Expertise-Suche') ?>
                    </a>


                    <div class="title">
                        <?= lang('Export &amp; Import') ?>
                    </div>

                    <a href="<?= ROOTPATH ?>/download" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('download') ?>">
                        <i class="ph ph-download" aria-hidden="true"></i>
                        Export <?= lang('Activities', 'Aktivitäten') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/cart" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('cart') ?>">
                        <i class="ph ph-shopping-cart" aria-hidden="true"></i>
                        <?= lang('Cart', 'Einkaufswagen') ?>
                        <?php
                        $cart = readCart();
                        if (!empty($cart)) { ?>
                            <span class="badge primary badge-pill ml-10" id="cart-counter">
                                <?= count($cart) ?>
                            </span>
                        <?php } else { ?>
                            <span class="badge primary badge-pill ml-10 hidden" id="cart-counter">
                                0
                            </span>
                        <?php } ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/import" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('import') ?>">
                        <i class="ph ph-upload" aria-hidden="true"></i>
                        <?= lang('Import') ?>
                    </a>

                    <?php if ($Settings->hasPermission('reports')) { ?>

                        <a href="<?= ROOTPATH ?>/reports" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('reports') ?>">
                            <i class="ph ph-printer" aria-hidden="true"></i>

                            <?= lang('Reports', 'Berichte') ?>
                        </a>

                        <?php if (IDA_INTEGRATION) { ?>
                            <a href="<?= ROOTPATH ?>/ida/dashboard" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('ida') ?>">
                                <i class="ph ph-clipboard-text" aria-hidden="true"></i>
                                <?= lang('IDA-Integration') ?>
                            </a>
                        <?php } ?>

                    <?php } ?>


                <?php } ?>


                <?php if ($Settings->hasPermission('admin-panel')) { ?>
                    <div class="title">
                        ADMIN
                    </div>
                    <a href="<?= ROOTPATH ?>/admin/general" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('admin/general') ?>">
                        <i class="ph ph-gear" aria-hidden="true"></i>
                        <?= lang('General settings') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/admin/roles" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('admin/roles') ?>">
                        <i class="ph ph-gear" aria-hidden="true"></i>
                        <?= lang('Roles', 'Rollen') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/admin/categories" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('admin/categories') ?>">
                        <i class="ph ph-gear" aria-hidden="true"></i>
                        <?= lang('Categories', 'Kategorien') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/admin/features" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('admin/features') ?>">
                        <i class="ph ph-gear" aria-hidden="true"></i>
                        <?= lang('Features', 'Funktionen') ?>
                    </a>
                <?php } ?>

                <div class="title">
                    OSIRIS
                </div>

                <a href="<?= ROOTPATH ?>/about" class="sidebar-link with-icon <?= $pageactive('about') ?>">
                    <i class="ph ph-info" aria-hidden="true"></i>
                    <?= lang('About OSIRIS', 'Über OSIRIS') ?>
                </a>

                <a href="<?= ROOTPATH ?>/new-stuff" class="sidebar-link with-icon <?= $pageactive('new-stuff') ?>">
                    <i class="ph ph-newspaper" aria-hidden="true"></i>
                    <?= lang('News', 'Neuigkeiten') ?>
                </a>

                <a href="<?= ROOTPATH ?>/docs" class="sidebar-link with-icon <?= $pageactive('docs') ?>">
                    <i class="ph ph-question" aria-hidden="true"></i>
                    <?= lang('Documentation', 'Dokumentation') ?>
                </a>

                <!-- <a href="mailto:julia.koblitz@dsmz.de?subject=OSIRIS Feedback" class="sidebar-link with-icon">
                    <i class="ph ph-chat-text" aria-hidden="true"></i>
                    <?= lang('Feedback') ?>
                </a> -->
                <a href="https://github.com/JKoblitz/osiris/issues" target="_blank" class="sidebar-link with-icon">
                    <i class="ph ph-chat-text" aria-hidden="true"></i>
                    <?= lang('Report an issue', "Problem melden") ?>
                </a>

                <a href="<?= currentGET([], ['language' => lang('de', 'en')]) ?>" class="sidebar-link with-icon">
                    <i class="ph ph-translate" aria-hidden="true"></i>
                    <?= lang('Deutsch', 'English') ?>
                </a>

            </div>
        </div>
        <!-- Content wrapper start -->
        <div class="content-wrapper">
            <?php if ($pageactive('preview')) { ?>
                <div class="title-bar text-danger text-center font-weight-bold d-block font-size-20">
                    <b>PREVIEW</b>
                </div>
            <?php } ?>


            <div class="content-container">
                <?php
                if (function_exists('printMsg') && isset($_GET['msg'])) {
                    printMsg();
                }

                if ($Settings->hasPermission('admin') && isset($Settings->errors) && !empty($Settings->errors)) {
                ?>
                    <div class="alert danger mb-20">
                        <h3 class="title">There are errors in your settings:</h3>
                        <?= implode('<br>', $Settings->errors) ?>
                        <br>
                        Default settings are used. Go to the <a href="<?= ROOTPATH ?>/admin/general">Admin Panel</a> to fix this.
                    </div>
                <?php
                }
                ?>