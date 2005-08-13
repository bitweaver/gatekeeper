{literal}
<!-- ATS - CDATA element enclosure removed b/c it causes javascript parser errors -->
<script type="text/javascript"><!--
function updateControls(form) {
        bHideControls = true;
        for ( i = 0; i < form.access_level.length; i++) {
                if (form.access_level[i].value == "protected" && form.access_level[i].checked == true) {
                        bHideControls = false;
                }
        }
        form.access_question.disabled = bHideControls;
        form.access_answer.disabled = bHideControls;
}
--></script>
{/literal}
{formfeedback error=$errors.security}

<div class="row">
	{formlabel label="Security Level Description" for="security-description"}
	{forminput}
		<input type="text" name="security_description" id="security-description" value="{$security.security_description}" maxlength="160" size="50"/>
	{formhelp note="Enter a description for the types of people who will be using this secutiry level such as \"Colleagues\" or \"Family\""}
	{/forminput}
</div>
{if $security.selected}
{assign var=radioSel value=$security.selected}
{/if}
<div class="row">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="hidden" output="Hidden" name="access_level" checked=$radioSel}
		{formhelp note="This `$gContent->mType.content_description` is accessible only by typing the exact URL. It will not appear in listings and search"}
	{/forminput}
</div>
<div class="row">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="protected" output="Protected" name="access_level" checked=$radioSel}
	{/forminput}
	{forminput}
		{formlabel label="Question" for="access_question"}
		{forminput}
			<input type="text" size="50" maxlength="256" name="access_question" id="access_question" value="{$security.access_question|default:"What is the password to view this `$gContent->mType.content_description`?"}" /><br/>
		{/forminput}
	{/forminput}
	{forminput}
		{formlabel label="Answer" for="access_answer"}
		{forminput}
			<input type="text" size="50" maxlength="128" name="access_answer" id="access_answer" value="{$security.access_answer}" /><br/>
		{/forminput}
		{formhelp note="Users will be prompted to correctly answer the above question before they can view this `$gContent->mType.content_description`"}
	{/forminput}
</div>
<div class="row">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="private" output="Private" name="access_level" checked=$radioSel}
		{formhelp note="This `$gContent->mType.content_description` is only visible to you."}
	{/forminput}
</div>

{literal}
<script type="text/javascript"><!--
// can someone smarter than me can figure out how to get the current form when running inline js ? xoxo spiderr
//updateControls(this);
--></script>
{/literal}
