<div class="edit gatekeeper">
	<div class="header">
		<h1>{if $security.security_id}{tr}Edit Security List{/tr} {$security.security_description} {else}{tr}Create Security List{/tr}{/if}</h1>
	</div>

	{include file="bitpackage:users/my_bitweaver_bar.tpl"}

	<div class="body">
		{form id="editSecurityForm" ipackage="gatekeeper" ifile="edit.php" legend="Edit Security"}
			<input type="hidden" name="security_id" value="{$security.security_id}"/>

			{include file="bitpackage:gatekeeper/edit_security_inc.tpl" security=$security}

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="cancelsecurity" value="Cancel"/>&nbsp;
				<input type="submit" class="btn btn-default" name="savesecurity" value="Save Security List"/>
			</div>
		{/form}
	</div> <!-- end .body -->
</div> <!-- end .gatekeeper -->
