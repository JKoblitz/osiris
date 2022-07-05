<?php if ($user == $_SESSION['username']) { ?>

    <h2><?= lang('Welcome', 'Willkommen') ?>, <?= $name ?></h2>

    <p class="text-muted">
        <?= lang(
            'This is your personal page. Please review your recent research activities carefully and add new activities.',
            'Dies ist deine persönliche Seite. Bitte überprüfe deine letzten Aktivitäten sorgfältig und füge neue hinzu, falls angebracht.'
        ) ?>
    </p>
<?php } else { ?>
    <h2><?= $name ?></h2>
<?php } ?>

<div class="box box-primary">
    <div class="content">

        <h3 class="title"><i class="far fa-books mr-5"></i> <?= lang('Publications', 'Publikationen') ?></h3>

        <table class="table table-simple">
            <tbody>
                <?php
                $stmt = $db->prepare(
                    "SELECT publication_id, quartal FROM `authors` 
                    INNER JOIN publication USING (publication_id) 
                    WHERE user LIKE ? ORDER BY quartal DESC"
                );
                $stmt->execute([$user]);
                $publication = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($publication)) {
                    echo "<p>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</p>";
                } else foreach ($publication as $pub) { ?>
                    <tr>
                        <td>
                            <?= str_replace('Q', ' Q', $pub['quartal']) ?>
                        </td>
                        <td>
                            <?php print_publication($pub['publication_id']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>


<div class="box box-signal">
    <div class="content">

        <h3 class="title"><i class="far fa-presentation-screen mr-5"></i> Poster</h3>

        <table class="table table-simple">
            <tbody>
                <?php
                $stmt = $db->prepare(
                    "SELECT poster_id, quartal FROM `authors` 
                    INNER JOIN poster USING (poster_id) 
                    WHERE user LIKE ? ORDER BY quartal DESC"
                );
                $stmt->execute([$user]);
                $publication = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($publication)) {
                    echo "<p>" . lang('No publications found.', 'Keine Publikationen gefunden.') . "</p>";
                } else foreach ($publication as $pub) { ?>
                    <tr>
                        <td>
                            <?= str_replace('Q', ' Q', $pub['quartal']) ?>
                        </td>
                        <td>
                            <?php print_poster($pub['poster_id']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<div class="box box-success">
    <div class="content">

        <h3 class="title"><i class="far fa-calendar-days mr-5"></i> <?= lang('Activities', 'Aktivitäten') ?></h3>

        <table class="table table-simple">
            <tbody>
                <?php
                //     $stmt = $db->prepare(
                //         "SELECT publication_id, quartal FROM `authors` 
                // LEFT JOIN publication USING (publication_id) 
                // WHERE user LIKE ? ORDER BY quartal DESC"
                //     );
                //     $stmt->execute([$user]);
                $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($activities)) {
                    echo "<p>" . lang('No activities found.', 'Keine Aktivitäten gefunden.') . "</p>";
                } else foreach ($activities as $pub) { ?>
                    <tr>
                        <td>
                            <?= str_replace('Q', ' Q', $pub['quartal']) ?>
                        </td>
                        <td>
                            <?php print_publication($pub['publication_id']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>