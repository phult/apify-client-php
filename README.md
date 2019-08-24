# Apify Client for PHP

## Installation

### System requirements
 - PHP: >= 5.6
 - Laravel/ Lumen Framework: 4.* or newer

### Require the composer package
    `composer require megaads/apify-client-php`

## Create query builder

```php
use Megaads\ApifyClient\Client;

$query = Client::endpoint("product", [
    Client::OPTION_API_HOST => "https://api.domain.com",
    Client::OPTION_API_AUTH => "token=dsfqwe123sdf2342c",
    Client::OPTION_REQUEST_HEADER => ["Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l"]
]);
```

## Add custom field
Add a custom parameter to request URL
```php
$query->addField("customer_id", 123);

$query->addField("version", "1.0.0");
```
## Get request URL
The simplest method to see the generated request URL
 ```php
$query->toURL();
```

## Pagination

| Parameter   | Required    | Default    | Description                                                      |
|-------------|-------------|------------|------------------------------------------------------------------|
| page_id     | No          | 0          | Page index, start at 0
| page_size   | No          | 50         | Number of rows to retrieve per page

```php
$query->pageId(0);

$query->pageSize(100);
```

## Sorting

### Sort ascending

```php
$query->sort("user_id");
```

### Sort descending

```php
$query->sort("-created_at");
```

### Sort by multiple columns

```php
$query->sort(["user_id", "-created_at"]);
```

### Selection

Select columns from the records. SQL aggregate functions such as `COUNT`, `MAX`, `MIN`, `SUM`, `AVG`, SQL aliases are also available

```php
$query->select("id");

$query->select(["content", "user_id", "sum(view_count) as view_sum"]);
```

### Group By

Group the result-set by one or more columns and combine with aggregate functions using `Selection`

```php
$query->select(["user_id", "sum(view_count) as view_sum"]);

$query->group("user_id");
```

### Filtering

| Operator     | Condition          |  For example                                         
|--------------|--------------------|----------------------------------
| Client::SELECTION_EQUAL           |  Equal to          | ```$query->filter("user_id", Client::SELECTION_EQUAL, 1);```
| Client::SELECTION_NOT_EQUAL       |  Not equal         | ```$query->filter("user_id", Client::SELECTION_NOT_EQUAL, 1);```
| Client::SELECTION_GREATER         |  Greater           | ```$query->filter("user_id", Client::SELECTION_GREATER, 1);```
| Client::SELECTION_GREATER_EQUAL   |  Greater or equal  | ```$query->filter("user_id", Client::SELECTION_GREATER_EQUAL, 1);```
| Client::SELECTION_LESS            |  Less              | ```$query->filter("user_id", Client::SELECTION_LESS, 1);```
| Client::SELECTION_LESS_EQUAL      |  Less or equal     | ```$query->filter("user_id", Client::SELECTION_LESS_EQUAL, 1);```
| Client::SELECTION_IN              |  In                | ```$query->filter("user_id", Client::SELECTION_IN, [1,2,3]);```
| Client::SELECTION_NOT_IN          |  Not in            | ```$query->filter("user_id", Client::SELECTION_NOT_IN, [1,2,3]);```
| Client::SELECTION_BETWEEN         |  Between           | ```$query->filter("user_id", Client::SELECTION_BETWEEN, [1,20]);```
| Client::SELECTION_NOT_BETWEEN     |  Not between       | ```$query->filter("user_id", Client::SELECTION_NOT_BETWEEN, [1,20]);```
| Client::SELECTION_LIKE            |  Like              | ```$query->filter("title", Client::SELECTION_LIKE, "hello");```
| Client::SELECTION_NOT_LIKE        |  Not like          | ```$query->filter("title", Client::SELECTION_NOT_LIKE, "hello");```

## Relationships

Apify provides the ability to embed relational data into the results

For example

```php
$query->embed("cities");

$query->embed(["nation", "districts"]);
```

### Filtering on relationships

```php
$query->filter("nation.location_code", Client::SELECTION_EQUAL, "EU");

$query->filter("districts.name", Client::SELECTION_LIKE, land);
```

## Retrieve data

### Find: Retrieve single record

```php
$query->find(1);
```

### Get: Retrieve all records that match the query

```php
$query->get();
```

Response format

```php
[
    "meta" => [
        "has_next" => true,
        "total_count" => 100,
        "page_count" => 2,
        "page_size" => 50,
        "page_id" => 0
    ],
    "result" => [],
    "status" => "successful"
]
```

### First: Retrieve the first record that matchs the query


```
$query->first();
```

Response format

```php
[    
    "result" => [],
    "status" => "successful"
]
```

### Count: Retrieve the number of records that match the query

```
$query->count();
```

Response format

```php
[    
    "result" => 50,
    "status" => "successful"
]
```

### Increment/ Decrement: Provides convenient methods for incrementing or decrementing the value of a selected column

```
$query->select("view_count");

$query->increment();
```

Response format

```php
[    
    "result" => 1,
    "status" => "successful"
]
```

## Send custom request
```php
use Megaads\ApifyClient\Client;

$query = Client::request("https://api.domain.com/product", 
Client::METHOD_POST, 
[
    "name" => "Hello",
    "code" => "C0001"
],
[
    "Authorization: Basic QWxhZGRpbjpPcGVuU2VzYW1l"
]);
```

## License

The Apify is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Contact us/ Instant feedback

Email: info@megaads.vn

Skype: [phult.bk](skype:phult.bk?chat)
