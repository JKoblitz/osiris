"""This module wraps the OpenAlex API."""
from typing import Optional, Iterable, List

from diophila.api_caller import APICaller
from diophila.endpoints import Authors, Concepts, Institutions, Venues, Works


class OpenAlex:
    """This class wraps the OpenAlex API."""

    def __init__(self, email: Optional[str] = None) -> object:
        """ Init wrapper, preferably with an email to get into the polite pool.

        Args:
            email (Optional[str]): email address of the user that will be added
                        in the request header to get into the polite pool, optional.

        Returns:
            object wrapping the OpenAlex API.
        """
        self._api_caller = APICaller("https://api.openalex.org", email)

    # Get single entity: Random
    def get_random_author(self) -> dict:
        """ Get random author.

        Returns:
            JSON object from HTTP response containing a single Author entity.
        """
        return Authors(self._api_caller).get_random()

    def get_random_concept(self) -> dict:
        """ Get random concept.

        Returns:
            JSON object from HTTP response containing a single Concept entity.
        """
        return Concepts(self._api_caller).get_random()

    def get_random_institution(self) -> dict:
        """ Get random institution.

        Returns:
            JSON object from HTTP response containing a single Institution entity.
        """
        return Institutions(self._api_caller).get_random()

    def get_random_venue(self) -> dict:
        """ Get random venue.

        Returns:
            JSON object from HTTP response containing a single Venue entity.
        """
        return Venues(self._api_caller).get_random()

    def get_random_work(self) -> dict:
        """ Get random work.

        Returns:
            JSON object from HTTP response containing a single Work entity.
        """
        return Works(self._api_caller).get_random()

    # Get single entity: By ID
    def get_single_author(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get single author by using an ID.

        Args:
            id_value (str): value of an ID identifying a specific author.
            id_type (Optional[str]): type of the specified id_value e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the id_value. optional.

        Returns:
            JSON object from HTTP response containing a single author.
         """
        return Authors(self._api_caller).get_single(id_value, id_type)

    def get_single_concept(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get single concept by using an ID.

        Args:
            id_value (str): value of an ID identifying a specific concept.
            id_type (Optional[str]): type of the specified id_value e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the id_value. optional.

        Returns:
            JSON object from HTTP response containing a single concept.
         """
        return Concepts(self._api_caller).get_single(id_value, id_type)

    def get_single_institution(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get single institution by using an ID.

        Args:
            id_value (str): value of an ID identifying a specific institution.
            id_type (Optional[str]): type of the specified id_value e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the id_value. optional.

        Returns:
            JSON object from HTTP response containing a single institution.
        """
        return Institutions(self._api_caller).get_single(id_value, id_type)

    def get_single_venue(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get single venue by using an ID.

        Args:
            id_value (str): value of an ID identifying a specific venue.
            id_type (Optional[str]): type of the specified id_value e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the id_value. optional.

        Returns:
            JSON object from HTTP response containing a single venue.
        """
        return Venues(self._api_caller).get_single(id_value, id_type)

    def get_single_work(self, id_value: str, id_type: Optional[str] = None) -> dict:
        """ Get single work by using an ID.

        Args:
            id_value (str): value of an ID identifying a specific work.
            id_type (Optional[str]): type of the specified id_value e.g. 'openalex', 'mag'.
                        Will be used as a namespace for the id_value. optional.

        Returns:
            JSON object from HTTP response containing a single work.
        """
        return Works(self._api_caller).get_single(id_value, id_type)

    # Get groups of entities: group_by
    def get_groups_of_authors(self, group_by: str,
                              filters: Optional[dict] = None,
                              search: Optional[str] = None,
                              sort: Optional[dict] = None) -> dict:
        """ Get author groups.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of authors
             after grouping them, optional.

        Returns:
            JSON object from HTTP response containing groups of authors.
        """
        return Authors(self._api_caller).get_groups(group_by=group_by,
                                                    filters=filters,
                                                    search=search,
                                                    sort=sort)

    def get_groups_of_concepts(self, group_by: str,
                               filters: Optional[dict] = None,
                               search: Optional[str] = None,
                               sort: Optional[dict] = None) -> dict:
        """ Get concept groups.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of concepts
             after grouping them, optional.

        Returns:
            JSON object from HTTP response containing groups of concepts.
        """
        return Concepts(self._api_caller).get_groups(group_by=group_by,
                                                     filters=filters,
                                                     search=search,
                                                     sort=sort)

    def get_groups_of_institutions(self, group_by: str,
                                   filters: Optional[dict] = None,
                                   search: Optional[str] = None,
                                   sort: Optional[dict] = None) -> dict:
        """ Get institution groups.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of institutions
             after grouping them, optional.

        Returns:
            JSON object from HTTP response containing groups of institutions.
        """
        return Institutions(self._api_caller).get_groups(group_by=group_by,
                                                         filters=filters,
                                                         search=search,
                                                         sort=sort)

    def get_groups_of_venues(self, group_by: str,
                             filters: Optional[dict] = None,
                             search: Optional[str] = None,
                             sort: Optional[dict] = None) -> dict:
        """ Get venue groups.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of venues
             after grouping them, optional.

        Returns:
            JSON object from HTTP response containing groups of venues.
        """
        return Venues(self._api_caller).get_groups(group_by=group_by,
                                                   filters=filters,
                                                   search=search,
                                                   sort=sort)

    def get_groups_of_works(self, group_by: str,
                            filters: Optional[dict] = None,
                            search: Optional[str] = None,
                            sort: Optional[dict] = None) -> dict:
        """ Get work groups.

        Args:
            group_by (str): property used to construct groups.
            filters (Optional[dict]): dictionary with properties to filter results
             before grouping them, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort the groups of works
             after grouping them, optional.

        Returns:
            JSON object from HTTP response containing groups of works.
        """
        return Works(self._api_caller).get_groups(group_by=group_by,
                                                  filters=filters,
                                                  search=search,
                                                  sort=sort)

    # Get list of entities
    def get_list_of_authors(self, filters: Optional[dict] = None,
                            search: Optional[str] = None,
                            sort: Optional[dict] = None,
                            per_page: Optional[int] = None,
                            pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of authors.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                                      Needs to be between [1;200]
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Authors(self._api_caller).get_list(filters=filters,
                                                  search=search,
                                                  sort=sort,
                                                  per_page=per_page,
                                                  pages=pages)

    def get_list_of_concepts(self, filters: Optional[dict] = None,
                             search: Optional[str] = None,
                             sort: Optional[dict] = None,
                             per_page: Optional[int] = None,
                             pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of concepts.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                                      Needs to be between [1;200]
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Concepts(self._api_caller).get_list(filters=filters,
                                                   search=search,
                                                   sort=sort,
                                                   per_page=per_page,
                                                   pages=pages)

    def get_list_of_institutions(self, filters: Optional[dict] = None,
                                 search: Optional[str] = None,
                                 sort: Optional[dict] = None,
                                 per_page: Optional[int] = None,
                                 pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of institutions.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                                      Needs to be between [1;200]
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Institutions(self._api_caller).get_list(filters=filters,
                                                       search=search,
                                                       sort=sort,
                                                       per_page=per_page,
                                                       pages=pages)

    def get_list_of_venues(self, filters: Optional[dict] = None,
                           search: Optional[str] = None,
                           sort: Optional[dict] = None,
                           per_page: Optional[int] = None,
                           pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of venues.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                                      Needs to be between [1;200]
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Venues(self._api_caller).get_list(filters=filters,
                                                 search=search,
                                                 sort=sort,
                                                 per_page=per_page,
                                                 pages=pages)

    def get_list_of_works(self, filters: Optional[dict] = None,
                          search: Optional[str] = None,
                          sort: Optional[dict] = None,
                          per_page: Optional[int] = None,
                          pages: Optional[List[int]] = None) -> Iterable[dict]:
        """ Get list of works.

        Args:
            filters (Optional[dict]): dictionary with properties to filter results, optional.
            search (Optional[str]): search string to find results that match
             a given text search, optional.
             If you search for a multiple-word phrase, OpenAlex will treat each word separately.
             If you only want results matching the exact phrase, enclose it in double quotes.
            sort (Optional[dict]): dictionary with properties to sort entities, optional.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                                      Needs to be between [1;200]
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Works(self._api_caller).get_list(filters=filters,
                                                search=search,
                                                sort=sort,
                                                per_page=per_page,
                                                pages=pages)

    # Convenience method to retrieve works referenced by another entity
    def get_works_by_api_url(self, works_api_url:str,
                          per_page: Optional[int] = None,
                          pages: Optional[List[int]] = None):
        """ Get list of works via another entity's `works_api_url` property.

        Args:
            works_api_url (str): reference to a list of works connected to another entity.
            per_page (Optional[int]): number of entities per page, defaults to 25.
                Needs to be between [1;200].
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of works.
        """
        return Works(self._api_caller).get_by_api_url(works_api_url, per_page, pages)
