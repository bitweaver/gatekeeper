	{if $gContent->mInfo.is_hidden=='y' || $gContent->mInfo.is_private=='y' || $gContent->mInfo.access_answer}
		{biticon ipackage=liberty iname="security" iexplain="Security" label=TRUE}
	{/if}
	{if $gContent->mInfo.is_hidden=='y'}
		{tr}Hidden{/tr}
	{/if}
	{if $gContent->mInfo.is_private=='y'}
		{tr}Private{/tr}
	{/if}
	{if $gContent->mInfo.access_answer}
		{tr}Password{/tr}
	{/if}
