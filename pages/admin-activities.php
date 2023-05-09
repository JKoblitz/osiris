<?php
include_once BASEPATH . "/php/Modules.php";
$Modules = new Modules();

$activities = $Settings->getActivities();

if (isset($_GET['type']) && isset($_GET['type']['id'])) {
    $type = $_GET['type'];
    $id = $type['id'];
    $activities[$id] =
        [
            "id" => $id,
            "icon" => $type['icon'] ?? 'placeholder',
            "color" => $type['color'] ?? '#000000',
            "name" => $type['name'],
            "name_de" => $type['name_de'] ?? $type['name'],
            "subtypes" => [
                $id => [
                    "id" => $id,
                    "icon" => $type['icon'] ?? 'placeholder',
                    "name" => $type['name'],
                    "name_de" => $type['name_de'] ?? $type['name'],
                    "modules" => [
                        "title",
                        "authors",
                        "date"
                    ],
                    "template" => [
                        "print" => "{authors} ({year}) {title}.",
                        "title" => "{title}",
                        "subtitle" => "{authors}, {date}"
                    ]
                ]
            ]

        ];
}

if (isset($_GET['subtype']) && isset($_GET['subtype']['id'])) {
    $type = $_GET['subtype'];
    $id = $type['id'];
    $activities[$type['type']][$id] = [
        "id" => $id,
        "icon" => $type['icon'] ?? 'placeholder',
        "name" => $type['name'],
        "name_de" => $type['name_de'] ?? $type['name'],
        "modules" => [
            "title",
            "authors",
            "date"
        ],
        "template" => [
            "print" => "{authors} ({year}) {title}.",
            "title" => "{title}",
            "subtitle" => "{authors}, {date}"
        ]

    ];
}
?>
<style>
    form>.box {
        border-left-width: 4px;
    }
</style>

