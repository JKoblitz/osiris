# API docs


### `/api/activities` All activities

##### Parameters
| Type | Parameter | Description |
| ---- | ---- | ---- |
| {String} | `apikey` <small class="badge">optional</small> | Your API key, if defined |
| {Object} | `filter` <small class="badge">optional</small> | Filter as valid params request |
| {String} | `json` <small class="badge">optional</small> | Filter string from the advanced search (will overwrite filter) |
| {String} | `full` <small class="badge">optional</small> | If parameter is given, the full database entries are retreived instead of rendered output |



##### Returns (200)
| Type | Field | Description |
| ---- | ---- | ---- |
| {String} | `id` | Unique ID of the activity. |
| {String} | `activity` |  Full web formatted activity (with relative links to osiris). |
| {String} | `print` | Formatted activity for export (print)  |
| {String} | `icon` | OSIRIS activity icon |
| {String} | `type` | Type of activity |
| {String} | `subtype` | Subtype of activity |
| {Int} | `year` | Year of activity |
| {String} | `authors` | Formatted authors (affiliated authors are bold) |
| {String} | `title` | Title of the activity |
| {String[]} | `departments` | All associated departments, indicated by their ID |

##### Example
[/api/activities?filter[type]=publication](/api/activities?filter[type]=publication)


```json
 {
        "id": "6458fcb30d695c593828763f",
        "activity": "<a href='/activities/view/6458fcb30d695c593828763f'>Metabolism from the magic angle</a><br><small class='text-muted d-block'><a href='/osiris/profile/juk20'>Koblitz,&nbsp;J.</a><br> <i>Nature Chemical Biology</i> (2023) <a href='/uploads/6458fcb30d695c593828763f/Metabolism from the magic angle.pdf' target='_blank' data-toggle='tooltip' data-title='pdf: Metabolism from the magic angle.pdf' class='file-link'><i class='ph ph-file ph-file-pdf'></i></a></small>",
        "print": "<b>Koblitz,&nbsp;J.</b> (2023) Metabolism from the magic angle. <i>Nature Chemical Biology</i> <a target='_blank' href=''></a>",
        "icon": "<span data-toggle='tooltip' data-title='Non-refereed'><i class='ph text-publication ph-newspaper'></i></span>",
        "type": "Publikationen",
        "subtype": "Non-refereed",
        "year": 2023,
        "authors": "<b>Koblitz,&nbsp;J.</b>",
        "title": "Metabolism from the magic angle",
        "departments": [
            "BID"
        ]
    }, ...
```

### `/api/projects` Get projects based on search criteria

##### Parameters
| Type | Parameter | Description |
| ---- | ---- | ---- |
| {String} | `apikey` <small class="badge">optional</small> | Your API key, if defined |
| {String} | `search` <small class="badge">optional</small> | Search string (looked for in name and ID of the Project) |
| {Object} | `filter` <small class="badge">optional</small> | Filter as valid params request |
| {String} | `json` <small class="badge">optional</small> | Filter string from the advanced search (will overwrite filter)   |


##### Returns (200)
| Type | Field | Description |
| ---- | ---- | ---- |
| {Object} |  | Full Object containing project data, see example. |


##### Example
[/api/projects?filter[public]=1](/api/projects?filter[public]=1)


```json
 {
    "_id": {
        "$oid": "65c9c42f1e82e991fd06f5d2"
    },
    "name": "OSIRIS",
    "type": "Eigenfinanziert",
    "title": "OSIRIS - das moderne Forschungsinformationssystem",
    "contact": "juk20",
    "status": "applied",
    "funder": "Eigenmittel",
    "funding_organization": "Eigenmittel",
    "funding_number": null,
    "purpose": "others",
    "role": "coordinator",
    "coordinator": null,
    "start": {
        "year": 2023,
        "month": 1,
        "day": 1
    },
    "end": {
        "year": 2025,
        "month": 12,
        "day": 31
    },
    "grant_sum": null,
    "grant_income": null,
    "personal": null,
    "website": 'https://osiris-app.de',
    "abstract": null,
    "created": "2024-02-12",
    "public": true,
    "persons": [
        {
            "user": "juk20",
            "role": "PI",
            "name": "Julia Koblitz"
        }
    ]
}, ...
```

