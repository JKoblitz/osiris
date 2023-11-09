<?php

/**
 * Page for admin dashboard for role settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /admin/general
 *
 * @package OSIRIS
 * @since 1.2.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

include BASEPATH . "/components/admin-nav.php";
?>

<?php
$json = file_get_contents(BASEPATH . "/roles.json");
$role_groups = json_decode($json, true, 512, JSON_NUMERIC_CHECK);

?>

<h1>
    <?=lang('Roles &amp; Rights', 'Rollen &amp; Rechte')?>
</h1>

<form action="#" method="post" id="role-form">

    <?php
    $roles = $Settings->get('roles');
    $rights = $Settings->get('rights');
    ?>

    <table class="table my-20">

        <thead>
            <th></th>
            <?php foreach ($roles as $role) { ?>
                <th>
                    <input type="hidden" readonly name="roles[roles][]" value="<?= $role ?>">
                    <?= strtoupper($role) ?>
                </th>
            <?php } ?>
        </thead>
        <tbody>
            <?php foreach ($role_groups as $group) {
                ?>
                <tr>
                    <th colspan="<?=count($roles)+1?>">
                        <?= lang($group['en'], $group['de']) ?>
                    </th>
                </tr>
                <?php foreach ($group['fields'] as $field) {
                    $right = $field['id'];
                    $values = $rights[$right];
                ?>
                    <tr>
                        <td class="pl-20">
                            <?= lang($field['en'], $field['de']) ?> 
                            <code class="code font-size-12 text-muted"><?=$right?></code>
                    </td>
                        <?php foreach ($roles as $n => $key) {
                            $val = $values[$n];
                        ?>
                            <td>
                                <input type="checkbox" <?= $val ? 'checked' : '' ?> onchange="$(this).next().val(Number(this.checked))">
                                <input type="hidden" name="roles[rights][<?= $right ?>][]" value="<?= $val ? 1 : 0 ?>">
                            </td>
                        <?php } ?>

                    </tr>
                <?php } ?>

            <?php } ?>
        </tbody>

    </table>

    <button class="btn success">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>


</form>