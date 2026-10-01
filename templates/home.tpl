{extends file="layout.tpl"}
{block name=title}Blog{/block}
{block name=content}
    <h1>Latest posts</h1>
    {foreach $categories as $category}
        <section>
            <h2>{$category.name}</h2>
            {if $category.description}<p>{$category.description}</p>{/if}
            {foreach $postsByCategory[$category.id] as $post}
                <article>
                    <img src="{$post.image}" alt="{$post.title}">
                    <h3><a href="/post/{$post.id}">{$post.title}</a></h3>
                    <p>{$post.description}</p>
                    <time
                    datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
                </article>
            {/foreach}
            <p><a href="/category/{$category.id}">All posts</a></p>
        </section>
    {/foreach}
{/block}
