<div id="preview"></div>

<script>
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/portal/<?=$type?>/<?=$id?>",
        dataType: "html",
        success: function(response) {
            $('#preview').html(response)
        },
        error: function(response) {
            console.log(response);
        }
    });
</script>
