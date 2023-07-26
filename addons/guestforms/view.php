<?php
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;

require_once BASEPATH . "/vendor/autoload.php";



$url = 'guests.dsmz.de/' . $id;
$options = new QROptions([]);

try {
    $qr = (new QRCode($options))->render($url);
} catch (Throwable $e) {
    exit($e->getMessage());
}
?>


<h1>
    <?= lang('Guest ', 'Gast') ?>
    #<?= $id ?>
</h1>


<div class="box box-primary">
    <div class="content">
    <h4 class="title mb-0">
        Formular
    </h4>
    <img src="<?= $qr ?>" alt="<?= $id ?>" class="d-block">
    <a href="http://<?= $url ?>" target="_blank" rel="noopener noreferrer" class="link">
        <?= $url ?>
    </a>
    </div>
</div>


<div class="box box-success">



<table class="table table-simple">

<div class="content">
    <h4 class="title mb-0">
        Formulardaten
    </h4>
</div>
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
            <?= $form['category'] ?? '-' ?>
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
</div>