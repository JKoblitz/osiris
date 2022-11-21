<?php
include_once BASEPATH . "/php/_config.php";
include_once BASEPATH . "/php/_db.php";

$breadcrumb = $breadcrumb ?? [];
$pagetitle = array('OSIRIS');
foreach ($breadcrumb as $crumb) {
    array_push($pagetitle, $crumb['name']);
}
$pagetitle = implode(' | ', array_reverse($pagetitle));

$lasturl = explode("/", $_SERVER['REQUEST_URI']);
$page = $page ?? end($lasturl);
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
    <meta name="viewport" content="width=device-width" />

    <!-- Favicon and title -->
    <link rel="icon" href="<?= ROOTPATH ?>/img/favicon.png">
    <title><?= $pagetitle ?? 'OSIRIS' ?></title>

    <style>
        :root {
            --affiliation: "<?= AFFILIATION ?>";
        }
    </style>

    <link href="<?= ROOTPATH ?>/css/fontawesome/css/all.css" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/fontello/css/osiris.css" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/digidive.css" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/datatables.css">

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/style.css?<?= filemtime(BASEPATH . '/css/style.css') ?>">

    <script>
        const ROOTPATH = "<?= ROOTPATH ?>";
        const AFFILIATION = "<?= AFFILIATION ?>";
    </script>

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/shepherd.css" />
    <script src="<?= ROOTPATH ?>/js/digidive.js"></script>
    <script src="<?= ROOTPATH ?>/js/jquery-3.3.1.min.js"></script>
    <!-- Quill (rich-text editor) -->
    <script src="<?= ROOTPATH ?>/js/quill.min.js"></script>

    <!-- <link rel="stylesheet" href="shepherd.js/dist/css/shepherd.css"/> -->
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.3.1/dist/js/shepherd.min.js"></script>

    <script src="<?= ROOTPATH ?>/js/script.js?<?= filemtime(BASEPATH . '/js/script.js') ?>"></script>
    <script src="<?= ROOTPATH ?>/js/osiris.js?<?= filemtime(BASEPATH . '/js/osiris.js') ?>"></script>

</head>

