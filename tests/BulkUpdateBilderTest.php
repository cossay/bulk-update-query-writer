<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use Cosman\Query\Builder\BulkUpdateBuilder;

/**
 *
 * @author cosman
 *        
 */
class BulkUpdateBilderTest extends TestCase
{

    /**
     *
     * @var BulkUpdateBuilder
     */
    protected $builder;

    /**
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp()
    {
        $pdo = new PDO('mysql:dbname=databasename;host=localhost', 'username', 'password');
        
        $this->builder = new BulkUpdateBuilder($pdo);
    }

    /**
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    public function tearDown()
    {
        $this->builder = null;
    }

    /**
     * Data provider
     */
    public function queryDataProvider()
    {
        $data = [];
        
        $data[] = array(
            'users',
            'id',
            array(
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
            ),
            '',
            "UPDATE `users` SET `name` = (CASE WHEN `id` = '1' THEN 'Cosman'  WHEN `id` = '2' THEN 'Newton'  ELSE `name` END), `age` = (CASE WHEN `id` = '1' THEN '32'  WHEN `id` = '2' THEN '13'  ELSE `age` END), `salary` = (CASE WHEN `id` = '1' THEN '5000'  WHEN `id` = '2' THEN '14000'  ELSE `salary` END) WHERE `id` IN ('1', '2')"
        );
        
        $data[] = array(
            'users',
            'id',
            array(
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
                ),
                array(
                    'id' => 3,
                    'name' => 'Hayford',
                    'age' => 33,
                    'salary' => 1400
                )
            ),
            'age > 13',
            "UPDATE `users` SET `name` = (CASE WHEN `id` = '1' THEN 'Cosman'  WHEN `id` = '2' THEN 'Newton'  WHEN `id` = '3' THEN 'Hayford'  ELSE `name` END), `age` = (CASE WHEN `id` = '1' THEN '32'  WHEN `id` = '2' THEN '13'  WHEN `id` = '3' THEN '33'  ELSE `age` END), `salary` = (CASE WHEN `id` = '1' THEN '5000'  WHEN `id` = '2' THEN '14000'  WHEN `id` = '3' THEN '1400'  ELSE `salary` END) WHERE age > 13 AND`id` IN ('1', '2', '3')"
        );
        
        return $data;
    }

    /**
     * @dataProvider queryDataProvider
     */
    public function testBuild(string $table, string $caseField, array $data, string $condition, string $query)
    {
        
        $generatedQuery = '';
        
        $this->assertEmpty($generatedQuery);
        
        $generatedQuery = $this->builder->build($table, $caseField, $data, $condition);
        
        $this->assertEquals($query, $generatedQuery);
    }
}
