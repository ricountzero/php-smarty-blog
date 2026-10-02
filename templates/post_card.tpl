<article>
    <time
        datetime="{$post.published_at|date_format:"%Y-%m-%d"}">{$post.published_at|date_format:"%d.%m.%Y"}</time>
    <br>
    <img src="{$post.image}" alt="{$post.title}">
    <h3><a href="/post/{$post.id}">{$post.title}</a></h3>
    <p>{$post.description}</p>
    <p><span>{$post.views} views</span></p>
</article>
