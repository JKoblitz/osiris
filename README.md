# OSIRIS

![](https://www.kerndatensatz-forschung.de/version1/technisches_datenmodell/v_1_2/EntityRelationshipModell/KDSF.png)

[Hier](https://www.kerndatensatz-forschung.de/version1/technisches_datenmodell/v_1_2/ER-Modell.html) findet sich ein ganz gut nutzbares Datenmodell.

## Nutzbare APIs

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


### Pubmed
Essumary kann auch JSON zurückgeben!

https://dataguide.nlm.nih.gov/eutilities/utilities.html#esummary

Beispiel:
https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=35293546&retmode=asn.1