### `/api/journal` Find a journal

##### Parameters
| Type | Parameter | Description |
| ---- | ---- | ---- |
| {String} | `apikey` <small class="badge">optional</small> | Your API key, if defined |
| {String} | `search` <small class="badge">optional</small> | Search string (looked for in name and ISSN of the journal) |


##### Returns (200)
| Type | Field | Description |
| ---- | ---- | ---- |
| {String} | `_id` | Unique Mongo ID of the journal. |
| {String} | `journal` |  Full name of the journal |
| {String} | `abbr` | Official abbreviation of the journal |
| {String} | `publisher` | Publisher of this journal |
| {Object[]} | `impact` | All known impact factors of the journal, given as an array of objects with year and impact |
| {String[]} | `issn` | All ISSN of the journal |
| {Boolean/Integer} | `oa` | Year of open access start of false |

##### Example
[/api/journal?search=Systematic](/api/journal?search=Systematic)


```json
 
    "_id": {
        "$oid": "6364d153f7323cdc8253104a"
    },
    "nlmid": 100899600,
    "journal": "International journal of systematic and evolutionary microbiology",
    "abbr": "Int J Syst Evol Microbiol",
    "publisher": "Microbiology Society",
    "issn": [
        "1466-5034",
        "1466-5026",
        "0020-7713"
    ],
    "impact": [
        {
            "year": 2021,
            "impact": 2.689
        },
        {
            "year": 2020,
            "impact": 2.747
        },
        {
            "year": 2019,
            "impact": 2.415
        }
    ],
    "oa": false,
}, ...
```

### `/api/journals` All journals


##### Parameters
| Type | Parameter | Description |
| ---- | ---- | ---- |
| {String} | `apikey` <small class="badge">optional</small> | Your API key, if defined |


##### Returns (200)
| Type | Field | Description |
| ---- | ---- | ---- |
| {String} | `id` | Unique ID of the journal. |
| {String} | `name` |  Full web formatted journal (with relative links to osiris). |
| {String} | `abbr` | Formatted journal for export (print)  |
| {String} | `publisher` | OSIRIS journal icon |
| {String} | `open_access` |  |
| {String} | `issn` | All ISSN of the journal, separated by comma |
| {Float} | `if` | Last year impact factor |
| {Integer} | `count` | Number of activities associated to this journal |

##### Example
[/api/journals](/api/journals)


```json
 {
      "id": "6389ae62c902176a283535e2",
      "name": "Frontiers in Microbiology",
      "abbr": "Front Microbiol",
      "publisher": "Frontiers Research Foundation",
      "open_access": "seit 2010",
      "issn": "1664-302X",
      "if": 6.064,
      "count": 103
    }, ...
```

### `/api/levenshtein` Search activity by title using the Levenshtein similarity


##### Parameters
| Type | Parameter | Description |
| ---- | ---- | ---- |
| {String} | `apikey` <small class="badge">optional</small> | Your API key, if defined |
| {String} | `title` | The title of the activity you are looking for |
| {String} | `doi` <small class="badge">optional</small> | If available: DOI of activity |
| {Integer} | `pubmed` <small class="badge">optional</small> | If available: Pubmed-ID of activity |
 *

##### Returns (200)
| Type | Field | Description |
| ---- | ---- | ---- |
| {Float} | `similarity` | The Levenshtein Similarity of the title. Will be 100, if ID matches |
| {String} | `id` |  Unique ID of the found activity |
| {String} | `title` | Title of the found activity |

##### Example
[/api/levenshtein?title=metabolism frm the magic angle](/api/levenshtein?title=metabolism frm the magic angle)


```json
 {
  "similarity": 98.4,
  "id": "6458fcb30d695c593828763f",
  "title": "metabolism from the magic angle"
}
```

