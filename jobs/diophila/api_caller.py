"""This module wraps all API calls to the OpenAlex API."""
from typing import Optional, List, Iterable
import requests


class APICaller:
    """This class wraps all API calls to the OpenAlex API."""

    # Basic paging only works for to read the first 10,000 results of any list.
    # see https://docs.openalex.org/api#basic-paging
    PAGING_RESULTS_MAX = 10000

    # per-page parameter can be any number between 1 and 200
    # see https://docs.openalex.org/api#basic-paging
    PER_PAGE_MAX = 200

    def __init__(self, base_url: str, email: Optional[str] = None) -> object:
        """ Init API caller, preferably with an email to get into the polite pool."""
        self.base_url = base_url
        self.headers = {'Accept': 'application/json'}
        if email:
            self.headers['User-Agent'] = f'mailto:{email}'

    def get(self, path: str, params: Optional[dict] = None) -> dict:
        """ Make a GET request to the API.

        Args:
            path (str): path that will be concatenated to the base URL of the OpenAlex API.
            params (Optional[dict]): dictionary containing items that will be constructed
                        into a query string, optional.

        Returns:
            JSON object from HTTP response.
         """
        response = requests.get(url=f"{self.base_url}/{path}",
                                params=params,
                                headers=self.headers)
        response.raise_for_status()
        result = response.json()
        return result

    def get_all(self,
                path: str,
                params: dict,
                per_page: Optional[int] = None,
                pages: Optional[List[int]] = None) -> Iterable:
        """ Make multiple GET requests to the API to paginate through results.

        Args:
            path (str): path that will be concatenated to the base URL of the OpenAlex API.
            params (dict): dictionary containing items that will be constructed
                        into a query string.
            per_page (Optional[int]): number of entities per page. Needs to be in [1;200].
                Defaults to 25.
            pages (Optional[List[int]]): list of page numbers to query from API, optional.
                If empty, cursor pagination will be used.

        Returns:
            Generator, each item a dict from JSON representing a (partial) list of entities.
         """
        params['per_page'] = self.__validate_per_page_param(per_page)
        if pages:
            return self.__do_basic_paging(path, params, pages)
        # else:
        return self.__do_cursor_paging(path, params)

    def __do_basic_paging(self, path: str, params: dict, pages: List[int]):
        """ Use basic pagination to loop thought the specified result pages. """
        pages = self.__validate_pages(pages, params['per_page'])
        for page in pages:
            params['page'] = page
            yield self.get(path, params)

    def __do_cursor_paging(self, path: str, params: dict):
        """ Use cursor pagination to loop thought the results. """
        params['cursor'] = "*"  # start cursor pagination
        while True:
            json_response = self.get(path, params)
            yield json_response

            next_cursor = json_response['meta']['next_cursor']
            if not next_cursor:
                break

            params['cursor'] = next_cursor

    def __validate_per_page_param(self, per_page: int) -> Optional[int]:
        """Helper method validating the 'per_page' parameter."""
        if not per_page or per_page <= 0:
            return 25   # set to default
        if 0 < per_page <= self.PER_PAGE_MAX:
            return per_page
        # elif per_page > self.PER_PAGE_MAX:
        return self.PER_PAGE_MAX

    def __validate_pages(self, pages, per_page):
        """Helper method validating the 'pages' parameter."""
        max_pages = self.PAGING_RESULTS_MAX / per_page
        valid_pages = [page for page in pages if 0 < page <= max_pages]
        return valid_pages
