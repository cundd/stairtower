Expand
======

The Expand module allows a Model's property to be filled with connected data - read from the same or another Database. In SQL one would speak about JOINs.


Simple Expand
-------------

An Expand statement can be described as follows:

- Document(s) are retrieved from a Database.
- Expand (the keyword `$expand`)
- the property (`property`)
- with the Document
- from Database (`database`)
- where the foreign property (`foreign`)
- matches the value of the local property (`property`).

Below are some example query strings utilizing the Expand module

### Expand a single property

Schema:

```
$expand=property/database/foreign
```


Example:

```
$expand=person/contacts/email
```


### Expand more than one properties

Schema:

```
$expand=property1/database1/foreign1/-/property2/database2/foreign2
```


Example:

```
$expand=person/contacts/email/-/book/book/isbn_10
```

### Expand a filtered set

Schema:

```
filter-property=filter-value&$expand=property/database/foreign

$expand=property/database/foreign&filter-property=filter-value
```


Example:

```
title=The Hobbit&$expand=person/contacts/email

$expand=person/contacts/email&title=The Hobbit
```

More about [filtering](http://stairtower.cundd.net/Docs/Search/).


Expand as
---------

It is possible to define a different property to be filled with the expanded Document(s). This prevents the original `property` from being overwritten.

Such Expand statements are slightly different:

- Document(s) are retrieved from a Database.
- Expand (the keyword `$expand`)
- the Document(s)
- with the Document
- from Database (`database`)
- where the foreign property (`foreign`)
- matches the value of the local property (`property`)
- as property key `as`.

Schema:

```
$expand=property/database/foreign/as
```


Example:

```
$expand=person/contacts/email/contact
```
