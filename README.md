# bulk-update-query-writer
A simple bulk update query writer

## USAGE

```php
use Cosman\Query\Builder\BulkUpdateBuilder;

$pdo = new PDO('mysql:dbname=my_database;host=localhost', 'username', 'my_password');

$builder = new BulkUpdateBuilder($pdo);

$attributes = array(
    array(
        'id' => 1,
        'name' => 'Cosman',
        'age' => 32,
        'salary' => 5000
    ),
    array(
        'id' => 2,
        'name' => 'Newton',
        'age' => 13,
        'salary' => 14000
    )
);

//WITHOUT CONDITIONS
echo $builder->build('users', 'id', $attributes);

//WITH ADDTIONAL CONDITIONS
$condition = sprintf('%s > %s', $builder->quoteField('age'), $builder->quoteValue('13'));
echo $builder->build('users', 'id', $attributes, $condition);

```
Then first ```echo``` statement will produce the following query
```sql
UPDATE
    `users`
SET
    `name` =(
        CASE WHEN `id` = '1' THEN 'Cosman' WHEN `id` = '2' THEN 'Newton' ELSE `name`
    END
),
`age` =(
    CASE WHEN `id` = '1' THEN '32' WHEN `id` = '2' THEN '13' ELSE `age`
END
),
`salary` =(
    CASE WHEN `id` = '1' THEN '5000' WHEN `id` = '2' THEN '14000' ELSE `salary`
END
)
WHERE
    `id` IN('1', '2')
```
The second ```echo``` statement will produce
```sql
UPDATE
    `users`
SET
    `name` =(
        CASE WHEN `id` = '1' THEN 'Cosman' WHEN `id` = '2' THEN 'Newton' ELSE `name`
    END
),
`age` =(
    CASE WHEN `id` = '1' THEN '32' WHEN `id` = '2' THEN '13' ELSE `age`
END
),
`salary` =(
    CASE WHEN `id` = '1' THEN '5000' WHEN `id` = '2' THEN '14000' ELSE `salary`
END
)
WHERE
    `age` > '13' AND `id` IN('1', '2')
```
