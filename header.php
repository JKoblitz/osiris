<?php
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}
$breadcrumb = $breadcrumb ?? [];
$pagetitle = array('Research report');
foreach ($breadcrumb as $crumb) {
    array_push($pagetitle, $crumb['name']);
}
$pagetitle = implode(' | ', array_reverse($pagetitle));

$page = $page ?? '';
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
    <!-- <link rel="icon" href="path/to/fav.png"> -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= ROOTPATH ?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= ROOTPATH ?>/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= ROOTPATH ?>/img/favicon-16x16.png">
    <title><?= $pagetitle ?? 'Research report' ?></title>

    <link href="<?= ROOTPATH ?>/css/fontawesome/css/all.css" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/digidive.css" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="<?= ROOTPATH ?>/css/components/form.css">
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/components/input.css">
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/components/divider.css"> -->
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/style.css?<?= filemtime(BASEPATH . '/css/style.css') ?>">
    <!--
    Or,
    Use the following (no variables, supports IE11):
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.1/css/halfmoon.min.css" rel="stylesheet" />
    Learn more: https://www.gethalfmoon.com/docs/customize/#notes-on-browser-compatibility
  -->
</head>

<body>
    <!-- Modals go here -->
    <div id="loader">
        <span></span>
    </div>

    <?php
    include BASEPATH . "/modals.php";
    ?>
    <!-- Page wrapper start -->
    <div class="page-wrapper">
        <!-- data-sidebar-hidden="hidden" to hide sidebar on start -->

        <!-- Sticky alerts (toasts), empty container -->
        <div class="sticky-alerts"></div>

        <!-- Sidebar overlay -->
        <div class="sidebar-overlay" onclick="digidive.toggleSidebar()"></div>

        <!-- Navbar start -->
        <div class="navbar navbar-top">
            <a href="index.php" class="navbar-brand ml-20">
                <i class="fad fa-abacus mr-10"></i>
                Research report
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

            <!-- <form id="navbar-search" action="/media" method="get" class="nav-search">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" autocomplete="off" placeholder="Search database">
                    <div class="input-group-append">
                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form> -->

        </nav>

        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">


                <!-- Sidebar links and titles -->
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>
                <?php } else { ?>

                    <div class="sidebar-title">
                        Content
                    </div>
                    <a href="<?= ROOTPATH ?>/browse/publication" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('publication') ?>">
                        <i class="far fa-books" aria-hidden="true"></i>
                        Publications
                    </a>

                    <a href="<?= ROOTPATH ?>/browse/poster" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('poster') ?>">
                        <i class="far fa-screen-users" aria-hidden="true"></i>
                        Posters
                    </a>

                    <a href="<?= ROOTPATH ?>/browse/activity" class="sidebar-link sidebar-link-success with-icon <?= $pageactive('activity') ?>">
                        <i class="far fa-calendar-days" aria-hidden="true"></i>
                        Activities
                    </a>

                    
                    <a href="<?= ROOTPATH ?>/browse/scientist" class="sidebar-link with-icon <?= $pageactive('scientist') ?>">
                        <i class="far fa-user-graduate" aria-hidden="true"></i>
                        Scientists
                    </a>

                    <a href="<?= ROOTPATH ?>/browse/journal" class="sidebar-link with-icon <?= $pageactive('journal') ?>">
                        <i class="far fa-institution" aria-hidden="true"></i>
                        Journals
                    </a>

                    <div class="sidebar-title">
                        User
                    </div>

                    <a href="<?= ROOTPATH ?>/" class="sidebar-link with-icon">
                        <i class="far fa-user" aria-hidden="true"></i>
                        <?= $_SESSION["name"] ?? 'User' ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/add-publication" class="sidebar-link sidebar-link-primary with-icon <?= $pageactive('add-publication') ?>">
                        <i class="far fa-book-medical" aria-hidden="true"></i>
                        Add publication
                    </a>
                    <a href="<?= ROOTPATH ?>/add-poster" class="sidebar-link sidebar-link-signal with-icon <?= $pageactive('add-poster') ?>">
                        <i class="far fa-file-plus" aria-hidden="true"></i>
                        Add poster
                    </a>
                    <a href="<?= ROOTPATH ?>/add-activity" class="sidebar-link sidebar-link-success with-icon <?= $pageactive('add-activity') ?>">
                        <i class="far fa-calendar-plus" aria-hidden="true"></i>
                        Add activity
                    </a>

                    <a href="<?= ROOTPATH ?>/user/logout" class="sidebar-link with-icon">
                        <i class="far fa-right-from-bracket" aria-hidden="true"></i>
                        Logout
                    </a>


                    <div class="sidebar-title">
                        Other stuff
                    </div>

                    <a href="<?= ROOTPATH ?>/todo" class="sidebar-link with-icon">
                        <i class="far fa-signs-post" aria-hidden="true"></i>
                        Roadmap
                    </a>
                    <a href="<?= currentGET([], ['language' => lang('de', 'en')]) ?>" class="sidebar-link with-icon">
                        <i class="far fa-language" aria-hidden="true"></i>
                        <?= lang('Deutsch', 'English') ?>
                    </a>
                <?php } ?>



            </div>
        </div>
        <!-- Content wrapper start -->
        <div class="content-wrapper">

            <div class="content-container">