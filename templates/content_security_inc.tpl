{if $contentHash.is_hidden=='y' || $contentHash.is_private=='y' || $contentHash.access_answer}
	{biticon ipackage=liberty iname="security" iexplain="Security" label=TRUE}
{/if}
{if $contentHash.is_hidden=='y'}
	{tr}Hidden{/tr}
{/if}
{if $contentHash.is_private=='y'}
	{tr}Private{/tr}
{/if}
{if $contentHash.access_answer}
	{tr}Password{/tr}
{/if}

