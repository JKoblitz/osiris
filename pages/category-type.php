<?php
$color = $color ?? '#000000';

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "crud/types/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/admin/types/" . $form['id'];
    $title = $name;
} else {
    $formaction .= "crud/types/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/admin/types/*";
    $title = lang('New category', 'Neue Kategorie');
}
?>

<div class="modal" id="unique" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('ID must be unique', 'Die ID muss einzigartig sein.') ?></h5>
            <p>
                <?= lang('Each category and each activity type must have a unique ID with which it is linked to an activity.', 'Jede Kategorie und jeder Aktivitätstyp muss eine einzigartige ID haben, mit der er zu einer Aktivität verknüpft wird.') ?>
            </p>
            <p>
                <?= lang('As the ID must be unique, the following previously used IDs and keywords (new) cannot be used as IDs:', 'Da die ID einzigartig sein muss, können folgende bereits verwendete IDs und Schlüsselwörter (new) nicht als ID verwendet werden:') ?>
            </p>
            <ul class="list" id="IDLIST">
                <?php foreach ($osiris->adminTypes->distinct('id') as $k) { ?>
                    <li><?= $k ?></li>
                <?php } ?>
                <li>new</li>
            </ul>
            <div class="text-right mt-20">
                <a href="#/" class="btn primary" role="button"><?= lang('I understand', 'Ich verstehe') ?></a>
            </div>
        </div>
    </div>
</div>



<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <div class="box subtype" style="border-color:<?= $color ?>;">
        <h4 class="header" style="background-color:<?= $color ?>20; color:<?= $color ?>">
            <?php if (!isset($type['new'])) { ?>
                <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> mr-10"></i>
                <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
                <?php if ($type['disabled'] ?? false) { ?>
                    <span class="badge danger ml-20">DISABLED</span>
                <?php } ?>

            <?php } else { ?>
                <?= lang('New type of activity', 'Neuer Typ von Aktivität') ?>
            <?php } ?>
        </h4>

        <?php // if (isset($type['new'])) { 
        ?>

        <div class="content">

            <?php if (isset($type['parent'])) { ?>
                <input type="hidden" name="original_parent" value="<?= $type['parent'] ?>">
            <?php } ?>

            <label for="parent" class="required"><?= lang('Category', 'Übergeordnete Kategorie') ?></label>
            <select name="values[parent]" id="parent" class="form-control" required>
                <?php foreach ($osiris->adminCategories->find() as $cat) { ?>
                    <option value="<?= $cat['id'] ?>" <?= $type['parent'] == $cat['id'] ? 'selected' : '' ?>><?= lang($cat['name'], $cat['name_de']) ?></option>
                <?php } ?>
            </select>
        </div>
        <hr>
        <?php // } 
        ?>
        <div class="content">

            <div class="row row-eq-spacing">

                <?php if (isset($type['id'])) { ?>
                    <input type="hidden" name="original_id" value="<?= $type['id'] ?>">
                <?php } ?>

                <div class="col-sm-2">
                    <label for="icon" class="required">ID</label>
                    <input type="text" class="form-control" name="values[id]" required value="<?= $type['id'] ?>" oninput="sanitizeID(this)">
                    <small><a href="#unique"><i class="ph ph-info"></i> <?= lang('Must be unqiue', 'Muss einzigartig sein') ?></a></small>
                </div>
                <div class="col-sm-2">
                    <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                    <input type="text" class="form-control" name="values[icon]" required value="<?= $type['icon'] ?? 'placeholder' ?>">
                </div>
                <div class="col-sm">
                    <label for="name" class="required ">Name (en)</label>
                    <input type="text" class="form-control" name="values[name]" required value="<?= $type['name'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="name_de" class="">Name (de)</label>
                    <input type="text" class="form-control" name="values[name_de]" value="<?= $type['name_de'] ?? '' ?>">
                </div>
            </div>


            <div class="row row-eq-spacing">
                <div class="col-sm">
                    <label for="description"><?= lang('Description', 'Beschreibung') ?> (en)</label>
                    <input type="text" class="form-control" name="values[description]" value="<?= $type['description'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="description_de" class=""><?= lang('Description', 'Beschreibung') ?> (de)</label>
                    <input type="text" class="form-control" name="values[description_de]" value="<?= $type['description_de'] ?? '' ?>">
                </div>
            </div>

        </div>
        <hr>

        <div class="content">
            <label for="module" class="font-weight-bold">Modules:</label>
            <div class="author-widget">
                <div class="author-list p-10">
                    <?php
                    $module_lst = [];
                    foreach ($type['modules'] ?? array() as $module) {
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
                            <input type='hidden' name='values[modules][]' value='<?= $module ?>'>
                            <a onclick='$(this).parent().remove()'>&times;</a>
                        </div>
                    <?php } ?>

                </div>
                <div class=" footer">
                    <div class="input-group sm d-inline-flex w-auto">
                        <select class="module-input form-control">
                            <option value="" disabled selected><?= lang('Add module ...', 'Füge Module hinzu ...') ?></option>
                            <?php
                            include_once BASEPATH . "/php/Modules.php";
                            $Modules = new Modules();
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
        </div>

        <hr>

        <div class="content">
            <label for="format" class="font-weight-bold">Templates:</label>

            <div class="input-group mb-10">
                <div class="input-group-prepend">
                    <span class="input-group-text w-100">Print</span>
                </div>
                <input type="text" class="form-control" name="values[template][print]" value="<?= $type['template']['print'] ?? '{title}' ?>">
            </div>

            <div class="input-group mb-10">
                <div class="input-group-prepend">
                    <span class="input-group-text w-100">Title</span>
                </div>
                <input type="text" class="form-control" name="values[template][title]" value="<?= $type['template']['title'] ?? '{title}' ?>">
            </div>

            <div class="input-group mb-10">
                <div class="input-group-prepend">
                    <span class="input-group-text w-100">Subtitle</span>
                </div>
                <input type="text" class="form-control" name="values[template][subtitle]" value="<?= $type['template']['subtitle'] ?? '{authors}' ?>">
            </div>

        </div>


        <hr>


        <div class="content">
            <label for="coins" class="font-weight-bold">Coins:</label>
            <input type="text" class="form-control" name="values[coins]" value="<?= $type['coins'] ?? '0' ?>">
            <span class="text-muted">
                <?= lang('Please note that <q>middle</q> authors will receive half the amount.', 'Bitte beachten Sie, dass <q>middle</q>-Autoren nur die Hälfte der Coins bekommen.') ?>
            </span>
        </div>

        <hr>


        <div class="content">
            <div class="custom-checkbox mb-10 danger">
                <input type="checkbox" id="disable-<?= $t ?>-<?= $st ?>" value="true" name="values[disabled]" <?= ($type['disabled'] ?? false) ? 'checked' : '' ?>>
                <label for="disable-<?= $t ?>-<?= $st ?>"><?= lang('Deactivate', 'Deaktivieren') ?></label>
            </div>
            <span class="text-muted">
                <?= lang('Deactivated types are retained for past activities, but no new ones can be added.', 'Deaktivierte Typen bleiben erhalten für vergangene Aktivitäten, es können aber keine neuen hinzugefügt werden.') ?>
            </span>
        </div>

    </div>
    <button class="btn success" id="submitBtn"><?= $btntext ?></button>
</form>



<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script src="<?= ROOTPATH ?>/js/admin-categories.js"></script>