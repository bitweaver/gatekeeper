<div class="display gatekeeper">
	<div class="header">
		<h1>{tr}Usage Gatekeeper{/tr}</h1>
	</div>

	<div class="body">
		{legend legend="Your Personal Usage Gatekeeper"}

			{formfeedback error=$errors.disk_gatekeeper}

			<div class="control-group">
				{formlabel label="Your disk gatekeeper"}
				{forminput}
					{formfeedback note="$gatekeeper MB"}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Your current usage"}
				{forminput}
					{formfeedback note="$usage MB <small style="padding-left:10px;">( `$gatekeeperPercent`% )</small>"}
				{/forminput}
			</div>

			<div class="control-group">
				<div style="border:1px solid #ccc;background:#eee;">
					<div style="width:{$gatekeeperPercent}%;background:#f80;text-align:left;color:#000;line-height:30px;"><small>{$gatekeeperPercent}%</small></div>
				</div>
			</div>
		{/legend}
	</div> <!-- end .body -->
</div> <!-- end .fisheye -->
