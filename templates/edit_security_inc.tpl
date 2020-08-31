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
	if( !bHideControls ) {
		BitBase.showById('protected-prompt'); 
	} else {
		BitBase.hideById('protected-prompt'); 
	}
	form.access_question.disabled = bHideControls;
	form.access_answer.disabled = bHideControls;
}
//]]></script>
{/literal}

{strip}
{formfeedback error=$errors.security}
{legend legend="Edit Security Access"}
<div class="form-group">
	{formlabel label="Security Description" for="security-description"}
	{forminput}
		<input type="text" name="security_description" id="security-description" value="{$security.security_description|escape}" class="form-control" maxlength="160"/>
		{formhelp note="Enter a description for the types of people who will be using this security level such as \"Colleagues\" or \"Family\""}
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

<div class="form-group">
	{forminput label="radio"}
		<input type="radio" onclick="updateControls(this.form)" value="hidden" name="access_level" {if $security.selected=='hidden'}checked{/if}> {tr}Hidden{/tr}
		{formhelp note="This `$contentName` is accessible only by typing the exact URL. It will not appear in listings and search"}
		<input type="radio" onclick="updateControls(this.form)" value="private" name="access_level" {if $security.selected=='private'}checked{/if}> {tr}Private{/tr}
		{formhelp note="This `$contentName` is only visible to you."}
		<input type="radio" onclick="updateControls(this.form)" value="protected" name="access_level" {if $security.selected=='protected'}checked{/if}> {tr}Protected{/tr}
		{formhelp note="Users will be prompted to correctly answer a question before they can view this `$contentName`."}
	{/forminput}

	{forminput label="radio" id="protected-prompt" style="display:none"}
		{formlabel label="Question" for="access_question"}
		{forminput}
			<input type="text" class="form-control" maxlength="256" name="access_question" id="access_question" value="{$security.access_question|default:"What is the password to view this `$contentName`?"|escape}" /><br/>
		{/forminput}
		{formlabel label="Answer" for="access_answer"}
		{forminput}
			<input type="text" class="form-control" maxlength="128" name="access_answer" id="access_answer" value="{$security.access_answer|escape}" /><br/>
		{/forminput}
	{/forminput}
</div>
{/legend}

</div>
{/strip}
