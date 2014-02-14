---
title: Creating custom NodeTypes
tags: [phpcr-tutorial,phpcr,jackalope]
date: 2014-01-18 17:30
related:
  posts_tags: [phpcr-tutorial]
---
This post is the fifth in a serie about PHPCR with Jackalope. In the [first post] we've setup our repository, in
[the second] we talked about reading and writing from and to our repository. The [third post] was about the
QueryObjectModel and in the [previous post] we talked about NodeTypes and Mixins.

In this post we'll be creating our own NodeType.

## Defining the NodeType

As we saw in the [previous post], NodeTypes have certain properties that define how a NodeType behaves and property
definitions that define what values are stored in the NodeType. To create our own NodeType, we need to tell our
repository how our NodeType should behave.

Our NodeType should have a name which shouldn't clash with other NodeTypes. To be sure of that, we need to register a
new namespace with our repository and use that namespace to name our NodeType.

~~~language-php
$session->getWorkspace()->getNamespaceRegistry()
        ->registerNamespace('acme', 'http://acme.example.com/phpcr/1.0');
~~~

To define the actual NodeType we need to use the `NodeTypeManager`, that manager takes care of registering the NodeType
with our repository. We need to pass a `NodeTypeDefinition` to `NodeTypeManager::registerNodeType`. We can build it from
an array, from XML or create a copy from another NodeTypeDefinition instance.

In this case I went for the XML solution which has the benefit that you can easily distribute the NodeType so others can
use it without the need for PHP.

~~~language-markup
<!-- customNodeTypes.xml -->
<?xml version="1.0" encoding="utf-8"?>
<nodeTypes>
    <nodeType
            name="acme:product" isMixin="false" isAbstract="false"
            isQueryable="true" hasOrderableChildNodes="true">
        <supertypes>
            <supertype>nt:base</supertype>
            <supertype>mix:title</supertype>
            <supertype>mix:referenceable</supertype>
        </supertypes>
        <propertyDefinition
                name="acme:rrpPrice"
                requiredType="decimal"
                declaringNodeType="acme:product"
                autoCreated="true"
                fullTextSearchable="false"
                mandatory="true"
                multiple="false"
                onParentVersion="COPY"
                protected="false"
                queryOrderable="true">
            <valueConstraints />
        </propertyDefinition>
        <propertyDefinition
                name="acme:media"
                requiredType="Reference"
                declaringNodeType="acme:product"
                autoCreated="false"
                fullTextSearchable="false"
                mandatory="false"
                multiple="true"
                onParentVersion="COPY"
                protected="false"
                queryOrderable="false">
            <valueConstraints>
                <valueConstraint>nt:resource</valueConstraint>
            </valueConstraints>
        </propertyDefinition>
    </nodeType>
</nodeTypes>
~~~

The definition above gives us a acme:product NodeType with 2 properties. Beside that, we also defined that it has 3
supertypes: `nt:base`, `mix:title` and `mix:reference`. For the acme:media property, we also added a valueConstraint.

## Registering the NodeType

To actually register the nodeType, we only need a few lines of code as seen in the [CreateNodeTypeCommand]

~~~language-php
$nodeTypesDocument = new \DOMDocument();
$nodeTypesDocument->load(__DIR__ . '/../Resources/data/customNodeTypes.xml');
$xpath = new \DOMXPath($nodeTypesDocument);
foreach ($xpath->query('//nodeType') as $nodeTypeElement) {
    $nodeType = new NodeType(
        new Factory(),
        $session->getWorkspace()->getNodeTypeManager(),
        $nodeTypeElement
    );
    $session->getWorkspace()
        ->getNodeTypeManager()
        ->registerNodeType($nodeType, true);
}
$session->save();
~~~

**Sidenote**: above code won't work correctly untill [PR 203] has been merged.

## Using the newly created NodeType

To use the new NodeType, we need to create a Node with the new NodeType as primaryType.

~~~language-php
$rootNode->addNode('customNodeTypeNode', 'acme:product');
~~~

We new now try to save the product, we get an error stating that the property acme:rrpPrice doesn't have a default value.
If we fill the price the node saves just fine. After that we can also add a property jcr:title, which is defined by our
supertype mix:title. But when we also try to add a property `foo` with value `bar`, we get an exception that our
NodeTypeDefinition doesn't allow to set a property `foo`.

By creating custom NodeTypes, you can create some structure in your data, while still having the flexibility from a
schemaless storage.

The examples in this blogpost can be found [on Github].

[first post]: {{site.url}}/2013/11/16/setup-jackalope-with-mysql
[the second]: {{site.url}}/2013/11/26/phpcr-reading-and-writing
[third post]: {{site.url}}/2013/12/09/phpcr-query-object-model
[previous post]: {{site.url}}/2014/01/10/nodetypes-and-mixins
[CreateNodeTypeCommand]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part5-creating-a-nodetype/src/Wjzijderveld/Command/CreateNodeTypeCommand.php
[PR 203]: https://github.com/jackalope/jackalope/pull/203
[on Github]: https://github.com/wjzijderveld/phpcr-blog-serie/tree/part5-creating-a-nodetype