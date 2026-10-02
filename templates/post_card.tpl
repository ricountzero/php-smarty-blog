<article class="card">
    <img class="card__image" src="{$post.image}" alt="{$post.title}" width="800" height="450" loading="lazy">
    <div class="card__body">
        <h3 class="card__title"><a href="/post/{$post.id}">{$post.title}</a></h3>
        <p class="card__description">{$post.description}</p>
        <p class="card__meta">
            <time datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
            &middot; {$post.views} views
        </p>
    </div>
</article>
