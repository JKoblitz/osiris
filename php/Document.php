<?php
require_once "Settings.php";

class Document extends Settings
{

    public $doc = array();
    public $type = array();
    public $subtype = array();

    private $highlight = true;
    private $appendix = '';

    public $title = "";
    public $subtitle = "";
    public $usecase = "web";
    public $full = false;



    function __construct($highlight = true, $usecase = 'web')
    {
        parent::__construct();
        $this->highlight = $highlight;
        $this->usecase = $usecase;
    }

    public function setDocument($doc)
    {
        $this->doc = $doc;
        $this->getActivityType();
    }



    function activity_icon($tooltip = true)
    {
        $icon = 'placeholder';

        if (!empty($this->subtype) && isset($this->subtype['icon'])) {
            $icon = $this->subtype['icon'];
        } elseif (!empty($this->type) && isset($this->type['icon'])) {
            $icon = $this->type['icon'];
        }
        if (empty($this->type)) {
            return "<i class='ph text-danger ph-warning'></i>";
        }
        $type = $this->type['id'];
        $icon = "<i class='ph text-$type ph-$icon'></i>";
        if ($tooltip) {
            $name = $this->activity_title();
            return "<span data-toggle='tooltip' data-title='$name'>
                $icon
            </span>";
        }

        return $icon;
    }


    function activity_title()
    {
        $name = lang("Other", "Sonstiges");
        if (!empty($this->subtype) && isset($this->subtype['name'])) {
            $name = lang(
                $this->subtype['name'],
                $this->subtype['name_de'] ?? $this->subtype['name']
            );
        } elseif (!empty($this->type) && isset($this->type['name'])) {
            $name = lang(
                $this->type['name'],
                $this->type['name_de'] ?? $this->type['name']
            );
        } else {
            return "ERROR: doc is not defined!";
        }
        return $name;
    }

    private function getActivityType()
    {
        if (is_string($this->doc)) {
            $type = strtolower(trim($this->doc));
        } else {
            $type = strtolower(trim($this->doc['type'] ?? $this->doc['subtype'] ?? ''));
        }

        $this->type = $this->activities[$type];

        if (!isset($this->activities[$type])) return;
        if (!isset($this->doc['subtype'])) {

            $subtype = $type;
            switch ($type) {
                case 'publication':
                    $subtype = strtolower(trim($this->doc['pubtype'] ?? $this->doc['subtype'] ?? $type));
                    switch ($subtype) {
                        case 'journal article':
                        case 'journal-article':
                            $subtype = "article";
                            break 2;
                        case 'magazine article':
                            $subtype = "magazine";
                            break 2;
                        case 'book-chapter':
                        case 'book chapter':
                        case 'book-editor':
                        case 'publication':
                            $subtype = "chapter";
                            break 2;
                        default:
                            break 2;
                    }
                case 'review':
                    switch (strtolower($this->doc['role'] ?? $this->doc['subtype'] ?? '')) {
                        case 'editorial':
                        case 'editor':
                            $subtype = "editorial";
                            break 2;
                        case 'grant-rev':
                            $subtype = "grant-rev";
                            break 2;
                        case 'thesis-rev':
                            $subtype = "thesis-rev";
                            break 2;
                        default:
                            $subtype = "review";
                            break 2;
                    }

                case 'misc':
                    $subtype = $this->doc['iteration'] ?? $this->doc['subtype'] ?? '';
                    if ($subtype == 'once' || $subtype == 'annual') {
                        $subtype = "misc-" . $subtype;
                    }
                    break;
                case 'students':
                    $cat = strtolower(trim($this->doc['category'] ?? $this->doc['subtype'] ?? 'thesis'));
                    if (str_contains($cat, "thesis") || $cat == 'doktorand:in' || $cat == 'students') {
                        $subtype = "students";
                        break;
                    }
                    $subtype = "guests";
                    break;
                default:
                    break;
            }
        } else {
            $subtype = $this->doc['subtype'];
        }

        if (isset($this->type['subtypes'][$subtype])) {
            $this->subtype = $this->type['subtypes'][$subtype];
            return;
        }
        foreach ($this->type['subtypes'] as $st) {
            if ($st['id'] == $subtype) {
                $this->subtype = $st;
                return;
            }
        }
    }

