<h1>
    <i class="fad fa-user-graduate"></i>
    <?= $data['name'] ?>
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
        <label for="dept">Dept</label>
        <select name="values[dept]" id="dept" class="form-control">
            <option value="">Unknown</option>
            <?php
            foreach (deptInfo() as $d => $dept) { ?>
                <option value="<?= $d ?>" <?= $data['dept'] == $d ? 'selected' : '' ?>><?= $dept['name'] ?></option>
            <?php } ?>
        </select>
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
        <label for="academic_title">Title</label>
        <input type="text" name="values[academic_title]" id="academic_title" class="form-control" value="<?= $data['academic_title'] ?? '' ?>">
    </div>
    <div class="form-group">
        <label for="orcid">ORCID</label>
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

    <div class="form-group">
        <label for="department">Department (from LDAP)</label>
        <input type="text" name="values[department]" id="department" class="form-control" value="<?= $data['department'] ?? '' ?>" disabled>
    </div>
    <div class="form-group">
        <label for="unit">Unit (from LDAP)</label>
        <input type="text" name="values[unit]" id="unit" class="form-control" value="<?= $data['unit'] ?? '' ?>" disabled>
    </div>
    
    <button type="submit" class="btn btn-primary">
        Update
    </button>
</form>