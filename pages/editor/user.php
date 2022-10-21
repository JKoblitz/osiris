
<h1>
    <i class="fad fa-user-graduate"></i>
    <?=$data['name']?>
</h1>


<form action="<?= ROOTPATH ?>/update-user/<?= $data['_id'] ?>" method="post">
<input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="values[username]" id="username" class="form-control disabled" value="<?= $data['username'] ?? '' ?>" readonly>
    </div>
    <div class="form-group">
        <label for="first">First</label>
        <input type="text" name="values[first]" id="first" class="form-control" value="<?= $data['first'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="last">Last</label>
        <input type="text" name="values[last]" id="last" class="form-control" value="<?= $data['last'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="department">Department</label>
        <input type="text" name="values[department]" id="department" class="form-control" value="<?= $data['department'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="unit">Unit</label>
        <input type="text" name="values[unit]" id="unit" class="form-control" value="<?= $data['unit'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="telephone">Telephone</label>
        <input type="text" name="values[telephone]" id="telephone" class="form-control" value="<?= $data['telephone'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="mail">Mail</label>
        <input type="text" name="values[mail]" id="mail" class="form-control" value="<?= $data['mail'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="dept">Dept</label>
        <select name="values[dept]" id="dept" class="form-control">
            <option value="">Unknown</option>
            <option value="BIDB" <?= $data['dept'] == 'BIDB' ? 'selected' : '' ?>>BIDB</option>
            <option value="BUG" <?= $data['dept'] == 'BUG' ? 'selected' : '' ?>>BUG</option>
            <option value="MIG" <?= $data['dept'] == 'MIG' ? 'selected' : '' ?>>MIG</option>
            <option value="MIOS" <?= $data['dept'] == 'MIOS' ? 'selected' : '' ?>>MIOS</option>
            <option value="MuTZ" <?= $data['dept'] == 'MuTZ' ? 'selected' : '' ?>>MuTZ</option>
            <option value="MÖD" <?= $data['dept'] == 'MÖD' ? 'selected' : '' ?>>MÖD</option>
            <option value="PFVI" <?= $data['dept'] == 'PFVI' ? 'selected' : '' ?>>PFVI</option>
            <option value="NFG" <?= $data['dept'] == 'NFG' ? 'selected' : '' ?>>NFG</option>
            <option value="Services" <?= $data['dept'] == 'Services' ? 'selected' : '' ?>>Services</option>
        </select>
    </div>
    <div class="form-group">
        <label for="academic_title">Title</label>
        <input type="text" name="values[academic_title]" id="academic_title" class="form-control" value="<?= $data['academic_title'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="orcid">Orcid</label>
        <input type="text" name="values[orcid]" id="orcid" class="form-control" value="<?= $data['orcid'] ?? '' ?>">
    </div>
    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_controlling" value="1" name="values[is_controlling]" <?= ($data['is_controlling'] ?? false) ? 'checked' : '' ?>>
        <label for="is_controlling">Is Controlling</label>
    </div>

    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_scientist" value="1" name="values[is_scientist]" <?= ($data['is_scientist'] ?? false) ? 'checked' : '' ?>>
        <label for="is_scientist">Is Scientist</label>
    </div>
    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_leader" value="1" name="values[is_leader]" <?= ($data['is_leader'] ?? false) ? 'checked' : '' ?>>
        <label for="is_leader">Is Leader</label>
    </div>
    <div class="form-group custom-checkbox">
        <input type="checkbox" id="is_active" value="1" name="values[is_active]" <?= ($data['is_active'] ?? false) ? 'checked' : '' ?>>
        <label for="is_active">Is Active</label>
    </div>

    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>