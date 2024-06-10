<?php
/**
 * Page to see and edit the download cart
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link /cart
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */
?>

<style>
    @media (min-width: 768px) {
        .row.row-eq-spacing-sm:not(.row-eq-spacing) {
            margin-left: calc(-2rem/2);
            padding-left: 0;
            margin-right: calc(-2rem/2);
            padding-right: 0;
        }
    }
</style>

<div class="container">
    <h3><?= lang('Download cart', 'Download-Sammlung') ?></h3>

    <form action="<?= ROOTPATH ?>/download" method="post">


        <?php
        $cart = readCart();
        if (!empty($cart)) {
            $Format = new Document($_SESSION['username']);
        ?>
            <input type="hidden" name="cart" value="1">
            <p>
                <?= lang('The following activities are in your download cart:', 'Die folgenden Aktivitäten sind in deinem Einkaufswagen:') ?>
            </p>
            <table class="table sm mb-20">
                <?php foreach ($cart as $id) {
                    $mongo_id = $DB->to_ObjectID($id);
                    $doc = $osiris->activities->findOne(['_id' => $mongo_id]);
                    if (empty($doc)) continue;
                    
                    $Format->setDocument($doc);
                ?>
                    <tr>
                        <td>
                            <span class='mr-10'><?=$Format->activity_icon() ?></span>
                            <?= $Format->formatShort() ?>
                        </td>
                        <td>
                            <button class="btn link small" type="button" onclick="addToCart(null, '<?= $id ?>')"><i class="ph ph-x"></i></button>
                        </td>
                    </tr>
                <?php } ?>

            </table>


            <div class="form-group">

                <?= lang('Highlight:', 'Hervorheben:') ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="highlight" id="highlight-user" value="user" checked="checked">
                    <label for="highlight-user"><?= lang('Me', 'Mich') ?></label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="highlight" id="highlight-aoi" value="aoi">
                    <label for="highlight-aoi"><?= $Settings->get('affiliation') ?><?= lang(' Authors', '-Autoren') ?></label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="highlight" id="highlight-none" value="">
                    <label for="highlight-none"><?= lang('None', 'Nichts') ?></label>
                </div>

            </div>


            <div class="form-group">

                <?= lang('File format:', 'Dateiformat:') ?>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="format" id="format-word" value="word" checked="checked">
                    <label for="format-word">Word</label>
                </div>

                <div class="custom-radio d-inline-block ml-10">
                    <input type="radio" name="format" id="format-bibtex" value="bibtex">
                    <label for="format-bibtex">BibTex</label>
                </div>

            </div>



            <button class="btn primary">Download</button>
        <?php } else { ?>
            <p class="text-danger">
                <?= lang('Cart is empty.', 'Der Einkaufswagen ist leer.') ?>
            </p>
        <?php } ?>
    </form>
</div>