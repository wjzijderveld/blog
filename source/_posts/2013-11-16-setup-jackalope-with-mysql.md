---
title: Setup Jackalope with MySQL
tags: [phpcr,jackalope,mysql]
---
This post is the first in a serie about PHPCR with Jackalope. If you want to know more about PHPCR, I recommend watching
[the slides about PHPCR].

In this post I will walk through the first steps, setting up your Content Repository using Jackalope. In this case we
will use MySQL as storage, but in a future post, we will migrate our data to a Jackrabbit server.

### Install Jackalope

First we will install Jackalope DoctrineDBAL. In this serie I will use [composer], and in the examples I assume it's
installed system wide, so the `composer` command is available.

~~~language-bash
$ mkdir phpcr-tutorial && cd phpcr-tutorial
$ composer require jackalope/jackalope-doctrine-dbal ~1.0
~~~

This will also install the base Jackalope package, the PHPCR and PHPCR-Utils packages and Doctrine DBAL (and their
dependencies).

To already be a bit prepared for switch storage backend later on, I setup a basic configuration that allows us to switch
easily later on. For this I installed symfony/yaml to parse the Yaml file.

~~~language-bash
$ composer require symfony/yaml ~2.3
~~~

The next step is to create your database and configure Jackalope with the correct transport layer.

### Configure jackalope

Jackalope provides a file that gives us some powerfull CLI commands. For this to work, we need to tell Jackalope what
repository connection we are using. Jackalope requires a cli-config.php file for that in the root of your project.
In that file, we need to define the connection, but we can also add a HelperSet to the CLI. The HelperSet will contain
the PHPCR session, so Jacklope CLI has something to talk to.

Luckily Jackalope provides an [example file], so we see how the connection is created. (For Jackrabbit, there is a
similar [example file][jackrabbit example file])

For this example, I created a bit of a revised version, to be prepared for the switch later. But you can choose to just
use the example provided by Jackalope Doctrine DBAL if you like. The full file I created can be found at [github][cli-config].
Below you will find the instantiation of the repository. Based on the config file, it's
decided which Jackalope Repository is created. In this part we only implemented the Doctrine DBAL version.

~~~language-php
use \Doctrine\DBAL\DriverManager;
switch ($config['jackalope']['transport']) {
    case 'jackalope-doctrine-dbal':
        $dbConnection = DriverManager::getConnection(array(
            /* database connection variables $config['jackalope']['dbal'] */
        ));

        $factory = new \Jackalope\RepositoryFactoryDoctrineDBAL();
        $repository = $factory->getRepository(array(
            'jackalope.doctrine_dbal_connection' => $dbConnection
        ));

        break;
    case 'jackalope-jackrabbit':
        throw new \Exception('Jackrabbit bootstrap has not yet been defined');
        break;
    default:
        throw new \RuntimeException(sprintf('Invalid transport "%s" given', $config['jackalope']['transport']));
}
~~~

After that we need to actual login to the repository to be able to read and write data.

~~~language-php
$credentials = new \PHPCR\SimpleCredentials($config['phpcr']['username'], $config['phpcr']['password']);
$session = $repository->login($credentials, $config['phpcr']['workspace']);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'session' => new \PHPCR\Util\Console\Helper\PhpcrHelper($session)
));
~~~

Now we have the config file we can init the database.

~~~language-bash
$ ./vendor/bin/jackalope jackalope:init:dbal
~~~

Jackalope will tell you it has initialized the database tables.

### Testing your repository

You should now have a working repository. We can test this by executing our first query, which should return the rootNode

~~~language-bash
$ ./vendor/bin/jackalope phpcr:workspace:query "SELECT * FROM [nt:unstructured]"
~~~

With this query, you request all notes with type `nt:unstructed`, which in this case, is only the rootNode.
Now we're done with the first part. In the next part, we will start with reading and writing to the repository via
Jackalope.

[the slides about PHPCR]: http://phpcr.github.io/slides.html
[composer]: http://getcomposer.org
[example file]: https://github.com/jackalope/jackalope-doctrine-dbal/blob/master/cli-config.php.dist
[jackrabbit example file]: https://github.com/jackalope/jackalope-jackrabbit/blob/master/cli-config.php.dist
[cli-config]: https://github.com/wjzijderveld/phpcr-blog-serie/blob/part1-setup/cli-config.php