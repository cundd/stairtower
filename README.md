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
- stores data in a human readable format
- fits into the PHP environment
- and is written in the language that we now and love

Additionally it should simply show that this is possible in the language that sometimes seems to be derided by followers of 'modern' programming languages.


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


Installation
------------

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

Usage
-----

[curl](http://curl.haxx.se/) will be used to interact with the database. But first the server has to be started.

### Starting the server

```bash
cd /path/to/stairtower/root/
bin/server
```

### Performing requests

#### Get a welcome message
```bash
curl http://127.0.0.1:1338/
```

#### Read statistics
```bash
curl http://127.0.0.1:1338/_stats
```

#### List all databases
```bash
curl http://127.0.0.1:1338/_all_dbs
```

#### Create a database 

Create a database with name 'myDb'. The empty body will let curl set the `Content-Length` header which Stairtower requires for `PUT` and `POST` requests

```bash
curl -X PUT http://127.0.0.1:1338/myDb -d ""

# Listing all databases should contain 'myDb'
curl http://127.0.0.1:1338/_all_dbs

# Listing the Documents of 'myDb' should return 
# an empty array
curl http://127.0.0.1:1338/myDb
```

#### Add a Document

If the Document body is a JSON string it is important to tell curl to send the appropriate header (`--header "Content-Type:application/json"`)
```bash
curl --header "Content-Type:application/json" \
	-X POST http://127.0.0.1:1338/myDb \
	-d '{
	"name": "T-Shirt - Stairtower",
	"type": "clothes",
	"category": "merchandise",
	"price": 12.50,
	"options": {
		"colors": ["green", "red", "blue"],
		"size": ["xs", "s", "m", "l"]
		}
	}'
```

Adding two more example Documents:

```bash
curl --header "Content-Type:application/json" -X POST http://127.0.0.1:1338/myDb -d '{	"name": "Hoodie - Stairtower",	"type": "clothes",		"category": "merchandise",	"price": 19.50,	"options": {	"colors": ["black", "blue"],		"size": ["s", "m", "l"]	}}';
curl --header "Content-Type:application/json" -X POST http://127.0.0.1:1338/myDb -d '{	"name": "USB stick",			"type": "electronics",	"category": "merchandise",	"price": 10.50,	"options": {	"memory": ["8GB", "32GB", "64GB"]	}}';
```

And check if the exist in the database:

```bash
curl http://127.0.0.1:1338/myDb
```

#### Query Documents by ID

You may have recognized that the Documents have been assigned an `_id` property. This defines a unique identifier inside the Database. These property is indexed by default and allows fast lookups.

To retrieve a single Document you can use it's resource URI, which is built from the Database identifier and the Document identifier (e.g. `myDb/stairtower_0.0.1_1920_document_1415440762`).

```bash
curl http://127.0.0.1:1338/myDb/document_identifier
```

#### Search for Documents

You can search by adding a property value pair as query string to the request.
This example should return the hoodie and the t-shirt we added above.

```bash
curl "http://127.0.0.1:1338/myDb/?type=clothes"
```


#### Update a Document

Documents are updated by sending a `PUT` request to the Documents resource URI. Please keep in mind that those updates do NOT patch a Document but will replace it completely.

```bash
curl --header "Content-Type:application/json" \
	-X PUT http://127.0.0.1:1338/myDb/document_identifier \
	-d '{
	"name": "T-Shirt - Stairtower",
	"type": "clothes",
	"category": "merchandise",
	"price": 13.50,
	"options": {
		"colors": ["green", "red", "blue"],
		"size": ["xs", "s", "m", "l"]
		}
	}'
```


#### Delete a Document

Documents are deleted by sending a `DELETE` request to the Documents resource URI.

```bash
curl -X DELETE http://127.0.0.1:1338/myDb/document_identifier
```


#### Delete a Database

Deletion of a whole Database is similar to removing a single Document. You simply omit the Document identifier when sending a `DELETE` request.

```bash
curl -X DELETE http://127.0.0.1:1338/myDb/
```


#### Manage the server

##### Restart

```bash
curl -X POST http://127.0.0.1:1338/_restart
```

##### Shutdown

```bash
curl -X POST http://127.0.0.1:1338/_shutdown
```


Roadmap
-------

This section lists some planed features.

- Intelligent database writes (currently all loaded databases will be written to the filesystem, even without modifications)
- Improved memory usage assumptions (before loading databases)
- Implement a queue to schedule tasks (like database updates or reindexing)
- Additional indexes and index types
- Update and delete collision prevention (like in [CouchDB](http://docs.couchdb.org/en/1.6.1/intro/api.html#revisions))
- Authentication (via Basic Auth and header)
- Request caching
- MapReduce/Views to customize data aggregation


