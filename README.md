Stairtower
==========

Stairtower is a database server for schema-free, JSON documents, that provides a restful API and is entirely written in PHP.

[![Build Status](https://travis-ci.org/cundd/pos.svg?branch=develop)](https://travis-ci.org/cundd/pos)

What?
-----

The server runtime is built on [React](http://reactphp.org/) and utilizes PHP's native JSON de- and encoding facilities to transform data. Where applicable the [Standard PHP Library (SPL)](http://php.net/manual/de/book.spl.php) is used to build on a solid foundation and increase performance.


Why?
----

The aim was to create a database system that is 
- portable
- flexible
- offers database seeding
- does not require additional configuration
- does not require additional software or languages
- fits into the PHP environment
- and is written in the language that we now and love

In addition it should simply show that this is possible in the language that sometimes seems to be derided by followers of 'modern' programming languages.


Cons
----

While creating such applications one may hit the wall of PHP. Managing memory and fine tuning performance in PHP has its limitations. The control over memory usage, allocation and freeing in a long running PHP application is complicated, if not even impossible (please correct me). So the performance and efficiency of other document stores may be a lot better. 

An attempt to memory management is done with the [Memory Manager](https://github.com/cundd/pos/blob/develop/Classes/Memory/Manager.php) which holds the only reference to memory intense object's (especially Database instances) and allows them to be freed (calling `unset()` followed by `gc_collect_cycles()`).


Pros
----

The whole system is written in a language that powers more than 80% of the web (http://w3techs.com/technologies/details/pl-php/all/all) and is used by some of the internet's biggest players. Writing the server in PHP opens the source code to be understood by a huge community and empowers them to adapt it to their needs. Furthermore parts of Stairtower may be reused in other projects (e.g. the server's backend could be used as data provider without using the REST interface).


Current State
-------------

The software is in alpha state. The biggest parts of the API are defined and most parts are covered by unit tests. Nevertheless their is much to improve.


Requirements
------------

PHP 5.4 or higher


Installation and Usage
----------------------

### 1. Get the source code

```bash
git clone https://github.com/cundd/pos.git stairtower
cd stairtower
```

### 2. Install requirements with [Composer](https://getcomposer.org)

Get Composer:

```bash
curl -sS https://getcomposer.org/installer | php
```

Install the libraries:

```bash
php composer.phar update
```

### 3. Start the server

```bash
bin/server
```

### 4. Use curl to play with Stairtower

```bash
# Get a welcome message
curl http://127.0.0.1:1338/

# Read stats
curl http://127.0.0.1:1338/_stats

# List all databases
curl http://127.0.0.1:1338/_all_dbs

# Create a database

# Add a document

```


Roadmap
-------

This section lists some planed features.

- Intelligent database writes (currently all loaded databases will be written to the filesystem, even without modifications)
- Improved memory usage assumptions (before loading databases)
- Implement a queue to schedule tasks (like database updates or reindexing)
- Additional indexes and index types
- Authentication (via Basic Auth and header)
- Request caching
- MapReduce/Views to customize data aggregation


