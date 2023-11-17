<?php

$Format = new Document(false, 'portal');
?>

<div class="container">

    <div class="row align-items-center my-0">
        <div class="col flex-grow-0">
            <img src="<?= $img ?>" alt="" class="profile-img" style="max-width: 300px;">
        </div>
        <div class="col ml-20">
            <h1 class="m-0">
                <?= $scientist['academic_title'] ?? '' ?>
                <?= $scientist['first'] ?? '' ?>
                <?= $scientist['last'] ?>
            </h1>


            <?php
            foreach ($scientist['depts'] as $i => $d) {
                $dept = $Groups->getGroup($d);
                if ($i > 0) echo ', ';
            ?>
                <a href="<?= ROOTPATH ?>/portal/group/<?= $dept['id'] ?>" style="color:<?= $dept['color'] ?? 'inherit' ?>">
                    <?php if (in_array($user, $dept['head'] ?? [])) { ?>
                        <i class="ph ph-crown"></i>
                    <?php } ?>
                    <?= $dept['name'] ?>
                </a>
            <?php } ?>
            <?php if (isset($scientist['position']) && !empty($scientist['position'])) { ?>
                <p class="my-0 lead"><?= $scientist['position'] ?></p>
            <?php } ?>
        </div>
    </div>

    <div class="row row-eq-spacing my-0">
        <div class="col-md-8">
            <?php if (isset($scientist['research']) && !empty($scientist['research'])) { ?>
                <div class="pb-10">
                    <h2 class="title"><?= lang('Research interest', 'Forschungsinteressen') ?></h2>

                    <ul class="list">
                        <?php foreach ($scientist['research'] as $key) { ?>
                            <li><?= $key ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if (isset($scientist['cv']) && !empty($scientist['cv'])) {
                $cv = DB::doc2Arr($scientist['cv']);
            ?>
                <div class="pb-10">
                    <h2 class="title"><?= lang('Curriculum Vitae') ?></h2>


                    <div class="biography">
                        <?php foreach ($cv as $entry) { ?>
                            <div class="cv">
                                <span class="time"><?= $entry['time'] ?></span>
                                <h5 class="title"><?= $entry['position'] ?></h5>
                                <span class="affiliation"><?= $entry['affiliation'] ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (isset($scientist['highlighted']) && !empty($scientist['highlighted'])) { ?>
                <div class="pb-10">

                    <h2>Highlighted research</h2>
                    <?php
                    // $scientist['highlighted'] = ['632da4672199cd3df8dbc166'];

                    foreach ($scientist['highlighted'] as $h) {
                        $doc = $DB->getActivity($h);
                        echo $doc['rendered']['web'];
                    } ?>

                </div>
            <?php } ?>


            <?php
            $filter = ['authors.user' => $user, 'type' => 'publication'];
            $options = ['sort' => ['year' => -1, 'month' => -1, 'day' => -1]];
            $N = $osiris->activities->count($filter);

            if ($N > 0) { ?>
                <div class="pb-10">
                    <h2>Publications</h2>
                    <table class="table simple">
                        <?php
                        foreach ($osiris->activities->find($filter, $options) as $doc) {
                            if (isset($scientist['highlighted']) && !empty($scientist['highlighted']) && in_array(strval($doc['_id']), $scientist['highlighted']))
                                continue;
                            echo "<tr><td>";
                            echo $doc['rendered']['icon'];
                            echo "</td><td>";
                            // echo $doc['rendered']['web'];
                            $Format->setDocument($doc);
                            echo $Format->formatShort();
                            echo "</td></tr>";
                        }
                        ?>
                    </table>
                </div>

            <?php } ?>
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