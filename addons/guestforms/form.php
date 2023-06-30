
<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script src="<?= ROOTPATH ?>/js/moment.min.js"></script>
<script src="<?= ROOTPATH ?>/js/jquery.daterangepicker.min.js"></script>

<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>
<script src="<?= ROOTPATH ?>/js/script.js"></script>


<style>
    form .title {
        margin-bottom: 1rem;
        /* padding-top: .5rem; */
        border-bottom: 1px solid var(--border-color);
    }
</style>

<div class="container">

    <h1><?= lang('Guest Forms', 'Gästeformular') ?></h1>

    <form action="<?= ROOTPATH ?>/guests/save" method="post" class="">
        <p class="text-muted">ID: <?= $id ?></p>

        <input type="hidden" name="values[id]" value="<?= $id ?>">

        <h5 class="title"><?= lang('Guest information', 'Angaben zum Gast') ?></h5>

        <div class="form-row row-eq-spacing" data-module="person">
            <div class="col-sm-2">
                <label for="academic-title"><?= lang('Title', 'Titel') ?>
                </label>
                <input type="text" class="form-control" name="values[guest][academic_title]" id="academic-title" value="<?= $form['guest']['academic_title'] ?? '' ?>">
            </div>
            <div class="col-sm-5">
                <label for="first-name" class="element-other">
                    <?= lang('First name', 'Vorname') ?>
                </label>
                <input type="text" class="form-control" name="values[guest][first]" id="first-name" value="<?= $form['guest']['first'] ?? '' ?>">
            </div>
            <div class="col-sm-5">
                <label for="last-name" class="element-other">
                    <?= lang('Last name', 'Nachname') ?>
                </label>
                <input type="text" class="form-control" name="values[guest][last]" id="last-name" value="<?= $form['guest']['last'] ?? '' ?>">
            </div>
        </div>
        <div class="row" data-module="person">
            <div class="col-sm-6">
                <label for="guest-birthday" class="element-other"><?= lang('Date of Birth', 'Geburtstag') ?></label>
                <input type="date" class="form-control" name="values[guest][birthday]" id="guest-birthday" value="<?= $form['guest']['birthday'] ?? '' ?>">
            </div>
        </div>


        <h5 class="title"><?= lang('Contact', 'Kontaktinformationen') ?></h5>

        <div class="form-group">
            <label for="guest-phone" class="element-other"><?= lang('Telephone', 'Telefon') ?></label>
            <input type="text" class="form-control" name="values[guest][phone]" id="guest-phone" value="<?= $form['guest']['phone'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label for="guest-mail" class="element-other"><?= lang('E-Mail', 'E-Mail') ?></label>
            <input type="text" class="form-control" name="values[guest][mail]" id="guest-mail" value="<?= $form['guest']['mail'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label for="guest-accomodation" class="element-other"><?= lang('Accomodation during stay', 'Unterkunftsadresse während des Aufenthalts') ?></label>
            <input type="text" class="form-control" name="values[guest][accomodation]" id="guest-accomodation" value="<?= $form['guest']['accomodation'] ?? '' ?>">
        </div>


        <h5 class="title"><?= lang('Company / University', 'Firma / Universität / Schule') ?></h5>

        <div class="form-group">
            <label for="guest-affiliation" class="element-other"><?= lang('Name', 'Name') ?></label>
            <input type="text" class="form-control" name="values[affiliation][name]" id="guest-affiliation" value="<?= $form['affiliation']['name'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label for="guest-address" class="element-other"><?= lang('Address', 'Anschrift') ?></label>
            <input type="text" class="form-control" name="values[affiliation][address]" id="guest-address" value="<?= $form['affiliation']['address'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label for="guest-country" class="element-other"><?= lang('Country', 'Land') ?></label>
            <input type="text" class="form-control" name="values[affiliation][country]" id="guest-country" value="<?= $form['affiliation']['country'] ?? '' ?>">
        </div>


        <h5 class="title"><?= lang('Details of the stay', 'Details zum Aufenthalt') ?></h5>

        <div class="form-group" data-module="date-range">
            <label class="element-time" for="date_start">
                <?= lang('Time frame of the stay', 'Dauer des Aufenthalts') ?>
            </label>
            <div class="input-group" id="date-range-picker">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= lang('from', 'von') ?></span>
                </div>
                <input type="date" class="form-control" name="values[start]" id="date_start" value="<?= valueFromDateArray($form['start'] ?? null) ?>">

                <div class="input-group-prepend">
                    <span class="input-group-text"><?= lang('to', 'bis') ?></span>
                </div>
                <input type="date" class="form-control" name="values[end]" id="date_end" value="<?= valueFromDateArray($form['end'] ?? null) ?>">
            </div>

            <!--  <div class="input-group" id="date-range-picker">
                <input class="form-control" name="values[start]" id="date_start">
                <input class="form-control" name="values[end]" id="date_end">
            </div>
            <script>
                // console.log(SINGLE);
                var dateRange = {
                    // format: 'DD.MM.YYYY',
                    separator: ' to ',
                    autoClose: true,
                    monthSelect: true,
                    yearSelect: true,
                    startOfWeek: 'monday',
                    getValue: function() {
                        if ($('#date_start').val() && $('#date_end').val())
                            return $('#date_start').val() + ' to ' + $('#date_end').val();
                        else if ($('#date_start').val())
                            return $('#date_start').val() + ' to ' + $('#date_start').val();
                        else
                            return '';
                    },
                    setValue: function(s, s1, s2) {
                        $('#date_start').val(s1);
                        $('#date_end').val(s2);
                    }
                }

                // $("#date_start")
                //     .dateRangePicker(dateRange)
                $("#date-range-picker").dateRangePicker(dateRange)
                <?php if (!empty($form)) { ?>
                    $('#date-range-picker').data('dateRangePicker')
                        .setStart('<?= valueFromDateArray($form['start'] ?? null) ?>')
                        .setEnd('<?= valueFromDateArray($form['end'] ?? null) ?>');

                <?php } ?>

                // $("#date_end").val($("#date_start").val()).removeClass('disabled')

            </script> -->
        </div>

        <div class="form-group">
            <label class="element-author" for="username">
                <?= lang('Responsible Scientist at the ' . $Settings->affiliation, 'Verantwortliche/r Wissenschaftler/in von ' . $Settings->affiliation) ?>
            </label>
            <select class="form-control" id="username" name="values[user]" autocomplete="off">
                <?php
                foreach ($osiris->users->find([], ['sort' => ["last" => 1]]) as $j) { ?>
                    <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($form['user'] ?? $_SESSION['username']) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group" data-module="title">
            <div class="lang-<?= lang('en', 'de') ?>">
                <label for="title" class="element-title">
                    <?= lang('Title / Topic / Description', 'Titel / Thema / Beschreibung') ?>
                </label>

                <div class="form-group title-editor" id="title-editor"><?= $form['title'] ?? '' ?></div>
                <input type="text" class="form-control hidden" name="values[title]" id="title" value="<?= $form['title'] ?? '' ?>">
            </div>
        </div>
        <script>
            initQuill(document.getElementById('title-editor'));
        </script>

        <div class="form-row row-eq-spacing">

            <div class="col-sm-6">
                <label for="category-guest" class="element-cat"><?= lang('Purpose of stay', 'Zweck des Aufenthalts') ?>:</label>
                <select name="values[category]" id="category-guest" class="form-control">
                    <option value="">-- bitte ausfüllen --</option>
                    <option value="guest scientist" <?= ($form['category'] ?? '') == 'guest scientist' ? 'selected' : '' ?>>Gastwissenschaftler:in</option>
                    <option value="lecture internship" <?= ($form['category'] ?? '') == 'lecture internship' ? 'selected' : '' ?>>Pflichtpraktikum im Rahmen des Studium</option>
                    <option value="student internship" <?= ($form['category'] ?? '') == 'student internship' ? 'selected' : '' ?>>Schülerpraktikum</option>
                    <option value="doctoral thesis" <?= ($form['category'] ?? '') == 'doctoral thesis' ? 'selected' : '' ?>><?= lang('Doctoral Thesis', 'Doktorarbeit') ?></option>
                    <option value="master thesis" <?= ($form['category'] ?? '') == 'master thesis' ? 'selected' : '' ?>><?= lang('Master Thesis', 'Master-Arbeit') ?></option>
                    <option value="bachelor thesis" <?= ($form['category'] ?? '') == 'bachelor thesis' ? 'selected' : '' ?>><?= lang('Bachelor Thesis', 'Bachelor-Arbeit') ?></option>
                    <option value="other" <?= ($form['category'] ?? '') == 'other' ? 'selected' : '' ?>>Sonstiges</option>
                </select>
            </div>
            <div class="col-sm-6">
                <label for="guest-payment" class="element-cat"><?= lang('The visit is financed by', 'Die Finanzierung erfolgt') ?>:</label>
                <select name="values[payment]" id="guest-payment" class="form-control">
                    <option value="">-- bitte ausfüllen --</option>
                    <option value="auf eigene Kosten" <?= ($form['payment'] ?? '') == 'auf eigene Kosten' ? 'selected' : '' ?>><?= lang('myself / my institute', 'auf eigene Kosten') ?></option>
                    <option value="DSMZ" <?= ($form['payment'] ?? '') == 'DSMZ' ? 'selected' : '' ?>><?= lang('DSMZ', 'durch die DSMZ') ?></option>
                    <option value="Alexander von Humboldt-Stiftung" <?= ($form['payment'] ?? '') == 'Alexander von Humboldt-Stiftung' ? 'selected' : '' ?>><?= lang('Alexander von Humboldt-Foundation', 'über die Alexander von Humboldt-Stiftung') ?></option>
                    <option value="DAAD" <?= ($form['payment'] ?? '') == 'DAAD' ? 'selected' : '' ?>><?= lang('DAAD', 'über den DAAD') ?></option>
                    <option value="sonstiges" <?= ($form['payment'] ?? '') == 'sonstiges' ? 'selected' : '' ?>><?= lang('Others (please comment)', 'Weiteres (bitte begründen)') ?></option>
                </select>
            </div>
        </div>




        <h5 class="title"><?= lang('Legal Instructions', 'Belehrungen') ?></h5>

        <div class="form-group">
            <div class="custom-checkbox">
                <input type="checkbox" id="checkbox-1" value="true" name="values[legal][general]" <?= ($form['legal']['general'] ?? false) ? 'checked' : '' ?>>
                <label for="checkbox-1">
                    I assure to observe DSMZ-security rules ...
                    <b> Möchte ich nicht abschreiben, bitte zuschicken.</b>
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="custom-checkbox">
                <input type="checkbox" id="checkbox-2" value="true" name="values[legal][data_security]" <?= ($form['legal']['data_security'] ?? false) ? 'checked' : '' ?>>
                <label for="checkbox-2">
                    Ich habe die <a href="#" onclick="todo()">Rechtsbelehrung zum Datenschutz</a> zur Kenntnis genommen.
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="custom-checkbox">
                <input type="checkbox" id="checkbox-3" value="true" name="values[legal][data_protection]" <?= ($form['legal']['data_protection'] ?? false) ? 'checked' : '' ?>>
                <label for="checkbox-3">
                    Ich habe die <a href="#" onclick="todo()">Rechtsbelehrung zur Datensicherheit</a> zur Kenntnis genommen.
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="custom-checkbox">
                <input type="checkbox" id="checkbox-4" value="true" name="values[legal][safety_instruction]" <?= ($form['legal']['safety_instruction'] ?? false) ? 'checked' : '' ?>>
                <label for="checkbox-4">
                    Ich habe die <a href="#" onclick="todo()">Sicherheitsbelehrung für Kurzzeitgäste</a> zur Kenntnis genommen.
                </label>
            </div>
        </div>


        <button type="submit" class="btn btn-primary">
            <?= lang('Save guest', 'Gast anlegen') ?>
        </button>

    </form>

</div>