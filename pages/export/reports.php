<div class="box box-primary">
    <div class="content">

        <h5><?= lang('Export reports', 'Exportiere Berichte') ?></h5>

        <form action="<?= ROOTPATH ?>/export/reports" method="post">

            <div class="form-row row-eq-spacing-sm">
                <div class="col-sm">
                    <label class="required" for="start">
                        <?= lang('Beginning of report', 'Anfang des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="start" id="start" value="<?=SELECTEDYEAR?>-01-01" required>
                </div>
                <div class="col-sm">
                    <label class="required" for="end">
                        <?= lang('End of report', 'Ende des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="end" id="end" value="<?=SELECTEDYEAR?>-06-30" required>
                </div>
            </div>

            <div class="form-group">
                <label for="style">Report-Style</label>
                <select name="style" id="style" class="form-control">
                    <option value="research-report">Research report</option>
                </select>
            </div>

            <button class="btn" type="submit"><?=lang('Generate report', 'Report erstellen')?></button>
        </form>

    </div>
</div>