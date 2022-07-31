{if !$serviceHash && $gContent->mInfo}
	{assign var=serviceHash value=$gContent->mInfo}
{/if}

{if $serviceHash.is_hidden=='y'}
	{assign var=securityLabel value="{tr}Hidden{/tr}"}
{/if}
{if $serviceHash.is_private=='y'}
	{assign var=securityLabel value="{tr}Private{/tr}"}
{/if}
{if $serviceHash.access_answer}
	{assign var=securityLabel value="{tr}Password Required{/tr}"}
{/if}
{if $securityLabel}
	{booticon iname="fa-lock" iexplain=$securityLabel_text}
{/if}
