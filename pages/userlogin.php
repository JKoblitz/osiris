<?php
/**
 * Page to log in
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<h1><?= lang('Welcome!', 'Willkommen') ?></h1>

<h5 class="">
    <?php
    if (defined('USER_MANAGEMENT') && USER_MANAGEMENT == 'AUTH') {
        if ($Settings->get('affiliation') === 'LISI') {
            echo lang('Please log-in with your Demo account.', 'Bitte melde dich mit deinem Demo-Benutzeraccount an.');
        } else {
            echo lang('Please log-in with your OSIRIS account.', 'Bitte melde dich mit deinem OSIRIS-Benutzeraccount an.');
        }
    } else {
        echo lang('Please log-in with your ' . $Settings->get('affiliation') . '-Account.', 'Bitte melde dich mit deinem ' . $Settings->get('affiliation') . '-Benutzeraccount an.');
    }
    ?>
</h5>

<form action="<?= ROOTPATH ?>/user/login" method="POST" class="w-400 mw-full">
    <input type="hidden" name="redirect" value="<?= $_GET['redirect'] ?? $_SERVER['REQUEST_URI'] ?>">
    <div class="form-group">
        <label for="username"><?= lang('User name', 'Nutzername') ?>: </label>
        <input class="form-control" id="username" type="text" name="username" placeholder="abc21" required />
    </div>
    <div class="form-group">
        <label for="password"><?= lang('Password', 'Passwort') ?>: </label>
        <input class="form-control" id="password" type="password" name="password" placeholder="your windows password" required />
    </div>


    <input class="btn primary" type="submit" name="submit" value="<?= lang("Log-in", 'Einloggen') ?>" />

    <?php
    if (defined('USER_MANAGEMENT') && USER_MANAGEMENT == 'AUTH') {
        echo "<hr><a class='link' href='" . ROOTPATH . "/auth/new-user'>" . lang(
            'No account? Register now',
            'Noch keinen Account? Jetzt registrieren'
        ) . "</a>";
    }

    if ($Settings->get('affiliation') === 'LISI') {
    ?>

        <div class="alert signal mt-20">
            <div class="title">
                Demo
            </div>

            <?= lang('
                This OSIRIS instance is a demo with the fictional institute LISI. ', '
                Bei dieser OSIRIS-Instanz handelt es sich um eine Demo mit dem fiktiven Institut LISI.'
            ) ?>
        </div>

    <?php
    }
    ?>
</form>