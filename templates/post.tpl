{extends file="layout.tpl"}
{block name=title}{$post.title}{/block}
{block name=content}
    <h1>{$post.title}</h1>
    {foreach $categories as $category}
        <p><a href="/category/{$category.id}">{$category.name}</a></p>
    {/foreach}
    <article>
        <img src="{$post.image}" alt="{$post.title}">
        <p>{$post.description}</p>
        {foreach $post.body|split:"\n\n" as $paragraph}<p>{$paragraph}</p>{/foreach}
        <p>{$post.views} views</p>
        <time
        datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
    </article>
    {if $relatedPosts}
        <section>
            <h2>Related posts</h2>
            {foreach $relatedPosts as $relatedPost}
                {include file="post_card.tpl" post=$relatedPost}
            {/foreach}
        </section>
    {/if}
{/block}
