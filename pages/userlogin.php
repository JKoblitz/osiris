<h1><?= lang('Welcome!', 'Willkommen') ?></h1>

<h5 class=""><?=lang('Please log-in to get access to the pages.', 'Bitte melde dich mit deinem '.$Settings->affiliation.'-Benutzeraccount an, um Zugang zu bekommen.')?></h5>

<form action="<?= ROOTPATH ?>/user/login" method="POST" class="w-400 mw-full">
    <input type="hidden" name="redirect" value="<?= $_GET['redirect'] ?? $_SERVER['REQUEST_URI'] ?>">
    <div class="form-group">
        <label for="username"><?=lang('User name', 'Nutzername')?>: </label>
        <input class="form-control" id="username" type="text" name="username" placeholder="abc21" required />
    </div>
    <div class="form-group">
        <label for="password"><?=lang('Password', 'Passwort')?>: </label>
        <input class="form-control" id="password" type="password" name="password" placeholder="your windows password" required />
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="<?=lang("Log-in", 'Einloggen')?>" />
</form>