<body>
    <!-- Modals go here -->
    <div class="loader">
        <span></span>
    </div>

    <!-- Page wrapper start -->
    <div class="page-wrapper">
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
                <img src="<?= ROOTPATH ?>/img/logo-beta.svg" alt="OSIRIS">
            </a>

            <a href="//www.dsmz.de/" class="navbar-brand ml-auto">
                <!-- DSMZ Logo is mandatory -->
                <img src="<?= ROOTPATH ?>/img/dsmz.svg" alt="DSMZ">
            </a>
        </div>
        <nav class="navbar navbar-bottom">
            <!-- Button to toggle sidebar -->
            <button class="btn btn-action active" type="button" onclick="digidive.toggleSidebar(this);"></button>
            <ul class="navbar-nav">
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
            </ul>

            <a href="<?= ROOTPATH ?>/news#18.11.22" class="btn text-signal">
                <i class="fas fa-stars"></i>
                NEWS
                (<?= time_elapsed_string('2022-11-18 10:00') ?>)
            </a>
        </nav>

        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">


                <!-- Sidebar links and titles -->
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>
                <?php } else { ?>

                    <div class="sidebar-title">
                        <!-- <?= lang('User', 'Nutzer') ?> -->
                        <?= $USER["displayname"] ?? 'User' ?>
                    </div>

                    <div class="cta">
                        <a href="<?= ROOTPATH ?>/activities/new" class="btn btn-osiris test-<?= $pageactive('activities/new') ?>">
                            <i class="far fa-plus" aria-hidden="true"></i>
                            <?= lang('Add activity', 'Aktivität hinzuf.') ?>
                        </a>
                    </div>

                    <?php if ($USER['is_controlling']) { ?>
                        <a href="<?= ROOTPATH ?>/" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('') ?>">
                            <i class="far fa-chart-column" aria-hidden="true"></i>
                            <?= lang('Dashboard') ?>
                        </a>
                    <?php } else { ?>
                        <a href="<?= ROOTPATH ?>/profile/<?= $_SESSION['username'] ?>" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('') ?>">
                            <i class="far fa-user-graduate" aria-hidden="true"></i>
                            <?= $USER["displayname"] ?? 'User' ?>
                        </a>
                        <a href="<?= ROOTPATH ?>/scientist" class="sidebar-link sidebar-link-success with-icon <?= $pageactive('scientist') ?>">
                            <i class="far fa-calendar" aria-hidden="true"></i>
                            <?= lang('My year', 'Mein Jahr') ?>
                        </a>
                    <?php } ?>



                    <a href="<?= ROOTPATH ?>/activities" class="sidebar-link sidebar-link-danger with-icon <?= $pageactive('activities') ?>">
                        <i class="far fa-book-bookmark" aria-hidden="true"></i>
                        <?= lang('All activities', 'Alle Aktivitäten') ?>
                    </a>

                    <?php if ($USER['is_scientist']) { ?>
                        <a href="<?= ROOTPATH ?>/my-activities" class="sidebar-link sidebar-link-danger with-icon <?= $pageactive('my-activities') ?>">
                            <i class="far fa-book-user" aria-hidden="true"></i>
                            <?= lang('My activities', 'Meine Aktivitäten') ?>
                        </a>

                    <?php } ?>
                    <a href="<?= ROOTPATH ?>/visualize" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('visualize') ?>">
                        <i class="far fa-chart-network" aria-hidden="true"></i>
                        <?= lang('Coauthor network', 'Koautoren-Netzwerk') ?>
                    </a>






                    <!-- <a href="<?= ROOTPATH ?>/achievements" class="sidebar-link with-icon">
                        <i class="far fa-trophy-star" aria-hidden="true"></i>
                       <?= lang('Achievements', 'Errungenschaften') ?>
                    </a> -->


                    <!--                     
                    <div class="sidebar-title">
                        <?= lang('Tools') ?>
                    </div> -->

                    <a href="<?= ROOTPATH ?>/download" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('download') ?>">
                        <i class="far fa-download" aria-hidden="true"></i>
                        Download
                    </a>


                    <a href="<?= ROOTPATH ?>/user/logout" class="sidebar-link with-icon mt-10">
                        <i class="far fa-right-from-bracket" aria-hidden="true"></i>
                        Logout
                    </a>






                    <div class="sidebar-title">
                        <?= lang('Others', 'Weiteres') ?>
                    </div>

                    <a href="<?= ROOTPATH ?>/browse/users" class="sidebar-link with-icon <?= $pageactive('users') ?>">
                        <i class="far fa-user-graduate" aria-hidden="true"></i>
                        <?= lang('Users', 'Nutzer:innen') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/browse/journal" class="sidebar-link with-icon <?= $pageactive('journal') ?>">
                        <i class="far fa-institution" aria-hidden="true"></i>
                        <?= lang('Journals', 'Journale') ?>
                    </a>




                    <?php if ($USER['is_admin'] || $USER['is_controlling']) { ?>

                        <div class="sidebar-title">
                            <?= lang('Controlling') ?>
                        </div>


                        <a href="<?= ROOTPATH ?>/lom" class="sidebar-link with-icon sidebar-link-signal <?= $pageactive('lom') ?>">
                            <i class="far fa-coin" aria-hidden="true"></i>
                            <?= lang('Points', 'Punkte') ?>
                        </a>

                        <a href="<?= ROOTPATH ?>/reports" class="sidebar-link sidebar-link-danger with-icon <?= $pageactive('reports') ?>">
                            <i class="far fa-file-chart-column" aria-hidden="true"></i>
                            Export <?= lang('Reports', 'Berichte') ?>
                        </a>

                    <?php } ?>


                <?php } ?>

                <div class="sidebar-title">
                    OSIRIS
                </div>

                <!-- <a href="<?= ROOTPATH ?>/about" class="sidebar-link with-icon <?= $pageactive('about') ?>">
                    <i class="far fa-signs-post" aria-hidden="true"></i>
                    <?= lang('About', 'Über OSIRIS') ?>
                </a> -->


                <a href="<?= ROOTPATH ?>/news" class="sidebar-link with-icon <?= $pageactive('news') ?>">
                    <i class="far fa-message-pen" aria-hidden="true"></i>
                    <?= lang('About &amp; news', 'Über OSIRIS &amp; News') ?>
                </a>

                <a href="mailto:julia.koblitz@dsmz.de?subject=OSIRIS Feedback" class="sidebar-link with-icon">
                    <i class="far fa-comments" aria-hidden="true"></i>
                    <?= lang('Feedback') ?>
                </a>

                <a href="<?= currentGET([], ['language' => lang('de', 'en')]) ?>" class="sidebar-link with-icon">
                    <i class="far fa-language" aria-hidden="true"></i>
                    <?= lang('Deutsch', 'English') ?>
                </a>

            </div>
        </div>
        <!-- Content wrapper start -->
        <div class="content-wrapper">

            <div class="content-container">
                <?php

                if (function_exists('printMsg') && isset($_GET['msg'])) {
                    printMsg();
                }
                ?>