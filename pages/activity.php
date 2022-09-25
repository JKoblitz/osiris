<?php
dump($activity, true);
?>

<div class="alert alert-signal mt-20">
    <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn btn-signal"><?= lang('Edit activity', 'Bearbeite Aktivität') ?></a>
</div>

<div class="alert alert-danger mt-20">

    <form action="<?= ROOTPATH ?>/delete/<?= $id ?>" method="post">
        <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH . "/activities" ?>">
        <button type="submit" class="btn btn-danger"><?= lang('Delete activity', 'Lösche Aktivität') ?></button>
    </form>
</div>