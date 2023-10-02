<?php

/**
 * Page to inactive a user
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /user/delete/<username>
 *
 * @package     OSIRIS
 * @since       1.2.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

use function PHPSTORM_META\type;

?>

<h1>
    <?= lang('Inactivate', 'Inaktivieren von') ?>
    <?= $data['name'] ?>
</h1>

<form action="<?= ROOTPATH ?>/delete-user/<?= $user ?>" method="post">

    <p class="text-danger">
        <?= lang(
            'Be aware that all personal data will be deleted, except for the name and the username:',
            'Sei dir bewusst, dass alle persönlichen Daten, abgesehen vom Namen und Nutzernamen gelöscht werden:'
        ) ?>
    </p>

    <table class="table">
        <tbody>
            <?php foreach ($data as $key => $value) {
                if (empty($value)) continue;
                if (in_array($key, ['_id', 'displayname', 'formalname', 'first_abbr', 'updated', 'updated_by'])) continue;
                $delete = true;
                if (in_array($key, [
                    "academic_title",
                    "first",
                    "last",
                    "name",
                    "dept",
                    "username"
                ])) {
                    $delete = false;
                }
            ?>
                <tr>
                    <th><?= $key ?></th>
                    <td>
                        <?php if ($value instanceof MongoDB\Model\BSONArray) {
                            echo implode(', ', DB::doc2Arr($value));
                        } elseif (is_array($value)) {
                            echo implode(', ', $value);
                        } else {
                            echo $value;
                        } ?>
                    </td>
                    <td class="text-danger">
                        <?php if ($delete) { ?>
                            <i class="ph ph-trash"></i>
                            <?= lang('Delete', 'Wird gelöscht') ?>
                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>
            <?php if (file_exists(BASEPATH . "/img/users/$user.jpg")) { ?>
                <tr>
                    <th>
                        profile_picture
                    </th>
                    <td>
                        <?= $data['username'] ?>.jpg
                    </td>
                    <td class="text-danger">
                        <i class="ph ph-trash"></i>
                        <?= lang('Delete', 'Wird gelöscht') ?>
                    </td>
                </tr>
            <?php } ?>


        </tbody>
    </table>

    <p>
        <?= lang(
            'After inactivation, a hint will be displayed in the user profile, indicating that it is an inactive account.',
            'Nach dem Inaktivieren wird ein Hinweis auf dem Nutzerprofil zu sehen sein, dass es sich um einen inaktiven Benutzeraccount handelt'
        ) ?>
    </p>

    <button class="btn danger">
        <i class="ph ph-trash"></i>
        <?= lang('Inactivate', 'Inaktivieren') ?>
    </button>

</form>