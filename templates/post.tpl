{extends file="layout.tpl"}
{block name=title}{$post.title}{/block}
{block name=content}
    <article class="post">
        <h1>{$post.title}</h1>
        <p class="post__meta muted">
            <time datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
            &middot; {$post.views} views
        </p>
        {if $categories}
            <p class="post__categories">
                {foreach $categories as $category}
                    <a class="tag" href="/category/{$category.id}">{$category.name}</a>
                {/foreach}
            </p>
        {/if}
        <img class="post__image" src="{$post.image}" alt="{$post.title}" width="800" height="450">
        <p class="post__lead muted">{$post.description}</p>
        <div class="post__body">
            {foreach $post.body|split:"\n\n" as $paragraph}<p>{$paragraph}</p>{/foreach}
        </div>
    </article>
    {if $relatedPosts}
        <section class="section">
            <h2>Related posts</h2>
            <div class="post-grid">
                {foreach $relatedPosts as $relatedPost}
                    {include file="post_card.tpl" post=$relatedPost}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
