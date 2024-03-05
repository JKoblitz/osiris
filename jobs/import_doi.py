from openalex_parser import OpenAlexParser

parser = OpenAlexParser()
with open('jobs/doi.csv') as f:
    for line in f:
        line = line.strip()
        parser.get_work(line, ignoreDupl=True)