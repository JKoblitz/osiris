<?php

/**
 * Page for reports
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /controlling
 *
 * @package     OSIRIS
 * @since       1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<style>
    .custom-radio input#open_access:checked~label::before {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .custom-radio input#open_access-0:checked~label::before {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
</style>


<div class="box primary">
    <div class="content">

        <h2 class="title">
            <?= lang('Export reports', 'Exportiere Berichte') ?>
        </h2>

        <form action="<?= ROOTPATH ?>/reports" method="post">

            <div class="form-row row-eq-spacing-sm">
                <div class="col-sm">
                    <label class="required" for="start">
                        <?= lang('Beginning of report', 'Anfang des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="start" id="start" value="<?= CURRENTYEAR ?>-01-01" required>
                </div>
                <div class="col-sm">
                    <label class="required" for="end">
                        <?= lang('End of report', 'Ende des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="end" id="end" value="<?= CURRENTYEAR ?>-06-30" required>
                </div>
            </div>

            <div class="form-group">
                <label for="style">Report-Style</label>
                <select name="style" id="style" class="form-control">
                    <option value="research-report">Research report</option>
                </select>
            </div>

            <button class="btn primary" type="submit"><?= lang('Generate report', 'Report erstellen') ?></button>
        </form>

    </div>
</div>


<div class="box danger">
    <div class="content">
        <h2 class="title">
            Zeitraum sperren
        </h2>

        <p>
            Du kannst einen Zeitraum sperren, sobald ein Report generiert wurde. Alle aktivitäten, die in diesem Zeitraum report-würdig waren, werden dann gesperrt und können nicht mehr gelöscht oder bearbeitet werden.
        </p>
        <p>
            Aktivitäten, die nicht report-würdig sind (z.B. Online ahead of print, Akt. ohne affilierte Autoren) werden nicht gesperrt.
        </p>

        <form action="#" method="post">

            <div class="form-row row-eq-spacing-sm">
                <div class="col-sm">
                    <label class="required" for="start">
                        <?= lang('Beginning', 'Anfang') ?>
                    </label>
                    <input type="date" class="form-control" name="start" id="start" value="<?= CURRENTYEAR ?>-01-01" required>
                </div>
                <div class="col-sm">
                    <label class="required" for="end">
                        <?= lang('End', 'Ende') ?>
                    </label>
                    <input type="date" class="form-control" name="end" id="end" value="<?= CURRENTYEAR ?>-06-30" required>
                </div>
            </div>
            <div class="my-20">
                <span>Aktion:</span>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="action" id="action-lock" value="lock" checked="">
                    <label for="action-lock"><i class="ph ph-fill ph-lock text-danger"></i> Sperren</label>
                </div>
                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="action" id="action-unlock" value="unlock">
                    <label for="action-unlock"><i class="ph ph-fill ph-lock-open text-success"></i> Entsperren</label>
                </div>
            </div>
            <button class="btn danger" type="submit"><?= lang('Submit', 'Bestätigen') ?></button>

        </form>
    </div>
</div>


<?php if (USER_MANAGEMENT != 'Auth') { ?>

    <div class="box box-signal">
        <div class="content">
            <h2 class="title">
                Nutzer-Synchronisation
            </h2>

            <p>
                Die Personen werden mit LDAP synchronisiert.
            </p>

            <a class="btn signal" href="<?= ROOTPATH ?>/synchronize-users">Synchronisieren</a>

        </div>
    </div>

<?php } ?>