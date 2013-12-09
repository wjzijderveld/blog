---
title: PHPCR: Query Object Model
tags: [phpcr, jackalope, query, qom]
date: 2013-12-09 22:00
---
This post is the third in a serie about PHPCR with Jackalope. In the [first post] we've setup our repository, in
[the second] we talked about reading and writing from and to our repository.

In this post, we will continue with the reading part, but this time by using queries. For this, PHPCR has multiple
possibilities. On of them is the Query Object Model (QOM) with a Factory. At first this can seem a bit verbose, but I
hope to make clear why that is. Besides the QOM, there is also the possibility to use JCR-SQL2 queries. A Query language
very similar to SQL, which (in the case of Jackalope DoctrineDBAL) gets converted to the QOM. Jackalope Jackrabbit uses
SQL2 to communicate with Jackrabbit. There are 2 other query methods (SQL1 and XPath), but I'm not showing those in this
serie.

For the examples I use in this post, I created [a fixture file] to load. This is loaded in the [new Command] I added.

But before we can start with querying, we need to understand what we are querying and how we limit the results. With
PHPCR you're not querying tables or collections in a way you might be used to with SQL or NoSQL databases. When you
create a query you specify a `selector` (or multiple if the repository supports joins). So, when you want to query for
all files, without knowing where they are in your tree, you simply use the following query.

~~~language-php
$factory = $session->getWorkspace()->getQueryManager()->getQOMFactory();
$source = $factory->selector('file', 'nt:file');
$qom = $factory->createQuery($source);
$result = $qom->execute();
~~~

Or with JCR-SQL2

~~~language-php
$result = $session->getWorkspace()->getQueryManager()
                  ->createQuery("SELECT * FROM [nt:file]",
                                QueryInterface::JCR_SQL2);
~~~

Already you can see the difference in verbosity of both methods. Because of this, you will probably use JCR-SQL2 when
building your queries. But in this post I will the QOM Factory for the most examples because (IMHO) that shows a bit
better what parts make up a query.

**Update:** Instead of working with the QOMFactory directly, you can also work with the more fluent QueryBuilder that's
provided by PHPCR Utils.

~~~language-php
$qomFactory = $session->getWorkspace()->getQueryManager()->getQOMFactory();
$queryBuilder = new \PHPCR\Util\QOM\QueryBuilder($qomFactory);
$queryBuilder
    ->from($qomFactory->selector('file', 'nt:file'))
    ->where($qomFactory->descendantNode('file', '/documents'))
    ->execute();
~~~

## Basic conditions

In the fixture file, I created some news items which we are going to query. The items have the nodeType `nt:unstructured`
so that's the what we are quering for. For the first example we are just retrieving all items, which are placed under
`/queryExamples/news`.

We first need to define what selector we are going to use.

~~~language-php
/** @var QueryObjectModelFactoryInterface $qomFactory */
$qomFactory = $session->getWorkspace()->getQueryManager()->getQOMFactory();
$source = $qomFactory->selector('news', 'nt:unstructured');
~~~

The first parameter is the name of the selector which you get to choose yourself, you might call it an alias. The second
is the primary nodeType we are querying for, so in this case `nt:unstructured`.

Now we can define what columns we want to return in the results. For this example we only select the title.

~~~language-php
$titleColumn = $qomFactory->column('news', 'title', 'title');
~~~

The first parameter is the selector from which we wan't to select something. The second parameter is the property we
want to select and the third is the name we want to use in the column, like an alias.

Next, we create the query with a condition to limit the nodes by path. This is called the `DescendantNodeConstraint`.

~~~language-php
$qom = $qomFactory->createQuery(
    $source,
    $qomFactory->descendantNode('news', '/queryExamples/news'),
    array(),
    array($titleColumn)
);
~~~

The first parameters defines the selector to query from, the second is the condition for the query. The next parameter
defines the order of the results. The last parameter are the columns to select. The first parameter of the constraint
tells which selector we want to user, the second is under what path we want to select nodes.

Now we are ready to execute the query and loop over he results.

~~~language-php
$result = $qom->execute();
foreach ($result->getRows() as $newsItem) {
    echo $newsItem->getValue('title');
}
~~~

Now we have our first results, we are going to limit our newsItem based on author. This is a simple PropertyValue
constraint. But because we now have 2 constraints, we need to alter our constraint to an `AndConstraint` and it becomes
very clear that the QOM Factory can get very verbose.

