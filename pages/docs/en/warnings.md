# <i class="ph ph-exclamation-triangle text-osiris"></i> Warnings

To avoid known sources of error and problems, OSIRIS carries out a whole series of logic checks in the background. If problems are found, they are either automatically corrected (if possible) or fed back to the user. 

# The "Warnings" page

The "Warnings" page can only be accessed via your own profile page or the "My year" page and only if there are warnings.

There is an info box in your personal profile that can contain warnings as well as useful links and messages. These are displayed quite clearly as a red box:

<div class="demo">
    <h5 class="title font-size-16 mt-0">This is your personal profile page.</h5>
    <div class="btn-group btn-group-lg">
        <span class="btn" data-toggle="tooltip" data-title="Add activity">
            <i class="icon-activity-plus text-osiris ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="My year">
            <i class="ph ph-calendar text-success ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="My activities">
            <i class="icon-activity-user text-primary ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="Edit profile">
            <i class="ph ph-user-list text-muted ph-fw"></i>
        </span>
        <span class="btn" data-toggle="tooltip" data-title="My achievements">
            <i class="ph ph-trophy text-signal ph-fw"></i>
        </span>
    </div>
    <div class="alert danger mt-20">
        <a class="link text-danger" href="#test">
            You have 6 unresolved issues with your activities.
        </a>
    </div>
</div>


Warnings are also displayed on the [My year](my-year) page. They are displayed as notes directly below the displayed activities:

<div class="demo">
    Media<i>Dive</i>: the expert-curated cultivation media database
    <br>
    <small class="text-muted d-block">
    <span class="d-block">Koblitz, J., Halama, P., Spring, S., Thiel, V., Baschien, C., Hahnke, R.L., Pester, M., Overmann, J., Reimer, L.C.</span> <i>Nucleic acids research</i> <i class="icon-open-access text-success" title="Open Access"></i>
    </small>
    <br>
    <b class="text-danger">
        This activity has unresolved warnings. <a class="link">Review</a>
    </b>       
</div>

Clicking on the links shown above will take you to the "Warnings" page. This page is divided into sections, each dealing with one type of problem. Each section is only displayed if corresponding warnings have been found. 


## Types of warnings

In the following I will briefly describe the warnings that OSIRIS currently displays. In each case, I will briefly explain why the warning is displayed, why it is necessary to resolve it and how to resolve it.

### Authorship verification needed
Sometimes other scientists or members of the institute add scientific activities,
in which you were also involved. The system tries to assign them automatically, which is why they appear here 
appear in this list. However, a lot can go wrong. For reporting purposes, for example 
it is not only important that the bibliographic data is correct, the users must also be correctly assigned. 
That's why it's important **if it's even you** (or perhaps someone with a similar name), 
that **your name is spelt correctly** and that you also **belong to the institute**. 

