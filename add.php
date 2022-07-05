<div class="content">

    <h2>
        Add derivative
    </h2>


    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000" />

        <div class="form-group">
            <label for="name" class="required">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="derivative_group">Metabolite name (if none is selected, dataset is added as <b>Unknown</b>)</label>
            <input type="text" id="derivative_group" name="derivative_group" list="derivative-group" class="form-control">
        </div>
        <datalist id="derivative-group">
            <?php
            $stmt = $db->query("SELECT derivative_group_name FROM `Derivative_Group` ");
            $groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($groups as $g) {
                echo "<option>$g</option>";
            }
            ?>
        </datalist>
        <div class="form-group">
            <label for="name" class="required">Retention Index</label>
            <input type="number" step="0.001" name="retention_index" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="name">Quantification ion</label>
            <input type="number" name="quantification_ion" class="form-control">
        </div>

        <div class="card mx-0">
            <div class="row align-items-center">
                <div class="col">
                    <div class="form-group mr-20">
                        <label for="name">Mass spectrum</label>
                        <textarea type="number" name="mass_spectrum" class="form-control"></textarea>
                    </div>
                </div>
            <div class="ui vertical divider">
                OR
            </div>
            <div class="col">
                <div class="custom-file ml-20">
                    <input type="file" id="mass_file" name="mass_file">
                    <label for="mass_file">Choose mass spectrum CSV file</label>
                </div>
            </div>

            </div>
        </div>





        <button class="btn btn-primary mt-2" type="submit">Upload</button>
        <button class="btn mt-2" type="reset">Reset</button>
    </form>



</div>