    function activity_badge()
    {
        $name = $this->activity_title();
        $icon = $this->activity_icon(false);
        $type = $this->doc['type'];
        return "<span class='badge badge-$type'>$icon $name</span>";
    }


    private static function commalist($array, $sep = "and")
    {
        if (empty($array)) return "";
        if (count($array) < 3) return implode(" $sep ", $array);
        $str = implode(", ", array_slice($array, 0, -1));
        return $str . " $sep " . end($array);
    }

    private static function abbreviateAuthor($last, $first, $reverse = true)
    {
        $fn = " ";
        if ($first) : foreach (preg_split("/(\s| |-|\.)/u", ($first)) as $name) {
                if (empty($name)) continue;
                // echo "<!--";
                // echo "-->";
                $fn .= "" . mb_substr($name, 0, 1) . ".";
            }
        endif;
        if (empty(trim($fn))) return $last;
        if ($reverse) return $last . "," . $fn;
        return $fn . " " . $last;
    }

    function formatAuthors($raw_authors)
    {
        if (empty($raw_authors)) return '';
        $n = 0;
        $authors = array();

        $first = 1;
        $last = 1;
        $corresponding = false;

        if (!empty($raw_authors) && $raw_authors instanceof BSONArray) {
            $raw_authors = $raw_authors->bsonSerialize();
        }
        if (!empty($raw_authors) && is_array($raw_authors)) {
            $pos = array_count_values(array_column($raw_authors, 'position'));
            $first = $pos['first'] ?? 1;
            $last = $pos['last'] ?? 1;
            $corresponding = array_key_exists('corresponding', $pos);
        }
        // dump($raw_authors);
        foreach ($raw_authors as $n => $a) {

            if (!$this->full) {
                if ($n > 9) break;
                $author = Document::abbreviateAuthor($a['last'], $a['first']);
                if ($this->highlight === true) {
                    //if (($a['aoi'] ?? 0) == 1) $author = "<b>$author</b>";
                } else if ($this->highlight && $a['user'] == $this->highlight) {
                    $author = "<u>$author</u>";
                }
                $authors[] = $author;
            } else {
                // if (!$this->full && $n++ >= 10 && ($a['aoi'] ?? 0) == 0) {
                //     if (end($authors) != '...')
                //         $authors[] = "...";
                //     continue;
                // }

                $author = Document::abbreviateAuthor($a['last'], $a['first']);

                if ($this->highlight === true) {
                    if (($a['aoi'] ?? 0) == 1) $author = "<b>$author</b>";
                } else if ($this->highlight && $a['user'] == $this->highlight) {
                    $author = "<b>$author</b>";
                }

                if ($first > 1 && $a['position'] == 'first') {
                    $author .= "<sup>#</sup>";
                }
                if ($last > 1 && $a['position'] == 'last') {
                    $author .= "<sup>*</sup>";
                }
                if (isset($a['position']) && $a['position'] == 'corresponding') {
                    $author .= "<sup>§</sup>";
                    $corresponding = true;
                }

                $authors[] = $author;
            }
        }

        if ($first > 1) {
            $this->appendix .= " <sup>#</sup> Shared first authors";
        }
        if ($last > 1) {
            $this->appendix .= " <sup>*</sup> Shared last authors";
        }
        if ($corresponding) {
            $this->appendix .= " <sup>§</sup> Corresponding author";
        }

        $append = "";
        $separator = 'and';
        if (!$this->full && $n > 10 && end($authors) == '...') {
            $append = " et al.";
            $separator = ", ";
            array_pop($authors);
        }
        return Document::commalist($authors, $separator) . $append;
    }


