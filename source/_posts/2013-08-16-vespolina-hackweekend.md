---
layout: post
title: Vespolina hackweekend
tags: [vespolina, ecommerce, framework]
---
As I mentioned in [Working on an OpenSource project]({{ site.url }}/2012/06/02/working-on-opensource/),
I'm somewhat involved with [Vespolina](http://www.vespolina.org).
Last weekend we had our first hackweekend, [Daniel](https://github.com/inspiran)
and I sat together, and [Richard](https://github.com/iampersistent) and
[Luis](https://github.com/cordoval) joined us remote. On friday evening
we had a conference call where [Philipp](https://github.com/hacfi) also
joined us. In that conference call we talked a bit about what we wanted
to do that weekend.

The biggest change that we did that weekend, was merging some of the libraries
together in 1 lirbary. This to make it easier for people to start working
with Vespolina, and the make development easier, because maintaining around
40 repositories isn't trivial. Eventually the following libraries got merged
into [a single library](https://github.com/vespolina/commerce).

 - Billing
 - Invoice
 - Order
 - Partner
 - Pricing
 - Product

So essentially everything that was already done with the CommerceBundle,
but now for the libraries.

And I continued working on the new website and a bit on the documentation. The
website is almost finished, just needs some more content and probably some minor
bugfixes. But the documentation still needs lots of work.

In the coming weeks, I hope to continue working on writing documentation
and focus on writing more tests. The combination is probably a good way
to learn Vespolina better and hopefully lead to better documentation and tests.