<div class="modal" id="add-type" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title">
                <?= lang('Add category', 'Kategorie hinzufügen') ?>
            </h5>

            <form action="#" method="get">
                <div class="form-group">
                    <label for="id" class="required element-time">ID</label>
                    <input type="text" class="form-control" name="type[id]" required>

                </div>
                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="icon" class="required element-time">Icon</label>
                        <input type="text" class="form-control" name="type[icon]" required>
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="type[color]">
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name (en)</label>
                        <input type="text" class="form-control" name="type[name]" required>
                    </div>
                    <div class="col-sm">
                        <label for="name_de" class="">Name (de)</label>
                        <input type="text" class="form-control" name="type[name_de]">
                    </div>
                </div>
                <button class="btn">Submit</button>
            </form>

            <div class="text-right mt-20">
                <a href="#/" class="btn mr-5" role="button">Close</a>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="add-sub-type" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title">
                <?= lang('Add category', 'Kategorie hinzufügen') ?>
            </h5>

            <form action="#" method="get">
                <div class="row row-eq-spacing">
                    <div class="col-sm">
                        <label for="name" class="required "><?= lang('Category', 'Kategorie') ?></label>
                        <select name="subtype[type]" class="form-control" required>
                            <?php foreach ($Settings->getActivities() as $m => $val) { ?>
                                <option value="<?= $m ?>"><?= $m ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm">
                        <label for="id" class="required">ID</label>
                        <input type="text" class="form-control" name="subtype[id]" required>
                    </div>
                </div>
                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="icon" class="required">Icon</label>
                        <input type="text" class="form-control" name="subtype[icon]" required>
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="subtype[color]">
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name (en)</label>
                        <input type="text" class="form-control" name="subtype[name]" required>
                    </div>
                    <div class="col-sm">
                        <label for="name_de" class="">Name (de)</label>
                        <input type="text" class="form-control" name="subtype[name_de]">
                    </div>
                </div>
                <button class="btn">Submit</button>
            </form>
            <div class="text-right mt-20">
                <a href="#/" class="btn mr-5" role="button">Close</a>
            </div>
        </div>
    </div>
</div>



<form action="#" method="post" id="modules-form" class="">
    <button class="btn btn-success btn-lg position-fixed" style="top: calc(100vh - 10rem);right: 0;z-index: 30;margin: 3rem">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>
    <?php foreach ($activities as $t => $type) {
        $color = $type['color'] ?? '';
    ?>

        <div class="box type" id="type-<?= $t ?>" style="border-color:<?= $color ?>">
            <h2 class="header" style="background-color:<?= $color ?>20">
                <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> text-<?= $t ?>"></i>
                <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
                <a class="btn btn-link px-5 text-primary ml-auto" onclick="moveElementUp('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                <a class="btn btn-link px-5 text-primary" onclick="moveElementDown('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                <a class="btn btn-link px-5 ml-20 text-danger " onclick="deleteElement('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
            </h2>

            <div class="content">
                <input type="hidden" name="activities[<?= $t ?>][id]" value="<?= $t ?>">
                <input type="hidden" name="add" value="type">

                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="icon" class="required element-time">Icon</label>
                        <input type="text" class="form-control" name="activities[<?= $t ?>][icon]" required value="<?= $type['icon'] ?? 'placeholder' ?>">
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="activities[<?= $t ?>][color]" value="<?= $type['color'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name (en)</label>
                        <input type="text" class="form-control" name="activities[<?= $t ?>][name]" required value="<?= $type['name'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name_de" class="">Name (de)</label>
                        <input type="text" class="form-control" name="activities[<?= $t ?>][name_de]" value="<?= $type['name_de'] ?? '' ?>">
                    </div>
                </div>


                <div class="subtypes">
                    <?php foreach ($type['subtypes'] as $subtype) {
                        $st = $subtype['id'];
                    ?>
                        <div class="box subtype" id="subtype-<?= $st ?>" style="border-color:<?= $color ?>">
                            <h4 class="header" style="background-color:<?= $color ?>20">
                                <i class="ph ph-<?= $subtype['icon'] ?? 'placeholder' ?> text-<?= $t ?>"></i>
                                <?= lang($subtype['name'], $subtype['name_de'] ?? $subtype['name']) ?>

                                <a class="btn btn-link px-5 text-primary ml-auto" onclick="moveElementUp('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                                <a class="btn btn-link px-5 text-primary" onclick="moveElementDown('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                                <a class="btn btn-link px-5 ml-20 text-danger " onclick="deleteElement('subtype-<?= $st ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
                            </h4>

                            <input type="hidden" name="activities[<?= $t ?>][subtypes][<?= $st ?>][id]" value="<?= $st ?>">

                            <div class="row row-eq-spacing">
                                <div class="col-sm">
                                    <label for="icon" class="required element-time">Icon</label>
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][icon]" required value="<?= $subtype['icon'] ?? 'placeholder' ?>">
                                </div>
                                <div class="col-sm">
                                    <label for="name" class="required ">Name (en)</label>
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][name]" required value="<?= $subtype['name'] ?? '' ?>">
                                </div>
                                <div class="col-sm">
                                    <label for="name_de" class="">Name (de)</label>
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][name_de]" value="<?= $subtype['name_de'] ?? '' ?>">
                                </div>
                            </div>

                            <hr>

                            <div class="content">
                                <label for="module" class="font-weight-bold">Modules:</label>
                                <div class="border author-widget">
                                    <div class="author-list p-10">
                                        <?php foreach ($subtype['modules'] ?? array() as $module) {
                                            $req = '';
                                            $name = $module;
                                            if (str_ends_with($module, '*') || in_array($module, ['title', 'authors', 'date', 'date-range'])) {
                                                $name = str_replace('*', '', $module);
                                                $module = $name .= "*";
                                                $req = 'required';
                                            }
                                        ?>
                                            <div class='author <?= $req ?>' ondblclick="toggleRequired(this)">
                                                <?= $name ?>
                                                <input type='hidden' name='activities[<?= $t ?>][subtypes][<?= $st ?>][modules][]' value='<?= $module ?>'>
                                                <a onclick='$(this).parent().remove()'>&times;</a>
                                            </div>
                                        <?php } ?>

                                    </div>
                                    <div class=" p-10 bg-light border-top d-flex">
                                        <div class="input-group input-group-sm d-inline-flex w-auto">
                                            <select class="module-input form-control">
                                                <option value="" disabled selected><?= lang('Add module ...', 'Füge Module hinzu ...') ?></option>
                                                <?php
                                                foreach ($Modules->all_modules as $m => $_) {
                                                    if (in_array($m, $subtype['modules'])) continue;
                                                ?>
                                                    <option><?= $m ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary h-full" type="button" onclick="addModule('<?= $t ?>', '<?= $st ?>');">
                                                    <i class="ph ph-regular ph-plus"></i>
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
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][template][print]" value="<?= $subtype['template']['print'] ?? '{title}' ?>">
                                </div>

                                <div class="input-group mb-10">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text w-100">Title</span>
                                    </div>
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][template][title]" value="<?= $subtype['template']['title'] ?? '{title}' ?>">
                                </div>

                                <div class="input-group mb-10">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text w-100">Subtitle</span>
                                    </div>
                                    <input type="text" class="form-control" name="activities[<?= $t ?>][subtypes][<?= $st ?>][template][subtitle]" value="<?= $subtype['template']['subtitle'] ?? '{authors}' ?>">
                                </div>

                            </div>

                        </div>
                    <?php } ?>

                </div>

                <a class="btn text-<?= $t ?>" href="#add-sub-type"><i class="ph ph-plus-circle"></i>
                    <?= lang('Add subtype', 'Neuen Typ hinzufügen') ?>
                </a>


            </div>

        </div>

    <?php } ?>


    <a class="btn btn-osiris" href="#add-type"><i class="ph ph-plus-circle"></i>
        <?= lang('Add category', 'Neue Kategorie hinzufügen') ?>
    </a>



</form>


<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>

<script>
    function addModule(type, subtype) {

        var el = $('#type-' + type).find('#subtype-' + subtype).find('.author-widget')
        var val = el.find('.module-input').val()
        if (val === undefined || val === null) return;
        console.log(val);
        var author = $('<div class="author">')
            .html(val);
        author.append('<input type="hidden" name="activities[' + type + '][subtypes][' + subtype + '][modules][]" value="' + val + '">')
        author.append('<a onclick="$(this).parent().remove()">&times;</a>')
        author.appendTo(el.find('.author-list'))
    }

    function toggleRequired(el) {
        const element = $(el)
        const input = element.find('input')
        if (element.hasClass('required')) {
            input.val(input.val().replace('*', ''))
        } else {
            input.val(input.val() + '*')
        }
        element.toggleClass('required')
    }

    function deleteElement(selector) {
        const el = $('#' + selector)
        if (el.hasClass('subtype')) {
            // check if there are other subtypes
            const parent = el.closest('.type')
            if (parent.find('.subtype').length <= 1) {
                toastError(lang('Each category needs at least one Type.', 'Jede Kategorie benötigt mindestens einen Typ.'))
                return
            }
        }
        el.remove()
    }

    function moveElementUp(selector) {
        const el = $('#' + selector)
        el.insertBefore(el.prev());
    }

    function moveElementDown(selector) {
        const el = $('#' + selector)
        el.insertAfter(el.next());
    }
    var authordiv = $('.author-list')
    if (authordiv.length > 0) {
        authordiv.sortable({});
    }
</script>