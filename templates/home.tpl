{extends file="layout.tpl"}
{block name=title}Blog{/block}
{block name=content}
    <h1>Latest posts</h1>
    {foreach $categories as $category}
        <section class="section">
            <h2>{$category.name}</h2>
            {if $category.description}<p class="muted">{$category.description}</p>{/if}
            <div class="post-grid">
                {foreach $postsByCategory[$category.id] as $post}
                    {include file="post_card.tpl" post=$post}
                {/foreach}
            </div>
            <p><a class="button" href="/category/{$category.id}">All posts</a></p>
        </section>
    {/foreach}
{/block}
