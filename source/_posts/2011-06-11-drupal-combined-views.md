---
title: Drupal and combined views
layout: post
---
This week I started to work with Drupal 7, hadn’t worked with Drupal before, so was excited to explore the possibilities.

One of the goals was to familiarize  myself with Drupal and see if it is suited to create simple websites within a short time.

Somebody else started the project, so there were some blocks already created and a big piece of the theme was already build. I started with the thing I thought was most challenging, never expecting that to be pretty difficult.

The goal was about 2 entities, Services and Cases. Each Case having one service, and every Service having multiple Cases. I tried to create a page with 2 blocks, the first just plain text, the second a listing of Services. The challenge was to display the Cases for each Service in that same listing. Pretty soon I found that I required the module Views for that.

At first I tried with a module References, I created the 2 Entities, and added the Field Service to the Cases entity as a Node Reference

I did manage to join the two entities with a Relationship and grouping, but that didn’t got me very far. For every Case it would also repeat the Service description. After a lot of time in the Drupal IRC channel, I found that the problem was in the fact that I required more then 1 field from the Service Entity to be shown.

After a lot of help in the IRC channel, I found the following solution:

Just create 2 Views, one simple view with the Nodes of type Service. And a View with Nodes of type Cases, in that View I used a Contextual Filter for

In the template for the Description Field (in my case, can be any Field template), I called the function `views_embed_view`, that function does as the name says, it embeds another view. The first parameter is the machine_name of the view, the second paramater is the machine_name of the display in the view (You can find that under Advanced -> Other).

The third paramater Ihas some magic to it, because it accepts mixed data, but for me the following worked: `array(‘nid’ => $row->nid)`.

So the solution was to embed an View in a View, although there are modules that should do this, I haven’t found another way then modifying the template and using code.

I could write some sort of tutorial, a step by step explanation, just ask. For now this summary will do:

Used modules:

 - [Views](http://drupal.org/project/views)
 - [References](http://drupal.org/project/references)

Created 2 Views, use `views_embed_view` to embed the second view into the first one.

Also I have to say, the help I got on IRC was amazing, I probably was as unclear as a newbie can be, but they kept on asking question until they knew what I required.
Thanks RumpledElf and damiankloip (and some others that made a suggestion)!

Join [#drupal](irc://irc.freenode.org/drupal) on [freenode.org](http://www.freenode.org)