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

$color = $idype['color'] ?? '';
$member = $osiris->activities->count(['type' => $id]);

$level = 0;

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "categories/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/categories/view/" . $form['_id'];
    $iditle = $name;

    $level = $Groups->getLevel($id);
} else {
    $formaction .= "categories/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/categories/view/*";
    $iditle = lang('New category', 'Neue Kategorie');
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


?>

<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">


    <?php
    $type = $form;
    $t = $id;
          $color = $type['color'] ?? '';
          $member = $osiris->activities->count(['type' => $t]);
      ?>

          <div class="box type" id="type-<?= $t ?>" style="border-color:<?= $color ?>; <?= isset($type['new']) ? 'opacity:.8;font-style:italic;' : '' ?>">
              <h2 class="header" style="background-color:<?= $color ?>20">
                  <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> text-<?= $t ?> mr-10"></i>
                  <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
                  <a class="btn link px-5 text-primary ml-auto" onclick="moveElementUp('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                  <a class="btn link px-5 text-primary" onclick="moveElementDown('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                  <?php if ($member == 0) { ?>
                      <a class="btn link px-5 ml-20 text-danger " onclick="deleteElement('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
                  <?php } else { ?>
                      <a class="btn link px-5 ml-20 text-muted " href='<?= ROOTPATH ?>/search/activities#{"$and":[{"type":"<?= $t ?>"}]}' target="_blank" data-toggle="tooltip" data-title="<?= lang("Can\'t delete category: $member activities associated.", "Kann Kategorie nicht löschen: $member Aktivitäten zugeordnet.") ?>"><i class="ph ph-trash"></i></a>
                  <?php } ?>


              </h2>

              <div class="content">
                  <input type="hidden" name="activities[<?= $t ?>][id]" value="<?= $t ?>">
                  <input type="hidden" name="add" value="type">

                  <div class="row row-eq-spacing">

                      <div class="col-sm">
                          <label for="icon" class="required">ID</label>
                          <input type="text" class="form-control disabled" name="activities[<?= $t ?>][id]" required value="<?= $type['id'] ?>" readonly>
                      </div>
                      <div class="col-sm">
                          <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                          <input type="text" class="form-control" name="activities[<?= $t ?>][icon]" required value="<?= $type['icon'] ?? 'placeholder' ?>">
                      </div>
                      <div class="col-sm">
                          <label for="name_de" class="">Color</label>
                          <input type="color" class="form-control" name="activities[<?= $t ?>][color]" value="<?= $type['color'] ?? '' ?>">
                      </div>
                  </div>

                  <div class="row row-eq-spacing">
                      <div class="col-sm">
                          <label for="name" class="required ">Name (en)</label>
                          <input type="text" class="form-control" name="activities[<?= $t ?>][name]" required value="<?= $type['name'] ?? '' ?>">
                      </div>
                      <div class="col-sm">
                          <label for="name_de" class="">Name (de)</label>
                          <input type="text" class="form-control" name="activities[<?= $t ?>][name_de]" value="<?= $type['name_de'] ?? '' ?>">
                      </div>
                  </div>


                  <div class="children">
                      <?php
                      foreach ($type['children'] as $subtype) {
                          $st = $subtype['id'];
                          $submember = $osiris->activities->count(['type' => $t, 'subtype' => $st]);
                      ?>
                          <div class="box subtype" id="subtype-<?= $st ?>" style="border-color:<?= $color ?>; <?= isset($subtype['new']) ? 'opacity:.9;font-style:italic;' : '' ?>">
                              <h4 class="header" style="background-color:<?= $color ?>20">
                                  <i class="ph ph-<?= $subtype['icon'] ?? 'placeholder' ?> text-<?= $t ?> mr-10"></i>
                                  <?= lang($subtype['name'], $subtype['name_de'] ?? $subtype['name']) ?>
                                  <?php if ($subtype['disabled'] ?? false) { ?>
                                      <span class="badge danger ml-20">DISABLED</span>
                                  <?php } ?>

                                  <a class="btn link px-5 text-primary ml-auto" onclick="moveElementUp('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                                  <a class="btn link px-5 text-primary" onclick="moveElementDown('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                                  <?php if ($submember == 0) { ?>
                                      <a class="btn link px-5 ml-20 text-danger " onclick="deleteElement('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
                                  <?php } else { ?>
                                      <a class="btn link px-5 ml-20 text-muted " href='<?= ROOTPATH ?>/search/activities#{"$and":[{"type":"<?= $t ?>"},{"subtype":"<?= $st ?>"}]}' target="_blank" data-toggle="tooltip" data-title="<?= lang("Can\'t delete type: $submember activities associated.", "Kann Typ nicht löschen: $submember Aktivitäten zugeordnet.") ?>"><i class="ph ph-trash"></i></a>
                                  <?php } ?>
                              </h4>

                              <input type="hidden" name="activities[<?= $t ?>][children][<?= $st ?>][id]" value="<?= $st ?>">
                              <div class="content">

                                  <div class="row row-eq-spacing">

                                      <div class="col-sm-2">
                                          <label for="icon" class="required">ID</label>
                                          <input type="text" class="form-control <?= isset($type['new']) ? '' : 'disabled' ?>" name="activities[<?= $t ?>][children][<?= $st ?>][id]" required value="<?= $subtype['id'] ?>" <?= isset($type['new']) ? '' : 'readonly' ?>>
                                      </div>
                                      <div class="col-sm-2">
                                          <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                                          <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][icon]" required value="<?= $subtype['icon'] ?? 'placeholder' ?>">
                                      </div>
                                      <div class="col-sm">
                                          <label for="name" class="required ">Name (en)</label>
                                          <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][name]" required value="<?= $subtype['name'] ?? '' ?>">
                                      </div>
                                      <div class="col-sm">
                                          <label for="name_de" class="">Name (de)</label>
                                          <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][name_de]" value="<?= $subtype['name_de'] ?? '' ?>">
                                      </div>
                                  </div>


                                  <div class="row row-eq-spacing">
                                      <div class="col-sm">
                                          <label for="description"><?= lang('Description', 'Beschreibung') ?> (en)</label>
                                          <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][description]" value="<?= $subtype['description'] ?? '' ?>">
                                      </div>
                                      <div class="col-sm">
                                          <label for="description_de" class=""><?= lang('Description', 'Beschreibung') ?> (de)</label>
                                          <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][description_de]" value="<?= $subtype['description_de'] ?? '' ?>">
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
                                          foreach ($subtype['modules'] ?? array() as $module) {
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
                              </div>

                              <hr>

                              <div class="content">
                                  <label for="format" class="font-weight-bold">Templates:</label>

                                  <div class="input-group mb-10">
                                      <div class="input-group-prepend">
                                          <span class="input-group-text w-100">Print</span>
                                      </div>
                                      <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][print]" value="<?= $subtype['template']['print'] ?? '{title}' ?>">
                                  </div>

                                  <div class="input-group mb-10">
                                      <div class="input-group-prepend">
                                          <span class="input-group-text w-100">Title</span>
                                      </div>
                                      <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][title]" value="<?= $subtype['template']['title'] ?? '{title}' ?>">
                                  </div>

                                  <div class="input-group mb-10">
                                      <div class="input-group-prepend">
                                          <span class="input-group-text w-100">Subtitle</span>
                                      </div>
                                      <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][template][subtitle]" value="<?= $subtype['template']['subtitle'] ?? '{authors}' ?>">
                                  </div>

                              </div>


                              <hr>


                              <div class="content">
                                  <label for="coins" class="font-weight-bold">Coins:</label>
                                  <input type="text" class="form-control" name="activities[<?= $t ?>][children][<?= $st ?>][coins]" value="<?= $subtype['coins'] ?? '0' ?>">
                                  <span class="text-muted">
                                      <?= lang('Please note that <q>middle</q> authors will receive half the amount.', 'Bitte bemerken Sie, <q>middle</q>-Autoren nur die Hälfte der Coins bekommen.') ?>
                                  </span>
                              </div>

                              <hr>


                              <div class="content">
                                  <div class="custom-checkbox mb-10 danger">
                                      <input type="checkbox" id="disable-<?= $t ?>-<?= $st ?>" value="true" name="activities[<?= $t ?>][children][<?= $st ?>][disabled]" <?= ($subtype['disabled'] ?? false) ? 'checked' : '' ?>>
                                      <label for="disable-<?= $t ?>-<?= $st ?>"><?= lang('Deactivate', 'Deaktivieren') ?></label>
                                  </div>
                                  <span class="text-muted">
                                      <?= lang('Deactivated types are retained for past activities, but no new ones can be added.', 'Deaktivierte Typen bleiben erhalten für vergangene Aktivitäten, es können aber keine neuen hinzugefügt werden.') ?>
                                  </span>
                              </div>

                          </div>
                      <?php } ?>

                  </div>

                  <a class="btn text-<?= $t ?>" href="#add-sub-type"><i class="ph ph-plus-circle"></i>
                      <?= lang('Add subtype', 'Neuen Typ hinzufügen') ?>
                  </a>


              </div>

          </div>




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