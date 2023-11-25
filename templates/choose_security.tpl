{strip}
{literal}
<script>/*<![CDATA[*/
function toggleSecuirtyEdit(form) {
	ele = document.getElementById("securityselect");
	editEle = document.getElementById("securityedit");
	if( ele.value=="new" ) {
		BitBase.showById( "securityedit" );
	} else {
		BitBase.hideById( "securityedit" );
 	}
}
/*]]>*/</script>
{/literal}
{if !$serviceHash}
	{assign var=serviceHash value=$gContent->mInfo}
{/if}
<div class="form-group">
	{formlabel label="Security Level"}
	{forminput}
		<select class="form-control" name="security_id" id="securityselect" onchange="toggleSecuirtyEdit(this)">
			<option value="public">{tr}Access based on User Information setting{/tr} ({tr}Currently{/tr} {if $gBitUser->isUserPrivate($gContent->getField('user_id'))}{tr}Private{/tr}{else}{tr}Public{/tr}{/if})</option>
				{foreach from=$securities key=secId item=sec}
					<option value="{$secId}" {if $secId==$smarty.request.security_id || ($secId==$serviceHash.security_id && !$secId==$smarty.request.security_id) }selected="selected"{/if}>{$sec.security_description}</option>
				{/foreach}
			<option value="new">{tr}Create New Security Level{/tr}...</option>
		</select>
		<div class="help-block"><a href="{$smarty.const.USERS_PKG_URL}preferences.php">{tr}Change User Information Access{/tr}</a> {if $securities}{tr}or{/tr} <a href="{$smarty.const.GATEKEEPER_PKG_URL}">Edit Security Levels</a></div>{/if}
	{/forminput}
</div>

{formfeedback error=$gatekeeperErrors warning=$errors.security}
<div id="securityedit">
	{include file="bitpackage:gatekeeper/edit_security_inc.tpl"}
</div>

<script>
	BitBase.hideById('securityedit');
</script>
{/strip}
