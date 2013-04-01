<div id="securitylevels">
{literal}
<script type="text/javascript">//<![CDATA[
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
//]]></script>
{/literal}

{strip}
{formfeedback error=$errors.security}

<div class="control-group">
	{formlabel label="Security Level Description" for="security-description"}
	{forminput}
		<input type="text" name="security_description" id="security-description" value="{$security.security_description|escape}" maxlength="160" size="50"/>
		{formhelp note="Enter a description for the types of people who will be using this secutiry level such as \"Colleagues\" or \"Family\""}
	{/forminput}
</div>

{if $security.selected}
	{assign var=radioSel value=$security.selected}
{/if}

{if $gContent}
	{assign var=contentName value=$gContent->getContentTypeName()}
{else}
	{assign var=contentName value="item"}
{/if}

<div class="control-group">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="hidden" output="Hidden" name="access_level" checked=$radioSel}
		{formhelp note="This `$contentName` is accessible only by typing the exact URL. It will not appear in listings and search"}
	{/forminput}
</div>

<div class="control-group">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="protected" output="Protected" name="access_level" checked=$radioSel}
	{/forminput}

	{forminput}
		{formlabel label="Question" for="access_question"}
		{forminput}
			<input type="text" size="40" maxlength="256" name="access_question" id="access_question" value="{$security.access_question|default:"What is the password to view this `$contentName`?"|escape}" /><br/>
		{/forminput}
	{/forminput}

	{forminput}
		{formlabel label="Answer" for="access_answer"}
		{forminput}
			<input type="text" size="40" maxlength="128" name="access_answer" id="access_answer" value="{$security.access_answer|escape}" /><br/>
		{/forminput}
		{formhelp note="Users will be prompted to correctly answer the above question before they can view this `$contentName`."}
	{/forminput}
</div>

<div class="control-group">
	{forminput}
		{html_radios onclick="updateControls(this.form)" values="private" output="Private" name="access_level" checked=$radioSel}
		{formhelp note="This `$contentName` is only visible to you."}
	{/forminput}
</div>
{/strip}

</div>