~~~{.language-php.line-numbers}
$qom = $qomFactory->createQuery(
    // SelectorInterface
    $qomFactory->selector('news', 'nt:unstructured'),
    // AndInterface
    $qomFactory->andConstraint(
        // DescendantNodeInterface
        $qomFactory->descendantNode('news', '/queryExamples/news'),
        // ComparisonInterface
        $qomFactory->comparison(
            // PropertyValueInterface
            $qomFactory->propertyValue('news', 'jcr:author'),
            // 'jcr.operator.equal.to'
            QueryObjectModelConstantsInterface::JCR_OPERATOR_EQUAL_TO,
            // LiteralInterface
            $qomFactory->literal('foo')
        )
    ) // No orderings or columns for this example
);
echo count($qom->execute()->getRows()); // 2
~~~

This seems like a lot of code for a simple query, so I'll try to explain why that is. You already can see that there
are different kind of constraints. You don't always compare 2 properties but, as already used, you might need to test
if the nodes are descendants of a specific node, or if the node is a direct child of a given node. Or maybe you only
need to query nodes that have a specific property. Now everything is an object, it's way easier to walk the query to be
able toe execute it.

## More operands

The next queries have a bit more constraints, and a constraint that you might expected to work differently.
We are going to query for nodes under `/queryExamples/news`, where the author is **NOT** `bar` **AND** where the node
does **NOT** have a categories property **OR** where the node contains a category `foo`. Then we order the result based
on NodeName in descending order.

~~~{.language-php.line-numbers}
$qom = $qomFactory->createQuery(
    $source,
    // First and constraint
    $qomFactory->andConstraint(
        // Descendant constraint
        $qomFactory->descendantNode('news', '/queryExamples/news'),
        // Second and constraint
        $qomFactory->andConstraint(
            // Compare author
            $qomFactory->comparison(
                $qomFactory->propertyValue('news', 'jcr:author'),
                QueryObjectModelConstantsInterface::JCR_OPERATOR_NOT_EQUAL_TO,
                $qomFactory->literal('bar')
            ),
            // Or constraint
            $qomFactory->orConstraint(
                // Check for missing property categories
                $qomFactory->notConstraint(
                    $qomFactory->propertyExistence('news', 'categories')
                ),
                // Or check for category foo
                $qomFactory->comparison(
                    $qomFactory->propertyValue('news', 'categories'),
                    QueryObjectModelConstantsInterface::JCR_OPERATOR_EQUAL_TO,
                    $qomFactory->literal('foo')
                )
            )
        )
    ),
    // Order nodes based on name
    array($qomFactory->descending($qomFactory->nodeName('news')))
);
~~~

We now receive 2 rows, `item2` and `item1` in that order.

#### To keep in mind

Especially with Doctrine DBAL there are a few limitations with querying. So is comparing dates a bit tricky because the
properties are stored in XML. A workaround would be to store the date as a numeric format, so it becomes a lot easier to
compare and order it.


## JCR-SQL2

Now let's have a quick look at the JCR-SQL2 variant of above query. We can just generate that from the above code with
`$qom->getStatement();`

~~~language-sql
SELECT * FROM [nt:unstructured] AS news
WHERE (
    ISDESCENDANTNODE(news, [/queryExamples/news])
    AND (
        news.[jcr:author] <> 'bar'
        AND (
            NOT news.categories IS NOT NULL
            OR news.categories = 'foo'
        )
    )
)
ORDER BY NAME(news) DESC
~~~

As you can see, is that a lot shorter and probably more more readable then the QOM Factory version. But a JCR-SQL2 can
get hard to read when you build it dynamicly, so it definitely isn't always the better solution.

## Other constraints

We already saw quit a few constraints and I named a few others. Here is a quick overview of all the constraints that
PHPCR defines.

  - And _Combine 2 required constraints_
  - ChildNode _Check parent node_
  - Comparison _Compare 2 values, can be any operand_
  - DescendantNode _Check if path is under specified path_
  - FullTextSearch _Full text search_
  - Not _Inverse given constraint_
  - Or _Combine 2 optional constraints_
  - PropertyExistence _Check if the node has a property_
  - SameNode _Compare node with another node_

**NOTE**
Not all constraints are available in the Jackalope DoctrineDBAL transport, so is the SameNodeConstraint not yet
implemented.

We might see some of these constraints begin used in future posts. But I guess most are pretty obvious in what they do.

## Next post

In the next post, I'll tell more nodeTypes and mixins. I'll do that by using versionable as an example.

[first post]: {{site.url}}/2013/11/16/setup-jackalope-with-mysql
[the second]: {{site.url}}/2013/11/26/phpcr-reading-and-writing
[a fixture file]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part3-query-and-querybuilder/src/Wjzijderveld/Resources/data/fixtures.xml
[new Command]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part3-query-and-querybuilder/src/Wjzijderveld/Command/QOMCommand.php