

<div id="preview"></div>

<?php if (isset($id)) { ?>
    
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

<?php } else { ?>
    
<script>
    $.ajax({
        type: "GET",
        url: ROOTPATH + "/portal/<?=$type?>",
        dataType: "html",
        success: function(response) {
            $('#preview').html(response)
        },
        error: function(response) {
            console.log(response);
        }
    });
</script>

<?php } ?>
