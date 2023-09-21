<h2 class="title">
    <i class="ph ph-lg ph-coin text-signal"></i>
    Coins
</h2>

<h5>
    <?= lang('What are coins?', 'Was sind Coins?') ?>
</h5>
<p class="">
    <?= lang(
        "To put it simply, coins are a currency that currently doesn\'t earn you anything and doesn\'t really interest anyone. Unless you like to collect, then you\'re welcome.",
        'Um es kurz zu sagen, Coins sind eine Währung, die dir im Moment überhaupt nichts bringt und auch eigentlich niemanden interessiert. Außer du sammelst gern, dann gern geschehen. Du kannst ihre Sichtbarkeit in deinen Profileinstellungen ändern.'
    ) ?>
</p>

<h5>
    <?= lang('How do I get them?', 'Wie bekomme ich sie?') ?>
</h5>

<p>
    <?= lang(
        'Very simple: you add scientific activities to OSIRIS. Whenever you publish, present a poster, give a talk, or complete a review, OSIRIS gives you coins for it (as long as you were an author of the ' . $Settings->affiliation . '). If you want to find out how exactly the points are calculated, you hover over the coins of an activity. A tooltip will show you more information. For a publication, for example, it matters where you are in the list of authors (first/last or middle author) and how high the impact factor of the journal is.',
        'Ganz einfach: du fügst wissenschaftliche Aktivitäten zu OSIRIS hinzu. Wann immer du publizierst, ein Poster präsentierst, einen Vortrag hältst, oder ein Review abschließt, bekommst du von OSIRIS dafür Coins (solange du dabei Autor der ' . $Settings->affiliation . ' warst). Wenn du herausfinden möchstest, wie genau sich die Punkte berechnen, kannst du mit dem Cursor auf die Coins einer Aktivität gehen. Ein Tooltip zeigt dir dann mehr Informationen. Bei einer Publikation spielt beispielsweise eine Rolle, an welcher Stelle du in der Autorenliste stehst (Erst/Letzt oder Mittelautor) und wie hoch der Impact Factor des Journals ist.'
    ) ?>
</p>

<p>
    <?=lang('In the following table you can see an overview of the Coins. The allocation of coins is currently based on the previously existing Möller points and will be revised soon.', 
    'In der folgenden Tabelle siehst du eine Übersicht über die Coins. Die Vergabe von Coins basiert zurzeit auf den zuvor existenten Möller-Punkten und soll demnächst überarbeitet werden.')?>
</p>

<table class="table simple small">
    <thead>
        <tr>
            <th>Type</th>
            <th>Role of author</th>
            <th>Coins</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Publications (Journal article)</td>
            <td>first/last/corresponding</td>
            <td>16 &times; IF</td>
        </tr>
        <tr>
            <td>"</td>
            <td>middle</td>
            <td>8 &times; IF</td>
        </tr>
        <tr>
            <td>Publications (Non-refereed)</td>
            <td>first/last/corresponding</td>
            <td>8</td>
        </tr>
        <tr>
            <td>"</td>
            <td>middle</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Publications (Book)</td>
            <td>Author</td>
            <td>12</td>
        </tr>
        <tr>
            <td>"</td>
            <td>Editor</td>
            <td>12</td>
        </tr>
        <tr>
            <td>Lecture (only presentor)</td>
            <td>short lecture</td>
            <td>8</td>
        </tr>
        <tr>
            <td>"</td>
            <td>long lecture</td>
            <td>16</td>
        </tr>
        <tr>
            <td>"</td>
            <td>repetition</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Poster</td>
            <td>first (presenting)</td>
            <td>8</td>
        </tr>
        <tr>
            <td>"</td>
            <td>other</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Review</td>
            <td>any</td>
            <td>4</td>
        </tr>
        <tr>
            <td>Editorial board</td>
            <td>any</td>
            <td>16</td>
        </tr>
        <tr>
            <td>Students</td>
            <td>any</td>
            <td>16</td>
        </tr>
        <tr>
            <td>Guest</td>
            <td>any</td>
            <td>8</td>
        </tr>
        <tr>
            <td>Teaching</td>
            <td>any</td>
            <td>6 &times; SWS</td>
        </tr>
        <tr>
            <td>Software</td>
            <td>any</td>
            <td>6</td>
        </tr>
        <tr>
            <td>Miscellaneous</td>
            <td>annual</td>
            <td>8</td>
        </tr>
        <tr>
            <td>"</td>
            <td>once</td>
            <td>4</td>
        </tr>
    </tbody>

</table>