<h1>
    <i class="fad fa-user-graduate"></i>
    <?= $data['name'] ?>
</h1>
<!-- 
<?php
dump($data, true);
?> -->


<form action="<?= ROOTPATH ?>/update-user/<?= $data['_id'] ?>" method="post">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <p>
        <b>Username:</b> <?= $data['username'] ?? '' ?>
    </p>

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm-2">
            <label for="academic_title">Title</label>
            <select name="values[academic_title]" id="academic_title" class="form-control">
                <option value="" <?= $data['academic_title'] == '' ? 'selected' : '' ?>></option>
                <option value="Dr." <?= $data['academic_title'] == 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                <option value="Prof. Dr." <?= $data['academic_title'] == 'Prof. Dr.' ? 'selected' : '' ?>>Prof. Dr.</option>
                <option value="PD Dr." <?= $data['academic_title'] == 'PD Dr.' ? 'selected' : '' ?>>PD Dr.</option>
                <option value="Prof." <?= $data['academic_title'] == 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                <option value="PD" <?= $data['academic_title'] == 'PD' ? 'selected' : '' ?>>PD</option>
                <!-- <option value="Prof. Dr." <?= $data['academic_title'] == 'Prof. Dr.' ? 'selected' : '' ?>>Prof. Dr.</option> -->
            </select>
        </div>
        <div class="col-sm">
            <label for="first"><?= lang('First name', 'Vorname') ?></label>
            <input type="text" name="values[first]" id="first" class="form-control" value="<?= $data['first'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="last"><?= lang('Last name', 'Nachname') ?></label>
            <input type="text" name="values[last]" id="last" class="form-control" value="<?= $data['last'] ?? '' ?>">
        </div>
    </div>

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm">
            <label for="dept"><?= lang('Department', 'Abteilung') ?></label>
            <select name="values[dept]" id="dept" class="form-control">
                <option value="">Unknown</option>
                <?php
                foreach (deptInfo() as $d => $dept) { ?>
                    <option value="<?= $d ?>" <?= $data['dept'] == $d ? 'selected' : '' ?>><?=$dept['name']!=$d ? "$d: ":''?><?= $dept['name'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm">
            <label for="orcid">ORCID</label>
            <input type="text" name="values[orcid]" id="orcid" class="form-control" value="<?= $data['orcid'] ?? '' ?>">
        </div>
    </div>

    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm">
            <label for="telephone"><?= lang('Telephone', 'Telefon') ?></label>
            <input type="text" name="values[telephone]" id="telephone" class="form-control" value="<?= $data['telephone'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="mail">Mail</label>
            <input type="text" name="values[mail]" id="mail" class="form-control" value="<?= $data['mail'] ?? '' ?>">
        </div>
    </div>

    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_controlling" value="1" name="values[is_controlling]" <?= ($data['is_controlling'] ?? false) ? 'checked' : '' ?>>
        <label for="is_controlling">Is Controlling</label>
    </div>

    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_scientist" value="1" name="values[is_scientist]" <?= ($data['is_scientist'] ?? false) ? 'checked' : '' ?>>
        <label for="is_scientist">Is Scientist</label>
    </div>
    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_leader" value="1" name="values[is_leader]" <?= ($data['is_leader'] ?? false) ? 'checked' : '' ?>>
        <label for="is_leader">Is Leader</label>
    </div>
    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_active" value="1" name="values[is_active]" <?= ($data['is_active'] ?? false) ? 'checked' : '' ?>>
        <label for="is_active">Is Active</label>
    </div>

    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>