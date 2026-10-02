{extends file="layout.tpl"}
{block name=title}Blog{/block}
{block name=content}
    <h1>Latest posts</h1>
    {foreach $categories as $category}
        <section>
            <h2>{$category.name}</h2>
            {if $category.description}<p>{$category.description}</p>{/if}
            {foreach $postsByCategory[$category.id] as $post}
                {include file="post_card.tpl" post=$post}
            {/foreach}
            <p><a href="/category/{$category.id}">All posts</a></p>
        </section>
    {/foreach}
{/block}
