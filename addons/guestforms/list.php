<?php
    
$pagetitle = lang('Guests', 'Gäste');
$filter = [];
if (!$Settings->hasPermission('guests.view')) {
    $filter = [
        'supervisor.user' => $_SESSION['username']
    ];
    $pagetitle = lang('My guests', 'Meine Gäste');
}

?>


<h1>
    <i class="ph ph-user-switch text-osiris"></i>
    <?= $pagetitle ?>
</h1>

<?php if ($Settings->hasPermission('guests.add')) { ?>
<a href="<?= ROOTPATH ?>/guests/new" class="btn osiris">
    <i class="ph ph-plus"></i>
    <?= lang('Add new guest', 'Neuen Gast anmelden') ?>
</a>
<?php } ?>

<?php
$guest_forms = $Settings->featureEnabled('guest-forms');
?>


<table class="table mt-10">
    <thead>
        <tr>
            <th>ID</th>
            <th><?= lang('Name of guest', 'Name des Gastes') ?></th>
            <th><?= lang('Affiliation', 'Affiliation') ?></th>
            <th><?= lang('Time of stay', 'Zeitraum des Aufenthalts') ?></th>
            <th><?= lang('Supervisor', 'Betreuer:in') ?></th>
            <?php if ($guest_forms) { ?>
                <th><?= lang('Complete', 'Vollständig') ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($osiris->guests->find($filter) as $entry) { 
            $i++;
            ?>
            <tr>
                <td>
                    <a href="<?= ROOTPATH ?>/guests/view/<?= $entry['id'] ?>">#<?= $entry['id'] ?></a>
                </td>
                <td>
                    <?= $entry['guest']['academic_title'] ?? '' ?>
                    <?= $entry['guest']['first'] ?? '' ?>
                    <?= $entry['guest']['last'] ?? '' ?>
                </td>
                <td>
                    <?= $entry['affiliation']['name'] ?? '' ?>
                </td>
                <td>
                    <?= fromToDate($entry['start'], $entry['end'] ?? null) ?>
                </td>
                <td>
                    <?= $entry['supervisor']['name'] ?>
                </td>
                <?php if ($guest_forms) { ?>
                    <td>
                        <?php
                        $finished = ($entry['legal']['general'] ?? false);
                        echo bool_icon($finished);
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
<?php if ($i==0) { ?>
    <tr>
        <td colspan="3"><?=lang('No data found.', 'Keine Daten gefunden.')?></td>
    </tr>
<?php } ?>


    </tbody>
</table>