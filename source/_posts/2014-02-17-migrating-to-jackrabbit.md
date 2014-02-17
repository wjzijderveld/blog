---
title: Migrating to jackrabbit
date: 2014-02-17 14:30
tags: [phpcr-tutorial, phpcr,jackalope,jackrabbit]
related:
  posts_tags: [phpcr-tutorial]
---
This is the sixth and probably last post about PHPCR with Jackalope. In the previous posts we played with PHPCR using
Jackalope Doctrine DBAL as storage backend. In this post we are going to migrate our data to [Jackrabbit].

Jackrabbit is a Apache project that fully implements the JCR-170 and JCR-283 spec. We are going to implement it using
[Jackalope Jackrabbit].

## Preperations

We first need to install the new Jackalope package.

~~~language-bash
$ require jackalope/jackalope-jackrabbit ~1.1
$ composer update
~~~

Since we installed it in november there has been quit a few releases (Symfony 2.4, Jackalope 1.1, etc), so we are updating
those as well.

In the first post, when we setup our repository, we already prepared a bit on the migration, by making our config aware
of the fact that we can use multiple backends.

_**Disclaimer**: I haven't performed this action in production yet, so I can't guarantee this works for all cases.
And as always: make a backup!_

## Exporting the data from MySQL

To do this migration, we will export the data from MySQL and import it again into Jackrabbit. To do this I added 2
commands from PHPCR-Utils to our console, the `WorkspaceExportCommand` and the `WorkspaceImportCommand`. I also added
the `WorkspaceQueryCommand` to perform simple SQL2 queries, but this is purely for demo purposes.

The export will be a XML file with your complete workspace. Running the export is as simple as running the command with
the target filename as an argument.

~~~language-bash
$ ./console phpcr:workspace:export tutorial.xml
~~~

You should now have a file `tutorial.xml` with your complete workspace. I included an example of my workspace in [the
git repository], I also included a formatted version for your convenience.

## Updating our configuration

Before we're going to import our data in Jackrabbit, we need to update our configuration. And you'll need a running
Jackrabbit server to import your data again. I'm not going into specifics how to install and run Jackrabbit, there are
multiple options to do that. In this example I'll be using the [standalone server].

First we are going to update the `config.yml` we created in the root of our project. In the `jackalope` section we add a
section for jackrabbit, under the dbal section. In the new section we only need to configure the URL of our repository.
And update the transport to `jackalope-jackrabbit`.

~~~language-markup
jackalope:
  transport: jackalope-jackrabbit
  [..]
  jackrabbit:
    url: http://localhost:8080/server
~~~

I've updated [the cli-config.php] in the git repository, similar to the one for DoctrineDBAL, but without the database
connection.

## Importing our data

Before we actually import our data, lets run a SQL2 query to check we actually changed our backend correctly and the
workspace is empty.

~~~language-bash
$ ./console phpcr:workspace:query "SELECT * FROM [nt:unstructured]"
~~~

This should return only a single row, the rootNode. Now, let's import our data! To do this, we first need to recreate
the custom nodeTypes we created in [the previous post]. After that, we can actually import our data.

~~~language-bash
$ ./console tutorial:create-nodetype
$ ./console phpcr:workspace:import tutorial.xml
~~~

You should get a message like `"Successfully imported file "<project path>/tutorial.xml" to path "/" in workspace "default"`.

When we now rerun the query from above, we should have some results! With Jackrabbit now configured, you might play with
some of the features Jackrabbit has built-in, like versioning.

## That's a wrap

As I mentioned in the intro, this is probably the last post in this serie. I hope this serie helped some people to
understand a bit what PHPCR is and how it can be used. If somebody would like me to create a more detailed example, or
expand a bit more on a specific subject, let me know!


[Jackalope Jackrabbit]: https://github.com/jackalope/jackalope-jackrabbit
[Jackrabbit]: http://jackrabbit.apache.org/
[standalone server]: http://jackrabbit.apache.org/standalone-server.html
[the git repository]: https://github.com/wjzijderveld/phpcr-blog-serie/tree/part6-migrate-to-jackrabbit
[the cli-config.php]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part6-migrate-to-jackrabbit/cli-config.php
[the previous post]: {{site.url}}/2014/01/18/creating-custom-nodetypes