    private static function getDateTime($date)
    {
        if (isset($date['year'])) {
            //date instanceof MongoDB\Model\BSONDocument
            $d = new DateTime();
            $d->setDate(
                $date['year'],
                $date['month'] ?? 1,
                $date['day'] ?? 1
            );
        } else {
            try {
                $d = date_create($date);
            } catch (TypeError $th) {
                $d = null;
            }
        }
        return $d;
    }

    public static function format_date($date)
    {
        // dump($date);
        $d = Document::getDateTime($date);
        return date_format($d, "d.m.Y");
    }

    private static function fromToDate($from, $to)
    {
        if (empty($to) || $from == $to) {
            return Document::format_date($from);
        }
        // $to = date_create($to);
        $from = Document::format_date($from);
        $to = Document::format_date($to);

        $f = explode('.', $from, 3);
        $t = explode('.', $to, 3);

        $from = $f[0] . ".";
        if ($f[1] != $t[1] || $f[2] != $t[2]) {
            $from .= $f[1] . ".";
        }
        if ($f[2] != $t[2]) {
            $from .= $f[2];
        }

        return $from . '-' . $to;
    }


    public static function format_month($month)
    {
        if (empty($month)) return '';
        $month = intval($month);
        $array = [
            1 => lang("January", "Januar"),
            2 => lang("February", "Februar"),
            3 => lang("March", "März"),
            4 => lang("April"),
            5 => lang("May", "Mai"),
            6 => lang("June", "Juni"),
            7 => lang("July", "Juli"),
            8 => lang("August"),
            9 => lang("September"),
            10 => lang("October", "Oktober"),
            11 => lang("November"),
            12 => lang("December", "Dezember")
        ];
        return $array[$month];
    }



    public static function translateCategory($cat)
    {
        switch ($cat) {
            case 'doctoral thesis':
                return "Doktorand:in";
            case 'master thesis':
                return "Master-Thesis";
            case 'bachelor thesis':
                return "Bachelor-Thesis";
            case 'guest scientist':
                return "Gastwissenschaftler:in";
            case 'lecture internship':
                return "Pflichtpraktikum im Rahmen des Studium";
            case 'student internship':
                return "Schülerpraktikum";
            case 'lecture':
                return lang('Lecture', 'Vorlesung');
            case 'practical':
                return lang('Practical course', 'Praktikum');
            case 'practical-lecture':
                return lang('Lecture and practical course', 'Vorlesung und Praktikum');
            case 'seminar':
                return lang('Seminar');
            case 'other':
                return lang('Other', 'Sonstiges');
            default:
                return $cat;
        }
    }

    private function getVal($field, $default = '')
    {
        return $this->doc[$field] ?? $default;
    }

