{strip}
<div class="body">
	{formfeedback error=$failedLogin}
	<h2>{$gContent->getTitle()}</h2>

	{tr}This {$gContent->getContentDescription()|strtolower} has been password protected by the owner.{/tr}

	{form legend="Authenticate" action=$smarty.server.REQUEST_URI}
	<input type="hidden" name="content_id" value="{$gContent->mContentId}" />

	<h3>{$gContent->mInfo.access_question}:</h3>
	<div class="row">
		{formlabel label="Answer" for="try-access-answer"}
		{forminput}
			<input type="text" name="try_access_answer" id="try-access-answer" value="" maxlength="128" size="50"/>
			<input type="submit" name="submit_answer" value="Submit Answer"/>
		{/forminput}
	</div>
	{/form}
</div><!-- end .body -->
{/strip}
