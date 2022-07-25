<?php
include_once BASEPATH . "/php/_config.php";

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
    if (str_contains($page, 'finder')) return '';
    if (count($breadcrumb) > 1 && $breadcrumb[0]['path'] == ("/" . $p)) return "active";
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

    <link href="<?= ROOTPATH ?>/css/fontawesome/css/all.css" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/digidive.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/style.css?<?= filemtime(BASEPATH . '/css/style.css') ?>">
    <script>
        const ROOTPATH = "<?=ROOTPATH?>";
</script>
<script src="<?=ROOTPATH?>/js/digidive.js"></script>
<script src="<?=ROOTPATH?>/js/jquery-3.3.1.min.js"></script>
<script src="<?=ROOTPATH?>/js/script.js?<?= filemtime(BASEPATH . '/js/script.js') ?>"></script>

</head>

<body>
    <!-- Modals go here -->
    <div id="loader">
        <span></span>
    </div>

    <!-- Page wrapper start -->
    <div class="page-wrapper">
        <!-- data-sidebar-hidden="hidden" to hide sidebar on start -->

        <!-- Sticky alerts (toasts), empty container -->
        <div class="sticky-alerts"></div>

        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" onclick="digidive.toggleSidebar()"></div>

        <!-- Navbar start -->
        <div class="navbar navbar-top">
            <a href="<?= ROOTPATH ?>/" class="navbar-brand ml-20">
                <img src="<?= ROOTPATH ?>/img/logo.svg" alt="OSIRIS">
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

            <form id="navbar-search" action="" method="get" class="nav-search">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" data-toggle="tooltip" data-title="<?=lang('Select quarter', 'Wähle ein Quartal aus')?>">
                        <i class="fa-regular fa-calendar-day"></i>
                        </div>
                    </div>
                    <select name="select-year" id="select-year" class="form-control">
                        <?php foreach (range(2017, CURRENTYEAR) as $year) { ?>
                            <option value="<?=$year?>" <?= SELECTEDYEAR == $year ? 'selected' : '' ?>><?=$year?></option>
                        <?php } ?>
                    </select>
                    <select name="select-quarter" id="select-quarter" class="form-control">
                        <option value="1" <?= SELECTEDQUARTER == '1' ? 'selected' : '' ?>>Q1</option>
                        <option value="2" <?= SELECTEDQUARTER == '2' ? 'selected' : '' ?>>Q2</option>
                        <option value="3" <?= SELECTEDQUARTER == '3' ? 'selected' : '' ?>>Q3</option>
                        <option value="4" <?= SELECTEDQUARTER == '4' ? 'selected' : '' ?>>Q4</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fas fa-check"></i></button>
                    </div>
                </div>
            </form>

        </nav>

        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">


                <!-- Sidebar links and titles -->
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>
                <?php } else { ?>

                    <div class="sidebar-title">
                        <?= lang('User', 'Nutzer') ?>
                    </div>

                    <a href="<?= ROOTPATH ?>/" class="sidebar-link with-icon">
                        <i class="far fa-user" aria-hidden="true"></i>
                        <?= $_SESSION["name"] ?? 'User' ?>
                    </a>



                    <a href="<?= ROOTPATH ?>/my-publication" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('my-publication') ?>">
                        <i class="far fa-book-bookmark" aria-hidden="true"></i>
                        <?= lang('My publications', 'Meine Publikationen') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/my-poster" class="sidebar-link sidebar-link-danger with-icon <?= $pageactive('my-poster') ?>">
                        <i class="far fa-presentation-screen" aria-hidden="true"></i>
                        <?= lang('My posters', 'Meine Poster') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/my-lecture" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('my-lecture') ?>">
                        <i class="far fa-keynote" aria-hidden="true"></i>
                        <?= lang('My lectures', 'Meine Vorträge') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/my-review" class="sidebar-link sidebar-link-success with-icon <?= $pageactive('my-review') ?>">
                        <i class="far fa-book-open-cover" aria-hidden="true"></i>
                        <?= lang('My reviews &amp; editorials', 'Reviews &amp; Editorials') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/my-misc" class="sidebar-link sidebar-link-muted with-icon <?= $pageactive('my-misc') ?>">
                        <i class="far fa-icons" aria-hidden="true"></i>
                        <?= lang('Misc') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/my-teaching" class="sidebar-link sidebar-link-muted with-icon <?= $pageactive('my-teaching') ?>">
                        <i class="far fa-people" aria-hidden="true"></i>
                        <?= lang('Teaching &amp; Guests') ?>
                    </a>


                    <a href="<?= ROOTPATH ?>/user/logout" class="sidebar-link with-icon">
                        <i class="far fa-right-from-bracket" aria-hidden="true"></i>
                        Logout
                    </a>
                    <a href="<?= currentGET([], ['language' => lang('de', 'en')]) ?>" class="sidebar-link with-icon">
                        <i class="far fa-language" aria-hidden="true"></i>
                        <?= lang('Deutsch', 'English') ?>
                    </a>


                    <div class="sidebar-title">
                        <?= lang('Content', 'Inhalte') ?>
                    </div>
                    <a href="<?= ROOTPATH ?>/browse/publication" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('publication') ?>">
                        <i class="far fa-books" aria-hidden="true"></i>
                        <?= lang('Publications', 'Publikationen') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/browse/poster" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('poster') ?>">
                        <i class="far fa-screen-users" aria-hidden="true"></i>
                        <?= lang('Posters', 'Poster') ?>
                    </a>

                    <!-- <a href="<?= ROOTPATH ?>/browse/activity" class="sidebar-link sidebar-link-success with-icon <?= $pageactive('activity') ?>">
                        <i class="far fa-calendar-days" aria-hidden="true"></i>
                        <?= lang('Activities', 'Aktivitäten') ?>
                    </a> -->



                    <?php if ($userClass->is_admin() || $userClass->is_controlling()) { ?>

                        <div class="sidebar-title">
                            <?= lang('Controlling') ?>
                        </div>

                        <a href="<?= ROOTPATH ?>/browse/scientist" class="sidebar-link with-icon <?= $pageactive('scientist') ?>">
                            <i class="far fa-user-graduate" aria-hidden="true"></i>
                            <?= lang('Scientists', 'Wissenschaftler:innen') ?>
                        </a>

                        <a href="<?= ROOTPATH ?>/browse/journal" class="sidebar-link with-icon <?= $pageactive('journal') ?>">
                            <i class="far fa-institution" aria-hidden="true"></i>
                            <?= lang('Journals', 'Journale') ?>
                        </a>
                    <?php } ?>


                <?php } ?>

                <a href="<?= ROOTPATH ?>/about" class="sidebar-link with-icon <?= $pageactive('about') ?>">
                        <i class="far fa-signs-post" aria-hidden="true"></i>
                        <?= lang('About', 'Über OSIRIS') ?>
                    </a>


                    <a href="<?= ROOTPATH ?>/news" class="sidebar-link with-icon <?= $pageactive('news') ?>">
                        <i class="far fa-newspaper" aria-hidden="true"></i>
                        <?= lang('News', 'Neuigkeiten') ?>
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