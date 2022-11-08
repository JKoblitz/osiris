<?php

$Format = new Format($user);

?>


<div class="modal" id="why-approval" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang(
                                    'Why do I have to confirm my authorships?',
                                    'Warum muss ich meine Autorenschaften bestätigen?'
                                ) ?></h5>
            <p>
                <?= lang('
                    Sometimes other scientists or members of the institute add scientific activities that you were also involved in. 
                    The system tries to assign them automatically, which is why they show up here in this list. 
                    However, a lot can go wrong with this. For reporting purposes, for example, it is not only important that the 
                    bibliographic data is correct, the users must also be correctly assigned. Therefore it is important, <b>if this 
                    is you at all</b> or maybe someone with a similar name, that your <b>name is spelled correctly</b> and 
                    that you were also <b>affiliated with the ' . AFFILIATION . '</b>. 
                    ', '
                    Manchmal fügen andere Wissenschaftler:innen oder Mitglieder des Institutes wissenschaftliche Aktivitäten hinzu,
                    an denen du ebenfalls beteiligt warst. Das System versucht, diese automatisch zuzuordnen, weshalb sie hier 
                    in dieser Liste auftauchen. Allerdings kann dabei sehr viel schief gehen. Für die Berichterstattung ist es z.B. 
                    nicht nur wichtig, dass die bibliographischen Daten korrekt sind, die Nutzer müssen auch korrekt zugeordnet sein. 
                    Deshalb ist es wichtig, <b>ob du das überhaupt bist</b> (oder vielleicht jemand mit einem ähnlichen Namen), 
                    dass <b>dein Name korrekt geschrieben</b> ist und du außerdem <b>der ' . AFFILIATION . ' zugehörig</b> bist. 
                ') ?>
            </p>
            <p>
                <?= lang('
                    <q><b>But I have already confirmed this activity once.</b></q><br>
                    That might be. 
                    Because as soon as an activity is edited, even if it is only that a document was deposited or a spelling mistake in the title was corrected, the confirmation of all authors is reset. 
                    This is to avoid that already confirmed activities are edited without your knowledge. 
                    ', '
                    <q><b>Ich habe diese Aktivität doch aber schon einmal bestätigt.</b></q><br>
                    Das kann sehr gut sein. 
                    Denn sobald eine Aktivität bearbeitet wird, und sei es nur, dass ein Dokument hinterlegt oder ein Rechtschreibfehler im Titel korrigiert wurde, wird die Bestätigung aller Autoren zurückgesetzt. 
                    Dadurch soll vermieden werden, dass ohne dein Wissen bereits bestätigte Aktivitäten bearbeitet werden. 
                ') ?>
            </p>
            <div class="text-right mt-20">
                <a href="#/" class="btn btn-primary" role="button"><?= lang('I understand', 'Ich verstehe') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="why-epub" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Why do I have to review Epubs?', 'Warum muss ich Epubs reviewen?') ?></h5>
            <p>
                <?= lang('
                    [Epub ahead of print] means that the publication is already available online, but the actual publication in an issue is still pending. 
                    An example is the NAR database issue, where publications are already available online in September or October, although the 
                    issue is not published until the following January.  
                    ', '
                    [Epub ahead of print] bedeutet, dass die Publikation bereits online verfügbar ist, 
                    die eigentliche Publikation in einem Issue jedoch noch aussteht. Als Beispiel kann man das NAR database issue nennen,
                    bei dem Publikationen bereits im September oder Oktober online verfügbar sind, obwohl das Issue erst im darauffolgenden Januar
                    erscheint.  
                ') ?>
            </p>
            <p>
                <?= lang('
                    <b>These publications cannot be included in the reports.</b>
                    They are included in OSIRIS in order not to lose sight of them and because they represent achievements already made.
                    But for this reason, it is regularly queried whether the publication has now been published. 
                    Because only then can it be taken into account in the reporting.
                    ', '
                    <b>Diese Publikationen können in den Berichterstattungen nicht berücksichtigt werden.</b>
                    Sie werden in OSIRIS aufgenommen, um sie nicht aus den Augen zu verlieren und weil sie bereits erbrachte Leistungen darstellen.
                    Doch aus diesem Grund wird regelmäßig abgefragt, ob die Publikation nun veröffentlicht wurde. Denn erst dann kann sie in der 
                    Berichterstattung berücksichtigt werden.
                ') ?>
            </p>
            <p>
                <?= lang('
                    <b>The bibliographic data must be checked again.</b> The check mark for "Epub" must be removed and the publication date is usually also adjusted. 
                    Furthermore, it also happens that something changes in the bibliographic data itself. Therefore, please check carefully if all data is correct.
                    ', '
                    <b>Die bibliographischen Daten müssen dazu erneut überprüft werden.</b> Dabei muss der Haken bei "Epub" entfernt werden und i.d.R. wird
                    auch das Veröffentlichungsdatum angepasst. 
                    Des Weiteren passiert es auch, dass sich an den bibliographischen Daten selbst noch etwas ändert. Deshalb überprüft bitte sorgfältig, 
                    ob alle Daten stimmen.
                ') ?>
            </p>
            <div class="text-right mt-20">
                <a href="#/" class="btn btn-primary" role="button"><?= lang('I understand', 'Ich verstehe') ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="why-students" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('Why do I have to review theses?', 'Warum muss ich Abschlussarbeiten überprüfen?') ?></h5>
            <p>
                <?= lang('
                    In order to ensure that the correct status and completion date is always indicated for theses, OSIRIS will issue a warning 
                    if this work is still "in progress" although the completion date is in the past.
                    <b>Please check if the work has already been completed.</b> 
                    If so, please enter whether the work has been successfully completed or not and provide the correct completion date.
                    If the thesis is still "in progress", please extend the period by entering a new expected completion date.
                    OSIRIS will then ask you again in due course if the thesis has been successfully completed.
                    ', '
                    Um sicherzustellen, dass bei Abschlussarbeiten immer der korrekte Status und das korrekte Abschlussdatum angegeben ist, gibt 
                    OSIRIS eine Warnung aus, sollten sich diese Arbeiten noch immer "in Progress" befinden, obwohl das Abschlussdatum in der Vergangenheit befindet.
                    <b>Bitte überprüft, ob die Arbeit bereits abgeschlossen wurde.</b> 
                    In diesem Fall tragt bitte ein, ob die Arbeit erfolgreich beendet wurde oder nicht und gebt das korrekte Abschlussdatum an.
                    Sollte sich die Abschlussarbeit noch immer "in Progress" befinden, verlängert bitte den Zeitraum, indem ihr ein neues voraussichtliches Abschlussdatum angebt.
                    OSIRIS wird euch dann zu gegebener Zeit erneut fragen, ob die Arbeit erfolgreich abgeschlossen wurde.
                ') ?>
            </p>
            <div class="text-right mt-20">
                <a href="#/" class="btn btn-primary" role="button"><?= lang('I understand', 'Ich verstehe') ?></a>
            </div>
        </div>
    </div>
</div>


<?php
// $filter = [
//     "authors" => [
//         '$elemMatch' => [
//             "user" => "$user",
//             "approved" => false
//         ]
//     ]
// ];

$user = $_SESSION['username'];
$filter = ['$or' => [['authors.user' => "$user"], ['editors.user' => "$user"], ['user' => "$user"]]];
$options = ['sort' => ["year" => -1, "month" => -1]];

$collection = $osiris->activities;
$cursor = $collection->find($filter);

$issues = array(
    "approval" => [],
    "epub" => [],
    "students" => [],
    "openend" => []
);

foreach ($cursor as $doc) {
    $has_issues = has_issues($doc);
    if (in_array("approval", $has_issues)) {
        $issues['approval'][] = $doc;
    }
    if (in_array("epub", $has_issues)) {
        $issues['epub'][] = $doc;
    }
    if (in_array("students", $has_issues)) {
        $issues['students'][] = $doc;
    }
    if (in_array("openend", $has_issues)) {
        $issues['openend'][] = $doc;
    }
}
?>

<?php
$a = array_map(function ($a) {
    return empty($a) ? 0 : 1;
}, $issues);
if (array_sum($a) === 0) {
    echo "<p>" . lang(
        "No problems found.",
        "Keine Probleme gefunden."
    ) . "</p>";
}
?>


<?php if (!empty($issues['approval'])) { ?>
    <h2 class="mb-0">
        <?= lang(
            'Please review the following authorships:',
            'Bitte überprüfe die folgenden Autorenschaften:'
        ) ?>
    </h2>
    <p class="mt-0">
        <a href="#why-approval" class=""><?= lang('What does it mean?', 'Was bedeutet das?') ?></a>
    </p>

    <div class="dropdown">
        <button class="btn mb-10 text-success" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-check"></i>
            <?= lang('Approve all', 'Alle bestätigen') ?>
        </button>
        <div class="dropdown-menu w-300" aria-labelledby="dropdown-1">
            <div class="content">
                <form action="<?= ROOTPATH ?>/approve-all" method="post">
                    <input type="hidden" name="user" value="<?= $user ?>">
                    <?= lang(
                        'I confirm that I am the author of <b>all</b> of the following publications and that my affiliation has always been the ' . AFFILIATION . '.',
                        'Ich bestätige, dass ich Autor:in <b>aller</b> folgenden Publikationen bin und meine Affiliation dabei immer die ' . AFFILIATION . ' war.'
                    ) ?>
                    <button class="btn btn-block btn-success" type="submit"><?= lang('Approve all', 'Alle bestätigen') ?></button>
                </form>

            </div>
        </div>
    </div>


    <table class="table">
        <?php
        foreach ($issues['approval'] as $doc) {
            $id = $doc['_id'];
            $type = $doc['type'];
        ?>
            <tr id="tr-<?= $id ?>">
                <td class="w-50"><?= activity_icon($doc); ?></td>
                <td>
                    <?= $Format->format($doc); ?>
                    <div class='alert alert-signal' id="approve-<?= $id ?>">
                        <?= lang('Is this your activity?', 'Ist dies deine Aktivität?') ?>
                        <br>
                        <button class="btn btn-sm text-success" onclick="_approve('<?= $id ?>', 1)">
                            <i class="fas fa-check"></i>
                            <?= lang('Yes, and I was affiliated to the' . AFFILIATION, 'Ja, und ich war der ' . AFFILIATION . ' angehörig') ?>
                        </button>
                        <button class="btn btn-sm text-danger" onclick="_approve('<?= $id ?>', 2)">
                            <i class="fas fa-handshake-slash"></i>
                            <?= lang('Yes, but I was not affiliated to the ' . AFFILIATION, 'Ja, aber ich war nicht der ' . AFFILIATION . ' angehörig') ?>
                        </button>
                        <button class="btn btn-sm text-danger" onclick="_approve('<?= $id ?>', 3)">
                            <i class="fas fa-xmark"></i>
                            <?= lang('No, this is not me', 'Nein, das bin ich nicht') ?>
                        </button>
                        <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn btn-sm text-primary">
                            <i class="fas fa-edit"></i>
                            <?= lang('Edit activity', 'Aktivität bearbeiten') ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if (!empty($issues['epub'])) { ?>
    <h2 class="mb-0">
        <?= lang(
            'Please review the following Epubs:',
            'Bitte überprüfe die folgenden Epubs:'
        ) ?>
    </h2>
    <p class="mt-0">
        <a href="#why-epub" class=""><?= lang('What does it mean?', 'Was bedeutet das?') ?></a>
    </p>

    <table class="table">
        <?php
        foreach ($issues['epub'] as $doc) {
            $id = $doc['_id'];
            $type = $doc['type'];
        ?>
            <tr id="tr-<?= $id ?>">
                <td class="w-50"><?= activity_icon($doc); ?></td>
                <td>
                    <?= $Format->format($doc); ?>
                    <div class='alert alert-signal' id="approve-<?= $id ?>">
                        <?= lang(
                            'This publication is marked as <q>Epub ahead of print</q>. Is it still not officially published?',
                            'Diese Aktivität ist markiert als <q>Epub ahead of print</q>. Ist sie noch nicht offiziell publiziert?'
                        ) ?>
                        <br>
                        <form action="<?= ROOTPATH ?>/update/<?= $id ?>" method="post" class="d-inline mt-5">
                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                            <input type="hidden" name="values[epub-delay]" value="<?= date('Y-m-d') ?>" class="hidden">
                            <button class="btn btn-sm">
                                <i class="fas fa-check"></i>
                                <?= lang('Yes, still epub (ask again later).', 'Ja, noch immer Epub (frag später noch mal).') ?>
                            </button>
                        </form>


                        <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>?epub=true" class="btn btn-sm">
                            <?= lang('No longer Epub (Review)', 'Nicht länger Epub (Review)') ?>
                        </a>
                        <!-- <div class="input-group input-group-sm w-500 d-inline-flex">
                            <input type="date" class="form-control" value="<?= valueFromDateArray(["year" => $doc['year'], "month" => $doc['month'], "day" => $doc['day'] ?? 1]) ?>">
                            <div class="input-group-append">
                                <button class="btn" type="button" onclick="todo()">
                                    <i class="fas fa-xmark"></i>
                                    <?= lang('No longer Epub and officially issued under this date.', 'Kein Epub mehr und unter diesem Datum offiziell veröffentlicht.') ?>
                                </button>
                            </div>
                        </div> -->
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if (!empty($issues['students'])) { ?>
    <h2 class="mb-0">
        <?= lang(
            'Please review the following theses:',
            'Bitte überprüfe die folgenden Abschlussarbeiten:'
        ) ?>
    </h2>
    <p class="mt-0">
        <a href="#why-students" class=""><?= lang('What does it mean?', 'Was bedeutet das?') ?></a>
    </p>

    <table class="table">
        <?php
        foreach ($issues['students'] as $doc) {
            $id = $doc['_id'];
            $type = $doc['type'];
        ?>
            <tr id="tr-<?= $id ?>">
                <td class="w-50"><?= activity_icon($doc); ?></td>
                <td>
                    <?= $Format->format($doc); ?>
                    <div class='alert alert-signal' id="approve-<?= $id ?>">
                        <?= lang(
                            "The Thesis of $doc[name] has ended. Please confirm if the work has been successfully completed or not or extend the time frame.",
                            "Die Abschlussarbeit von $doc[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
                        )  ?>
                        <br>
                        <form action="update/<?= $id ?>" method="post" class="form-inline mt-5">
                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

                            <label class="required" for="end"><?= lang('Ended at / Extend until', 'Geendet am / Verlängern bis') ?>:</label>
                            <input type="date" class="form-control w-200" name="values[end]" id="date_end" value="<?= valueFromDateArray($doc['end'] ?? '') ?>" required>
                            <div>
                                <div class="custom-radio d-inline">
                                    <input type="radio" name="values[status]" id="status-in-progress-<?= $id ?>" value="in progress" checked="checked">
                                    <label for="status-in-progress-<?= $id ?>"><?= lang('In progress', 'In Arbeit') ?></label>
                                </div>

                                <div class="custom-radio d-inline">
                                    <input type="radio" name="values[status]" id="status-completed-<?= $id ?>" value="completed">
                                    <label for="status-completed-<?= $id ?>"><?= lang('Completed', 'Abgeschlossen') ?></label>
                                </div>

                                <div class="custom-radio mr-10 d-inline">
                                    <input type="radio" name="values[status]" id="status-aborted-<?= $id ?>" value="aborted">
                                    <label for="status-aborted-<?= $id ?>"><?= lang('Aborted', 'Abgebrochen') ?></label>
                                </div>
                            </div>
                            <button class="btn" type="submit"><?= lang('Submit', 'Bestätigen') ?></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>



<?php if (!empty($issues['openend'])) { ?>
    <h2 class="mb-0">
        <?= lang(
            'Do you still work on the following activities?',
            'Arbeitest du noch immer an den folgenden Aktivitäten?'
        ) ?>
    </h2>
    <!-- <p class="mt-0">
        <a href="#why-openend" class=""><?= lang('What does it mean?', 'Was bedeutet das?') ?></a>
    </p> -->

    <table class="table">
        <?php
        foreach ($issues['openend'] as $doc) {
            $id = $doc['_id'];
            $type = $doc['type'];
        ?>
            <tr id="tr-<?= $id ?>">
            <tr id="tr-<?= $id ?>">
                <td class="w-50"><?= activity_icon($doc); ?></td>
                <td>
                    <?= $Format->format($doc); ?>
                    <div class='alert alert-signal' id="approve-<?= $id ?>">

                        <form action="<?= ROOTPATH ?>/update/<?= $id ?>" method="post" class="d-inline mt-5">
                            <input type="hidden" class="hidden" name="redirect" value="<?= $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
                            <input type="hidden" name="values[end-delay]" value="<?= date('Y-m-d') ?>" class="hidden">
                            <button class="btn btn-sm text-success">
                                <i class="fas fa-check"></i>
                                <?= lang('Yes (ask again later)', 'Ja (frag später nochmal)') ?>
                            </button>
                        </form>

                        <a href="<?= ROOTPATH ?>/activities/edit/<?= $id ?>" class="btn btn-sm text-danger">
                        <i class="fas fa-xmark"></i>
                            <?= lang('No (Edit)', 'Nein (Bearbeiten)') ?>
                        </a>

                    </div>
                </td>
            </tr>
            </tr>
        <?php } ?>
    </table>
<?php } ?>