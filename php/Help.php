<?php
include_once "_config.php";

class Help
{
    public $topics = array(
        "article",
        "magazine",
        "book",
        "chapter",
        "preprint",
        "dissertation",
        "others",
        "poster",
        "lecture",
        "review",
        "editorial",
        "thesis-rev",
        "grant-rev",
        "students",
        "theses",
        "guests",
        "teaching",
        "software",
        "award",
        "misc",
        "misc-once",
        "misc-annual"
    );

    // function __construct()
    // {
    // }

    public static function getHelp($topic)
    {
        switch ($topic) {
            case "article":
                return lang(
                    'A journal article has been published in an academic journal and is peer-reviewed. The journal is usually indexed, which means that the article can be found by interested experts.',
                    'Ein Zeitschriftenartikel wurde in einer Fachzeitschrift veröffentlicht und ist in der Regel referiert (peer-reviewed). Die Zeitschrift ist in der Regel indiziert, was dazu führt, dass der Artikel von interessierten Experten gefunden werden kann.'
                );
            case "magazine":
                return lang(
                    'A non-refereed publication or magazine article has not been reviewed by experts (not peer-reviewed) and is often targeted at academic readers from other (more or less distant) disciplines or even for the general public. These articles may appear, for example, in journals (print or digital) or newspapers that are often not indexed in Pubmed.',
                    'Ein nicht-referierter Beitrag oder auch Magazinartikel wurde nicht von Fachexperten überprüft (not peer-reviewed) und ist oftmals zielgerichtet auf akademische Leser aus anderen (mehr oder weniger entfernten) Disziplinen oder sogar für die generelle Öffentlichkeit. Diese Beiträge können beispielsweise in Zeitschriften (print oder digital) oder Zeitungen erscheinen, die oftmals nicht in PubMed oder ähnlichem indiziert sind.'
                );
            case "book":
                return lang(
                    'A book in this context is usually a monograph that deals with a significant topic and has one or more authors. It can also be an anthology, as long as the author relevant in the OSIRIS context not only contributed one or more chapters, but acted as author or editor of the entire work.',
                    'Als Buch wird in diesem Zusammenhang zumeist eine Monographie bezeichnet, die ein wesentliches Thema behandelt und einen oder mehrere Autoren hat. Es kann sich auch um einen Sammelband handeln, solange der im OSIRIS-Kontext relevante Autor nicht nur eines oder mehrere Kapitel beigetragen, sondern als Autor oder Editor des gesamten Werkes tätig war.'
                );
            case "chapter":
                return lang(
                    'Books in science are often written by several authors, each of whom contributes individual chapters to an anthology. These are recorded individually under the term book chapters in OSIRIS, as they can often have different authors. The title of this activity refers to the title of the chapter - a separate data field is included for the title of the book.',
                    'Oftmals werden Bücher in der Wissenschaft von mehreren Autoren verfasst, die jeweils einzelne Kapitel zu einem Sammelband beisteuern. Diese werden unter dem Begriff Buchkapitel in OSIRIS einzeln erfasst, da sie oftmals unterschiedliche Autoren haben können. Der Titel dieser Aktivität bezieht sich dabei auf den Titel des Kapitels - für den Titel des Buchs wird ein gesondertes Datenfeld aufgenommen.'
                );
            case "preprint":
                return lang(
                    'A preprint, also known as an advance publication, is a scientific publication that has already been made available to the (specialist) public but has not yet been peer-reviewed. A preprint of an article usually differs not only in layout, but also in content from the published version, since changes requested in the peer review process are not yet included. For this reason, the preprint continues to exist even after a possible publication by a publisher.',
                    'Ein Preprint, auch Vorab-Publikation genannt, ist eine wissenschaftliche Publikation, die zwar schon der (Fach-)Öffentlichkeit zur Verfügung gestellt, aber noch nicht in einem Peer-Review-Verfahren begutachtet wurde. Ein Preprint eines Artikels unterscheidet sich in der Regel nicht nur im Layout von der Verlagsversion, sondern auch inhaltlich von der veröffentlichten, da im Begutachtungsprozess geforderten Änderungen noch nicht enthalten sind. Aus diesem Grund bleibt der Preprint auch nach einer möglichen Publikation durch einen Verlag weiterhin bestehen.'
                );
            case "dissertation":
                return lang(
                    'A thesis is a document submitted in support of candidature for an academic degree or professional qualification presenting the author\'s research and findings. It can be a bachelor or master thesis, or a dissertation for the purpose of obtaining a doctoral degree at a scientific university. ',
                    'Eine Abschlussarbeit ist ein Dokument, das zur Erlangung eines akademischen Grades oder einer beruflichen Qualifikation vorgelegt wird und in dem die Forschungsergebnisse des Autors dargestellt werden. Es kann sich dabei um eine Bachelor- oder Masterarbeit handeln oder um eine Dissertation zur Erlangung eines Doktorgrades an einer wissenschaftlichen Universität.'
                );
            case "others":
                return lang(
                    'If the publication cannot be classified into any of the other categories, the option for a free field is given here. It is important that the type of publication (e.g. report, white paper, blog article, ...) is specified. This ensures that entries can be retrieved or transformed afterwards.',
                    'Falls die Publikation in keine der anderen Kategorien einteilen lässt, ist hier die Möglichkeit für ein Freifeld gegeben. Dabei ist es wichtig, dass die Art der Publikation (z.B. Bericht, White Paper, Blog-Artikel, ...) angegeben wird. Das stellt sicher, dass im Nachhinein Einträge wiedergefunden bzw. transformiert werden können.'
                );
            // case "poster":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "lecture":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "review":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "editorial":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "thesis-rev":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "grant-rev":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "students":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "theses":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "guests":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "teaching":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "software":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "award":
            //     return lang(
            //         '',
            //         ''
            //     );
            // case "misc":
            //     return lang(
            //         '',
            //         ''
            //     );
            case "misc-once":
                return lang(
                    'These are one-time activities that have <b>external impact</b> but do not fall into any of the other categories. These include, for example, organizing workshops or participating in panel discussions (unless otherwise listed as a category).',
                    'Hierbei handelt es sich um einmalige Tätigkeiten, die eine <b>Außenwirksamkeit</b> haben, aber in keine der anderen Kategorien fallen. Dazu gehören beispielsweise die Organisation von Workshops oder die Teilnahme an Posiumsdiskussionen (falls nicht anders als Kategorie aufgeführt). '
                );
            case "misc-annual":
                return lang(
                    'These are regular activities that take place over a longer period of time, have an <b>external impact</b>, but do not fall into any of the other categories. These include, for example, memberships of boards, societies and committees.',
                    'Hierbei handelt es sich um regelmäßige Tätigkeiten, die über einen längeren Zeitraum verlaufen, eine <b>Außenwirksamkeit</b> haben, aber in keine der anderen Kategorien fallen. Dazu gehören beispielsweise die Mitgliedschaften in Gremien, Gesellschaften und Ausschüssen.'
                );

            default:
                return false;
        }
    }
}