    public function get_field($module)
    {
        switch ($module) {
            case "affiliation": // ["book"],
                return $this->getVal('affiliation');
            case "authors": // ["authors"],
                return $this->formatAuthors($this->getVal('authors'));
            case "book-series": // ["series"],
                return $this->getVal('series');
            case "book-title": // ["book"],
                return $this->getVal('book');
            case "city": // ["city"],
                return $this->getVal('city');
            case "conference": // ["conference"],
                return $this->getVal('conference');
            case "correction": // ["correction"],
                $val = $this->getVal('correction', false);
                if ($this->usecase == 'list')
                    return bool_icon($val);
                if ($val)
                    return "<span style='color:#B61F29;'>[Correction]</span>";
                else return '';
            case "date": // ["year", "month", "day"],
            case "date-range": // ["start", "end"],
                // return $this->fromToDate($this->getVal('start'), $this->getVal('end') ?? null);
                return $this->fromToDate($this->getVal('start', $this->doc), $this->getVal('end', null));
            case "date-range-ongoing":
                if (!empty($this->doc['start'])) {
                    if (!empty($this->doc['end'])) {
                        $date = lang("from ", "von ");
                        $date .= Document::format_month($this->doc['start']['month']) . ' ' . $this->doc['start']['year'];
                        $date .= lang(" until ", " bis ");
                        $date .= Document::format_month($this->doc['end']['month']) . ' ' . $this->doc['end']['year'];
                    } else {
                        $date = lang("since ", "seit ");
                        $date .= Document::format_month($this->doc['start']['month']) . ' ' . $this->doc['start']['year'];
                    }
                }
                return $date;
            case "year": // ["year", "month", "day"],
                return $this->getVal('year');
            case "month": // ["year", "month", "day"],
                return Document::format_month($this->getVal('month'));
            case "details": // ["details"],
                return $this->getVal('details');
            case "doctype": // ["doc_type"],
                return $this->getVal('doc_type');
            case "doi": // ["doi"],
                $val = $this->getVal('doi');
                if (empty($val)) return '';
                return "DOI: <a target='_blank' href='https://doi.org/$val'>$val</a>";
            case "edition": // ["edition"],
                $val = $this->getVal('edition');
                if ($val == 1) $val .= "st";
                elseif ($val == 2) $val .= "nd";
                elseif ($val == 3) $val .= "rd";
                else $val .= "th";
                return $val;
            case "editor": // ["editors"],
                return $this->formatAuthors($this->getVal('editors'));
            case "editorial": // ["editor_type"],
                return $this->getVal('editor_type');
            case "file-icons":
                $files = '';
                foreach ($this->getVal('files', array()) as $file) {
                    $icon = getFileIcon($file['filetype']);
                    $files .= " <a href='$file[filepath]' target='_blank' data-toggle='tooltip' data-title='$file[filetype]: $file[filename]' class='file-link'>
                        <i class='ph ph-regular ph-file ph-$icon'></i>
                        </a>";
                }
                return $files;
            case "guest": // ["category"],
                return $this->translateCategory($this->getVal('category'));
            case "isbn": // ["isbn"],
                return $this->getVal('isbn');
            case "issue": // ["issue"],
                return $this->getVal('issue');
            case "iteration": // ["iteration"],
                return $this->getVal('iteration');
            case "journal": // ["journal", "journal_id"],
                $val = $this->doc['journal_id'] ?? '';
                if (!empty($val)) {
                    $j = getConnected('journal', $this->getVal('journal_id'));
                    return $j['journal'];
                }
                return $this->getVal('journal');
            case "journal-abbr":
                $val = $this->doc['journal_id'] ?? '';
                if (!empty($val)) {
                    $j = getConnected('journal', $this->getVal('journal_id'));
                    return $j['abbr'];
                }
                return $this->getVal('journal');
            case "lecture-invited": // ["invited_lecture"],
                $val = $this->getVal('invited_lecture', false);
                if ($this->usecase == 'list')
                    return bool_icon($val);
                if ($val)
                    return "Invited lecture";
                else return '';
            case "lecture-type": // ["lecture_type"],
                return $this->getVal('lecture_type');
            case "link": // ["link"],
                $val = $this->getVal('link');
                return "<a target='_blank' href='$val'>$val</a>";
            case "location": // ["location"],
                return $this->getVal('location');
            case "magazine": // ["magazine"],
                return $this->getVal('magazine');
            case "online-ahead-of-print": // ["epub"],
                if ($this->usecase == 'list')
                    return bool_icon($this->getVal('epub', false));
                if ($this->getVal('epub', false))
                    return "<span style='color:#B61F29;'>[Online ahead of print]</span>";
                else return '';
            case "openaccess": // ["open_access"],
                if (!empty($this->getVal('open_access', false))) {
                    return '<i class="icon-open-access text-success" title="Open Access"></i>';
                } else {
                    return '<i class="icon-closed-access text-danger" title="Closed Access"></i>';
                }
            case "pages": // ["pages"],
                return $this->getVal('pages');
            case "person": // ["name", "affiliation", "academic_title"],
                return $this->getVal('name');
            case "publisher":; // ["publisher"],
                return $this->getVal('publisher');
            case "pubmed": // ["pubmed"],
                $val = $this->getVal('pubmed');
                if (empty($val)) return '';
                return "<a target='_blank' href='https://pubmed.ncbi.nlm.nih.gov/$val'>$val</a>";
            case "pubtype": // ["pubtype"],
                return $this->getVal('pubtype');
            case "review-description": // ["title"],
                return $this->getVal('title');
            case "review-type": // ["title"],
                return $this->getVal('review-type');
            case "scientist": // ["authors"],
                return $this->formatAuthors($this->getVal('authors'));
            case "semester-select": // [],
                return '';
            case "subtype":
                return $this->activity_title();
            case "software-link": // ["link"],
                return $this->getVal('link');
            case "software-type": // ["software_type"],
                $val = $this->getVal('software_type');
                switch ($val) {
                    case 'software':
                        return "Computer software";
                    case 'database':
                        return "Database";
                    case 'webtool':
                        return "Webpage";
                    case 'dataset':
                        return "Dataset";
                    default:
                        return "Computer software";
                }
            case "software-venue": // ["software_venue"],
                return $this->getVal('software_venue');
            case "status": // ["status"],
                return $this->getVal('status');
            case "student-category": // ["category"],
                return $this->translateCategory($this->getVal('category'));
            case "supervisor": // ["authors"],
                return $this->formatAuthors($this->getVal('authors'));
            case "teaching-category": // ["category"],
                return $this->translateCategory($this->getVal('category'));
            case "teaching-course": // ["title", "module", "module_id"],
                if (isset($this->doc['module_id'])) {
                    $m = getConnected('teaching', $this->getVal('module_id'));
                    return $m['title'];
                }
                return $this->getVal('title');
            case "title": // ["title"],
                return $this->getVal('title');
            case "university": // ["publisher"],
                return $this->getVal('publisher');
            case "version": // ["version"],
                return $this->getVal('version');
            case "volume": // ["volume"],
                return $this->getVal('volume');
            default:
                return '';
        }
    }

