


<h1><?=$dashboard['title']['de']?></h1>

<div class="link-list">
<?php foreach ($dashboard['dashboard_item_formulars'] as $formular) { ?>
<a href="<?=ROOTPATH?>/ida/formular/<?=$formular['formular_id']?>"><?=$formular['formular_short_title']?></a>
<?php } ?>

</div>