<?php

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;

require_once BASEPATH . "/vendor/autoload.php";



$url = GUEST_SERVER . "/" . $id;
$options = new QROptions([]);

try {
    $qr = (new QRCode($options))->render($url);
} catch (Throwable $e) {
    exit($e->getMessage());
}
?>


<h1>
    <?= lang('Guest ', 'Gast') ?>
    <?php if (!empty($form['guest']) && !empty($form['guest']['last'])) { ?>
        <span class="text-osiris">
            <?= $form['guest']['academic_title'] ?? '-' ?>
            <?= $form['guest']['first'] ?? '-' ?>
            <?= $form['guest']['last'] ?? '-' ?>
        </span>
    <?php } else { ?>
        #<?= $id ?>
    <?php } ?>
</h1>

<div class="d-flex">

    <div class="mr-20 badge bg-white">
        <small><?= lang('Responsible', 'Verantwortlich') ?>: </small>
        <br />
        <b><a href="<?= ROOTPATH ?>/profile/<?= $form['supervisor']['user'] ?? '' ?>">
                <?= $form['supervisor']['name'] ?? '-' ?>
            </a></b>
    </div>
    <div class="mr-20 badge bg-white">
        <small><?= lang('Time frame of the stay', 'Dauer des Aufenthalts') ?>: </small>
        <br />
        <b><?= fromToDate($form['start'], $form['end'] ?? null) ?></b>
    </div>

</div>


