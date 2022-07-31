<h2><?= lang('Welcome', 'Willkommen') ?>, <?= $USER['name'] ?></h2>

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
                
            $cursor = $osiris->users->find(['authors.user' => $user]);
                if (empty($cursor)) {
                    echo "<div class='content'>" . lang('No scientists found.', 'Keine Forschenden gefunden.') . "</div>";
                } else foreach ($cursor as $s) {
                ?>
                    <tr>
                        <td>
                            <a href="<?=ROOTPATH?>/view/scientist/<?=$s['_id']?>">
                            <?= $s['last'] ?>, <?= $s['first'] ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($s['approved']??0 == 1) { ?>
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

