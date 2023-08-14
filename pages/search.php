<?php
/**
 * Page to search for activities in PubMed
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /activities/pubmed-search
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<div class="content">
<h2 class="mb-0">
            <i class="ph ph-regular ph-plus-circle"></i>
            <?= lang('Search in Pubmed', 'Suche in Pubmed') ?>
        </h2>

        <a href="<?=ROOTPATH?>/activities/new" class="link mb-10 d-block"><?=lang('Add manually', 'Füge manuell hinzu')?></a>


    <form action="#" class="form-inline w-500 mw-full" onsubmit="searchLiterature(event)">
        <div class="form-group">
            <label class=" w-100" for="authors">Author(s)</label>
            <input type="text" class="form-control" placeholder="" id="authors" value="<?=$_GET['authors'] ?? ''?>">
        </div>
        <div class="form-group">
            <label class=" w-100" for="affiliation">Affiliation</label>
            <input type="text" class="form-control" placeholder="" id="affiliation" value="<?= $Settings->affiliation ?>">
        </div>
        <div class="form-group">
            <label class=" w-100" for="title">Title</label>
            <input type="text" class="form-control" placeholder="" id="title" value="<?=$_GET['title'] ?? ''?>">
        </div>
        <div class="form-group">
            <label class=" w-100" for="year">Year</label>
            <input type="text" class="form-control" placeholder="" id="year" value="<?=$_GET['year'] ?? CURRENTYEAR?>">
        </div>
        <div class="form-group mb-0">
            <input type="submit" class="btn btn-primary ml-auto" value="Search">
        </div>
    </form>

    <hr>

    <p class="text-primary text-right" id="details"></p>

    <table class="table">
        <tbody id="results">
            <tr>
                <td>
                    Enter your search terms.
                </td>
            </tr>
        </tbody>
    </table>

</div>