# Availability && shortages check

PHP code that helps answer following questions: 
- Is the equipment the customer want to hire available in a certain period?
- Where in the planning are equipment shortages (more planned than the stock)?

## Requirements
PHP >= 7.1.3
- Sqlite3 PHP Extension
- PDO PHP Extension
- JSON PHP Extension
- Composer

## Installation

Clone or download project from git

```git clone https://github.com/noreff/availability_n_shortages.git```

Change directory to project directory

```cd availability_n_shortages```

Create autoloader

```composer install```

Run the PHP builtin server 

```php -S localhost:8123```

## Usage

Open http://localhost:8123/ in your browser and add some of this params to the url:

**action**, possible values:

- isAvailable
- getShortages

**start** - date in yyyy-mm-dd format

**end**  - date in yyyy-mm-dd format

**equipmentId** - positive integer

**quantity** - positive integer

## Request examples

**Is available**

http://localhost:8123/?action=isAvailable&equipmentId=1&quantity=2&start=2019-05-30&end=2019-06-03

**Response**

```
{
    "result": true
}
```

___

**Get shortages**

http://localhost:8123/?action=getShortages&start=2019-05-30&end=2019-06-03

```
{
    "result": {
        "14": "-2",
        "18": "-1",
        "27": "-3"
    }
}
```
___


**Get Ice cream** (wrong input)

http://localhost:8123/?action=getIceCream&start=2019-05-30&end=2019-06-03

```
{
    "error": [
        {
            "code": 100500,
            "message": "Unsupported action 'getIceCream'"
        }
    ]
}
```

## Database optimisation

To make things faster it's good idea to add those indexes:

```
create index planning_end_index
	on planning (end);

create index planning_equipment_index
	on planning (equipment);

create index planning_start_index
	on planning (start);
```


