<?php
if (!isset($project['collaborators']) || empty($project['collaborators'])){
    $collaborators = [$Settings->get('affiliation_details')];
    $collaborators[0]['role'] = $project['role'];
} else {
    $collaborators = $project['collaborators'];
}
?>


<h2>
    <?= lang('Collaborators', 'Kooperationspartner') ?>
</h2>


<div class="modal" id="collaborators-select" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#close-modal" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>

            <label for="collaborators-search"><?= lang('Search Collaborators', 'Suche nach Kooperationspartnern') ?></label>
            <small class="text-muted">Powered by <a href="https://ror.org/" target="_blank" rel="noopener noreferrer">ROR</a></small>
            <div class="input-group">
                <input type="text" class="form-control" id="collaborators-search" onchange="getCollaborators(this.value)">
                <div class="input-group-append">
                    <button class="btn" onclick="getCollaborators($('#collaborators-search').val())"><i class="ph ph-magnifying-glass"></i></button>
                </div>
            </div>
            <table class="table simple">
                <tbody id="collaborators-suggest">

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="btn-toolbar mb-10">
    <a href="#collaborators-select" class="btn primary">
        <i class="ph ph-edit"></i>
        <?= lang('Add new partner', 'Neuen Partner hinzufügen') ?>
    </a>
    <a href="#" class="btn link text-muted" onclick="addCollabRow()">
        <i class="ph ph-edit"></i>
        <?= lang('Add manually', 'Manuell hinzufügen') ?>
    </a>
</div>

<form action="<?= ROOTPATH ?>/crud/projects/update-collaborators/<?= $id ?>" method="POST">
    <table class="table">
        <thead>
            <tr>
                <th><label class="required" for="name"><?= lang('Name', 'Name') ?></label></th>
                <th><label class="required" for="lead"><?= lang('Role', 'Rolle') ?></label></th>
                <th>
                    <label class="required" for="type"><?= lang('Type', 'Typ') ?></label>
                    <a href="https://ror.readme.io/docs/ror-data-structure#types" target="_blank" rel="noopener noreferrer"><i class="ph ph-arrow-square-out"></i></a>
                </th>
                <!-- <th><label for="ror"><?= lang('ROR-ID') ?></label></th> -->
                <th><label for="location"><?= lang('Location', 'Ort') ?></label></th>
                <th><label class="required" for="country"><?= lang('Country', 'Land') ?></label></th>
                <th><label for="lat"><?= lang('Latitute') ?></label></th>
                <th><label for="lng"><?= lang('Longitude') ?></label></th>
                <th></th>
            </tr>
        </thead>
        <tbody id="collaborators">
            <?php
            foreach ($collaborators as $i => $con) {
            ?>
                <tr id="collab-<?= $i ?>">
                    <td>
                        <input name="values[name][]" type="text" class="form-control " value="<?= $con['name'] ?? '' ?>" required>
                    </td>
                    <td>
                        <?php $t = $con['role'] ?? ''; ?>
                        <select name="values[role][]" type="text" class="form-control " required>
                            <option <?= $t == 'partner' ? 'selected' : '' ?> value="partner">Partner</option>
                            <option <?= $t == 'coordinator' ? 'selected' : '' ?> value="coordinator">Coordinator</option>
                        </select>
                    </td>
                    <td>
                        <?php $t = $con['type'] ?? ''; ?>
                        <select name="values[type][]" type="text" class="form-control " required>
                            <option value="Education" <?= $t == 'Education' ? 'selected' : '' ?>>Education</option>
                            <option value="Healthcare" <?= $t == 'Healthcare' ? 'selected' : '' ?>>Healthcare</option>
                            <option value="Company" <?= $t == 'Company' ? 'selected' : '' ?>>Company</option>
                            <option value="Archive" <?= $t == 'Archive' ? 'selected' : '' ?>>Archive</option>
                            <option value="Nonprofit" <?= $t == 'Nonprofit' ? 'selected' : '' ?>>Nonprofit</option>
                            <option value="Government" <?= $t == 'Government' ? 'selected' : '' ?>>Government</option>
                            <option value="Facility" <?= $t == 'Facility' ? 'selected' : '' ?>>Facility</option>
                            <option value="Other" <?= $t == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </td>
                    <td class="hidden">
                        <input name="values[ror][]" type="text" class="form-control " value="<?= $con['ror'] ?? '' ?>">
                    </td>
                    <td>
                        <input name="values[location][]" type="text" class="form-control " value="<?= $con['location'] ?? '' ?>">
                    </td>
                    <td>
                        <input name="values[country][]" type="text" maxlength="2" class="form-control w-50" value="<?= $con['country'] ?? '' ?>" required>
                    </td>
                    <td>
                        <input name="values[lat][]" type="text" class="form-control w-100" value="<?= $con['lat'] ?? '' ?>">
                    </td>
                    <td>
                        <input name="values[lng][]" type="text" class="form-control w-100" value="<?= $con['lng'] ?? '' ?>">
                    </td>
                    <td>
                        <a class="text-danger my-10" onclick="$(this).closest('tr').remove()"><i class="ph ph-trash"></i></a>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>
    </table>

    <button type="submit" class="btn primary mt-10">
        Save
    </button>
</form>




<script src="<?= ROOTPATH ?>/js/collaborators.js"></script>