    public function format()
    {
        $this->full = true;
        $template = '{title}';
        $template = $this->subtype['template']['print'] ?? $template;

        $line = $this->template($template);
        $line .= $this->get_field('file-icons');

        if (!empty($this->appendix)) {
            $line .= "<br><small style='color:#878787;'>" . $this->appendix . "</small>";
        }
        return $line;
    }

    public function formatShort($link = true)
    {
        $this->full = false;
        $line = "";
        $template = $this->subtype['template']['title'] ?? '{title}';
        $title = $this->template($template);

        if ($link) {
            $id = strval($this->doc['_id']);
            $line = "<a class='colorless' href='" . ROOTPATH . "/activities/view/$id'>$title</a>";
        } else {
            $line = $title;
        }


        $template = $this->subtype['template']['subtitle'] ?? '{authors}';
        $line .= "<br><small class='text-muted d-block'>";
        $line .= $this->template($template);
        $line .= $this->get_field('file-icons');
        $line .= "</small>";

        // if (!empty($this->appendix)) {
        //     $line .= "<br><small style='color:#878787;'>" . $this->appendix . "</small>";
        // }
        return $line;
    }

    private function template($template)
    {

        $vars = array();

        $pattern = "/{([^}]*)}/";
        preg_match_all($pattern, $template, $matches);
        // dump($matches[1], true);

        foreach ($matches[1] as $module) {
            $m = explode('|', $module, 2);
            $value = $this->get_field($m[0]);

            if (empty($value) && count($m) == 2) {
                $value = $m[1];
            }
            $vars['{' . $module . '}'] = ($value);
        }

        $line = strtr($template, $vars);

        $line = preg_replace('/\(\s*\)/', '', $line);
        $line = preg_replace('/\s+[,]+/', ',', $line);
        $line = preg_replace('/,\s\./', '.', $line);
        $line = preg_replace('/\s+/', ' ', $line);

        return $line;
    }
}