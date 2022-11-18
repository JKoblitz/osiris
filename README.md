# OSIRIS

## Nutzbare APIs


http://dx.doi.org/ ???



### OpenCitations
Es gibt eine offene API von OpenCitations, mit der man Publikations-Metadaten herunterladen kann: [Link](https://opencitations.net/index/api/v1/metadata/10.1093/nar/gkab961).

### DataCite
Mit DataCite kann man auch andere Aktivitäten finden, z.B. Zenodo-Archive (https://api.datacite.org/dois/10.5281/zenodo.3742817)


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
Esummary kann auch JSON zurückgeben!

https://dataguide.nlm.nih.gov/eutilities/utilities.html#esummary

Beispiel:
https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=35293546&retmode=asn.1



## Journal impact factors
-[x] Add Count of publications to journals 
```sql
SELECT journal.*, COUNT(*) AS publications, IFNULL(impact_factor, 'unknown') AS impact
FROM `journal` 
LEFT JOIN publication USING (journal_id) 
LEFT JOIN (
    SELECT journal_id, impact_factor FROM journal_if WHERE `year` = 2021
) AS impact USING (journal_id)
GROUP BY journal_id ORDER BY `publications` DESC 
```

https://www.scimagojr.com/journalrank.php?wos=true&openaccess=true