<div class="row row-eq-spacing mt-0">

    <div class="col-md-6">

        <?php if ($form['legal']['general'] ?? false) { ?>
            <div class="alert success my-20">
                <h4 class="title">
                    Status
                </h4>
                Der Gast ist angemeldet und hat alle Belehrungen zur Kenntnis genommen.
            </div>
        <?php } else { ?>
            <div class="box danger">
                <div class="content">
                    <h4 class="title">
                        Status
                    </h4>
                    <p class="text-danger">
                        Der Nutzer ist noch nicht vollständig angelegt. Bitte lassen Sie das folgende Formular ausfüllen, um den Vorgang abzuschließen:
                    </p>
                    <img src="<?= $qr ?>" alt="<?= $id ?>" class="d-block">
                    <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer" class="link">
                        <?= $url ?>
                    </a>

                    <form action="<?= ROOTPATH ?>/guests/synchronize/<?= $id ?>" method="post">
                        <button class="btn danger" type="submit">
                            <?= lang('Refresh', 'Aktualisieren') ?>
                        </button>
                    </form>
                </div>
            </div>
        <?php } ?>




        <div class="box">

            <div class="content">
                <h4 class="title mb-0">
                    Formulardaten
                </h4>
            </div>
            <table class="table simple">

                <tr>
                    <th class="w-300"><?= lang('Title', 'Titel') ?></th>
                    <td>
                        <?= $form['guest']['academic_title'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('First name', 'Vorname') ?></th>
                    <td>
                        <?= $form['guest']['first'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Last name', 'Nachname') ?></th>
                    <td>
                        <?= $form['guest']['last'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Date of Birth', 'Geburtstag') ?></th>
                    <td>
                        <?= $form['guest']['birthday'] ?? '-' ?>
                    </td>
                </tr>
                <!-- <tr>
    <td><?= lang('Nationality') ?></td>
    <td>
        <?= $form['guest']['nationality'] ?? '-' ?>
    </td>
</tr> -->
                <tr>
                    <th class="w-300"><?= lang('Telephone', 'Telefon') ?></th>
                    <td>
                        <?= $form['guest']['phone'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('E-Mail', 'E-Mail') ?></th>
                    <td>
                        <?= $form['guest']['mail'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Accomodation during stay', 'Unterkunftsadresse während des Aufenthalts') ?></th>
                    <td>
                        <?= $form['guest']['accomodation'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Company / University', 'Firma / Universität / Schule') ?></th>
                    <td>
                        <?= $form['affiliation']['name'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Address', 'Anschrift') ?></th>
                    <td>
                        <?= $form['affiliation']['address'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Country', 'Land') ?></th>
                    <td>
                        <?= $form['affiliation']['country'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Time frame of the stay', 'Dauer des Aufenthalts') ?></th>
                    <td>
                        <?= format_date($form['start'] ?? null) ?>
                        <?= lang('to', 'bis') ?>
                        <?= format_date($form['end'] ?? null) ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Responsible Scientist', 'Verantwortliche/r Wissenschaftler/in') ?></th>
                    <td>
                        <a href="<?= ROOTPATH ?>/profile/<?= $form['supervisor']['user'] ?? '' ?>">
                            <?= $form['supervisor']['name'] ?? '-' ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Title', 'Titel') ?></th>
                    <td>
                        <?= $form['title'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Purpose of stay', 'Zweck des Aufenthalts') ?></th>
                    <td>
                        <?= $form['category'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('The visit is financed by', 'Die Finanzierung erfolgt') ?></th>
                    <td>
                        <?= $form['payment'] ?? '-' ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('General Agreement', 'Generelle Zustimmung') ?></th>
                    <td>
                        <?= bool_icon($form['legal']['general'] ?? false) ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Legal instructions for data protection', 'Rechtsbelehrung zum Datenschutz') ?></th>
                    <td>
                        <?= bool_icon($form['legal']['data_security'] ?? false) ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Legal instructions for data security', 'Rechtsbelehrung zur Datensicherheit') ?></th>
                    <td>
                        <?= bool_icon($form['legal']['data_protection'] ?? false) ?>
                    </td>
                </tr>
                <tr>
                    <th class="w-300"><?= lang('Safety-Instructions for Short-Time Guests', 'Sicherheitsbelehrung für Kurzzeitgäste') ?></th>
                    <td>
                        <?= bool_icon($form['legal']['safety_instruction'] ?? false) ?>
                    </td>
                </tr>


            </table>

            <div class="content">
                <a href="<?= ROOTPATH ?>/guests/edit/<?= $id ?>" class="btn danger"><?= lang('Edit information', 'Formular bearbeiten') ?></a>
            </div>
        </div>


    </div>



    <div class="modal" id="connect-user" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a data-dismiss="modal" class="btn float-right" role="button" aria-label="Close" href="#close-modal">
                    <span aria-hidden="true">&times;</span>
                </a>
                <h4 class="title"><?= lang('Connect user', 'Nutzer verknüpfen') ?></h4>

                <form action="<?= ROOTPATH ?>/guests/update/<?= $id ?>" method="post">

                    <div class="form-group">
                        <label class="element-author" for="username">
                            <?= lang('Select a person', 'Wähle eine Person') ?>
                        </label>
                        <select class="form-control" id="username" name="values[username]" autocomplete="off">
                            <option value=""><?= lang('-- No user --', '-- Kein Nutzer --') ?></option>
                            <?php
                            $persons = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ['last' => 1]]);
                            foreach ($persons as $j) { ?>
                                <option value="<?= $j['username'] ?>" <?= $j['username'] == ($form['username'] ?? '') ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn primary"><?= lang('Connect', 'Verknüpfen') ?></button>
                </form>
            </div>
        </div>
    </div>


    <div class="col-md-6">

        <div class="box box-signal">
            <div class="content">
                <h4 class="title mb-0">
                    Verknüpfter Nutzer
                </h4>
                <?php if (empty($form['username'] ?? false)) { ?>
                    <p>
                        Es ist zurzeit kein Nutzer verknüpft.
                    </p>
                <?php } else {
                    $username = strval($form['username']);
                    $userArr = $DB->getPerson($username);

                    $img = ROOTPATH . "/img/no-photo.png";
                    if (file_exists(BASEPATH . "/img/users/" . $username . "_sm.jpg")) {
                        $img = ROOTPATH . "/img/users/" . $username . "_sm.jpg";
                    }
                ?>
                    <div class="d-flex align-items-center my-20">

                        <img src="<?= $img ?>" alt="" style="max-height: 7rem;" class="mr-20">
                        <div class="">

                            <h5 class="my-0">
                                <?= $userArr['first'] ?>
                                <?= $userArr['last'] ?>
                            </h5>
                            User:
                            <a href="<?= ROOTPATH ?>/profile/<?= $username ?>" class="badge"><?= $username ?></a>

                        </div>
                    </div>
                <?php } ?>
                <a class="btn primary" href="#connect-user">Nutzer verknüpfen</a>
            </div>
        </div>



        <div class="box">
            <div class="content">
                <h4 class="title mb-0">
                    Hinterlegte Dokumente
                </h4>

                <table class="table simple">
                    <tbody>
                        <tr>
                            <td>
                                Es sind keine Dokumente hinterlegt.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn primary" onclick="todo()">Dokument hochladen</button>
            </div>
        </div>


        <div class="box">
            <div class="content">
                <h4 class="title mb-0">
                    Zugangschips
                </h4>
                <p>
                    Die Person hat keinen Zugangschip
                </p>

                <button class="btn primary" onclick="todo()">Chip hinterlegen</button>

            </div>
        </div>
    </div>

</div>