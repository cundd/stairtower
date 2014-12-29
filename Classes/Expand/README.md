Expand
======

The Expand module allows a Model's property to be filled with connected data - read from the same or another Database. In SQL one would speak about JOINs.

An Expand statement can be described as follows:

- Document(s) are retrieved from a Database.
- Expand (the keyword `$expand`)
- the property (`property`)
- with the Document
- from Database (`database`)
- where the foreign property (`foreign`)
- matches the value of the local property (`property`).

As query string:

```
$expand=property-database-foreign
```

Learn more about [filtering Documents](http://stairtower.cundd.net/Docs/Search/).