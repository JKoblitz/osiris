<?php

/**
 * Page to edit user web visibility
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /user/visibility/<username>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$scientist = $data;

$img_exist = file_exists(BASEPATH . "/img/users/$user.jpg");
if ($img_exist) {
    $img = ROOTPATH . "/img/users/$user.jpg";
} else {
    // standard picture
    $img = ROOTPATH . "/img/person.jpg";
}
?>
<h1 class="mt-0">
    <i class="ph ph-eye"></i>
    <?= $data['name'] ?>
</h1>

<h2 class="subtitle">
    <?= lang('Configure web profile', 'Webprofil konfigurieren') ?>
</h2>



<div class="box p-20">

    <div class="row align-items-center my-0">
        <div class="col flex-grow-0">
            <div class="position-relative">
                <img src="<?= $img ?>" alt="" class="profile-img">
            </div>
        </div>
        <div class="col ml-20">
            <h1 class="m-0">
                <?= $scientist['academic_title'] ?? '' ?>
                <?= $scientist['first'] ?? '' ?>
                <?= $scientist['last'] ?>
            </h1>

            <p class="my-0 lead text-primary" style="font-weight: 500">
                <?php
                if (!empty($scientist['dept'])) {
                    echo $Settings->getDepartments($scientist['dept'])['name'] ?? '';
                }
                ?>
            </p>
            <?php if (isset($scientist['position']) && !empty($scientist['position'])) { ?>
                <p class="my-0 lead"><?= $scientist['position'] ?></p>
            <?php } ?>
        </div>
    </div>

    <div class="row row-eq-spacing my-0">
        <div class="col-md-8">
            <div class="pb-10">
                <h2 class="title"><?= lang('Research interest', 'Forschungsinteressen') ?></h2>

                <?php if (isset($scientist['research']) && !empty($scientist['research'])) { ?>
                    <ul class="list">
                        <?php foreach ($scientist['research'] as $key) { ?>
                            <li><?= $key ?></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p><?= lang('No research interests stated.', 'Keine Forschungsinteressen angegeben.') ?></p>
                <?php } ?>
            </div>

            <div class="pb-10">
                <h2 class="title"><?= lang('Curriculum Vitae') ?></h2>

                <?php if (isset($scientist['cv']) && !empty($scientist['cv'])) {
                    $cv = DB::doc2Arr($scientist['cv']);
                    // usort ( $cv , function ($a, $b) {
                    //     $a = $a['from']['year'].'.'.$a['from']['month'];
                    //     $b = $b['from']['year'].'.'.$b['from']['month'];
                    //     return strnatcmp($b, $a); 
                    // });
                ?>
                    <div class="biography">
                        <?php foreach ($cv as $entry) { ?>
                            <div class="cv">
                                <span class="time"><?= $entry['time'] ?></span>
                                <h5 class="title"><?= $entry['position'] ?></h5>
                                <span class="affiliation"><?= $entry['affiliation'] ?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p><?= lang('No CV given.', 'Kein CV angegeben.') ?></p>
                <?php } ?>
            </div>

            <div class="pb-10">

                <h2>Highlighted research</h2>
                <?php
                $scientist['highlighted'] = ['632da4672199cd3df8dbc166'];
                if (isset($scientist['highlighted']) && !empty($scientist['highlighted'])) {
                    foreach ($scientist['highlighted'] as $h) {
                        $doc = $DB->getActivity($h);
                        echo $doc['rendered']['web'];
                    }
                } else { ?>
                    <p><?= lang('No highlighted research', 'Keine Forschung hervorgehoben') ?></p>
                <?php } ?>


            </div>

            <div class="pb-10">
                <h2>Publications</h2>
                <?php
                $filter = ['authors.user'=>$user, 'type'=>'publication'];
                $options = ['sort'=> ['year'=> -1, 'month'=> -1, 'day' => -1]];
                $N = $osiris->activities->count($filter);
                if ($N > 0) {
                    foreach ($osiris->activities->find($filter, $options) as $doc) {
                        if (isset($scientist['highlighted']) && !empty($scientist['highlighted']) && in_array(strval($doc['_id']), $scientist['highlighted']) )
                            continue;
                        echo '<p>'.$doc['rendered']['web'].'</p>';
                    }
                } else { ?>
                    <p><?= lang('No publications found', 'Keine Publikationen gefunden') ?></p>
                <?php } ?>

            </div>
        </div>


        <div class="col-md-4">
            <h2 class="title">Contact</h2>
            <table class="table simple small">
                <tbody>
                    <tr>
                        <td>
                            <span class="key">Email</span>
                            <?= $scientist['mail'] ?? '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="key"><?= lang('Telephone', 'Telefon') ?></span>
                            <?= $scientist['telephone'] ?? '' ?>
                        </td>
                    </tr>
                    <?php if (!empty($scientist['twitter'] ?? null)) { ?>
                        <tr>
                            <td>
                                <span class="key">Twitter</span>

                                <a href="https://twitter.com/<?= $scientist['twitter'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['twitter'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['orcid'] ?? null)) { ?>
                        <tr>
                            <td>
                                <span class="key">ORCID</span>

                                <a href="http://orcid.org/<?= $scientist['orcid'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['orcid'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['researchgate'] ?? null)) { ?>
                        <tr>
                            <td>
                                <span class="key">ResearchGate</span>

                                <a href="https://www.researchgate.net/profile/<?= $scientist['researchgate'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['researchgate'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['google_scholar'] ?? null)) { ?>
                        <tr>
                            <td>
                                <span class="key">Google Scholar</span>

                                <a href="https://scholar.google.com/citations?user=<?= $scientist['google_scholar'] ?>" target="_blank" rel="noopener noreferrer"><?= $scientist['google_scholar'] ?></a>

                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!empty($scientist['webpage'] ?? null)) {
                        $web = preg_replace('/^https?:\/\//', '', $scientist['webpage']);
                    ?>
                        <tr>
                            <td>
                                <span class="key">Personal web page</span>

                                <a href="https://<?= $web ?>" target="_blank" rel="noopener noreferrer"><?= $web ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>




</div>





<table class="table">
    <div class="tbody">
        <tr>
            <td>Bild</td>
        </tr>
        <tr>
            <td>Telefon</td>
        </tr>
        <tr>
            <td>Mail</td>
        </tr>
        <tr>
            <td>Position</td>
        </tr>
        <tr>
            <td>Forschungsinteressen</td>
        </tr>
        <tr>
            <td>CV</td>
        </tr>
        <tr>
            <td>Gremien</td>
        </tr>
        <tr>
            <td>Publikationen</td>
        </tr>
        <tr>
            <td>Vorträge & Poster</td>
        </tr>
    </div>
</table>