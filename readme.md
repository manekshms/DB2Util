# DB2Util
Database utility and query builder for db2 

## Table of Contents
* [Requirements](#requirements)
* [Basic Usage](#basic-usage)
* [Configuration and Connection](#configuration-and-connection)
* [Select data](#select-data)
* [Insert data](#insert-data)
* [Update data](#update-data)
* [Delete data](#delete-data)
* [Get Last sql query](#get-last-sql-query)
* [Complex query](#for-complex-query)

## Requirements
* php >= 5.4
* enable php extension [pdo_ibm](https://www.ibm.com/support/knowledgecenter/no/SSEPGG_9.7.0/com.ibm.swg.im.dbclient.php.doc/doc/t0011926.html)

## Installation

Install latest version with

```bash
composer require manekshms/db2util
```   



## Basic Usage 

### Configuration and connection

```php
    $config = [];
    $config['dsn'] = 'ibm:dbname';
    $config['username'] = 'db2admin';
    $config['password'] = 'db2admin';
    $db2Util = new DB2Util\DB2Util($config);
    $db2Util->connect();
```

### Select data 

#### Select all data from table
$db2Util->getQueryBuilder()->table('table_name')->get();

#### Select data with where condition
$db2Util->getQueryBuilder()->table('table_name')->where('column-name' , 'column-value')->get();
Example:  

```php
$db2Util->getQueryBuilder()->table('table_name')->where('name' , 'bob')->get();
```

#### Select data with multiple column where condition
$db2Util->getQueryBuilder()->table('table_name')->where(['column-name' => 'column-value'])->get();
Example : 
```php
$db2Util->getQueryBuilder()->table('table_name')->where(['name' => 'bob', 'age' => 40])->get();
```
**Select with where conditions and operators**  
$db2Util->getQueryBuilder()->table('table_name')->where('column-name', 'operators', 'values')->get();  
Example: 
```php
$db2Util->getQueryBuilder()->table('table_name')->where('AGE', '>=', 40)->get();
```

**Select with where conditions for multiple column check**  
$db2Util->getQueryBuilder()->table('user')->where([ array of column name operator and value ])->get(); 

Example:
```php
$db2Util->getQueryBuilder()->table('user')->where([ ['AGE', '>=', 40], ['NAME', '=', 'bob'] ])->get(); 
```
**OR**
```php
$db2Util->getQueryBuilder()->table('user')->where([ ['AGE', '>=', 40], ['NAME', 'bob'] ])->get(); 
```

**Select with in operator**  
$db2Util->getQueryBuilder()->table('user')->where('column-name', 'in', [array of data])->get();

Example:
```php
$db2Util->getQueryBuilder()->table('user')->where('AGE', 'in', [40, 30])->get();
```

**Select With Limit**  
$db2Util->getQueryBuilder()->table('table name')->limit(number of records)->get(); 
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->limit(3)->get(); 
```

**Select with Limit and Offset**  
$db2Util->getQueryBuilder()->table('table naem')->limit(limit number)->offset( offset number)->get();  
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->limit(2)->offset(2)->get();  
```

**Select with Like Operator**  

$db2Util->getQueryBuilder()->table('table name')->where('column', 'like', '%expected value%')->get();  
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->where('name', 'like', '%doe%')->get();  
```  
**Select with Join**  

$db2Util->getQueryBuilder()->table('table name')->join('JOIN TABLE NAME', 'table column ', '=', 'table column')->get();  
Example:  

```php
    $db2Util->getQueryBuilder()
            ->table('user_PRODUCT AS USER_PRODUCT')
            ->select(['USER_PRODUCT.ID', 'USER.NAME', 'PRODUCT.PRODUCT_NAME', 'PRODUCT.PRICE'])
            ->join('user AS USER ', 'USER_PRODUCT.USER_ID', '=', 'USER.ID')
            ->join('PHPUNIT_TEST_PRODUCT AS PRODUCT', 'USER_PRODUCT.PRODUCT_ID', '=', 'PRODUCT.ID')
            ->get();
```
Generated Sql :  

```sql

SELECT USER_PRODUCT.ID, USER.NAME, PRODUCT.PRODUCT_NAME, PRODUCT.PRICE FROM user_PRODUCT AS USER_PRODUCT INNER JOIN  user AS USER  ON ( USER_PRODUCT.USER_ID = USER.ID )  INNER JOIN  PHPUNIT_TEST_PRODUCT AS PRODUCT ON ( USER_PRODUCT.PRODUCT_ID = PRODUCT.ID )

```
**Other join Methods**
* leftJoin
* rightJoin
* outerJoin


**Select with nested where condition**

                $db2Util->getQueryBuilder()
                               ->table('table name')
                               ->where('column name', 'operator' 'value')
                               ->where(function($query){
                                    $query->where('column name', 'operator', 'value')
                                        ->orWhere('column name', 'operator', 'value');
                                 })
                               ->get()

  

Example :  
```php
    $db2Util->getQueryBuilder()
                    ->table('user')
                    ->where('AGE', '>=', 40)
                    ->where(function($query){
                        $query->where('EMAIL', 'like', 'm%')
                            ->orWhere('NAME', 'in', ['bob', 'boo']);
                        })
                    ->get()
```  

Above Example Will generate SQL like

```sql
SELECT * FROM user WHERE AGE >= '40'  AND  ( EMAIL like 'm%'  OR NAME in (  'bob',  'boo' )  ) 
```  

**Select with Union and unionAll**  
$db2Util->getQueryBuilder()->table('table name')->unionAll('query builder instance')->get();  

Example :  
```php
    $firstQuery = $db2Util->getQueryBuilder()->table('user')->where('AGE','40');
    $unionResult = $db2Util->getQueryBuilder()->table('user')->where('NAME','like', 'b%')->unionAll($firstQuery)->get();
```

Above Example generate SQL like

```sql
( SELECT * FROM user WHERE NAME like 'b%'  )  UNION ALL (SELECT * FROM user WHERE AGE = '40'  )
```
**Select with groupby and having**

$db2Util->getQueryBuilder()->table('table name')->select('select columns')->groupBy('column')->having('column|aggrigate functions', 'operator', value)->get();

Example :  

```php
$result = $db2Util->getQueryBuilder()->table('user')->select(["count(*) as cnt", "age"])->groupBy('age')->having('count(*)', '>', 1)->get();
```

Above example will generate sql like  :  
```sql
SELECT count(*) as cnt, age FROM user GROUP BY age HAVING count(*) >  '1'
```

#### Where Helper method

**Where in**  

$db2Util->getQueryBuilder()->table('table name')->whereIn('column name', ['values'])->get()  

Example:  

```php
$db2Util->getQueryBuilder()->table('user')->whereIn('first_name', ['john', 'jack'])->get();
```
**Where not in**  

$db2Util->getQueryBuilder()->table('table name')->whereNotIn('column name', ['values'])->get()  

Example:  

```php
$db2Util->getQueryBuilder()->table('user')->whereNotIn('first_name', ['john', 'jack'])->get();
```


**Pluck**
pluck a single column values to a collection 
$db2Util->getQueryBuilder()->table('table name')->pluck('column name');

Example:  

```php
$db2Util->getQueryBuilder()->table('name')->pluck('age');
```

**Count number of records in table**  
$db2Util->getQueryBuilder()->table('table name')->count();  

Example:  

```php
$db2Util->getQueryBuilder()->table('table name')->count();
```  

**Max value of column in table**  
$db2Util->getQueryBuilder()->table('table name')->max('column name');  

Example:  

```php
$db2Util->getQueryBuilder()->table('table name')->max('age');
```
**Min value of column in table** 
$db2Util->getQueryBuilder()->table('table name')->min('column name');  

Example:  

```php
$db2Util->getQueryBuilder()->table('table name')->min('age');
``` 

### Insert data

**Insert Single row**  
$db2Util->getQueryBuilder()->table('table name')->insert(data associative array);  

Example:  
```php
$data =  [ 'NAME' => 'bob', 'AGE' => 40,'EMAIL' => 'bob@gmail.com', 'ADDRESS' =>  'bob address goes here'];
$db2Util->getQueryBuilder()->table('user')->insert($data);
```

**Insert Multiple row**
$db2Util->getQueryBuilder()->table('table name')->insert(two dimensional data associative array);  
Example:  
```php
$data = [
['NAME' => 'mikee', 'AGE' => 54,'EMAIL' => 'mikee@gmail.com', 'ADDRESS' =>  'mikee address goes here'],
['NAME' => 'mark', 'AGE' => 64,'EMAIL' => 'mark@gmail.com', 'ADDRESS' =>  'mark address goes here'],
['NAME' => 'momo', 'AGE' => 34,'EMAIL' => 'momo@gmail.com', 'ADDRESS' =>  'momo address goes here']
];
$db2Util->getQueryBuilder()->table('user')->insert($data);
```

### Update data

**Update all records**  
$db2Util->getQueryBuilder()->table('table name')->update(['column name' => 'new data']);  

Example:  
```php
$db2Util->getQueryBuilder()->table('user')->update(['EMAIL' => 'new@gamil.com']);
```

**update with where conditions**  

$db2Util->getQueryBuilder()->table('table name')->where('column name', 'data')->update(['column name' => 'new data']);  
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->where([['age', 40], ['NAME', 'bob']])->update(['EMAIL' => 'newemail@gamil.com']);
```  

**incrementing value of column**  

$db2Util->getQueryBuilder()->table('table name')->increment('column name', increment cound default 1);  

Example:  

```php
$db2Util->getQueryBuilder()->table('user')->where('ID', '1')->increment('age', 2);
```  

**decrementing value of column**  

$db2Util->getQueryBuilder()->table('table name')->decrement('column name', decrement count default 1);  

Example:  

```php
$db2Util->getQueryBuilder()->table('user')->where('ID', '1')->decrement('age');
```  

### Delete data

**Delete all data**
$db2Util->getQueryBuilder()->table('table name')->delete();  
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->delete();
```  
**Delete with where conditions**  
$db2Util->getQueryBuilder()->table('user')->where('column name', 'operant', 'value')->delete();  
Example:  
```php
$db2Util->getQueryBuilder()->table('user')->where('age', '!=', '40')->delete();
```
### Get Last sql query
$db2Util->getConnection()->getLastSQLQuery(debug true or false)
if debug is false
output will be string 

```php
$db2Util->getConnection()->getLastSQLQuery(true);
```  

if debug is true 
output will be associative array with query, params and processed query

```php
array(3) {
  'query' => " SELECT * FROM user WHERE AGE = ?  ",
  'params' =>[
    40
  ],
  'processed_query' => " SELECT * FROM user WHERE AGE = '40'  "
}
```  

### For complex query

**Execute Query**  
```php
    $sql = " INSERT INTO USER (name, age, country) VALUES ( ?, ?, ? ) ";
    $params = [
        'bob',
        20,
        'India'
    ];
   $db2Util->getConnection()->executeQuery($sql, $params); 

```  

**Query**  
```php
   $sql = " SELECT * FROM USER WHERE ID = ? ";
   $params = [20];
   $stmt = $db2Util->getConnection()->query($sql, $params); 
   $stmt->fetchAll();

```

## Author
**[Maneksh M S](http://manekshms.com) - [manekshms@gmail.com](mailto:manekshms@gmail.com)**
