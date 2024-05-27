
# <i class="ph ph-magnifying-glass-plus text-primary"></i> The advanced activity search

The **Advanced Search** page allows you to search activities using advanced filters, aggregate data by specific fields, save your queries and view the results in a detailed table.


## Apply filters

To start your search, you can apply different filters.

1. **Builder mode**:
    - The default view shows the "Builder" where you can add rules to filter activities.
    - Click on "Add rule" to create a new filter.
    - Select a category (e.g. "Category", "Type") and set the condition (e.g. "equals", "contains").

    **Example**:
    - Add a rule: "Category equals publication" to find all activities that are publications.

2. **Expert mode**:
    - Switch to expert mode by clicking on "Expert mode".
    - In this mode you can manually enter complex queries to MongoDB.
    - Write your query in the text field provided.

    **Example**:
    - Enter: `{"type": "publication"}` to achieve the same as above.



## Aggregate results

You can aggregate your search results to see summarised data.

1. select an aggregation option from the drop-down menu
2. click on "Apply" to see the aggregated results.

**Example**:
- Select "Year" to aggregate the results by year. You will get a list of years with the number of all publications.



## View results

The results of your search are displayed in a table.

- The columns include "Type", "Result", "Number", "Link".
- Type and link are only relevant without aggregation, number only with.
- Use the buttons above the table to copy the data, export it to Excel or download it as a CSV.
- In the Excel table you also have the columns "Year", "Print", "Subtype", "Title" and "Authors".



## Save and manage queries

You can save your queries for future use.

1. **Save a query**:
    - Enter a name for your query in the "Save query" area.
    - Click on "Save query".

    **Example**:
    - Name your query "My publication search" and save it.

2. **Load a saved query**:
    - Click on the name of a saved query in the "My queries" section.
    - The filters and rules are applied automatically.

3. **Delete a saved query**:
    - Click on the red "X" next to the saved query you want to delete.



## Additional tips

- **Delete filter**: To start a new search, delete all filters by refreshing the page or manually removing each rule.
- **How to deal with errors**: If an error occurs, check the format of your rules or try reloading the page.
- **Multilingual support**: The user interface supports both English and German.

We hope this guide will help you get the most out of the advanced activity search!

Good luck with your search!
