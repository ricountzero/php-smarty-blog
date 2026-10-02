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
    {foreach $posts as $post}
        <article>
            <time
                datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
            <br>
            <img src="{$post.image}" alt="{$post.title}">
            <h2><a href="/post/{$post.id}">{$post.title}</a></h2>
            <p>{$post.description}</p>
            <p><span>{$post.views} views</span></p>
        </article>
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
