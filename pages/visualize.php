<?php
$users = $osiris->users->find(['is_scientist' => true], ['sort' => ["last" => 1]]);

$scientist = $_GET['scientist'] ?? $_SESSION['username'];
$selectedUser = $osiris->users->findone(['_id' => $scientist]);

?>

<div class="content">

    <h1>
        <i class="ph ph-regular ph-graph" aria-hidden="true"></i>
        <?= lang('Visualizations', 'Visualisierungen') ?>
    </h1>


    <div class="tiles">
        <a href="<?= ROOTPATH ?>/visualize/coauthors" class="tile">
            <h5 class="title">
                <?= lang('Coauthor network', 'Koautoren-Netzwerk') ?>
            </h5>
            <img src="<?= ROOTPATH ?>/img/charts/chord.svg" alt="" class="w-full">
        </a>
        <a href="<?= ROOTPATH ?>/visualize/sunburst" class="tile">
            <h5 class="title">
    <?= lang('Department overview', 'Abteilungs-Übersicht') ?>
            </h5>
            <img src="<?= ROOTPATH ?>/img/charts/sunburst.svg" alt="" class="w-full">
        </a>
        <a href="<?= ROOTPATH ?>/visualize/departments" class="tile">
            <h5 class="title">
                <?= lang('Department network', 'Abteilungs-Netzwerk') ?>
            </h5>
            <img src="<?= ROOTPATH ?>/img/charts/departments.svg" alt="" class="w-full">
        </a>
    </div>
</div>