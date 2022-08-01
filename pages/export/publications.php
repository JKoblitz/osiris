<div class="box box-primary">
    <div class="content">
        <form action="<?= ROOTPATH ?>/export/publications" method="post">

            <h5><?= lang('Export publications', 'Exportiere Publikationen') ?></h5>

            <div class="form-group">
                <label for="filter-user"><?= lang('Filter by user', 'Filter nach Nutzer') ?></label>
                <select name="filter[user]" id="filter-user" class="form-control">
                    <option value="<?= $_SESSION['username'] ?>"><?= lang('Only my own publication', 'Nur meine eigenen Publikationen') ?></option>
                    <option value=""><?= lang('All users', 'Alle Nutzer') ?></option>
                </select>
            </div>


            <div class="form-group">
                <label for="filter-year"><?= lang('Filter by year', 'Filter nach Nutzer') ?></label>
                <select name="filter[year]" id="filter-year" class="form-control">
                    <option value="<?= SELECTEDYEAR ?>"><?= lang('Only currently selected year', 'Nur aktuell ausgewähltes Jahr') ?></option>
                    <option value=""><?= lang('All years', 'Alle Jahre') ?></option>
                </select>
            </div>

            <div class="form-group">
                <div class="custom-radio">
                    <input type="radio" name="format" id="format-word" value="word" checked="checked">
                    <label for="format-word">Word</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-radio">
                    <input type="radio" name="format" id="format-bibtex" value="bibtex">
                    <label for="format-bibtex">BibTex</label>
                </div>
            </div>

            <button class="btn">Download</button>

        </form>
    </div>
</div>