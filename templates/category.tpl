{extends file="layout.tpl"}
{block name=title}{$category.name}{/block}
{block name=content}
    <h1>{$category.name}</h1>
    {if $category.description}<p>{$category.description}</p>{/if}
    <p>
        Sort:
        {foreach ['date' => 'By date', 'views' => 'By views'] as $key => $label}
            {if $key === $sort}<strong>{$label}</strong>{else}<a href="?sort={$key}">{$label}</a>{/if}
        {/foreach}
    </p>
    <h2>Posts</h2>
    {foreach $posts as $post}
        {include file="post_card.tpl" post=$post}
    {foreachelse}
        <p>No posts yet.</p>
    {/foreach}

    {if $totalPages > 1}
        <nav>
            {for $i = 1 to $totalPages}
                {if $i === $page}<strong>{$i}</strong>{else}<a href="?sort={$sort}&amp;page={$i}">{$i}</a>{/if}
            {/for}
        </nav>
    {/if}
{/block}
