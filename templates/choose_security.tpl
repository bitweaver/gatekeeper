{literal}
<script type="text/javascript"><!--
function toggleSecuirtyEdit(form) {
	ele = document.getElementById("securityselect");
	setBlockDisplay( "securityedit", ele.value=="new" );
}
--></script> 
{/literal}

{strip}
<div class="row">
	{formlabel label="Security Level"}
	{forminput}
		<select name="security_id" id="securityselect" onchange="toggleSecuirtyEdit(this)">
			<option value="public">~~ {tr}Publically Visible{/tr} ~~</option>
				{foreach from=$securities key=secId item=sec}
					<option value={$secId} {if $secId==$gContent->mInfo.security_id}selected="selected"{/if}>{$sec.security_description}</option>
				{/foreach}
			<option value="new">{tr}Create New Security Level{/tr}...</option>
		</select>
		{if $securities}
			<a href="{$gBitLoc.GATEKEEPER_PKG_URL}">Edit Security Levels</a>
		{/if}
	{/forminput}
</div>

{formfeedback error=$gatekeeperErrors warning=$errors.security}
<span id="securityedit">
	{include file="bitpackage:gatekeeper/edit_security_inc.tpl}
</span>
{/strip}

{literal}
<script type="text/javascript"><!--
toggleBlockDisplay('securityedit');
--></script>
{/literal}
