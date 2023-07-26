<?php


class Schema
{

    public static function authors($authors)
    {
        $result = [];
        foreach ($authors as $a) {
            $result[] = [
                "@type" => "Person",
                "givenName" => $a['last'] ?? '',
                "familyName" => $a['first'] ?? '',
            ];
        }
        if (count($result) === 1) $result = $result[0];
        return $result;
    }

    public static function identifier($type, $value)
    {
        return [
            "@type" => "PropertyValue",
            "name" => $type,
            "value" => $value
        ];
    }

    public static function organisation($name, $address = null)
    {
        return [
            "@type" => "Organization",
            "name" => $name,
            "address" => $address
        ];
    }

    public static function event($d)
    {
        $event = [
            "@type" => "PublicationEvent"
        ];
        if (isset($d['conference']))
            $event['name'] = $d['conference'];
        if (isset($d['location']))
            $event['location'] = $d['location'];

        if (isset($d['start']) && !empty($d['start']))
            $event['startDate'] = Schema::date($d['start']);
        else 
            $event['startDate'] = Schema::date($d);
        if (isset($d['end']) && !empty($d['end']))
            $event['endDate'] = Schema::date($d['end']);
        return $event;
    }

    public static function journal($journal)
    {
        $issn = $journal['issn']->bsonSerialize();
        if (empty($issn)) $issn = null;
        elseif (count($issn) == 1) $issn = $issn[0];
        return $journal = [
            "@type" => "Periodical",
            "@id" => "#journal",
            "name" => $journal['journal'],
            "issn" => $issn,
            "publisher" => $journal['publisher']
        ];
    }

    public static function issue($issue)
    {
        return [
            "@id" => "#issue",
            "@type" => "PublicationIssue",
            "issueNumber" => $issue
        ];
    }

    public static function date($d)
    {
        $date = $d['year'];
        if (!empty($d['month'])) $date .= "-" . ($d['month'] < 10 ? '0' . $d['month'] : $d['month']);
        if (!empty($d['day'])) $date .= "-" . ($d['day'] < 10 ? '0' . $d['day'] : $d['day']);
        return $date;
    }
}
