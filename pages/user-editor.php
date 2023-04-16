<h1>
    <i class="ph ph-regular ph-student"></i>
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


    <?php
    if (!isset($data['names'])) {
        $names = [
            $data['formalname'],
            abbreviateAuthor($data['last'], $data['first'])
        ];
    } else {
        $names = $data['names'];
    }
    ?>

    <div class="box" id="">
        <div class="p-10 pb-0">

            <label for="names" class="font-weight-bold"><?= lang('Names for author matching', 'Namen für das Autoren-Matching') ?></label>
        </div>
        <div class="p-10 pt-0">
            <?php foreach ($names as $n) { ?>
                <div class="input-group input-group-sm d-inline-flex w-auto">
                    <input type="text" name="values[names][]" value="<?= $n ?>" required>
                    <div class="input-group-append">
                        <a class="btn" onclick="$(this).closest('.input-group').remove();">×</a>
                    </div>
                </div>
            <?php } ?>

            <button class="btn btn-primary btn-sm ml-10" type="button" onclick="addName(event, this);">
                <i class="ph ph-regular ph-plus"></i> <?= lang('Add name', 'Füge Namen hinzu') ?>
            </button>
        </div>

    </div>

    <div class="form-group">
        <span><?= lang('Gender', 'Geschlecht') ?>:</span>
        <?php
        $gender = $data['gender'] ?? 'n';
        ?>

        <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="values[gender]" id="gender-m" value="m" <?= $gender == 'm' ? 'checked' : '' ?>>
            <label for="gender-m"><?= lang('Male', 'Männlich') ?></label>
        </div>
        <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="values[gender]" id="gender-f" value="f" <?= $gender == 'f' ? 'checked' : '' ?>>
            <label for="gender-f"><?= lang('Female', 'Weiblich') ?></label>
        </div>
        <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="values[gender]" id="gender-d" value="d" <?= $gender == 'd' ? 'checked' : '' ?>>
            <label for="gender-d"><?= lang('Non-binary', 'Divers') ?></label>
        </div>
        <div class="custom-radio d-inline-block ml-10">
            <input type="radio" name="values[gender]" id="gender-n" value="n" <?= $gender == 'n' ? 'checked' : '' ?>>
            <label for="gender-n"><?= lang('Not specified', 'Nicht angegeben') ?></label>
        </div>

    </div>


    <div class="form-row row-eq-spacing-sm">
        <div class="col-sm">
            <label for="dept"><?= lang('Department', 'Abteilung') ?></label>
            <select name="values[dept]" id="dept" class="form-control">
                <option value="">Unknown</option>
                <?php
                foreach ($Settings->getDepartments() as $d => $dept) { ?>
                    <option value="<?= $d ?>" <?= $data['dept'] == $d ? 'selected' : '' ?>><?= $dept['name'] != $d ? "$d: " : '' ?><?= $dept['name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm">
            <label for="telephone"><?= lang('Telephone', 'Telefon') ?></label>
            <input type="text" name="values[telephone]" id="telephone" class="form-control" value="<?= $data['telephone'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="mail">Mail</label>
            <input type="text" name="values[mail]" id="mail" class="form-control" value="<?= $data['mail'] ?? '' ?>">
        </div>

    </div>

    <div class="form-row row-eq-spacing-sm">

        <div class="col-sm">
            <label for="orcid">ORCID</label>
            <input type="text" name="values[orcid]" id="orcid" class="form-control" value="<?= $data['orcid'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="twitter">Twitter</label>
            <input type="text" name="values[twitter]" id="twitter" class="form-control" value="<?= $data['twitter'] ?? '' ?>">
        </div>
    </div>

    <div class="form-row row-eq-spacing-sm">

        <div class="col-sm">
            <label for="researchgate">ResearchGate Handle</label>
            <input type="text" name="values[researchgate]" id="researchgate" class="form-control" value="<?= $data['researchgate'] ?? '' ?>">
        </div>
        <div class="col-sm">
            <label for="google_scholar">Google Scholar ID</label>
            <input type="text" name="values[google_scholar]" id="google_scholar" class="form-control" value="<?= $data['google_scholar'] ?? '' ?>">
            <small class="text-muted">
                <?= lang('Not the URL! Only the bold part: https://scholar.google.com/citations?user=<b>2G1YzvwAAAAJ</b>&hl=de ', 'Nicht die URL! Nur der fettgedruckte Teil: https://scholar.google.com/citations?user=<b>2G1YzvwAAAAJ</b>&hl=de') ?>
            </small>
        </div>
        <div class="col-sm">
            <label for="webpage">Personal web page</label>
            <input type="text" name="values[webpage]" id="webpage" class="form-control" value="<?= $data['webpage'] ?? '' ?>">
        </div>
    </div>

    <div>
        <div class="form-group custom-checkbox d-inline-block ml-10">
            <input type="checkbox" id="is_active" value="1" name="values[is_active]" <?= ($data['is_active'] ?? false) ? 'checked' : '' ?>>
            <label for="is_active">Is Active</label>
        </div>
        <div class="form-group custom-checkbox d-inline-block ml-10">
            <input type="checkbox" id="is_scientist" value="1" name="values[is_scientist]" <?= ($data['is_scientist'] ?? false) ? 'checked' : '' ?>>
            <label for="is_scientist">Is Scientist</label>
        </div>


        <div class="form-group custom-checkbox ml-10  <?= ($USER['is_admin'] || $USER['is_controlling']) ? ' d-inline-block ' : 'd-none' ?>">
            <input type="checkbox" id="is_controlling" value="1" name="values[is_controlling]" <?= ($data['is_controlling'] ?? false) ? 'checked' : '' ?>>
            <label for="is_controlling">Is Controlling</label>
        </div>

        <div class="form-group custom-checkbox ml-10  <?= ($USER['is_admin'] || $USER['is_controlling']) ? ' d-inline-block ' : 'd-none' ?>">
            <input type="checkbox" id="is_leader" value="1" name="values[is_leader]" <?= ($data['is_leader'] ?? false) ? 'checked' : '' ?>>
            <label for="is_leader">Is Leader</label>
        </div>

    </div>

    <?php if ($data['username'] == $_SESSION['username'] || $USER['is_admin']) { ?>

        <div class="alert alert-signal mb-20">
            <div class="title">
                <?= lang('Profile preferences', 'Profil-Einstellungen') ?>
            </div>

            <div class="mt-10">
                <span><?= lang('Activity display', 'Aktivitäten-Anzeige') ?>:</span>
                <?php
                $display_activities = $data['display_activities'] ?? 'web';
                ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[display_activities]" id="display_activities-web" value="web" <?= $display_activities == 'web' ? 'checked' : '' ?>>
                    <label for="display_activities-web"><?= lang('Web') ?></label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[display_activities]" id="display_activities-print" value="print" <?= $display_activities != 'web' ? 'checked' : '' ?>>
                    <label for="display_activities-print"><?= lang('Print', 'Druck') ?></label>
                </div>
            </div>



            <div class="mt-10">
                <span><?= lang('Coin visibility', 'Sichtbarkeit der Coins') ?>:</span>
                <?php
                $show_coins = $data['show_coins'] ?? 'none';
                ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[show_coins]" id="show_coins-true" value="none" <?= $show_coins == 'none' ? 'checked' : '' ?>>
                    <label for="show_coins-true"><?= lang('For nobody', 'Für niemanden') ?></label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[show_coins]" id="show_coins-myself" value="myself" <?= $show_coins == 'myself' ? 'checked' : '' ?>>
                    <label for="show_coins-myself"><?= lang('For myself', 'Für mich') ?></label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[show_coins]" id="show_coins-all" value="all" <?= $show_coins == 'all' ? 'checked' : '' ?>>
                    <label for="show_coins-all"><?= lang('For all', 'Für jeden') ?></label>
                </div>
            </div>


            <div class="mt-10">
                <span><?= lang('Show achievements', 'Zeige Errungenschaften') ?>:</span>
                <?php
                $hide_achievements = $data['hide_achievements'] ?? false;
                ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[hide_achievements]" id="hide_achievements-false" value="false" <?= $hide_achievements ? '' : 'checked' ?>>
                    <label for="hide_achievements-false"><?= lang('Yes', 'Ja') ?></label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="values[hide_achievements]" id="hide_achievements-true" value="true" <?= $hide_achievements ? 'checked' : '' ?>>
                    <label for="hide_achievements-true"><?= lang('No', 'Nein') ?></label>
                </div>
            </div>
        </div>

        <div class="alert alert-danger mb-20">
            <div class="title">
                <?= lang('Transfer the maintenance of your profile to someone else:', 'Übertrage die Pflege deines Profils an jemand anderes:') ?>
            </div>

            <div class="form-group form-inline mb-0">
                <label for="maintenance">Username:</label>

                <input type="text" list="user-list" name="values[maintenance]" id="maintenance" class="form-control" value="<?= $data['maintenance'] ?? '' ?>">
            </div>

            <datalist id="user-list">
                <?php
                $all_users = $osiris->users->find();
                foreach ($all_users as $s) { ?>
                    <option value="<?= $s['username'] ?>"><?= "$s[last], $s[first] ($s[username])" ?></option>
                <?php } ?>
            </datalist>
        </div>
    <?php } ?>




    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>

<script>
    function addName(evt, el) {
        var group = $('<div class="input-group input-group-sm d-inline-flex w-auto"> ')
        group.append('<input type="text" name="values[names][]" value="" required>')
        // var input = $()
        var btn = $('<a class="btn">')
        btn.on('click', function() {
            $(this).closest('.input-group').remove();
        })
        btn.html('&times;')

        group.append($('<div class="input-group-append">').append(btn))
        // $(el).prepend(group);
        $(group).insertBefore(el);
    }
</script>