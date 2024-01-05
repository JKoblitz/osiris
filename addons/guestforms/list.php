<h1>
    <i class="ph ph-user-switch text-osiris"></i>
    <?= lang('Guests', 'Gäste') ?>
</h1>


<div class="alert danger mb-20">
    <p class="text-danger">
        Achtung! Dies ist nur ein Prototyp für die Entwicklung! Bitte noch nicht verwenden!
    </p>
</div>


<a href="<?= ROOTPATH ?>/guests/new" class="btn osiris">
    <i class="ph ph-plus"></i>
    <?= lang('Add new guest', 'Neuen Gast anmelden') ?>
</a>

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
        <?php foreach ($osiris->guests->find([]) as $entry) { ?>
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


    </tbody>
</table>