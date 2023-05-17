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
            --affiliation: "<?= $Settings->affiliation ?>";
        }
    </style>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/fill/style.css" />
    <style>
        .ph,
        .ph ph-regular,
        .ph-fill {
            font-size: 1.5em;
            line-height: 1em;
            vertical-align: -0.2em;
        }

        .sidebar-link.with-icon>.sidebar-icon,
        .sidebar-link.with-icon>i.ph {
            font-size: 1.6em;
        }

        .ph.ph-edit:before {
            content: '\ec15';
        }

        .ph.ph-search:before {
            content: '\ebdd';
        }
    </style>
    <!-- <link href="<?= ROOTPATH ?>/css/phosphoricons/style.css" rel="stylesheet" /> -->
    <!-- <link href="<?= ROOTPATH ?>/vendor/twbs/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" /> -->
    <!-- <link href="<?= ROOTPATH ?>/css/fontawesome/css/all.css" rel="stylesheet" /> -->
    <link href="<?= ROOTPATH ?>/css/fontello/css/osiris.css?v=2" rel="stylesheet" />
    <link href="<?= ROOTPATH ?>/css/digidive.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/datatables.css">
    <!-- Quill (rich-text editor) -->
    <link href="<?= ROOTPATH ?>/css/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=ROOTPATH?>/css/daterangepicker.min.css">

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/style.css?<?= filemtime(BASEPATH . '/css/style.css') ?>">
    <?php
    echo $Settings->generateStyleSheet();
    ?>

    <!-- <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
 
<script type="text/javascript" src="DataTables/datatables.min.js"></script> -->

    <script>
        const ROOTPATH = "<?= ROOTPATH ?>";
        const AFFILIATION = "<?= $Settings->affiliation ?>";
    </script>

    <link rel="stylesheet" href="<?= ROOTPATH ?>/css/shepherd.css" />
    <script src="<?= ROOTPATH ?>/js/digidive.js?v=2"></script>
    <script src="<?= ROOTPATH ?>/js/jquery-3.3.1.min.js"></script>

    <!-- <link rel="stylesheet" href="shepherd.js/dist/css/shepherd.css"/> -->
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@8.3.1/dist/js/shepherd.min.js"></script>

    <script src="<?= ROOTPATH ?>/js/script.js?<?= filemtime(BASEPATH . '/js/script.js') ?>"></script>
    <script src="<?= ROOTPATH ?>/js/osiris.js?<?= filemtime(BASEPATH . '/js/osiris.js') ?>"></script>

</head>

