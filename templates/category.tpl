{extends file="layout.tpl"}
{block name=title}{$category.name}{/block}
{block name=content}
    <h1>{$category.name}</h1>
    {if $category.description}<p class="muted">{$category.description}</p>{/if}
    <div class="toolbar">
        <h2>Posts</h2>
        <p class="sort">
            <span class="muted">Sort:</span>
            {foreach ['date' => 'By date', 'views' => 'By views'] as $key => $label}
                {if $key === $sort}<strong>{$label}</strong>{else}<a href="?sort={$key}">{$label}</a>{/if}
            {/foreach}
        </p>
    </div>
    {if $posts}
        <div class="post-grid">
            {foreach $posts as $post}
                {include file="post_card.tpl" post=$post}
            {/foreach}
        </div>
    {else}
        <p class="muted">No posts yet.</p>
    {/if}

    {if $totalPages > 1}
        <nav class="pagination" aria-label="Pages">
            {for $i = 1 to $totalPages}
                {if $i === $page}<strong>{$i}</strong>{else}<a href="?sort={$sort}&amp;page={$i}">{$i}</a>{/if}
            {/for}
        </nav>
    {/if}
{/block}
