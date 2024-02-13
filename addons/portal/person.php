<?php
$Format = new Document(false, 'portal');
?>
<style>
    .profile-img {
        max-width: 100px; margin-right: 2rem;
    }
</style>
<div class="container">

    <div class="profile-header" style="display: flex; align-items: center;">
        <div class="col" style="flex-grow: 0;">
        <?=$Settings->printProfilePicture($user, 'profile-img rounded')?>
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
                <a href="<?= PORTALPATH ?>/group/<?= $dept['id'] ?>" style="color:<?= $dept['color'] ?? 'inherit' ?>">
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

            <?php
            $highlights = DB::doc2Arr($scientist['highlighted'] ?? array());
            if (!empty($highlights)) { ?>
                <div class="pb-10">

                    <h2>Highlighted research</h2>
                    <table class="table simple">
                        <?php
                        // $highlights = ['632da4672199cd3df8dbc166'];

                        foreach ($highlights as $h) {
                            $doc = $DB->getActivity($h);
                            echo "<tr><td>";
                            echo $doc['rendered']['icon'];
                            echo "</td><td>";
                            // echo $doc['rendered']['web'];
                            $Format->setDocument($doc);
                            echo $Format->formatShort();
                            echo "</td></tr>";
                        } ?>

                    </table>
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
                            if (!empty($highlights) && in_array(strval($doc['_id']), $highlights))
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


            <?php
            $project_filter = [
                '$or' => array(
                    ['contact' => $user],
                    ['persons.user' => $user]
                ),
                "status" => ['$ne' => "rejected"]
            ];

            $count_projects = $osiris->projects->count($project_filter);
            if ($count_projects > 0) { ?>

                <h2><?= lang('Projects', 'Projekte') ?></h2>
                <?php
                $projects = $osiris->projects->find($project_filter, ['sort' => ["start" => -1, "end" => -1]]);

                $ongoing = [];
                $past = [];

                require_once BASEPATH . "/php/Project.php";
                $Project = new Project();
                foreach ($projects as $project) {
                    $Project->setProject($project);
                    if ($Project->inPast()) {
                        $past[] = $Project->widgetPortal($user);
                    } else {
                        $ongoing[] = $Project->widgetPortal($user);
                    }
                }
                ?>
                <?php if (!empty($ongoing)) { ?>

                    <h3><?= lang('Ongoing projects', 'Laufende Projekte') ?></h3>
                    <?php foreach ($ongoing as $html) {
                        echo $html;
                    } ?>
                <?php } ?>
                <?php if (!empty($past)) { ?>
                    <h3><?= lang('Past projects', 'Vergangene Projekte') ?></h3>

                    <?php foreach ($past as $html) {
                        echo $html;
                    } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>