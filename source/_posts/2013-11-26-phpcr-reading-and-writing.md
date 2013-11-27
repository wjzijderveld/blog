---
title: PHPCR: Reading and writing
tags: [phpcr,jackalope,mysql,reading,writing]
draft: true
date: 2013-11-27 22:30
---
This post is the second in a serie about PHPCR with Jackalope. If you want to know more about PHPCR, I recommend watching
[the slides about PHPCR].

In the [previous post], I talked about setting up your repository. In this post we will start playing with some actual
content.

### Insert data

To show the basic usage of PHPCR, we are going to load some data into our repository. For this I used [Faker].

~~~language-bash
composer require fzaninotto/faker ~1.2
~~~

Because we are going to add more CLI scripts later on, I created a simple Symfony Console Application for this. I called
it `console` at placed it in the root. You can find the console and the Command [on Github].

With PHPCR, it is pretty easy to create new nodes. You simply call `addNode` on it's parent node. So before we can
create new nodes, we need to rootNode. You can request the rootNode from the PHPCR session.

~~~language-php
$session = $this->getHelper('phpcr')->getSession();
~~~

Here we use the helper that we defined in cli-config.php in my previous post to get the Session. Now we have the
session, we can simply retrieve the rootNode with `$session->getRootNode();` With the rootNode, we can start creating
fake nodes.

~~~{.language-php.line-numbers}
$faker = \Faker\Factory::create();
while (count($rootNode->getNodes()) < 10) {

    try {
        $node = $rootNode->addNode($faker->word);

        $node->setProperty('title', $faker->name);
        $node->setProperty('body', $faker->text(1000));
    } catch (\PHPCR\ItemExistsException $e) {
        // Ignore, and try again
    }
}

$session->save();
~~~

On line 5, you see the `addNode`, the first argument you give it, is the nodeName. You can give a second argument, to
define the nodeType but in this case we will use the default value (which will be nt:unstructured).
On line 7 and 8, we set 2 properties with random values. The first argument is the name of the property, the second is
the value. You can also give a third parameter, to indicate the type, but this is not required as [PHPCR will guess the
type][property guesser] based on given value.

Because nodeNames need to be unique, Jackalope will throw a ItemExists Exception when we try to create 2 nodes with the
same name. As that might happen with random date, we just catch the exception and try again.

### Reading the data

Now we have some fake data, we can try to query it again. We start with simply iterating over all the childNodes.
Because every Node implements `Traversable`, we can simply loop over a node to get its children.

~~~{.language-php.line-numbers}
foreach ($rootNode as $childNode) {
    $name = $childNode->getName();
    $title = $body = null;
    if ($childNode->hasProperty('title')) {
        $title = $childNode->getProperty('title')->getValue();
    }
    if ($childNode->hasProperty('body')) {
        $body = $childNode->getProperty('body')->getValue();
    }
    // .. do something
}
~~~

All pretty straight forward.

You can also retrieve a node by it's path:

~~~language-php
$dummyNode = $session->getNode('/dummy');
~~~

Or multiple by path:

~~~language-php
$nodes = $session->getNodes(array('/', '/dummy'));
foreach ($nodes as $node) {
    // Do something with the node
}
~~~

But you can also retrieve a childNode by it's name:
~~~language-php
$dummyNode = $rootNode->getNode('dummy');
~~~

### Other types

At this point, we only used string values. But we can store other stuff as well, like integers, floats, booleans,
binaries and more.

~~~language-php

~~~

[the slides about PHPCR]: http://phpcr.github.io/slides.html
[previous post]: {{site.url}}/2013/11/16/setup-jackalope-with-mysql
[Faker]: https://github.com/fzaninotto/Faker
[on Github]: https://github.com/wjzijderveld/phpcr-blog-serie/tree/part2-querying
[property guesser]: https://github.com/phpcr/phpcr/blob/master/src/PHPCR/PropertyType.php#L326