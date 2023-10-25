<h1>
    <?= lang('Forgot password', 'Passwort vergessen') ?>
</h1>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') { ?>
    <form action="#" method="post">
        <input type="hidden" name="username" value="<?=$user['username']?>">
        <div class="form-group">
            <label class="required" for="password"><?= lang('New password', 'Neues Password') ?></label>
            <input class="form-control" type="password" id="password" name="password" required>
        </div>
        <button class="btn"><?= lang('Reset password', 'Passwort zurücksetzen') ?></button>
    </form>
<?php } else { ?>
    <form action="#" method="post">
        <div class="row row-eq-spacing">
            <div class="col-sm">
                <label class="required" for="username">Username </label>
                <input class="form-control" type="text" id="username" name="values[username]" required>
            </div>
            <div class="col-sm">
                <label for="mail" class="required">Mail</label>
                <input type="text" name="values[mail]" id="mail" class="form-control" value="" required>
            </div>
        </div>

        <div class="row row-eq-spacing">
            <div class="col-sm">
                <label class="required" for="first"><?= lang('First name', 'Vorname') ?></label>
                <input type="text" name="values[first]" id="first" class="form-control" value="" required>
            </div>
            <div class="col-sm">
                <label class="required" for="last"><?= lang('Last name', 'Nachname') ?></label>
                <input type="text" name="values[last]" id="last" class="form-control" value="" required>
            </div>
        </div>

        <button class="btn"><?= lang('Reset password', 'Passwort zurücksetzen') ?></button>
    </form>
<?php } ?>