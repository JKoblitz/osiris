<?php
$departments = $Settings->getDepartments();

if (isset($_GET['type']) && isset($_GET['type']['id'])) {
    $dept = $_GET['type'];
    $id = $dept['id'];
    $departments[$id] =
        [
            "id" => $id,
            "color" => $dept['color'] ?? '#000000',
            "name" => $dept['name']
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
                <?= lang('Add department', 'Abteilung hinzufügen') ?>
            </h5>

            <form action="#" method="get">
                <div class="form-group">
                    <label for="id" class="required element-time">ID (<?= lang('Abbreviation', 'Abkürzung') ?>)</label>
                    <input type="text" class="form-control" name="type[id]" required>
                </div>
                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="type[color]" required>
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name</label>
                        <input type="text" class="form-control" name="type[name]" required>
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

<form action="#" method="post" id="modules-form">
    <?php foreach ($departments as $t => $dept) {
        $color = $dept['color'] ?? '';
    ?>

        <div class="box type" id="type-<?= $t ?>" style="border-color:<?= $color ?>">
            <h2 class="header" style="background-color:<?= $color ?>20">
                <?= $dept['id'] ?>: <?= $dept['name'] ?>
                <a class="btn btn-link px-5 text-primary ml-auto" onclick="moveElementUp('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                <a class="btn btn-link px-5 text-primary" onclick="moveElementDown('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                <a class="btn btn-link px-5 ml-20 text-danger " onclick="deleteElement('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
            </h2>

            <div class="content">
                <!-- <input type="hidden" name="add" value="type"> -->

                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="icon" class="required">ID</label>
                        <input type="text" class="form-control" name="departments[<?= $t ?>][id]" required value="<?= $dept['id'] ?>">
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="departments[<?= $t ?>][color]" value="<?= $dept['color'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name</label>
                        <input type="text" class="form-control" name="departments[<?= $t ?>][name]" required value="<?= $dept['name'] ?? '' ?>">
                    </div>
                </div>


            </div>

        </div>

    <?php } ?>


    <a class="btn btn-osiris" href="#add-type"><i class="ph ph-plus-circle"></i>
        <?= lang('Add department', 'Neue Abteilung hinzufügen') ?>
    </a>

    <button class="btn btn-osiris">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>

</form>


<script>
    function deleteElement(selector) {
        const el = $('#' + selector)
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
</script>