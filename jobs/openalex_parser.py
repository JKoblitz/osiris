
from pymongo import MongoClient
import configparser
import os

from diophila import OpenAlex
from nameparser import HumanName
from Levenshtein import ratio

TYPES = {
    "book-section": "chapter",
    "monograph": "book",
    "report-component": "others",
    "report": "others",
    "peer-review": "others",
    "book-track": "book",
    "journal-article": "article",
    "book-part": "book",
    "other": "others",
    "book": "book",
    "journal-volume": "article",
    "book-set": "book",
    "reference-entry": "others",
    "proceedings-article": "others",
    "journal": "others",
    "component": "others",
    "book-chapter": "chapter",
    "proceedings-series": "others",
    "report-series": "others",
    "proceedings": "others",
    "database": "others",
    "standard": "others",
    "reference-book": "book",
    "posted-content": "others",
    "journal-issue": "others",
    "dissertation": "dissertation",
    "grant": "others",
    "dataset": "others",
    "book-series": "book",
    "edited-book": "book",
}

# read the config file
config = configparser.ConfigParser()
path = os.path.dirname(__file__)
config.read(os.path.join(path, 'config.ini'))

inst_id = config['OpenAlex']['Institution']
startyear = config['DEFAULT']['StartYear']

# set up database connection
client = MongoClient(config['Database']['Connection'])
osiris = client[config['Database']['Database']]

# osiris['queue'].delete_many({})

possible_dupl = osiris['activities'].find({
            'type': 'publication',
            'year': {'$gte': int(startyear)},
        }, {'title': 1})
possible_dupl = [
    (i['_id'], i['title']) for i in possible_dupl
]

# set up OpenAlex
openalex = OpenAlex(config['DEFAULT'].get('AdminMail'))

def getUserId(name, orcid=None):
    if orcid:
        user = osiris['users'].find_one({'orcid': orcid})
        if user:
            return user['_id']
    user = osiris['users'].find_one(
        {'last': name.last, 'first': {'$regex': '^'+name.first+'.*'}})
    if user:
        return user['_id']
    return None


def getJournal(issn):
    journal = osiris['journals'].find_one({'issn': {'$in': issn}})
    if journal:
        return journal

    # if journal does not exist: create one
    source = openalex.get_single_venue(issn[-1], "issn")
    if not source or source['type'] != 'journal':
        return None

    new_journal = {
        'journal': source['display_name'],
        'abbr': source['abbreviated_title'],
        'publisher': source['host_organization_name'],
        'issn': source['issn'],
        'oa': source['is_oa'],
        'openalex': source['id'].replace('https://openalex.org/', '')
    }
    new_doc = osiris['journals'].insert_one(new_journal)

    new_journal['_id'] = new_doc.inserted_id
    return new_journal


# NOPE: use created_date and updated_date to filter
# Not possible, needs payed version

filters = {
    "from_publication_date": startyear + "-01-01",
    "institutions.id": inst_id,
    "has_doi": 'true',
    # 'doi': '10.12688/f1000research.111175.1'
}

pages_of_works = openalex.get_list_of_works(filters=filters, pages=None)

for page in pages_of_works:
    for work in page['results']:
        if work['is_retracted']:
            continue
        
        
        # print(work['doi'])
        if not work['doi'] or 'https://doi.org/' not in work['doi']:
            continue

        pubmed = work['ids'].get('pmid')
        if pubmed:
            pubmed = pubmed.replace('https://pubmed.ncbi.nlm.nih.gov/', '')

        # check if element is in the database
        doi = work['doi'].replace('https://doi.org/', '')
        if osiris["activities"].count_documents({
            '$or': [
                {'doi': doi},
                {'pubmed': pubmed}
            ]
        }) > 0:
            continue
        if osiris['queue'].count_documents({'doi': doi}) > 0:
            continue
        print(doi)
        # print(work['title'])

        typ = TYPES.get(work['type'])

        authors = []
        for a in work['authorships']:
            # match via name and ORCID
            name = HumanName(a['author']['display_name'])
            orcid = a['author'].get('orcid')
            if (orcid):
                orcid = orcid.replace('https://orcid.org/', '')

            user = getUserId(name, orcid)
            pos = a['author_position']
            if pos == 'middle' and a.get('is_corresponding'):
                pos = 'corresponding'

            authors.append({
                'last': name.last,
                'first': name.first + (' ' + name.middle if name.middle else ''),
                'position': pos,
                'aoi': ('https://openalex.org/'+inst_id in [i.get('id') for i in a['institutions']]),
                'orcid': orcid,
                'user': user
            })

        pages = None
        if work['biblio']['first_page']:
            pages = work['biblio']['first_page']
            if work['biblio']['last_page'] and work['biblio']['last_page'] != pages:
                pages += '-' + work['biblio']['last_page']

        # journal
        loc = work['primary_location']['source']
        # journal = loc['display_name']

        # date
        date = work['publication_date'].split('-')
        month = None
        day = None
        if len(date) >= 2:
            month = int(date[1])
        if len(date) >= 3:
            day = int(date[2])

        element = {
            'doi': doi,
            'type': 'publication',
            'subtype': typ,
            'title': work['title'],
            'year': work['publication_year'],
            'month': month,
            'day': day,
            'authors': authors,
            'pages': pages,
            'openalex': work['id'].replace('https://openalex.org/', ''),
            'pubmed': pubmed,
            'open_access': work['open_access']['is_oa'],
            'oa_status': work['open_access']['oa_status'],
            'correction': False,
            'epub': False
        }
        if (typ == 'others'):
            element['doc_type'] = work['type'].title()

        if (typ == 'article'):
            # print(element)
            if not loc or not loc['issn']:
                element['subtype'] = 'magazine'
                element['magazine'] = loc.get('display_name') if loc else None 
            else: 
                journal = getJournal(loc['issn'])
                element.update({
                    'volume': work['biblio']['volume'],
                    'issue': work['biblio']['issue'],
                    'journal': journal['journal'],
                    'journal_id': str(journal['_id'])
                })
                if (not element['volume']) and not element['issue']:
                    element['epub'] = True

        if (typ == 'chapter'):
            element.update({
                'book': loc['display_name'],
                # 'edition': None,
                # 'city': None,
                # 'publisher': None,
                # 'isbn': None
            })
        


        for id, dupl in possible_dupl: 
            dist = ratio(dupl, element['title'])
            # print(dist, dupl)
            if (dist>0.9): 
                element['duplicate'] = id
                break
        

        osiris['queue'].insert_one(element)