<body>
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
                    <a data-dismiss="modal" class="btn float-right" role="button" aria-label="Close" >
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
                <span style="position: absolute;bottom: 0;font-size: 1.3rem;color: var(--signal-color);">v1.1</span>
            </a>

            <a href="<?= $Settings->affiliation_details['link'] ?? '#' ?>" class="navbar-brand ml-auto" target="_blank">
                <img src="<?= ROOTPATH ?>/img/<?= $Settings->affiliation_details['logo'] ?? '#' ?>" alt="<?= $Settings->affiliation ?>">
            </a>
        </div>
        <nav class="navbar navbar-bottom">
            <!-- Button to toggle sidebar -->
            <button class="btn btn-action active" type="button" onclick="digidive.toggleSidebar(this);"></button>
            <ul class="navbar-nav">
                <?php if (false) { ?>
                    <!-- set to true during maintenance -->
                    <div class="alert alert-danger">
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

            <a href="<?= ROOTPATH ?>/new-stuff#12.05.23" class="btn btn-osiris">
                <i class="ph-fill ph-sparkle"></i>
                NEWS
                (<?= time_elapsed_string('2023-05-12 13:00') ?>)
            </a>
        </nav>
        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">

                <!-- Sidebar links and titles -->
                <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) { ?>

                    <div class="cta">
                        <a href="<?= ROOTPATH ?>/" class="btn btn-osiris" style="border-radius:2rem">
                            <i class="fa ph-sign-in mr-10" aria-hidden="true"></i>
                            <?= lang('Log in') ?>
                        </a>
                    </div>

                <?php } else { ?>

                    <?php
                    $realusername = $_SESSION['realuser'] ?? $_SESSION['username'];
                    $maintain = $osiris->users->find(['maintenance' => $realusername], ['projection' => ['displayname' => 1]])->toArray();
                    if (!empty($maintain)) { ?>
                        <form action="" class="content">
                            <select name="OSIRIS-SELECT-MAINTENANCE-USER" id="osiris-select-maintenance-user" class="form-control" onchange="$(this).parent().submit()">
                                <option value="<?= $realusername ?>"><?= $_SESSION['name'] ?></option>
                                <?php
                                foreach ($maintain as $d) { ?>
                                    <option value="<?= $d['_id'] ?>" <?= $d['_id'] ==  $_SESSION['username'] ? 'selected' : '' ?>><?= $d['displayname'] ?></option>
                                <?php } ?>
                            </select>
                        </form>
                    <?php } ?>


                    <div class="sidebar-title">
                        <!-- <?= lang('User', 'Nutzer') ?> -->
                        <?= $USER["displayname"] ?? 'User' ?>
                    </div>

                    <div class="cta">
                        <a href="<?= ROOTPATH ?>/activities/new" class="btn btn-osiris <?= $pageactive('activities/new') ?>" style="border-radius:2rem">
                            <i class="ph ph-regular ph-plus-circle mr-10" aria-hidden="true"></i>
                            <?= lang('Add activity', 'Aktivität hinzuf.') ?>
                        </a>
                    </div>

                    <?php if ($USER['is_controlling']) { ?>
                        <a href="<?= ROOTPATH ?>/profile/<?= $_SESSION['username'] ?>" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('profile/' . $_SESSION['username']) ?>">
                            <i class="ph ph-regular ph-user" aria-hidden="true"></i>
                            <?= $USER["displayname"] ?? 'User' ?>
                        </a>

                        <a href="<?= ROOTPATH ?>/dashboard" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('dashboard') ?>">
                            <i class="ph ph-regular ph-chart-line" aria-hidden="true"></i>
                            <?= lang('Dashboard') ?>
                        </a>

                        <a href="<?= ROOTPATH ?>/lom" class="sidebar-link with-icon sidebar-link-osiris <?= $pageactive('lom') ?>">
                            <i class="ph ph-regular ph-coin" aria-hidden="true"></i>
                            <?= lang('Coins') ?>
                        </a>

                    <?php } else { ?>
                        <a href="<?= ROOTPATH ?>/profile/<?= $_SESSION['username'] ?>" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('profile/' . $_SESSION['username']) ?>">
                            <i class="ph ph-regular ph-student" aria-hidden="true"></i>
                            <?= $USER["displayname"] ?? 'User' ?>
                        </a>
                        <a href="<?= ROOTPATH ?>/scientist" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('scientist') ?>">
                            <i class="ph ph-regular ph-calendar" aria-hidden="true"></i>
                            <?= lang('My year', 'Mein Jahr') ?>
                        </a>
                    <?php } ?>

                    <?php if ($USER['is_scientist']) { ?>
                        <a href="<?= ROOTPATH ?>/my-activities" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('my-activities') ?>">
                            <i class="ph ph-regular ph-folder-user" aria-hidden="true"></i>
                            <?= lang('My activities', 'Meine Aktivitäten') ?>
                        </a>
                    <?php } ?>
                    <?php if ($USER['is_admin']) { ?>
                        <a href="<?= ROOTPATH ?>/admin/general" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('admin') ?>">
                            <i class="ph ph-regular ph-gear" aria-hidden="true"></i>
                            <?= lang('Admin Dashboard') ?>
                        </a>
                    <?php } ?>

                    <a href="<?= ROOTPATH ?>/user/logout" class="sidebar-link  with-icon">
                        <i class="ph ph-regular ph-sign-out" aria-hidden="true"></i>
                        Logout
                    </a>

                    <div class="sidebar-title">
                        <?= lang('Data', 'Daten') ?>
                    </div>


                    <a href="<?= ROOTPATH ?>/activities" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('activities') ?>">
                        <i class="ph ph-regular ph-folders" aria-hidden="true"></i>
                        <?= lang('All activities', 'Alle Aktivitäten') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/user/browse" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('user/browse') ?>">
                        <i class="ph ph-regular ph-users" aria-hidden="true"></i>
                        <?= lang('Users', 'Nutzer:innen') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/journal/browse" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('journal/browse') ?>">
                        <i class="ph ph-regular ph-newspaper-clipping" aria-hidden="true"></i>
                        <?= lang('Journals', 'Journale') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/activities/teaching" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('activities/teaching') ?>">
                        <i class="ph ph-regular ph-chalkboard-simple" aria-hidden="true"></i>
                        <?= lang('Teaching modules', 'Lehrveranstaltungen') ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/activities/projects" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('projects') ?>">
                        <i class="ph ph-regular ph-tree-structure" aria-hidden="true"></i>
                        <?= lang('Projects', 'Projekte') ?>
                    </a>


                    <div class="sidebar-title">
                        <?= lang('Tools', 'Werkzeuge') ?>
                    </div>
                    <a href="<?= ROOTPATH ?>/activities/search" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('activities/search') ?>">
                        <i class="ph ph-regular ph-magnifying-glass-plus" aria-hidden="true"></i>
                        <?= lang('Advanced search', 'Erweiterte Suche') ?>
                    </a>

                    <?php if ($USER['is_scientist']) { ?>
                        <a href="<?= ROOTPATH ?>/dashboard" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('dashboard') ?>">
                            <i class="ph ph-regular ph-chart-line" aria-hidden="true"></i>
                            <?= lang('Dashboard') ?>
                        </a>
                    <?php } ?>
                    <a href="<?= ROOTPATH ?>/visualize" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('visualize') ?>">
                        <i class="ph ph-regular ph-graph" aria-hidden="true"></i>
                        <?= lang('Visualizations', 'Visualisierung') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/expertise" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('expertise') ?>">
                        <i class="ph ph-regular ph-barbell" aria-hidden="true"></i>
                        <?= lang('Expertise search', 'Expertise-Suche') ?>
                    </a>


                    <div class="sidebar-title">
                        <?= lang('Export &amp; Import') ?>
                    </div>

                    <a href="<?= ROOTPATH ?>/download" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('download') ?>">
                        <i class="ph ph-regular ph-download" aria-hidden="true"></i>
                        Export <?= lang('Activities', 'Aktivitäten') ?>
                    </a>

                    <a href="<?= ROOTPATH ?>/cart" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('cart') ?>">
                        <i class="ph ph-regular ph-shopping-cart" aria-hidden="true"></i>
                        <?= lang('Cart', 'Einkaufswagen') ?>
                        <?php
                        $cart = readCart();
                        if (!empty($cart)) { ?>
                            <span class="badge badge-primary badge-pill ml-10" id="cart-counter">
                                <?= count($cart) ?>
                            </span>
                        <?php } else { ?>
                            <span class="badge badge-primary badge-pill ml-10 hidden" id="cart-counter">
                                0
                            </span>
                        <?php } ?>
                    </a>
                    <a href="<?= ROOTPATH ?>/import" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('import') ?>">
                        <i class="ph ph-regular ph-upload" aria-hidden="true"></i>
                        <?= lang('Import') ?>
                    </a>


                    <?php if ($USER['is_controlling']) { ?>

                        <a href="<?= ROOTPATH ?>/reports" class="sidebar-link sidebar-link-osiris with-icon <?= $pageactive('reports') ?>">
                            <i class="ph ph-regular ph-file-chart-line" aria-hidden="true"></i>
                            <?= lang('Reports', 'Berichte') ?>
                        </a>

                    <?php } ?>


                <?php } ?>

                <div class="sidebar-title">
                    OSIRIS
                </div>

                <a href="<?= ROOTPATH ?>/new-stuff" class="sidebar-link with-icon <?= $pageactive('news') ?>">
                    <i class="ph ph-regular ph-info" aria-hidden="true"></i>
                    <?= lang('About &amp; news', 'Über OSIRIS &amp; News') ?>
                </a>

                <a href="<?= ROOTPATH ?>/docs" class="sidebar-link with-icon <?= $pageactive('docs') ?>">
                    <i class="ph ph-regular ph-question" aria-hidden="true"></i>
                    <?= lang('Documentation', 'Dokumentation') ?>
                </a>

                <!-- <a href="mailto:julia.koblitz@dsmz.de?subject=OSIRIS Feedback" class="sidebar-link with-icon">
                    <i class="ph ph-regular ph-chat-text" aria-hidden="true"></i>
                    <?= lang('Feedback') ?>
                </a> -->
                <a href="https://github.com/JKoblitz/osiris/issues" target="_blank" class="sidebar-link with-icon">
                    <i class="ph ph-regular ph-chat-text" aria-hidden="true"></i>
                    <?= lang('Report an issue', "Problem melden") ?>
                </a>

                <a href="<?= currentGET([], ['language' => lang('de', 'en')]) ?>" class="sidebar-link with-icon">
                    <i class="ph ph-regular ph-translate" aria-hidden="true"></i>
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

                if (isset($USER['is_admin']) && $USER['is_admin'] && isset($Settings->errors) && !empty($Settings->errors)) {
                ?>
                <div class="alert alert-danger mb-20">
                    <h3 class="title">There are errors in your settings:</h3>
                    <?=implode('<br>', $Settings->errors)?>
                    <br>
                    Default settings are used. Go to the <a href="<?=ROOTPATH?>/admin/general">Admin Dashboard</a> to fix this.
                </div>
                <?php
                }
                ?>