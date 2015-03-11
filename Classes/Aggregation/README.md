Aggregation
===========

The Aggregation module provides different Aggregation and MapReduce features


Aggregator
----------

The aggregator can be used to iterate over a collection (e.g. database or array of objects) and collect data.

### Usage

Create the closure that will be invoked for every item in the collection and pass it to the new instance:

```php
/**
 * @param DocumentInterface $document
 */
$aggregateFunction = function ($document) {
	/** @var Aggregator $this */
	// Add data to the $results array
	$this->results[time()] = 1;
};

// Create the aggregator instance 
$aggregator = new Aggregator($aggregateFunction);
```

Invoke the aggregator's `perform()` method with the collection:

```php
$results = $aggregator->perform($collection);
```

The returned value will be the $results array.


MapReduce
---------

Actually [MapReduce](http://en.wikipedia.org/wiki/MapReduce) is a programming model for parallel data processing on clusters. Although parallelism is not supported by Stairtower, a special aggregator class is included that performs map and reduce functions.

### Usage

Create the map closure that will be invoked for every item in the collection:

```php
/**
 * @param DocumentInterface $document
 */
$mapFunction = function ($document) {
	/** @var MapReduce $this */
	// Emit keys and values $this->emit($key, $mixed);
	$this->emit($key, 1);
};
```

Create the reduce closure that will be invoked for every key emitted by the map function and the emitted values:

```php
/**
 * @param string $key
 * @param array $values
 * @return number
 */
$reduceFunction = function ($key, $values) {
  return array_sum($values);
};
```

Pass the closures to the MapReduce constructor:

```php
$aggregator = new MapReduce($mapFunction, $reduceFunction);
```

Invoke the aggregator's `perform()` method with the collection:

```php
$results = $aggregator->perform($collection);
```

The returned value will be a dictionary of the emitted keys and their associated reduce-call results.