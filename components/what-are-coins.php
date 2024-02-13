<h2 class="title">
    <i class="ph ph-lg ph-coin text-signal"></i>
    Coins
</h2>

<h5>
    <?= lang('What are coins?', 'Was sind Coins?') ?>
</h5>
<p class="">
    <?=lang('You receive coins for research activities that you enter in OSIRIS. Your institute determines how many coins you receive for which activities.', 
    'Ihr bekommt Coins für Forschungsaktivitäten, die ihr in OSIRIS eintragt. Wie viele Coins ihr für welche Aktivitäten bekommt, bestimmt dabei euer Institut.')?>
</p>

<h5>
    <?= lang('How do I get them?', 'Wie bekomme ich sie?') ?>
</h5>

<p>
    <?= lang(
        'Very simple: you add scientific activities to OSIRIS. Whenever you publish, present a poster, give a talk, or complete a review, OSIRIS gives you coins for it (as long as you were an author of the ' . $Settings->get('affiliation') . '). If you want to find out how exactly the points are calculated, you hover over the coins of an activity. A tooltip will show you more information. For some activity types your role in the list of authors matters, since middle authors receive only half the amount. Some activity types are also calculated with factors such as the impact factors and SWS.',
        'Ganz einfach: du fügst wissenschaftliche Aktivitäten zu OSIRIS hinzu. Wann immer du publizierst, ein Poster präsentierst, einen Vortrag hältst, oder ein Review abschließt, bekommst du von OSIRIS dafür Coins (solange du dabei Autor der ' . $Settings->get('affiliation') . ' warst). Wenn du herausfinden möchstest, wie genau sich die Punkte berechnen, kannst du mit dem Cursor auf die Coins einer Aktivität gehen. Ein Tooltip zeigt dir dann mehr Informationen. Bei einigen Aktivitäten spielt es zum Beispiel eine Rolle, auf welcher Position du in der Autorenliste bist. Mittelautoren erhalten nämlich nur die Hälfte der Coins. Bei manchen Aktivitäten kann die Menge an Coins auch mit anderen Faktoren wie dem Impact Factor oder den SWS berechnet werden.'
    ) ?>
</p>

<p>
    <?=lang('In the following table you can see an overview of the Coins.', 
    'In der folgenden Tabelle siehst du eine Übersicht über die Coins.')?>
</p>

<table class="table simple small">
    <?php
        foreach ($Categories->categories as $cat) {
            $color = $cat['color'] ?? 'var(--muted-color)';
            ?>
            <tr>
                <th style="color: <?=$color?>;"><?=lang($cat['name'], $cat['name_de']?? $cat['name'])?></th>
            
                <td></td>
            </tr>
            <?php foreach ($cat['children'] as $type) { ?>
                   <tr><td></td>
                    <td>
                        <b class="key" style="color: <?=$color?>;"><?=lang($type['name'], $type['name_de']?? $type['name'])?></b>
                        <?=$type['coins'] ?? '-'?>
                    </td>
                   </tr>
                <?php } ?>
        <?php }
    ?>
    </tbody>

</table>