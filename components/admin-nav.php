<nav class="pills">
    <a href="<?= ROOTPATH ?>/admin/general" class="btn <?= $page == 'general' ? 'active' : '' ?>"><?= lang('General', 'Allgemein') ?></a>
    <a href="<?= ROOTPATH ?>/admin/departments" class="btn <?= $page == 'departments' ? 'active' : '' ?>"><?= lang('Departments', 'Abteilungen') ?></a>
    <a href="<?= ROOTPATH ?>/admin/activities" class="btn <?= $page == 'activities' ? 'active' : '' ?>"><?= lang('Activities', 'Aktivitäten') ?></a>
    <a href="<?= ROOTPATH ?>/admin/roles" class="btn <?= $page == 'roles' ? 'active' : '' ?>"><?= lang('Roles', 'Rollen') ?></a>
</nav>