<q>**I have already confirmed this activity**</q>.<br>
That could very well be the case. Because as soon as an activity is edited, even if it's just that a document has been added or a spelling mistake in the title has been corrected, the confirmation of all authors is reset. This is to prevent activities that have already been confirmed from being edited without your knowledge. 
You can also leave an [Editor comment](add-activities#Editor comments) when editing an activity so that your co-authors know exactly what has been changed. This makes the process easier for everyone.


The warning message looks like this:

<div class="demo">
    <div class="row py-10 px-20">
        <div class="col-md-6">
            <p class="mt-0">
                <b class="text-lecture">
                    <span data-toggle="tooltip" data-title="Vortrag"><i
                            class="ph text-lecture ph-chalkboard-teacher"></i></span> Vortrag </b> <br>
                <a class="colorless" href="/osiris/activities/view/650449e74430390609471786">Open-Source CRIS am
                    Beispiel von OSIRIS</a><br><small class="text-muted d-block"><a
                        href="/osiris/profile/juk20">Koblitz,&nbsp;J.</a> und <a
                        href="/osiris/profile/dok21">Koblitz,&nbsp;D.</a><br> Workshop-Reihe "Stärkung von CRIS",
                    Online. 07.09.2023, kurz <a
                        href="/uploads/650449e74430390609471786/OSIRIS_Leibniz-CRIS_Open-Source.pdf" target="_blank"
                        data-toggle="tooltip" data-title="pdf: OSIRIS_Leibniz-CRIS_Open-Source.pdf"
                        class="file-link"><i class="ph ph-file ph-file-pdf"></i></a></small>
            </p>
            <div class="" id="approve-650449e74430390609471786">
                Is this your activity? <br>
                <div class="btn-group mr-10">
                    <button class="btn small text-success" onclick="_approve('650449e74430390609471786', 1)"
                        data-toggle="tooltip" data-title="Ja, und ich war der DSMZ angehörig">
                        <i class="ph ph-check ph-fw"></i>
                    </button>
                    <button class="btn small text-signal" onclick="_approve('650449e74430390609471786', 2)"
                        data-toggle="tooltip" data-title="Ja, aber ich war nicht der DSMZ angehörig">
                        <i class="ph ph-push-pin-slash ph-fw"></i>
                    </button>
                    <button class="btn small text-danger" onclick="_approve('650449e74430390609471786', 3)"
                        data-toggle="tooltip" data-title="Nein, das bin ich nicht">
                        <i class="ph ph-x ph-fw"></i>
                    </button>
                </div>
                <a target="_blank" href="/osiris/activities/view/650449e74430390609471786"
                    class="btn small text-primary" data-toggle="tooltip" data-title="Aktivität ansehen">
                    <i class="ph ph-arrow-fat-line-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <span class="badge secondary float-md-right">27.05.2024</span>
            <h5 class="m-0">
                Edited by Dominic Koblitz </h5>
            <blockquote class="signal">
                <div class="title">
                    Kommentar </div>
                Location has been changed.
            </blockquote>
            <div class="font-weight-bold mt-10">Changes:</div>
            <table class="table simple w-auto small border px-10">
                <tbody>
                    <tr>
                        <td class="pl-0">
                            <span class="key">Ort</span>
                            <span class="del text-gefahr">-</span>
                            <i class="ph ph-arrow-right mx-10"></i>
                            <span class="ins text-success">Online</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

As you can see, there are five buttons under the activity: the first three perform an action, the other two are links to other pages. With all actions, the warning is deleted and thus disappears from the warning page. The buttons do the following:

<i class="ph ph-check ph-fw text-success mr-10"></i> Here you can confirm the activity if this is actually your activity and you were an author affiliated with the institute. You will receive coins for the activity and will be included in the reporting.

<i class="ph ph-push-pin-slash ph-fw text-signal mr-10"></i> This confirms that the activity is yours, but that you were not an author of the institute. The activity will appear in your profile, but you will not receive any coins for it. 

<i class="ph ph-x ph-fw text-danger mr-10"></i> This is not you. This removes the activity from your profile. 

<i class="ph-fw ph ph-regular ph-pencil-simple-line text-primary mr-10"></i> Here you can edit the activity directly if you notice any discrepancies. You don't have to confirm it, if you edit it, you will be ticked off directly.

<i class="ph-fw ph ph-regular ph-arrow-fat-line-right text-primary mr-10"></i> Last but not least, you can view the activity here. There you will also find lots more information and helpful links.


If you have a lot of checks, you can also confirm all of them at once. To do this, click on the <button class="btn btn-sm text-success"><i class="ph ph-check"></i>Confirm all</button> button at the top of the list. However, please only do this if you are sure that they have been added correctly and that they are really **your activities**. You should scroll through the list at least once to see if they look familiar.


### Online ahead of print
[Online ahead of print] means that the publication is already available online, but the actual publication in an issue is still pending. An example of this is the NAR database issue, where publications are already available online in September or October, although the issue is not published until the following January.  

**These publications cannot be included in the reports and are included in OSIRIS in order not to lose sight of them and because they represent work that has already been done. For this reason, however, regular enquiries are made as to whether the publication has now been published. Only then can it be included in the reporting and only then will you receive coins for this achievement.

**The bibliographic data must be checked again and the "Epub" tick must be removed and the publication date is usually also adjusted. It can also happen that the bibliographic data itself changes. Therefore, please check carefully whether all data is correct.


### Student degree
To ensure that final theses always have the correct status and completion date, OSIRIS issues a warning if these theses are still "in progress" even though the completion date is in the past. **Please check whether the work has already been completed **In this case, please enter whether the work has been successfully completed or not and enter the correct completion date. If the thesis is still "in progress", please extend the period by entering a new expected completion date. OSIRIS will then ask you again in due course whether the thesis has been successfully completed.


### Open-end
This warning will appear once a quarter if you are a member of committees, editorial boards or similar. The background is that such activities are given without an end date. This means that they will be added to your timeline every year until you set an end date yourself. Once a quarter, you will be reminded that this activity is still unfinished. Below you can see what a warning message looks like. 

<div class="demo">
    <b>Koblitz, J.</b> Member of a committee, from 18/01/2023 to today.                    
    <div class="alert signal">
        <span class="btn btn-sm text-success">
            <i class="ph ph-check"></i>
            Yes                  
        </span>
        <span class="btn btn-sm text-danger">
            <i class="ph ph-x"></i>
            No (edit)
        </span>
    </div>
</div>

The <span class="btn btn-sm text-success"><i class="ph ph-check"></i> Yes</span> button acts like a snooze button that suppresses the display of the warning message until the next quarter. However, if you select <span class="btn btn-sm text-danger"><i class="ph ph-x"></i> No</span>, you will be redirected to the "Edit activity" page where you can set the end date of the activity.