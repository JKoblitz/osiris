from openalex_parser import OpenAlexParser

parser = OpenAlexParser()
with open('jobs/doi.csv') as f:
    for line in f:
        line = line.strip()
        try:
            parser.get_work(line, ignoreDupl=True)
        except Exception as e:
            print(f'''
            There was an error with the DOI: {line}
            \t{e}
            The DOI will be skipped.
            ''')
            print(line)
            continue