{strip}
<div class="listing gatekeeper">
	{if $securities}
		<div class="header">
			<h1>{tr}Security Lists{/tr}</h1>
		</div>

		{include file="bitpackage:users/my_bitweaver_bar.tpl"}

		<div class="body">
			{smartlink ititle="Add Security Level" ipackage="gatekeeper" ifile="edit.php" newsecurity=1}

			<table class="table data">
				<caption>{tr}Security Levels{/tr}</caption>
				<tr>
					<th>{tr}Security Level Description{/tr}</th>
					<th>{tr}Type{/tr}</th>
					<th>{tr}Action{/tr}</th>
				</tr>
				{foreach from=$securities key=secId item=sec}
					<tr class="{cycle values="odd,even"}">
						<td>{$sec.security_description}</td>
						<td>
							{tr}
							{if $sec.is_hidden}Hidden {/if}
							{if $sec.is_private}Private {/if}
							{if $sec.access_question}Password{/if}
							{/tr}
						</td>
						<td class="actionicon">
							<a title="{tr}Edit{/tr}" href="{$smarty.const.GATEKEEPER_PKG_URL}edit.php?security_id={$secId}">{booticon iname="icon-file"  ipackage="icons"  iexplain="Edit"}</a>
							<a title="{tr}Delete{/tr}" href="{$smarty.const.GATEKEEPER_PKG_URL}edit.php?security_id={$secId}&deletesecurity=1">{booticon iname="icon-trash" ipackage="icons" iexplain="Delete"}</a>
						</td>
					</tr>
				{/foreach}
			</table>
		</div>	<!-- end .body -->
	{else}
		{include file="bitpackage:gatekeeper/edit_security.tpl"}
	{/if}
</div>	<!-- end .fisheye -->
{/strip}
