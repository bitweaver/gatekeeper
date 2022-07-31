<span class="security">
	{if $contentHash.is_hidden=='y' || $contentHash.is_private=='y' || $contentHash.access_answer} {booticon iname="fa-lock" iexplain="Security" label=TRUE} {/if}
	<span class="securityname">
	{if $contentHash.is_hidden=='y'}
		{tr}Hidden{/tr}
	{/if}
	{if $contentHash.is_private=='y'}
		{tr}Private{/tr}
	{/if}
	{if $contentHash.access_answer}
		{tr}Password{/tr}
	{/if}
	</span>
</span>
