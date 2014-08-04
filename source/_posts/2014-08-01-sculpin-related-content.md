---
title: Sculpin Related Content Bundle
tags: [sculpin, symfony, bundle, related]
---

When I wrote the [phpcr tutorial] serie, I wanted to show the previous posts in the same serie.
As this wasn't a feature Sculpin provided, I decided to write my own bundle to make this
possible, that's how [SculpinRelatedContentBundle] started.

Altough I only finished it for I wrote the last post in the serie, I'm still happy that I wrote it.
But it still took me 6 months to actually tag and release it... 

## Installation

Add the bundle to yuor dependencies in your [sculpin.json] and run `sculpin install`.

~~~language-javascript
{
    "require": {
        "wjzijderveld/sculpin-related-content-bundle": "~1.0"
    }
}
~~~

To use the bundle you need to do 2 things, the first is configure each post where you want to show related content.
You do this by defining the tags that relate to this content. I deliberately didn't choose to automatically couple
tags to eachother, because I didn't want to show all posts about phpcr with all posts about phpcr.

So we will configure for each content_type which tags should be used to find related content.


~~~language-javascript
title: Awesome content is awesome
tags: [phpcr, phpcr-tutorial]
related:
  posts_tags: [phpcr-tutorial]
~~~

But only configuring the content that is related to each other isn't enough,
We should also show the related content somewhere!

Because I didn't want to restrict where I can show the related content, I assigned the related content to
a global variable `related_content`. This way you can choose how and where to show the content.

~~~language-markup
{% verbatim %}
{% if related_content|length %}
    <ul class="block related">
        {% for relatedSource in related_content %}
            <li><a href="{{ site.url }}{{ relatedSource.url }}">
                {{ relatedSource.title }}
            </a></li>
        {% endfor %}
    </ul>
{% endif %}
{% endverbatim %}
~~~

Yesterday I tagged version 1.0.0, about 15 minutes later I tagged 1.0.1 because I forgot to fix the only issue that prevented me from tagging 1.0.0 sooner, because you know... I just didn't think that far ahead yesterday :)

It probably doesn't work with all content_types available as I only tried it with post_tags, but it shouldn't be that hard to add support for other types.

Contributions are welcome!

[phpcr tutorial]: {{site.url}}/2013/11/16/setup-jackalope-with-mysql
[SculpinRelatedContentBundle]: https://github.com/wjzijderveld/SculpinRelatedContentBundle
[sculpin.json]: https://sculpin.io/documentation/embedded-composer/
