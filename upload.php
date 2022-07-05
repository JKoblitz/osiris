<form action="index_old.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <label for="infile">Upload file to analyze:</label>
    <input type="file" name="infile" id="infile" class="form-control">

    <button class="btn btn-primary mt-2" type="submit">Upload</button>
</form>
<!--
<svg xmlns="http://www.w3.org/2000/svg" class="border">
    <style>
    text.description {
        font-size: small;
        fill: #333
    }
    </style>
</svg> -->

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<hr>";

    include "php/math.php";
    include "php/table.php";
}
?>