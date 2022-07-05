# Research Report Tool

## API

### OpenCitations
Es gibt eine offene API von OpenCitations, mit der man Publikations-Metadaten herunterladen kann: [Link](https://opencitations.net/index/api/v1/metadata/10.1093/nar/gkab961).

### Google-Scholar API
Es gibt eine Google-Scholar API, die alle Daten aus Google Scholar abfragt: [Link](https://serpapi.com/google-scholar-author-api).

### CrossRef
Es gibt eine API von CrossRef: [Link zu den Docs](https://api.crossref.org/swagger-ui/index.html)

URL für Anfragen:
https://api.crossref.org/works/10.1093/nar/gkab961

Es gibt auch ein Python-Paket: https://github.com/fabiobatalha/crossrefapi

Autoren-Abfrage: https://api.crossref.org/works?query.author=Julia+Koblitz

Affiliation-Abfrage: https://api.crossref.org/works?query.affiliation=Leibniz+Institute+DSMZ


Beispiel für eine Jahresabfrage:
https://api.crossref.org/works?query.affiliation=DSMZ&filter=from-pub-date:2021



title
journal_id
year
date_publication
issue
pages
volume
doi
pubmed
type
book_title
open_access 