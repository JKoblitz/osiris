"""This module wraps all endpoints of the OpenAlex API and their parameters."""
from typing import Optional, List, Iterable


class _Endpoint:
    """Base class for endpoints."""

    # Required parameters for every endpoint that need to be overwritten
    name: str = ""
    id_attrs: tuple = ()
    filter_attrs: tuple = ()
    groupable_attrs: tuple = ()

    # make sure every endpoint includes required parameters and overwrites empty values
    def __init_subclass__(cls,
                          required=('name', 'id_attrs', 'filter_attrs', 'groupable_attrs'),
                          **kwargs):
        """ Helper method making sure that the required class variables
         are present (overwritten) in every subclass."""
        for req in required:
            if not getattr(cls, req):
                raise TypeError(f"Can't instantiate subclass {cls.__name__}"
                                f" without {req} attribute defined")
        return super().__init_subclass__(**kwargs)

    # Attributes that can be used to sort entities,
    # see https://docs.openalex.org/api/get-lists-of-entities/sort-entity-lists
    sortable_attrs = (
        "cited_by_count",
        "display_name",
        "publication_date",
        "relevance_score",
        "works_count"
    )
    # Attributes that can be used to sort entities when using group_by,
    # see https://docs.openalex.org/api/get-groups-of-entities#sorting-groups
    sortable_attrs_for_groups = ("count", "key")
    # sort directions
    sortable_drctns = ("asc", "desc")

    # --------------------------------------------------------------------------
    # ----------------------------- QUERY  METHODS -----------------------------
    def __init__(self, api_caller):
        self.api_caller = api_caller

    # Get single entity: Random
    def get_random(self) -> dict:
        """ Get a single random entity. """
        random_path = f'{self.name}/random'
        return self.api_caller.get(random_path)

    # Get single entity: By ID
    def get_single(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get a specific entity by using one of its IDs.

        Args:
            id_value (str): value of an ID identifying a specific author.
            id_type (Optional[str]): type of the specified `id_value` e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the `id_value`.
                        `id_type` can be left out if `id_value` is a supported external ID
                         (URL) or OpenAlex ID.

        Returns:
            dict from JSON describing the entity.

        Raises:
            ValueError: if `id_value` is empty or None.
                        if 'id_type' is not specified in the list of IDs for this endpoint.
                        if 'id_type' is empty or None and id_value is not a URL or OpenAlex ID.
        """
        if not id_value:
            raise ValueError("'id_value' argument can not be empty")  # fail fast

        # if user specified the id_type, use it as namespace in front of the id_value
        if id_type and id_type in self.id_attrs:
            single_path = f'{self.name}/{id_type}:{id_value}'
            return self.api_caller.get(single_path)

        # id_type can only be left out if id_value is a URL
        # or OpenAlex ID (starting with endpoint name's first letter)
        if not id_type and id_value.lower().startswith((self.name[0], "http")):
            single_path = f'{self.name}/{id_value}'
            return self.api_caller.get(single_path)

        raise ValueError(f"'id_type' is not valid. Valid values are {self.id_attrs}" if id_type
                         else "'id_value' not valid. Needs to be a URL or OpenAlex ID.")

    # Get grouped entities: GroupBy
    def get_groups(self, group_by: str,
                   filters: Optional[dict] = None,
                   search: Optional[str] = None,
                   sort: Optional[dict] = None) -> dict:
        """ Get entities grouped into facets.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of entities
             after grouping them, optional.

        Returns:
            dict from JSON representing the grouped entities.

        Raises:
            ValueError: if `group_by` is empty or None or not a groupable attribute.
                        if `sort` contains items, where an item.key is not "count" or "key"
                        or a item.value is not "asc" or "desc".
                        if `filters` contains keys that are not valid filter attributes
                        for this endpoint.
        """
        if not group_by:  # fail fast
            raise ValueError("'group_by' argument can not be empty")

        params = {'group_by': self.__build_group_by_param(group_by),
                  'filter': self.__build_filter_param(filters),
                  'search': search,
                  'sort': self.__build_sort_param_for_groups(sort)}
        return self.api_caller.get(self.name, params)

    # Get list of entities
    def get_list(self, filters: Optional[dict] = None,
                 search: Optional[str] = None,
                 sort: Optional[dict] = None,
                 per_page: Optional[int] = None,
                 pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of entities.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page. Needs to be in [1;200].
                                      Defaults to 25.
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of entities.

        Raises:
            ValueError: if `sort` contains keys that are not valid sort attributes
                        or one of the sort values is not "asc" or "desc".
                        if `filters` contains keys that are not valid filter attributes
                        for this endpoint.
        """
        is_search = self.__is_search(filters=filters, search=search)
        params = {'filter': self.__build_filter_param(filters),
                  'search': search,
                  'sort': self.__build_sort_param_for_list(sort, is_search)}

        return self.api_caller.get_all(self.name, params, per_page, pages)

    # --------------------------------------------------------------------------
    # ----------------------------- HELPER METHODS -----------------------------
    def __build_filter_param(self, filters: Optional[dict]) -> Optional[str]:
        """Helper method validating and constructing the 'filter' parameter."""
        if not filters:
            return None  # nothing to do here

        if all(f in self.filter_attrs for f in filters.keys()):
            return ",".join(f"{k}:{v}" for k, v in filters.items())

        raise ValueError("Value for 'filter' key not valid."
                         f"Valid filter keys are {','.join(self.filter_attrs)}.")

    def __is_search(self, filters: Optional[dict], search: Optional[str]) -> bool:
        """Helper method determining if 'search' is used, either in filter or search parameter."""
        search_in_filters = filters and any(f.endswith(".search") for f in filters.keys())
        if search_in_filters or search is not None:
            return True
        # else
        return False

    def __build_sort_param_for_list(self, sort: Optional[dict],
                                    is_search: bool) -> Optional[str]:
        """Helper method validating and constructing the 'sort' parameter for lists ."""
        if not sort:
            return None  # nothing to do here

        # special case: 'relevance_score' only valid sorting key if search is used
        relevance_score = "relevance_score"
        if not is_search and relevance_score in sort.keys():
            raise ValueError("Sorting by 'relevance_score' only available when using search.")

        # special case: 'relevance_score' only valid with sort direction "desc"
        if is_search and relevance_score in sort.keys():
            if sort[relevance_score] == "asc":
                raise ValueError("Sorting by 'relevance_score' ascending not allowed.")

        sortable_keys = self.sortable_attrs
        sortable_values = self.sortable_drctns
        return self.__build_sort_param(sort, sortable_keys, sortable_values)

    def __build_sort_param_for_groups(self, sort: Optional[dict]) -> Optional[str]:
        """Helper method validating and constructing the 'sort' parameter for groups."""
        if not sort:
            return None  # nothing to do here

        sortable_keys = self.sortable_attrs_for_groups
        sortable_values = self.sortable_drctns
        return self.__build_sort_param(sort, sortable_keys, sortable_values)

    def __build_sort_param(self, sort: dict,
                           sortable_keys: Iterable,
                           sortable_values: Iterable) -> Optional[str]:
        """Helper method constructing the 'sort' parameter."""
        if (all(sk in sortable_keys for sk in sort.keys())
                and all(sv in sortable_values for sv in sort.values())):
            return ",".join(f"{k}:{v}" for k, v in sort.items())

        raise ValueError("Item for sorting dict not valid.\n"
                         f"Valid sorting keys are {','.join(sortable_keys)} "
                         f"and valid sorting values are {','.join(sortable_values)}.")

    def __build_group_by_param(self, group_by: str) -> str:
        """Helper method validating and building the 'group_by' parameter."""
        if group_by in self.groupable_attrs:
            return group_by

        raise ValueError("Value for 'group_by' not in groupable attributes."
                         f"\nGroupable attributes are {','.join(self.groupable_attrs)}.")


# --------------------------------------------------------------------------
# --------------------------- SPECIFIC ENDPOINTS ---------------------------
class Authors(_Endpoint):
    """Authors endpoint."""
    name = "authors"
    id_attrs = ("openalex", "orcid", "mag")
    filter_attrs = (
        "cited_by_count",
        "display_name",
        "display_name.search",
        "from_created_date",
        "from_updated_date",
        "has_orcid",
        "last_known_institution.country_code",
        "last_known_institution.id",
        "last_known_institution.ror",
        "last_known_institution.type",
        "openalex_id",
        "orcid",
        "works_count",
        "x_concepts.id"
    )
    groupable_attrs = (
        "has_orcid",
        "last_known_institution.country_code",
        "last_known_institution.id",
        "last_known_institution.ror",
        "last_known_institution.type",
        "x_concepts.id"
    )


class Concepts(_Endpoint):
    """Concepts endpoint."""
    name = "concepts"
    id_attrs = ("openalex", "wikidata", "mag")
    filter_attrs = (
        "ancestors.id",
        "cited_by_count",
        "display_name",
        "display_name.search",
        "from_created_date",
        "from_updated_date",
        "has_wikidata",
        "level",
        "openalex_id",
        "wikidata_id",
        "works_count"
    )
    groupable_attrs = (
        "ancestors.id",
        "has_wikidata",
        "level"
    )


class Institutions(_Endpoint):
    """Institutions endpoint."""
    name = "institutions"
    id_attrs = ("openalex", "ror", "mag")
    filter_attrs = (
        "cited_by_count",
        "country_code",
        "display_name",
        "display_name.search",
        "from_created_date",
        "from_updated_date",
        "has_ror",
        "openalex_id",
        "ror",
        "type",
        "works_count",
        "x_concepts.id"
    )
    groupable_attrs = (
        "country_code",
        "has_ror",
        "type",
        "x_concepts.id"
    )


class Venues(_Endpoint):
    """Venues endpoint."""
    name = "sources"
    id_attrs = ("openalex", "issn", "issn_l", "mag")
    filter_attrs = (
        "cited_by_count",
        "display_name",
        "display_name.search",
        "from_created_date",
        "from_updated_date",
        "has_issn",
        "issn",
        "is_in_doaj",
        "is_oa",
        "openalex_id",
        "publisher",
        "works_count",
        "x_concepts.id"
    )
    groupable_attrs = (
        "has_issn",
        "issn",
        "is_in_doaj",
        "is_oa",
        "publisher",
        "x_concepts.id"
    )


class Works(_Endpoint):
    """Works endpoint."""
    name = "works"
    id_attrs = ("openalex", "doi", "pmid", "mag")
    filter_attrs = (
        "abstract.search",
        "alternate_host_venues.id",
        "alternate_host_venues.license",
        "alternate_host_venues.version",
        "author.id",
        "author.orcid",
        "authorships.author.id",
        "authorships.author.orcid",
        "authorships.institutions.country_code",
        "authorships.institutions.id",
        "authorships.institutions.ror",
        "authorships.institutions.type",
        "cited_by",
        "cited_by_count",
        "cites",
        "concept.id",
        "concepts.id",
        "concepts.wikidata",
        "display_name",
        "display_name.search",
        "doi",
        "from_created_date",
        "from_publication_date",
        "from_updated_date",
        "has_doi",
        "has_oa_accepted_or_published_version",
        "has_oa_submitted_version",
        "host_venue.id",
        "host_venue.issn",
        "host_venue.publisher",
        "ids.mag",
        "ids.openalex",
        "ids.pmcid",
        "ids.pmid",
        "institution.id",
        "institutions.country_code",
        "institutions.id",
        "institutions.ror",
        "institutions.type",
        "is_oa",
        "is_paratext",
        "is_retracted",
        "journal.id",
        "mag",
        "oa_status",
        "open_access.is_oa",
        "open_access.oa_status",
        "openalex",
        "openalex_id",
        "pmcid",
        "pmid",
        "publication_date",
        "publication_year",
        "raw_affiliation_string.search",
        "referenced_works",
        "related_to",
        "title.search",
        "to_publication_date",
        "type"
    )
    groupable_attrs = (
        "alternate_host_venues.id",
        "alternate_host_venues.license",
        "alternate_host_venues.version",
        "author.id",
        "author.orcid",
        "authorships.author.id",
        "authorships.author.orcid",
        "authorships.institutions.country_code",
        "authorships.institutions.id",
        "authorships.institutions.ror",
        "authorships.institutions.type",
        "cites",
        "concepts.id",
        "concepts.wikidata",
        "has_doi",
        "host_venue.id",
        "host_venue.issn",
        "host_venue.publisher",
        "institutions.country_code",
        "institutions.id",
        "institutions.ror",
        "institutions.type",
        "is_oa",
        "is_paratext",
        "is_retracted",
        "journal.id",
        "open_access.is_oa",
        "open_access.oa_status",
        "publication_year",
        "type"
    )

    def get_by_api_url(self, works_api_url:str,
                          per_page: Optional[int] = None,
                          pages: Optional[List[int]] = None):
        """ Convenience method to get list of works by a `works_api_url`."""
        query_string = works_api_url.split(self.name, 1)[1]
        path = f"{self.name}{query_string}"
        return self.api_caller.get_all(path, {}, per_page, pages)
