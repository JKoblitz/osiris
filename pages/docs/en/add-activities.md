
# <i class="icon-activity-plus text-osiris"></i> Add activities


## Add an activity using a DOI

The easiest way to add an activity is via a DOI or a Pubmed ID.
    To do this, enter the DOI or Pubmed ID in the search slot:

<div class="demo">
    <div class="form-group">
        <label for="doi">Search using the DOI or Pubmed ID:</label>
        <div class="input-group">
            <input type="text" class="form-control" value="10.1093/nar/gkab961" name="doi"
                id="search-doi">
            <div class="input-group-append">
                <button class="btn secondary" type="submit"><i class="ph ph-search"></i></button>
            </div>
        </div>
    </div>
</div>

OSIRIS searches for the ID in one of three different services (CrossRef, DataCite, Pubmed) and automatically recognises what type of activity it is. Only publications are retrieved from CrossRef and Pubmed. However, other activities can also be queried via DataCite, for example posters, presentations or software packages. To obtain a DOI for such an activity, the files can be uploaded to a provider such as [Zenodo](https://zenodo.org/). It is important that the metadata is stored correctly (e.g. authors).

Once OSIRIS has found the activity in the external service, the form is pre-filled as well as possible. This usually works better with DOIs than with Pubmed, as author information is often insufficiently stored in the latter. In any case, <b>all information should be checked again manually</b> and corrected if necessary. Correct spelling and formatting should also be ensured for the title. If possible, the formatting should correspond to the formatting in the original title.

Some fields cannot be filled in automatically, especially if the publisher has not stored the information in the metadata. Automatically completed fields are therefore highlighted in green, even if the data in the public database was empty (for example, if page numbers are missing). Unmarked fields may have to be added.

If a publication is retrieved whose journal is not yet available in the database, the journal selection window opens. More information on this can be found [below](#edit the journal).

## Add an activity manually

Of course, an activity can also be added manually.

### Select a category

When adding an activity manually, you must first select what type of activity it is. 
The categories to be found have been configured by your organisation. 
Here is an example of how this can look. To add an activity, click on the button.

<div class="demo">
    <div class="select-btns" id="select-btns">
        <button class="btn btn-select text-publication" id="publication-btn"><i class="ph text-publication ph-book-bookmark"></i>Publication</button>
        <button class="btn btn-select text-poster" id="poster-btn"><i class="ph text-poster ph-presentation-chart"></i>Poster</button>
        <button class="btn btn-select text-lecture" id="lecture-btn"><i class="ph text-lecture ph-chalkboard-teacher"></i>Lectures</button>
        <button class="btn btn-select text-review" id="review-btn"><i class="ph text-review ph-article"></i>Reviews &amp; Editorials</button>
        <button class="btn btn-select text-teaching" id="teaching-btn"><i class="ph text-teaching ph-chalkboard-simple"></i>Teaching</button>
        <button class="btn btn-select text-students" id="students-btn"><i class="ph text-students ph-student"></i>Students &amp; guests</button>
        <button class="btn btn-select text-software" id="software-btn"><i class="ph text-software ph-desktop-tower"></i>Software &amp; Data</button>
        <button class="btn btn-select text-misc" id="misc-btn"><i class="ph text-misc ph-shapes"></i>Misc</button>
    </div>
</div>

Once an activity has been selected, a form opens with all the data fields that may be relevant for the activity. Required data fields are marked with <span class="text-danger">*</span> and cannot be left blank.

For some activities, there are subcategories that have an influence on the data fields. For example, you can select the publication type for <span class="text-publication">publications</span>, e.g. journal article, book or dissertation. The subcategories are controlled with buttons similar to those shown above and can only be selected after the main category has been selected. 


### Use examples

At the top of the form there is a small button that can be used to switch the <button class="btn secondary btn-sm">examples</button>. When examples are activated, a formatted text appears above the form. This text depends on the selected activity category (and possibly subcategory). Here is an example of a journal article:

<div class="demo">
    <span class="element-author" data-element="Author(s)">Spring, S., Rohde, M., Bunk, B., Spröer, C., Will, S. E. and Neumann-Schaal, M. </span>
    (<span class="element-time" data-element="year">2022</span>)
    <span class="element-title" data-element="Titel">New insights into the energy metabolism and taxonomy of Deferribacteres revealed by the characterisation of a new isolate from a hypersaline microbial mat</span>.
    <span class="element-cat" data-element="Journal">Environmental microbiology </span>
    <span data-element="Issue, Volume, Pages">24(5):2543-2575</span>.
    DOI: http://dx.doi.org/<span class="element-link" data-element="DOI">10.1111/1462-2920.15999</span>
</div>


### Formatting the title

The title can be edited and formatted using a rich text editor. It is important that the spelling and formatting match the original work. The following formatting is supported: italics, underlining, superscript and subscript. There are also shortcuts for the most common Greek special characters. Here you can play around with the editor:

<div class="demo">
    <label for="title" class="required element-title">
        <span style="">Title</span>
    </label>
    <div class="form-group" id="title-editor"></div>
    <input type="text" class="form-control hidden" name="values[title]" id="title" required="" value="">  
    <script>
        initQuill(document.getElementById('title-editor'));
    </script> 
</div>

### Edit authors

A simple author editor is available to edit the author list. Here is a working editor to try out:

<div class="demo">
    <label for="author" class="element-author">
        Author(s) (in correct order, format: surname, first name)
    </label>
    <div class="author-widget" id="author-widget">
        <div class="author-list p-10" id="author-list">
            <div class="author author-aoi ui-sortable-handle" ondblclick="toggleAffiliation(this);">
                Koblitz, Julia<input type="hidden" name="values[authors][]" value="Koblitz;Julia;1">
                <a onclick="removeAuthor(event, this);">×</a>
            </div>
        </div>
        <div class="footer">
            <div class="input-group small d-inline-flex w-auto">
                <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event);" id="add-author" list="scientist-list">
                <div class="input-group-append">
                    <button class="btn secondary h-full" type="button" onclick="addAuthor(event);">
                        <i class="ph ph-plus"></i>
                    </button>
                </div>
            </div>
            <div class="ml-auto" data-visible="article,preprint" id="author-numbers">
                <label for="first-authors">Number of first authors:</label>
                <input type="number" name="values[first_authors]" id="first-authors" value="1" class="form-control form-control-sm w-50 d-inline-block mr-10" autocomplete="off">
                <label for="last-authors">Last authors:</label>
                <input type="number" name="values[last_authors]" id="last-authors" value="1" class="form-control form-control-sm w-50 d-inline-block" autocomplete="off">
            </div>
        </div>
    </div>
</div>


To add an **author**, you must enter it in the field labelled "Add author ...". Please use the format <code>last name, first name</code> so that OSIRIS can assign the authors correctly. You can confirm by pressing <kbd>Enter</kbd> or the <i class="ph ph-plus"></i> button. Authors from your institute are suggested in a list. An author from the list is automatically assigned to the institute.

To remove an **author**, you must click on the &times; after their name.

To change the **author order**, you can take an author and drag & drop him/her to the desired position.

To mark an **author as belonging to the institute**, you can simply double-click on it. The name will then be highlighted in blue and the abbreviation of the institute (or an *) will appear in front of it. It is important for reporting that all authors are marked according to their affiliation! If authors are employees of your institute but were not at the time of the activity, they must not be marked accordingly!

Prescribed? An author is not correctly assigned to a user? After you have added the data record, you can edit the author list in detail again. Read the [following section](#the-author-editor).

### The author editor

Once an activity has been added, a detailed author editor is available. To do this, click on *Edit* under Authors on the overview page of the activity.

For edited books, the same is also available for editors.

A table now opens in the author editor with all the details about the authors of an activity. This table can look like this

<div class="demo">
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>Last name</th>
                <th>First name</th>
                <th>Position</th>
                <th>*</th>
                <th>Username</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="author-detail-editor" class="ui-sortable">
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[0][last]" type="text" class="form-control" value="Becker">
                </td>
                <td>
                    <input name="authors[0][first]" type="text" class="form-control" value="Patrick">
                </td>
                <td>
                    <select name="authors[0][position]" class="form-control">
                        <option value="first" selected="">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-0" name="authors[0][aoi]" value="1">
                        <label for="checkbox-0" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[0][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[0][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[1][last]" type="text" class="form-control" value="Kirstein">
                </td>
                <td>
                    <input name="authors[1][first]" type="text" class="form-control" value="Sarah">
                </td>
                <td>
                    <select name="authors[1][position]" class="form-control">
                        <option value="first" selected="">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-1" name="authors[1][aoi]" value="1">
                        <label for="checkbox-1" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[1][user]" type="text" class="form-control" list="user-list" value="sak20">
                    <input name="authors[1][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[3][last]" type="text" class="form-control" value="Koblitz">
                </td>
                <td>
                    <input name="authors[3][first]" type="text" class="form-control" value="Julia">
                </td>
                <td>
                    <select name="authors[3][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle" selected="">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-3" name="authors[3][aoi]" value="1" checked="">
                        <label for="checkbox-3" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[3][user]" type="text" class="form-control" list="user-list" value="juk20">
                    <input name="authors[3][approved]" type="hidden" class="form-control" value="1">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[4][last]" type="text" class="form-control" value="Buschen">
                </td>
                <td>
                    <input name="authors[4][first]" type="text" class="form-control" value="Ramona">
                </td>
                <td>
                    <select name="authors[4][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle" selected="">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-4" name="authors[4][aoi]" value="1">
                        <label for="checkbox-4" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[4][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[4][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[20][last]" type="text" class="form-control" value="Neumann-Schaal">
                </td>
                <td>
                    <input name="authors[20][first]" type="text" class="form-control" value="Meina">
                </td>
                <td>
                    <select name="authors[20][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding" selected="">corresponding</option>
                        <option value="last">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-20" name="authors[20][aoi]" value="1" checked="">
                        <label for="checkbox-20" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[20][user]" type="text" class="form-control" list="user-list" value="men17">
                    <input name="authors[20][approved]" type="hidden" class="form-control" value="1">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
            <tr>
                <td>
                    <i class="ph ph-dots-six-vertical text-muted handle ui-sortable-handle"></i>
                </td>
                <td>
                    <input name="authors[21][last]" type="text" class="form-control" value="Rabus">
                </td>
                <td>
                    <input name="authors[21][first]" type="text" class="form-control" value="Ralf">
                </td>
                <td>
                    <select name="authors[21][position]" class="form-control">
                        <option value="first">first</option>
                        <option value="middle">middle</option>
                        <option value="corresponding">corresponding</option>
                        <option value="last" selected="">last</option>
                    </select>
                </td>
                <td>
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkbox-21" name="authors[21][aoi]" value="1">
                        <label for="checkbox-21" class="blank"></label>
                    </div>
                </td>
                <td>
                    <input name="authors[21][user]" type="text" class="form-control" list="user-list" value="">
                    <input name="authors[21][approved]" type="hidden" class="form-control" value="">
                </td>
                <td>
                    <button class="btn" type="button" onclick="$(this).closest('tr').remove()"><i
                            class="ph ph-trash"></i></button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr id="last-row">
                <td></td>
                <td colspan="6">
                    <button class="btn" type="button" onclick="addAuthorRow()"><i class="ph ph-plus"></i> Author
                        add</button>
                </td>
            </tr>
        </tfoot>
    </table>
    <script>
        var counter = 10;
        function addAuthorRow() {
            counter++;
            var tr = $('<tr>')
            tr.append('<td><i class="ph ph-dots-six-vertical text-muted handle"></i></td>')
            tr.append('<td><input name="authors[' + counter + '][last]" type="text" class="form-control"></td>')
            tr.append('<td><input name="authors[' + counter + '][first]" type="text" class="form-control"></td>')
            tr.append('<td><select name="authors[' + counter + '][position]" class="form-control"><option value="first">first</option><option value="middle" selected>middle</option><option value="corresponding">corresponding</option><option value="last">last</option></select></td>')
            tr.append('<td><div class="custom-checkbox"><input type="checkbox" id="checkbox-' + counter + '" name="authors[' + counter + '][aoi]" value="1"><label for="checkbox-' + counter + '" class="blank"></label></div></td>')
            tr.append('<td> <input name="authors[' + counter + '][user]" type="text" class="form-control" list="user-list"></td>')
            var btn = $('<button class="btn" type="button">').html('<i class="ph ph-trash"></i>').on('click', function() {
                $(this).closest('tr').remove();
            });
            tr.append($('<td>').append(btn))
            $('#author-detail-editor').append(tr)
        }
        $(document).ready(function() {
            $('#author-detail-editor').sortable({
                handle: ".handle",
            });
        })
    </script>
</div>


Right at the beginning of each line there is a so-called handle (<i class="ph ph-dots-six-vertical text-muted"></i>), which can be used to change the order of the authors by drag & drop. This is followed by the author's first name and surname. The surname is a mandatory field, the first name can be omitted (e.g. for consortia). 

The position of the author can be selected from a drop-down menu. In this way, several authors can be defined as first or last authors (in the case of shared authorship). A corresponding author can also be defined here if they are neither the first nor the last author. 

A checkbox follows to indicate whether the author was a member of your institute at the time of publication or not. In the next field, you can enter the user account to which this author is linked. In the example shown above, you can see that you do not have to be a member of the institute to link the activities to the user profile. The second first author is assigned to a user account, but was not affiliated with the institute at the time of the work. The activity is linked to her account and is displayed in her profile, but she is not marked as an affiliated author in the report and does not receive any coins for the activity.

Last but not least, there is a button (<i class="ph ph-trash"></i>) that can be used to delete an author completely.

At the bottom of the table, additional authors can be added to the table using the <span class="btn btn-sm">+ Add author</span> button. New rows with authors have the same functions as shown above.


### Edit the journal

Journals are relevant for <span class="text-publication">journal articles</span> and <span class="text-review">reviews &amp; editorials</span>. 

For reasons of standardisation, a journal cannot be entered as a free text field. Instead, the following module can be found:

<div class="demo">
    <div class="data-module col-12" data-module="journal">
        <a href="/osiris/docs/add-activities#edit-journal" target="_blank" class="required float-right">
            <i class="ph ph-question"></i> Help </a>
        <label for="journal" class="element-cat required">Journal</label>
        <a href="#journal-select" id="journal-field" class="module">
            <span class="float-right text-secondary"><i class="ph ph-edit"></i></span>
            <div id="selected-journal">
                <span class="title">No journal selected</span>
                                        </div>
            <input type="hidden" class="form-control hidden" name="values[journal]" value="" id="journal" list="journal-list" required="" readonly="">
            <input type="hidden" class="form-control hidden" name="values[journal_id]" value="" id="journal_id" required="" readonly="">
        </a>
    </div>
</div>

To edit the journal, simply click in this field. This opens the following window in which you can search for a journal in OSIRIS by name or (preferably) ISSN. 
In the following example, there are two journals in OSIRIS that match the search term:

<div class="demo" id="journal-select">
    <div class="modal-content">
        <label for="journal-search">Search journal by name or ISSN</label>
        <div class="input-group">
            <input type="text" class="form-control is-valid" list="journal-list" id="journal-search" value="Nucleic acid" data-value="Nucleic acid">
            <div class="input-group-append">
                <button class="btn"><i class="ph ph-search"></i></button>
            </div>
        </div>
        <table class="table table-simple">
            <tbody id="journal-suggest"><tr><td class="w-50"><button class="btn" title="select"><i class="ph ph-check"></i></button></td><td><h5 class="m-0">Nucleic acid therapeutics</h5><span class="float-right">Mary Ann Liebert, Inc. </span><span class="text-muted">2159-3345, 2159-3337, 1545-4576</span></td></tr><tr><td class="w-50"><button class="btn" title="select"><i class="ph ph-check"></i></button></td><td><h5 class="m-0">Nucleic acids research</h5><span class="float-right">Oxford University Press</span><span class="text-muted">1362-4962, 0305-1048</span></td></tr><tr><td><button class="btn">Search the NLM catalogue</button></td></tr></tbody>
        </table>
    </div>
<div class="text-muted text-center mt-10">This window is not functional for technical reasons.</div>
</div>

A journal can be selected by clicking on the <span class="btn btn-sm"><i class="ph ph-check"></i></span> tick. The window closes automatically and the module is filled in. It then looks like this:

<div class="demo">
    <div class="data-module col-12" data-module="journal">
        <a href="/osiris/docs/add-activities#edit-journal" target="_blank" class="required float-right">
            <i class="ph ph-question"></i> Help </a>
        <label for="journal" class="element-cat required">Journal</label>
        <a href="#journal-select" id="journal-field" class="module">
            <span class="float-right text-secondary"><i class="ph ph-edit"></i></span>
            <div id="selected-journal"><h5 class="m-0">Nucleic acids research</h5><span class="float-right">Oxford University Press</span><span class="text-muted">ISSN: 1362-4962, 0305-1048</span></div>
            <input type="hidden" class="form-control hidden" name="values[journal]" value="Nucleic acids research" id="journal" list="journal-list" required="" readonly="">
            <input type="hidden" class="form-control hidden" name="values[journal_id]" value="6364d154f7323cdc82531a01" id="journal_id" required="" readonly="">
        </a>
    </div>
</div>

If the journal you are looking for is not found in OSIRIS, you can start an advanced search by clicking on <span class="btn btn-sm">Search in NLM catalogue</span>. All journals indexed in the NLM catalogue are searched and suggested. This is also confirmed by ticking the box. If the selected journal already exists in OSIRIS (possibly under a slightly different name), the existing journal will be selected. The corresponding comparison takes place via the ISSN and you will be informed of this by a pop-up. If the journal is still unknown, OSIRIS will request all information about it from external services. **This process may take a moment**, so please wait until the module has been completed.

## Advanced functions

### Store documents
Once an activity has been added, documents can be stored. Ideally, publications, posters and presentations are supplemented with a PDF attachment. To do this, click on the **Upload file** button on the details page of an activity (where you land after creating it).

At the top of the following page you will find an overview of the activity you are currently working on. This is followed by a list of any existing files, which can also be downloaded (<i class="ph ph-download text-secondary"></i>) or deleted (<i class="ph ph-trash text-danger"></i>) at this point. 

Further down is a form that can be used to upload new documents. These may not exceed a maximum size of 16 MB and should be in a standard format if possible. Good examples are PDF (preferred), PPTX, XLSX, DOCX. 

Please note that PPTX and DOCX can be easily converted to PDF using the export function in Microsoft Office (File > Export > Create PDF/XPS document). **We recommend uploading PDF documents** for the following reasons:
- PDF can often be opened directly in the browser
- PDF is displayed in the same way on all devices (regardless of operating system, linked images and installed fonts)
- PDF often has a smaller file size than other formats due to compression
- PDF documents are not so easy to edit accidentally
- PDF is less susceptible to virus attacks



### Author notes

It is possible to leave your own notes on an activity in OSIRIS. 
This note is only visible to the authors of the activity and the admins (a.k.a Julia and Controlling). 
To leave a note, the following box must be expanded when adding or editing an activity (at the bottom before the button for submitting the form):

<div class="demo">
    <a onclick="$(this).next().toggleClass('hidden')">
        <label onclick="$(this).next().toggleClass('hidden')" for="comment" class="cursor-pointer">
            <i class="ph ph-plus"></i> Note (only visible to authors and admins)
        </label>
    </a>
    <textarea name="values[comment]" id="comment" cols="30" rows="2" class="form-control hidden"></textarea>

</div>

Examples of notes are the title of publications that have been reviewed, comments on the expected publication date of online ahead of print articles, etc.

### Editor comments

If you change an activity, all your co-authors will be notified and may need to reconfirm that they are authors and have reviewed the activity. To make this process easier for them, you can tell them exactly what you have changed. There is an editing area in the form for this purpose, which only becomes visible when you edit an existing activity. In the following example, only the spelling of the title has been changed:

<div class="demo">
    <div class="alert signal p-10 mb-10">
        <div class="title">
            Editing area 
        </div>
        <label for="editor-comment">Editor comment (tell your co-authors what you have changed)</label>
        <textarea name="values[editor-comment]" id="editor-comment" cols="30" rows="2" class="form-control">Marked as Open Access.</textarea>
        <div class="mt-10">
            <div class="custom-checkbox" id="minor-div">
                <input type="checkbox" id="minor" value="1" name="minor" data-value="1">
                <label for="minor">Changes are minimal and co-authors do not need to be notified.</label>
            </div>
            <small class="text-muted">
                Please note that changes to the authors are ignored if this checkmark is set.
            </small>
        </div>
    </div>
</div>

This is how this information now looks on the review page of your co-authors:
<div class="demo">
    <div class="row py-10 px-20">
        <div class="col-md-6">
            <p class="mt-0">
                <b class="text-lecture">
                    <span data-toggle="tooltip" data-title="Lecture"><i
                            class="ph text-lecture ph-chalkboard-teacher"></i></span> Lecture </b> <br> <br
                <a class="colourless" href="/osiris/activities/view/650449e74430390609471786">Open-Source CRIS using the example of
                    example of OSIRIS</a><br><small class="text-muted d-block"><a
                        href="/osiris/profile/juk20">Koblitz,&nbsp;J.</a> and <a
                        href="/osiris/profile/dok21">Koblitz,&nbsp;D.</a><br> Workshop series "Strengthening CRIS",
                    Online. 07.09.2023, short <a
                        href="/uploads/650449e74430390609471786/OSIRIS_Leibniz-CRIS_Open-Source.pdf" target="_blank"
                        data-toggle="tooltip" data-title="pdf: OSIRIS_Leibniz-CRIS_Open-Source.pdf"
                        class="file-link"><i class="ph ph-file ph-file-pdf"></i></a></small>
            </p>
            <div class="" id="approve-650449e74430390609471786">
                Is this your activity? <br>
                <div class="btn-group mr-10">
                    <button class="btn small text-success" onclick="_approve('650449e74430390609471786', 1)"
                        data-toggle="tooltip" data-title="Yes, and I belonged to the DSMZ">
                        <i class="ph ph-check ph-fw"></i>
                    </button>
                    <button class="btn small text-signal" onclick="_approve('650449e74430390609471786', 2)"
                        data-toggle="tooltip" data-title="Yes, but I was not a member of the DSMZ">
                        <i class="ph ph-push-pin-slash ph-fw"></i>
                    </button>
                    <button class="btn small text-danger" onclick="_approve('650449e74430390609471786', 3)"
                        data-toggle="tooltip" data-title="No, that's not me">
                        <i class="ph ph-x ph-fw"></i>
                    </button>
                </div>
                <a target="_blank" href="/osiris/activities/view/650449e74430390609471786"
                    class="btn small text-secondary" data-toggle="tooltip" data-title="View activity">
                    <i class="ph ph-arrow-fat-line-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <span class="badge primary float-md-right">27/05/2024</span>
            <h5 class="m-0">
                Edited by Dominic Koblitz </h5>
            <blockquote class="signal">
                <div class="title">
                    Comment </div>
                Location has been updated.
            </blockquote>
            <div class="font-weight-bold mt-10">Changes to the activity:</div>
            <table class="table simple w-auto small border px-10">
                <tbody>
                    <tr>
                        <td class="pl-0">
                            <span class="key">Location</span>
                            <span class="del text-danger">-</span>
                            <i class="ph ph-arrow-right mx-10"></i>
                            <span class="ins text-success">Online</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


If you are of the opinion that your changes are too minimal to require a check by the co-authors, you can also tick the corresponding box. In most cases, however, it is good practice to inform your co-authors of any changes.


## Copy an activity

It is currently possible to create a copy of the following activities: Poster, Lecture, Review, Misc, Students.
The idea is that you repeat an activity, making only minor changes, for example to the date.

An example:

I give a lecture at a conference. At a mini-symposium a few weeks later, I give exactly the same lecture again. There is a separate lecture category in OSIRIS for this, because in addition to short and long lectures, there are also "Repetitions". So I go into OSIRIS and select the lecture at the conference. There I find the following button:

<div class="demo">
    <div class="btn-group">
    <span class="btn primary">
        <i class="ph ph-regular ph-pencil-simple-line"></i>
        Edit            
    </span>
    <span class="btn primary active">
        <i class="ph ph-copy"></i>
        Copy            
    </span>
    </div>
</div>

I click on this and a form is displayed in which all the data for my presentation has already been pre-filled. OSIRIS even recognises that I am copying a lecture and automatically selects the "Repetition" category. I now only have to adjust the date, the conference and the location. The title and authors are correct. 

It is even easier with reviews, for example, as I only have to change the date if I have worked for the same journal several times. And if several guests come to the same event, I can copy the activity and just adjust the names.