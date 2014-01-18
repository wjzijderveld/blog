---
title: NodeTypes and Mixins
tags: [phpcr,jackalope]
date: 2014-01-10 22:15
---
This post is the fourth in a serie about PHPCR with Jackalope. In the [first post] we've setup our repository, in
[the second] we talked about reading and writing from and to our repository. In the [previous post] we played with the
QOM and wrote our first queries.

In this post, we will be digging a bit deeper into PHPCR. We'll see how NodeTypes work and how you can combine some
properties with the use of Mixins.

## Introduction to NodeTypes

What are NodeTypes? The JCR specification defines it like this:

> Node types are used to enforce structural restrictions on the nodes and properties in a workspace by defining for
> each node, its required and permitted child nodes and properties.

A repository can determine what NodeTypes are supported. In this case we will look at the types that Jackalope defines.
For that, we use the Command provided by PHPCR-Utils. I added that to the console used by the

~~~language-bash
$ ./console phpcr:node-type:list
~~~

This will give you a list of the NodeTypes defined by Jackalope. If you would have defined custom NodeTypes, then those
would show here as well. As you already can see in that list almost all of them have one or more SuperTypes. That means
you can extend NodeTypes with one or more other NodeTypes.

But this list doesn't show everything we want to see, so I created a [new info Command] in the example that also shows
PropertyDefinitions and ChildNodeDefinitions. That way we can get a better understanding what NodeTypes actually define.

~~~language-bash
$ ./console tutorial:info
~~~

In this case, we are going to look at the NodeType `nt:nodeType` the nodeType that is used to define custom NodeTypes.

~~~language-css
nt:nodeType
  Supertypes:
    > nt:base
  PropertyDefinitions:
    > jcr:hasOrderableChildNodes (Boolean: Required)
    > jcr:isQueryable (Boolean: Required)
    > jcr:isMixin (Boolean: Required)
    > jcr:nodeTypeName (Name: Required)
    > jcr:isAbstract (Boolean: Required)
    > jcr:primaryItemName (Name: Optional)
    > jcr:supertypes (Name: Optional)
    > jcr:mixinTypes (Name: Optional)
    > jcr:primaryType (Name: Required)
  ChildNodeDefinitions:
    > jcr:childNodeDefinition (Optional)
    > jcr:propertyDefinition (Optional)
~~~

As you can see, a NodeType defines a lot of stuff. It defines that it should have a property that defines it's name
`jcr:nodeTypeName`. It also defines if the NodeType is Queryable and if it's childNodes have an order.
Besides that, it can also define if and which childNodes it can contain. As an example, NodeType `nt:activity` doesn't
support any ChildNodes, `nt:folder` can contain any NodeType and `nt:file` has a required ChildNode `jcr:content`.

## Mixin example

For an example with Mixins, I planned to use some versioning code examples to demonstrate how mixins can be used. But
that was until I found out Versioning is not yet supported by Jackalope Doctrine DBAL. So I'm just going to explain what
mixins can do for you, and I will use some of the other default mixins available.

A Mixin looks like a NodeType, it also defines some parameters of how a Node should behave. But different from the
primary NodeType, you can add multiple mixins to a single node. Let's see how we add a Mixin to a Node first, in this
case `mix:created`.

~~~language-php
$mixinExample = $rootNode->addNode('mixinExample');
$mixinExample->addMixin('mix:created');
$session->save();
~~~

That's simple isn't it? Directly we can see what this mixin can do:

~~~language-php
var_dump($mixinExample->getProperty('jcr:created')->getString());
// string(29) "{{ 'now'|date('Y-m-d') }}T{{ 'now'|date('H:i:s') }}.{{ 'now'|date('u')|slice(0,3) }}{{ 'now'|date('P') }}"
~~~

So we now have a creation date without us adding it manually. We also got a `jcr:createdBy` property, which is empty by
default. Let's add another mixin.

~~~language-php
$mixinExample->addMixin('mix:lastModified');
~~~

That mixin provides us with a `jcr:lastModified` and a `jcr:lastModifiedBy` property. It differs per transport layer if
the `jcr:lastModified` is automatically updated. Jackalope Doctrine DBAL doesn't do that this moment. But we can still
set it manually.

That's it for this post. It took a lot longer then planned to finish this post, partly because of the holidays and
partly because I stumbled on some bugs in Jackalope that I wanted to fix first.

I hope the next part of this blogpost will appear within the next 2 weeks.

[first post]: {{site.url}}/2013/11/16/setup-jackalope-with-mysql
[the second]: {{site.url}}/2013/11/26/phpcr-reading-and-writing
[previous post]: {{site.url}}/2013/12/09/phpcr-query-object-model
[new info Command]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part4-nodetypes-and-mixins/src/Wjzijderveld/Command/InfoCommand.php