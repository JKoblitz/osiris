<h1>
    <i class="fad fa-trophy text-signal"></i>
    <?= lang('Achievements', 'Errungenschaften') ?>
</h1>

<?php
// SassCompiler::run("scss/", "css/");
?>

<link rel="stylesheet" href="<?= ROOTPATH ?>/css/achievements.css">

<?php
$Achievement = new Achievement($osiris);
$Achievement->initUser($user);

$achievements = $Achievement->achievements;

$Achievement->checkAchievements();
$user_ac = $Achievement->userac;
if ($user == $_SESSION['username'] && !empty($Achievement->new)) {
    echo '<div class="alert alert-signal m-10">';
    echo '<h5 class="title font-size-16">' . lang('Congratulation, you achieved something new: ', 'Glückwunsch, du hast neue Errungenschaften erlangt:') . '</h5>';
    foreach ($Achievement->new as $i => $n) {
        $Achievement->snack($n);
    }
    echo '</div>';
    $Achievement->save();
}

$firstname = $scientist['first'];
?>


<div class="row">

    <?php foreach ($achievements as $id => $ac) {
        $uac = $user_ac[$id] ?? [];
        $user_descr = lang('<em>Unachieved.</em>', '<em>Noch nicht erreicht.</em>');
    ?>
        <div class="d-flex col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-25">
            <div class="tile achievement max-<?= $ac['maxlvl'] ?> lvl<?= $uac['level'] ?? 0 ?>" id="<?= $id ?>">
                <h3 class="title"><?= $ac['title'][$Achievement->lang_g] ?></h3>
                <h5>Level
                    <?php
                    $lvl = $uac['level'] ?? 0;
                    if ($lvl == $ac['maxlvl']) $lvl = "max";
                    echo $lvl;
                    ?>
                </h5>
                <div class="tile-content">
                    <small class="text-muted"><?= $uac['achieved'] ?? '-' ?></small>
                    <?php
                    include BASEPATH . "/img/achievements/ac_$ac[icon].svg";
                    ?>
                    <div class="level">
                        <?php foreach ($ac['levels'] ?? [] as $lvl) {
                            $descr = $lvl[$Achievement->lang];
                            if ($user == $_SESSION['username']) {
                                $descr = str_replace(lang('have', 'hat'), lang('has', 'hast'), $descr);
                                $descr = str_replace('*', 'Du', $descr);
                            } else {
                                $descr = str_replace('*', $firstname, $descr);
                            }
                            if ($lvl['level'] == ($uac['level'] ?? 0)) {
                                $user_descr = $descr;
                            }
                        ?>
                            <span data-toggle="tooltip" data-title="<?= $descr ?>"></span>
                        <?php } ?>
                    </div>

                    <p>
                        <?= $user_descr ?>
                    </p>
                </div>
            </div>
        </div>
    <?php } ?>

</div>