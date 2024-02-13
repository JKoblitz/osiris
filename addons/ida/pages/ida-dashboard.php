<h1><?= $dashboard['title']['de'] ?></h1>

<form action="<?= ROOTPATH ?>/ida/update-institute" method="post" class="w-300 mw-full">
    <div class="input-group">
        <select name="institute" id="institute-picker" class="form-control">
            <?php foreach ($IDA->institutes as $inst) { ?>
                <option value="<?= $inst['id'] ?>" <?=($inst['id'] == $IDA->institute_id ? 'selected' : '')?>><?= $inst['title'] ?></option>
            <?php } ?>
        </select>
        <div class="input-group-append">
            <button class="btn osiris">Select</button>
        </div>
    </div>
</form>


<div class="link-list">
    <?php foreach ($dashboard['dashboard_item_formulars'] as $formular) { ?>
        <a href="<?= ROOTPATH ?>/ida/formular/<?= $formular['formular_id'] ?>"><?= $formular['formular_short_title'] ?></a>
    <?php } ?>
</div>