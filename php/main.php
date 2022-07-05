<form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <label for="infile">Upload file to analyze:</label>
    <input type="file" name="infile" id="infile" class="form-control">

    <button class="btn btn-primary mt-2" type="submit">Upload</button>
</form>