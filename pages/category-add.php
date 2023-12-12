<?php

/**
 * Page to add new groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /groups/new
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$form = $form ?? array();

$color = $type['color'] ?? '';
$member = $osiris->activities->count(['type' => $id]);

$level = 0;

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "categories/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/categories/view/" . $form['_id'];
    $title = $name;

    $level = $Groups->getLevel($id);
} else {
    $formaction .= "categories/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/categories/view/*";
    $title = lang('New category', 'Neue Kategorie');
}

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

function sel($index, $value)
{
    return val($index) == $value ? 'selected' : '';
}

// TODO: position

?>

<?php if ($lvl == 1) { ?>
    <form action="<?= $formaction ?>" method="post" id="group-form">
        <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">


        <div class="box type" id="type-<?= $id ?>" style="border-color:<?= $color ?>; <?= isset($form['new']) ? 'opacity:.8;font-style:italic;' : '' ?>">
            <h2 class="header" style="background-color:<?= $color ?>20">
                <i class="ph ph-<?= $form['icon'] ?? 'placeholder' ?> text-<?= $id ?> mr-10"></i>
                <?= lang($form['name'], $form['name_de'] ?? $form['name']) ?>
            </h2>

            <div class="content">
                <input type="hidden" name="activities[<?= $id ?>][id]" value="<?= $id ?>">
                <input type="hidden" name="add" value="type">

                <div class="row row-eq-spacing">

                    <div class="col-sm">
                        <label for="icon" class="required">ID</label>
                        <input type="text" class="form-control disabled" name="activities[<?= $id ?>][id]" required value="<?= $form['id'] ?>" readonly>
                    </div>
                    <div class="col-sm">
                        <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                        <input type="text" class="form-control" name="activities[<?= $id ?>][icon]" required value="<?= $form['icon'] ?? 'placeholder' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="activities[<?= $id ?>][color]" value="<?= $form['color'] ?? '' ?>">
                    </div>
                </div>

                <div class="row row-eq-spacing">
                    <div class="col-sm">
                        <label for="name" class="required ">Name (en)</label>
                        <input type="text" class="form-control" name="activities[<?= $id ?>][name]" required value="<?= $form['name'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name_de" class="">Name (de)</label>
                        <input type="text" class="form-control" name="activities[<?= $id ?>][name_de]" value="<?= $form['name_de'] ?? '' ?>">
                    </div>
                </div>
            </div>
        </div>

        <button class="btn primary" type="submit" id="submit-btn">
            <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
        </button>

        <datalist id="unit-list">
            <?php
            $units = $osiris->groups->distinct('unit');
            foreach ($units as $u) { ?>
                <option><?= $u ?></option>
            <?php } ?>
        </datalist>
    </form>
<?php } else {
    $t = $form['parent'] ?? $_GET['parent'] ?? null;
    if (empty($t)) die('Parent is needed');
    $parent = $osiris->categories->findOne(['id' => $t, 'level' => 1]);

    $st = $form['id'];
    $member = $osiris->activities->count(['type' => $t, 'subtype' => $st]);
?>
    <h1>
        <i class="ph ph-<?= $form['icon'] ?? 'placeholder' ?> text-<?= $t ?> mr-10"></i>
        <?= lang($form['name'], $form['name_de'] ?? $form['name']) ?>
    </h1>
    <?php if ($form['disabled'] ?? false) { ?>
        <span class="badge danger ml-20">DISABLED</span>
    <?php } ?>

    <fieldset class="alert">
        <legend><?= lang('Parent Category', 'Übergeordnete Kategorie') ?></legend>

        <h5 class="mt-0">
            <i class="ph ph-<?= $parent['icon'] ?? 'placeholder' ?> text-<?= $t ?> mr-10"></i>
            <?= lang($parent['name'], $parent['name_de'] ?? $parent['name']) ?>
        </h5>

        <label for="parent"><?= lang('Change parent', 'Kategorie ändern') ?>:</label>

        <select name="parent" id="parent" class="form-control d-inline-block w-auto">
            <?php foreach ($Categories->categories as $cat) { ?>
                <option value="<?= $cat['id'] ?>" <?=$cat['id'] == $t ? 'selected' : ''?>><?= lang($cat['name'], $cat['name_de']) ?></option>
            <?php } ?>
        </select>

    </fieldset>

    <input type="hidden" name="activities[<?= $t ?>][children][<?= $st ?>][id]" value="<?= $st ?>">
    <fieldset class="alert">
        <legend><?= lang('General Settings', 'Generelle Einstellungen') ?></legend>

        <div class="row row-eq-spacing">

            <div class="col-sm-2">
                <label for="icon" class="required">ID</label>
                <input type="text" class="form-control <?= isset($type['new']) ? '' : 'disabled' ?>" name="activities[<?= $t ?>][children][<?= $st ?>][id]" required value="<?= $form['id'] ?>" <?= isset($type['new']) ? '' : 'readonly' ?>>
            </div>
            <div class="col-sm-2">
                <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][icon]" required value="<?= $form['icon'] ?? 'placeholder' ?>">
            </div>
            <div class="col-sm">
                <label for="name" class="required ">Name (en)</label>
                <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][name]" required value="<?= $form['name'] ?? '' ?>">
            </div>
            <div class="col-sm">
                <label for="name_de" class="">Name (de)</label>
                <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][name_de]" value="<?= $form['name_de'] ?? '' ?>">
            </div>
        </div>


        <div class="row row-eq-spacing">
            <div class="col-sm">
                <label for="description"><?= lang('Description', 'Beschreibung') ?> (en)</label>
                <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][description]" value="<?= $form['description'] ?? '' ?>">
            </div>
            <div class="col-sm">
                <label for="description_de" class=""><?= lang('Description', 'Beschreibung') ?> (de)</label>
                <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][description_de]" value="<?= $form['description_de'] ?? '' ?>">
            </div>
        </div>

    </fieldset>

    <fieldset class="alert">
        <legend>Modules</legend>
        <div class="author-widget">
            <div class="author-list p-10">
                <?php
                $module_lst = [];
                foreach ($form['modules'] ?? array() as $module) {
                    $req = '';
                    $name = trim($module);
                    if (str_ends_with($name, '*') || in_array($name, ['title', 'authors', 'date', 'date-range'])) {
                        $name = str_replace('*', '', $name);
                        $module = $name . "*";
                        $req = 'required';
                    }
                    $module_lst[] = $name;
                ?>
                    <div class='author <?= $req ?>' ondblclick="toggleRequired(this)">
                        <?= $name ?>
                        <input type='hidden' name='activities[<?= $t ?>][children][<?= $st ?>][modules][]' value='<?= $module ?>'>
                        <a onclick='$(this).parent().remove()'>&times;</a>
                    </div>
                <?php } ?>

            </div>
            <div class=" footer">
                <div class="input-group sm d-inline-flex w-auto">
                    <select class="module-input form-control">
                        <option value="" disabled selected><?= lang('Add module ...', 'Füge Module hinzu ...') ?></option>
                        <?php
                        foreach ($Modules->all_modules as $m => $_) {
                            if (in_array($m, $module_lst)) continue;
                        ?>
                            <option><?= $m ?></option>
                        <?php } ?>
                    </select>
                    <div class="input-group-append">
                        <button class="btn primary h-full" type="button" onclick="addModule('<?= $t ?>', '<?= $st ?>');">
                            <i class="ph ph-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>


    <fieldset class="alert">
        <legend>Templates</legend>

        <div class="input-group mb-10">
            <div class="input-group-prepend">
                <span class="input-group-text w-100">Print</span>
            </div>
            <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][print]" value="<?= $form['template']['print'] ?? '{title}' ?>">
        </div>

        <div class="input-group mb-10">
            <div class="input-group-prepend">
                <span class="input-group-text w-100">Title</span>
            </div>
            <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][title]" value="<?= $form['template']['title'] ?? '{title}' ?>">
        </div>

        <div class="input-group mb-10">
            <div class="input-group-prepend">
                <span class="input-group-text w-100">Subtitle</span>
            </div>
            <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][subtitle]" value="<?= $form['template']['subtitle'] ?? '{authors}' ?>">
        </div>

    </fieldset>




    <fieldset class="alert">
        <legend>Coins</legend>
        <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][coins]" value="<?= $form['coins'] ?? '0' ?>">
        <span class="text-muted">
            <?= lang('Please note that <q>middle</q> authors will receive half the amount.', 'Bitte bemerken Sie, <q>middle</q>-Autoren nur die Hälfte der Coins bekommen.') ?>
        </span>
    </fieldset>



    <fieldset class="alert">
        <legend><?= lang('Deactivate', 'Deaktivieren') ?></legend>
        <div class="custom-checkbox mb-10 danger">
            <input type="checkbox" id="disable-<?= $t ?>-<?= $st ?>" value="true" name="activities[<?= $t ?>][children][<?= $st ?>][disabled]" <?= ($form['disabled'] ?? false) ? 'checked' : '' ?>>
            <label for="disable-<?= $t ?>-<?= $st ?>"><?= lang('Deactivate', 'Deaktivieren') ?></label>
        </div>
        <span class="text-muted">
            <?= lang('Deactivated types are retained for past activities, but no new ones can be added.', 'Deaktivierte Typen bleiben erhalten für vergangene Aktivitäten, es können aber keine neuen hinzugefügt werden.') ?>
        </span>
    </fieldset>

<?php } ?>



<?php if (!empty($form)) { ?>


    <?php if ($member == 0) { ?>
        <div class="alert danger mt-20">
            <form action="<?= ROOTPATH ?>/category/delete/<?= $id ?>" method="post">
                <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH ?>/groups">
                <button class="btn danger"><i class="ph ph-trash"></i> <?= lang('Delete', 'Löschen') ?></button>
                <span class="ml-20"><?= lang('Warning! Cannot be undone.', 'Warnung, kann nicht rückgängig gemacht werden!') ?></span>
            </form>
        </div>
    <?php } else { ?>

        <div class="alert danger mt-20">
            <?= lang("Can\'t delete category: $member activities associated.", "Kann Kategorie nicht löschen: $member Aktivitäten zugeordnet.") ?><br>
            <a href='<?= ROOTPATH ?>/search/activities#{"$and":[{"type":"<?= $id ?>"}]}' target="_blank"><?= lang('View activities', 'Aktivitäten zeigen') ?></a>

        </div>
    <?php } ?>


<?php } ?>

<script>
    function deptSelect(val) {
        console.log(val);
        var opt = $('#parent').find('[value=' + val + ']')
        console.log(opt.attr('data-level'));
        if (opt.attr('data-level') != '0') {
            $('#color-row').hide()
        } else {
            $('#color-row').show()
        }
    }
</script>