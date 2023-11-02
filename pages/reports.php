<?php
/**
 * Page to export report
 * 
 * Component of the controlling page.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /controlling
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<div class="box primary">
    <div class="content">
    <br />
<font size='1'><table class='xdebug-error xe-deprecated' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Deprecated: Return type of PhpOffice\PhpWord\Shared\XMLWriter::writeAttribute($name, $value) should either be compatible with XMLWriter::writeAttribute(string $name, string $value): bool, or the #[\ReturnTypeWillChange] attribute should be used to temporarily suppress the notice in C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Shared\XMLWriter.php on line <i>174</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0002</td><td bgcolor='#eeeeec' align='right'>366536</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0026</td><td bgcolor='#eeeeec' align='right'>477784</td><td bgcolor='#eeeeec'>Route::run( <span>$basepath = </span><span>&#39;/osiris&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>1905</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.0027</td><td bgcolor='#eeeeec' align='right'>479032</td><td bgcolor='#eeeeec'>{closure:C:\Users\juk20\Nextcloud\testserver\osiris\export.php:416-826}(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\php\Route.php' bgcolor='#eeeeec'>...\Route.php<b>:</b>115</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.2564</td><td bgcolor='#eeeeec' align='right'>8821280</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007->save( <span>$filename = </span><span>&#39;php://output&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\export.php' bgcolor='#eeeeec'>...\export.php<b>:</b>825</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.2578</td><td bgcolor='#eeeeec' align='right'>8825064</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\ContentTypes->write(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007.php' bgcolor='#eeeeec'>...\Word2007.php<b>:</b>138</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.2578</td><td bgcolor='#eeeeec' align='right'>8825064</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\AbstractPart->getXmlWriter(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\ContentTypes.php' bgcolor='#eeeeec'>...\ContentTypes.php<b>:</b>66</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.2578</td><td bgcolor='#eeeeec' align='right'>8825064</td><td bgcolor='#eeeeec'>Composer\Autoload\ClassLoader->loadClass( <span>$class = </span><span>&#39;PhpOffice\\PhpWord\\Shared\\XMLWriter&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\AbstractPart.php' bgcolor='#eeeeec'>...\AbstractPart.php<b>:</b>90</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.2579</td><td bgcolor='#eeeeec' align='right'>8825224</td><td bgcolor='#eeeeec'>Composer\Autoload\includeFile( <span>$file = </span><span>&#39;C:\\Users\\juk20\\Nextcloud\\testserver\\osiris\\vendor\\composer/../phpoffice/phpword/src/PhpWord\\Shared\\XMLWriter.php&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\composer\ClassLoader.php' bgcolor='#eeeeec'>...\ClassLoader.php<b>:</b>428</td></tr>
</table></font>
<br />
<font size='1'><table class='xdebug-error xe-uncaught-exception' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Fatal error: Uncaught PhpOffice\PhpWord\Exception\Exception: Invalid parameters passed. in C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\Rels.php on line <i>127</i></th></tr>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> PhpOffice\PhpWord\Exception\Exception: Invalid parameters passed. in C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\Rels.php on line <i>127</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0002</td><td bgcolor='#eeeeec' align='right'>366536</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0026</td><td bgcolor='#eeeeec' align='right'>477784</td><td bgcolor='#eeeeec'>Route::run( <span>$basepath = </span><span>&#39;/osiris&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\index.php' bgcolor='#eeeeec'>...\index.php<b>:</b>1905</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>3</td><td bgcolor='#eeeeec' align='center'>0.0027</td><td bgcolor='#eeeeec' align='right'>479032</td><td bgcolor='#eeeeec'>{closure:C:\Users\juk20\Nextcloud\testserver\osiris\export.php:416-826}(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\php\Route.php' bgcolor='#eeeeec'>...\Route.php<b>:</b>115</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>4</td><td bgcolor='#eeeeec' align='center'>0.2564</td><td bgcolor='#eeeeec' align='right'>8821280</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007->save( <span>$filename = </span><span>&#39;php://output&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\export.php' bgcolor='#eeeeec'>...\export.php<b>:</b>825</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>5</td><td bgcolor='#eeeeec' align='center'>0.2935</td><td bgcolor='#eeeeec' align='right'>8832592</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\RelsDocument->write(  )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007.php' bgcolor='#eeeeec'>...\Word2007.php<b>:</b>138</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>6</td><td bgcolor='#eeeeec' align='center'>0.2935</td><td bgcolor='#eeeeec' align='right'>8832672</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\Rels->writeRels( <span>$xmlWriter = </span><span>class PhpOffice\PhpWord\Shared\XMLWriter { private $tempFileName = &#39;&#39; }</span>, <span>$xmlRels = </span><span>[&#39;styles.xml&#39; =&gt; &#39;officeDocument/2006/relationships/styles&#39;, &#39;numbering.xml&#39; =&gt; &#39;officeDocument/2006/relationships/numbering&#39;, &#39;settings.xml&#39; =&gt; &#39;officeDocument/2006/relationships/settings&#39;, &#39;theme/theme1.xml&#39; =&gt; &#39;officeDocument/2006/relationships/theme&#39;, &#39;webSettings.xml&#39; =&gt; &#39;officeDocument/2006/relationships/webSettings&#39;, &#39;fontTable.xml&#39; =&gt; &#39;officeDocument/2006/relationships/fontTable&#39;]</span>, <span>$mediaRels = </span><span>[0 =&gt; [&#39;mediaIndex&#39; =&gt; 1, &#39;source&#39; =&gt; &#39;https://doi.org/10.3389/fmicb.2023.1197837&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.3389/fmicb.2023.1197837&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 1], 1 =&gt; [&#39;mediaIndex&#39; =&gt; 2, &#39;source&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.gbm02077&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.gbm02077&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 2], 2 =&gt; [&#39;mediaIndex&#39; =&gt; 3, &#39;source&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.fbm00409&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.fbm00409&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 3], 3 =&gt; [&#39;mediaIndex&#39; =&gt; 4, &#39;source&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.obm00191&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.obm00191&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 4], 4 =&gt; [&#39;mediaIndex&#39; =&gt; 5, &#39;source&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.cbm00091&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.cbm00091&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 5], 5 =&gt; [&#39;mediaIndex&#39; =&gt; 6, &#39;source&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.pbm00055&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1002/9781118960608.pbm00055&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 6], 6 =&gt; [&#39;mediaIndex&#39; =&gt; 7, &#39;source&#39; =&gt; &#39;&#39;, &#39;target&#39; =&gt; &#39;&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 7], 7 =&gt; [&#39;mediaIndex&#39; =&gt; 8, &#39;source&#39; =&gt; &#39;https://doi.org/10.1101/2023.05.09.539844&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1101/2023.05.09.539844&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 8], 8 =&gt; [&#39;mediaIndex&#39; =&gt; 9, &#39;source&#39; =&gt; &#39;https://doi.org/10.1101/2023.05.10.540175&#39;, &#39;target&#39; =&gt; &#39;https://doi.org/10.1101/2023.05.10.540175&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 9]]</span>, <span>$relId = </span>??? )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\RelsDocument.php' bgcolor='#eeeeec'>...\RelsDocument.php<b>:</b>46</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>7</td><td bgcolor='#eeeeec' align='center'>0.2936</td><td bgcolor='#eeeeec' align='right'>8832672</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\Rels->writeMediaRel( <span>$xmlWriter = </span><span>class PhpOffice\PhpWord\Shared\XMLWriter { private $tempFileName = &#39;&#39; }</span>, <span>$relId = </span><span>13</span>, <span>$mediaRel = </span><span>[&#39;mediaIndex&#39; =&gt; 7, &#39;source&#39; =&gt; &#39;&#39;, &#39;target&#39; =&gt; &#39;&#39;, &#39;type&#39; =&gt; &#39;link&#39;, &#39;rID&#39; =&gt; 7]</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\Rels.php' bgcolor='#eeeeec'>...\Rels.php<b>:</b>70</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>8</td><td bgcolor='#eeeeec' align='center'>0.2936</td><td bgcolor='#eeeeec' align='right'>8832752</td><td bgcolor='#eeeeec'>PhpOffice\PhpWord\Writer\Word2007\Part\Rels->writeRel( <span>$xmlWriter = </span><span>class PhpOffice\PhpWord\Shared\XMLWriter { private $tempFileName = &#39;&#39; }</span>, <span>$relId = </span><span>13</span>, <span>$type = </span><span>&#39;officeDocument/2006/relationships/hyperlink&#39;</span>, <span>$target = </span><span>&#39;&#39;</span>, <span>$targetMode = </span><span>&#39;External&#39;</span> )</td><td title='C:\Users\juk20\Nextcloud\testserver\osiris\vendor\phpoffice\phpword\src\PhpWord\Writer\Word2007\Part\Rels.php' bgcolor='#eeeeec'>...\Rels.php<b>:</b>95</td></tr>
</table></font>

        <h5><?= lang('Export reports', 'Exportiere Berichte') ?></h5>

        <form action="<?= ROOTPATH ?>/reports" method="post">

            <div class="form-row row-eq-spacing-sm">
                <div class="col-sm">
                    <label class="required" for="start">
                        <?= lang('Beginning of report', 'Anfang des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="start" id="start" value="<?=CURRENTYEAR?>-01-01" required>
                </div>
                <div class="col-sm">
                    <label class="required" for="end">
                        <?= lang('End of report', 'Ende des Reports') ?>
                    </label>
                    <input type="date" class="form-control" name="end" id="end" value="<?=CURRENTYEAR?>-06-30" required>
                </div>
            </div>

            <div class="form-group">
                <label for="style">Report-Style</label>
                <select name="style" id="style" class="form-control">
                    <option value="research-report">Research report</option>
                </select>
            </div>

            <button class="btn" type="submit"><?=lang('Generate report', 'Report erstellen')?></button>
        </form>

    </div>
</div>