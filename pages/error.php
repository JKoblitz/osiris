    
<?php if ($error == 404) { ?>
    <img src="<?=ROOTPATH?>/img/404.svg" alt="404 - Page not found" class="img-fluid m-auto d-block" style="max-width:80vw; max-height: 65vh;">
<?php } else { ?>
    <?=$error?>
<?php } ?>
