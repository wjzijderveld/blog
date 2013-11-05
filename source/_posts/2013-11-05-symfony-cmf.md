---
layout: post
title: Symfony CMF
tags: [cmf, framework]
---
Today I got into a discussion about [Symfony CMF]. The discussion started with the question, if Symfony CMF isn't trying
to create a solution for a problem that doesn't exists. That person thought CMF only solved a developers problem, but
couldn't solve the problems for front-end developers or editors (or even added more problems). It came down to it, that
he had seen the admin interfaces that come with Symfony CMF and saw that it was... less from perfect (to put it nice).

#### Goals of Symfony CMF

Symfony CMF aims to provide a toolbox to create your own custom CMS, not by reinventing the wheel, but by reusing
existing code and sharing the produced code (parts of it will be used in Drupal 8). It aims to make it easier for
developers to add CMS functionality to applications built on Symfony 2.

#### PHPCR

To do that, they looked at other solutions, solutions not limited to PHP, to structure content. This because a CMS has
mostly unstructured data, and forcing this into a RDBMS isn't always a good fit. [Graph database][graph] are a better
fit. They found [JCR], a Java specification that deals with content. They ported that to PHP and called it [PHPCR];
PHP Content Repository. With PHPCR, there is an API to interface with your content repository.

#### What is doesn't solve

Althought Symfony CMF does provide some basic admin interfaces (with Sonata Admin), it isn't meant as an end product.
For most customers and editors, those screens just won't be enough. But there is nothing to stop you from developing
your own admin screens that does fit you customers needs.

Symfony CMF isn't a solution aimed at the end-user, that is still you job as a developer, to implement a solution that
helps your end-user and the editors. Symfony CMF just helps you to not get a panic attack when somebody tells you to add
content management to your Symfony2 application.

For more information about Symfony CMF, check the [bigger picture slides][bigger_picture]

[Symfony CMF]: http://cmf.symfony.com
[graph]: https://en.wikipedia.org/wiki/Graph_database
[JCR]: http://jcp.org/en/jsr/detail?id=283
[PHPCR]: https://github.com/phpcr/ "PHPCR Github"
[bigger_picture]: http://cmf.symfony.com/slides/bigpicture.html