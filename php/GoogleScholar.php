<?php

// include(BASEPATH . '/php/simple_html_dom.php');

include('simple_html_dom.php');

class GoogleScholar
{
    // adapted from https://github.com/fredrike/googlescholar-api
    private $html = null;
    private $user = null;

    function __construct($user)
    {
        $this->html = new simple_html_dom();
        $this->user = $user;
    }

    function getAllUserEntries()
    {
        $result = array(
            "total_citations" => 0,
            "citations_per_year" => array(),
            "publications" => array(),
        );
        $url = "http://scholar.google.se/citations?user=" . $this->user;
        $url .= "&view_op=list_works&sortby=pubdate"; // sort by date
        $url .= "&pagesize=100"; // pagination limit
        $url .= "&cstart=0"; // pagination offset
        $this->html->load_file($url);

        $result['total_citations'] = $this->html->find("#gsc_rsb_st td.gsc_rsb_std", 0)->plaintext;

        $years = $this->html->find('.gsc_g_t');
        $scores = $this->html->find('.gsc_g_al');
        foreach ($scores as $key => $score) {
            $result['citations_per_year'][trim($years[$key]->plaintext)] = trim($score->plaintext);
        }

        // $str = " \"publications\": [";
        foreach ($this->html->find("#gsc_a_t .gsc_a_tr") as $pub) {
            // dump($pub);
            // dump($pub->find(".gsc_a_at", 0));
            $el = array();
            $el['title'] = trim($pub->find(".gsc_a_at", 0)->plaintext);
            $href = trim($pub->find(".gsc_a_at", 0)->href);
            $href = explode(':', $href);
            $href = end($href);
            $el['link'] = $href;
            $el['authors'] = trim($pub->find(".gs_gray", 0)->plaintext);
            $el['venue'] = trim($pub->find(".gs_gray", 1)->plaintext);
            if (!is_numeric($pub->find(".gsc_a_ac", 0)->plaintext))
                $el['citations'] = 0;
            else
                $el['citations'] = $pub->find(".gsc_a_ac", 0)->plaintext;

            if ($pub->find(".gsc_a_h", 0)->plaintext == " ")
                $el['year'] = 0;
            else
                $el['year'] = $pub->find(".gsc_a_h", 0)->plaintext;

            $result['publications'][] = $el;
        }
        return $result;
    }

    function googleDocLink($doc)
    {
        return "https://scholar.google.com/citations?view_op=view_citation&hl=de&user=$this->user&citation_for_view=$this->user:$doc";
    }

    function getDocumentDetails($doc)
    {
        $url = $this->googleDocLink($doc);
        $this->html->load_file($url);
        $result = array();
        $result['title'] = trim($this->html->find("#gsc_oci_title", 0)->plaintext);

        $links = $this->html->find("#gsc_oci_title_gg", 0);
        $result['doi'] = '';
        if (!empty($links)) {
            // $doi = preg_match("/^10.\d{4,9}\/[-._;()\/:A-Za-z0-9]+$/i", $links, $match);
            $doi = preg_match("/(10\.\d{4,9}\/[^\s&\"\']+)/", $links->innertext, $match);
            $doi = $match[0] ?? '';
            $result['doi'] = $doi; //trim($this->html->find(".gsc_oci_title_ggi", 0)->plaintext);
        }

        foreach ($this->html->find("#gsc_oci_table .gs_scl") as $field) {
            $k = trim($field->find(".gsc_oci_field", 0)->plaintext);
            $v = trim($field->find(".gsc_oci_value", 0)->plaintext);
            if ($k == 'Autoren') $v = explode(', ', $v);
            $result[$k] = $v;
        }
        return $result;
    }
}
