

<div class="row row-eq-spacing">
   <div class="col-md">
      <div class="box">
         <div class="header">
            <?=lang('News', 'Neuigkeiten')?>
         </div>
         <div class="content h-500 overflow-auto">
         <?php
         include_once "php/Parsedown.php";
            $text = file_get_contents(BASEPATH . "/news.md");
            $parsedown = new Parsedown;
            echo $parsedown->text($text);
            ?>
         </div>
      </div>
   </div>
   <div class="col-md">
      <div class="box">
         <div class="header">
            <?= lang('OSIRIS presentation from the Betriebsversammlung', 'OSIRIS-Präsentation von der Betriebsversammlung') ?>
         </div>
         <object data="<?= ROOTPATH ?>/uploads/OSIRIS.pdf" width="100%" height="500">
         </object>
      </div>
   </div>
</div>

<div class="tiles">
   <a class="tile tile-link" href="<?= ROOTPATH ?>/profile/<?= $_SESSION['username'] ?>">
      <i class="fal fa-user-graduate tile-icon"></i>
      <?= lang('Go to your profile', 'Geh zu deinem Profil') ?>
   </a>
</div>