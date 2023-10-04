<h1>
    <i class="ph ph-user-switch text-osiris"></i>
    <?= lang('Guests', 'Gäste') ?>
</h1>


<a href="<?= ROOTPATH ?>/guests/new" class="btn osiris">
    <i class="ph ph-plus"></i>
    <?= lang('Add new guest', 'Neuen Gast anmelden') ?>
</a>


<table class="table mt-10">
    <thead>

        <tr>
            <th>ID</th>
            <th><?= lang('Name of guest', 'Name des Gastes') ?></th>
            <th><?= lang('Affiliation', 'Affiliation') ?></th>
            <th><?= lang('Time of stay', 'Zeitraum des Aufenthalts') ?></th>
            <th><?= lang('Supervisor', 'Betreuer:in') ?></th>
            <th><?=lang('Complete', 'Vollständig')?></th>
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
                <td>
                    <?php
                    $finished = (isset($entry['legal']['general']) && $entry['legal']['general']);
                    echo bool_icon($finished);
                    ?>

                </td>
            </tr>
        <?php } ?>


    </tbody>
</table>