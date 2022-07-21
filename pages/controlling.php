<h2><?= lang('Welcome', 'Willkommen') ?>, <?= $userClass->name ?></h2>

<h4 class="text-muted font-weight-normal">Controlling</h4>


<div class="box box-primary">
    <div class="content">

        <h3 class="title"><i class="far fa-books mr-5"></i> 
        <?= lang('Scientist overview (selected quarter)', 'Übersicht der Forschenden (ausgewähltes Quartal)') ?>
    </h3>

    </div>
        <table class="table table-simple">
            <tbody>
                <?php
                $stmt = $db->prepare(
                    "SELECT users.*, IFNULL(approved, 0) AS approved  FROM `users` 
                    LEFT JOIN (
                        SELECT * FROM users_quarter WHERE q_id LIKE ?
                    ) AS q USING (user) 
                    WHERE is_scientist = 1 ORDER BY last_name"
                );
                $stmt->execute([SELECTEDQUARTER]);
                $scientists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($scientists)) {
                    echo "<div class='content'>" . lang('No scientists found.', 'Keine Forschenden gefunden.') . "</div>";
                } else foreach ($scientists as $s) {
                ?>
                    <tr>
                        <td>
                            <a href="<?=ROOTPATH?>/view/scientist/<?=$s['user']?>">
                            <?= $s['last_name'] ?>, <?= $s['first_name'] ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($s['approved'] == 1) { ?>
                                <i class="fas fa-check text-success"></i>
                            <?php } else { ?>
                                <i class="fas fa-xmark text-danger"></i>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


</div>

