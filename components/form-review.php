<?php

$form = array();
// no update possible here
$authors = authorForm($USER);
$formaction = ROOTPATH . "/";
$formaction .= "create/review";
$btntext = '<i class="fas fa-plus"></i> ' . lang("Add", "Hinzufügen");
?>

<form action="<?= $formaction ?>" method="post">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm-3">
            <label class="required" for="role-input">
                <?= lang('Role', 'Rolle') ?>
            </label>
            <select class="form-control" id="role-input" name="values[role]" required autocomplete="off" onchange="updateUI(this)">
                <option value="Reviewer" disabled selected>-- <?= lang('Select role', 'Wähle deine Rolle') ?> --</option>
                <option value="Reviewer">Reviewer</option>
                <option value="Editor">Editorial</option>
            </select>
        </div>
        <div class="col-sm">
            <label class="required" for="journal-input">
                <?= lang('Journal') ?>
            </label>
            <input type="text" class="form-control" placeholder="Journal" id="journal-input" name="values[journal]" list="journal-list" required>

        </div>
        <div class="col-sm-3">
            <label class="required" for="username">
                <?= lang('Scientist', 'Wissenschaftler:in') ?>
            </label>
            <select class="form-control" id="username" name="values[user]" required autocomplete="off">
                <?php
                $userlist = $osiris->users->find();
                foreach ($userlist as $j) { ?>
                    <option value="<?= $j['_id'] ?>" <?=$j['_id']==$user ? 'selected':''?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="reviewer-role" style="display: none;">
        <label class="required" for="date">
            <?= lang('Review date', 'Review-Datum') ?>

        </label>
        <input type="date" class="form-control date" name="values[dates][]" id="date">
        <small class="text-muted">
            <?= lang('More dates can be added later; only month and year are considered', 'Mehr Daten können später hinzugef. werden; nur Monat und Jahr sind relevant') ?>
        </small>
    </div>

    <div class="editor-role" style="display: none;">
        <div class="form-row row-eq-spacing-sm">
            <div class="col-sm">
                <label class="required" for="start">
                    <?= lang('Beginning of editorial activity', 'Anfang der Editor-Tätigkeit') ?>
                </label>
                <input type="date" class="form-control start" name="values[start]" id="start">
            </div>
            <div class="col-sm">
                <label class="" for="end">
                    <?= lang('End', 'Ende') ?>
                </label>
                <input type="date" class="form-control" name="values[end]" id="end">
            </div>
        </div>
        <small class="text-muted">
            <?= lang('Only month and year are considered', 'Nur Monat und Jahr werden gezeigt') ?>
        </small>
    </div>

    <button class="btn btn-primary" type="submit"><?= $btntext ?></button>


    <datalist id="journal-list">
        <?php
        $journal = $osiris->journals->find();
        foreach ($journal as $j) { ?>
            <option><?= $j['journal'] ?></option>
        <?php } ?>
    </datalist>
</form>

<script>
    function updateUI(el) {
        var form = $(this).closest('form')
        if (el.value == "Reviewer") {
            $('.editor-role').slideUp()
            $('.editor-role').find('input.start').attr('required', false)
            $('.reviewer-role').slideDown()
            $('.reviewer-role').find('input.date').attr('required', true)
        } else {
            $('.editor-role').slideDown()
            $('.editor-role').find('input.start').attr('required', true)
            $('.reviewer-role').slideUp()
            $('.reviewer-role').find('input.date').attr('required', false)
        }
    }
</script>