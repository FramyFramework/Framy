{extends file="projectManager/blank.tpl" }

{block name="content"}
    <h1>Definition</h1>
    <p>The <b>Task</b> is a single task that can be done by one or more people in a certain or indefinite time. </p>

    <p>The <b>Task</b> will consist of some elements like a title, a description, labels or tags, and assigned people or people groups, plus if I'm on it steep then a comment section will be added.</p>

    <hr>

    <h1>Attributes</h1>

    <h2>Title</h2>
    <p>The title is the first thing you see of the <b>Task</b> so it serves as an identifier for users and should be long enough to allow a rough description.</p>

    <h2>Description</h2>
    <p> It is used to describe details and objectives and should be as long as possible. I also want you to be able to format something: markdown makes it possible. The texts will then be saved unformatted and formatted in html before being displayed, if I'm not mistaken, and for editing the unformatted text will also be displayed.</p>


    <h2>Status</h2>
    <p>The traffic jam must be specified, if we also take time tracking with us, whether the <b>Task</b> is processed or what else happens with it. Possibilities could be:</p>

    <ul>
        <li>(0) no status</li>
        <li>(1) Open</li>
        <li>(2) in progress</li>
        <li>(3) Ready for testing</li>
        <li>(4) Finished</li>
        <li>(5) Archived</li>
    </ul>

    <h2>Piority</h2>
    <p>The urgency with which this must be done. The higher the priority the more prominent this task should be.</p>

    <ul>
        <li>(1) Trivial</li>
        <li>(2) Minor</li>
        <li>(3) Major</li>
        <li>(4) Critical</li>
        <li>(5) Blocker</li>
    </ul>

    <hr>
    <h1 class="text-warning">Soon to be added</h1>
    <h2>Tag</h2>
    <p>Creatable per project, they should serve to categorize the <b>Task</b>. The content of the tags, many of which can be entered per <b>Task</b>, should contain a short text of a maximum of two words.</p>

    <h2>Assigned persons or groups </h2>
    <p>These are for your purpose to store the information who should edit the <b>Task</b>. And that they also receive the corresponding notification.</p>

    <h2>Time</h2>
    <p>We can enter a deadline as an example. And possibly also from when we start with it and the expected time expenditure.</p>

    <h2>Related task</h2>
    <p>adding related tasks (with different types: relates to, duplicate of, copy of etc) would be quite convenient</p>
{/block}
