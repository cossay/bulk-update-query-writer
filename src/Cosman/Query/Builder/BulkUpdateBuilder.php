<?php
declare(strict_types = 1);
namespace Cosman\Query\Builder;

use PDO;

/**
 *
 * @author cosman
 *        
 */
class BulkUpdateBuilder
{

    /**
     *
     * @var PDO
     */
    protected $pdo;

    /**
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Quotes a database field
     *
     * @param string $field
     * @param string $separator
     * @return string
     */
    public function quoteField(string $field, string $separator = '.'): string
    {
        $components = [];
        
        foreach (explode($separator, $field) as $component) {
            $components[] = '`' . trim($component) . '`';
        }
        
        return implode($separator, $components);
    }

    /**
     * Quotes a value using the underlying PDO driver
     *
     * @param mixed $value
     * @return string
     */
    public function quoteValue($value): string
    {
        return $this->pdo->quote((string) $value);
    }

    /**
     * Generates final update query
     *
     * @param string $table
     * @param string $caseField
     * @param array $values
     * @param string $condition
     * @return string
     */
    public function build(string $table, string $caseField, array $values, string $condition = ''): string
    {
        $whereIn = [];
        
        $query = sprintf('UPDATE %s SET ', $this->quoteField($table));
        
        if ($condition) {
            $condition .= ' AND';
        }
        
        $cases = [];
        
        foreach ($values as $value) {
            
            if (is_array($value) && isset($value[$caseField])) {
                
                $whereIn[] = $this->quoteValue($value[$caseField]);
                
                foreach ($value as $field => $fieldValue) {
                    if ($field != $caseField) {
                        if (! array_key_exists($field, $cases)) {
                            $cases[$field] = [];
                        }
                        
                        $cases[$field][] = sprintf('WHEN %s = %s THEN %s ', $this->quoteField($caseField), $this->quoteValue($value[$caseField]), $this->quoteValue($fieldValue));
                    }
                }
            }
        }
        
        $caseStatements = [];
        
        foreach ($cases as $field => $subStatement) {
            $caseStatements[] = sprintf('%s = (CASE %s ELSE %s END)', $this->quoteField($field), implode(' ', $subStatement), $this->quoteField($field));
        }
        
        $query .= sprintf('%s WHERE %s%s IN (%s)', implode(', ', $caseStatements), $condition, $this->quoteField($caseField), implode(', ', $whereIn));
        
        unset($cases, $whereIn, $caseStatements);
        
        return $query;
    }

    /**
     * Creates an instance of this query builder
     *
     * @param PDO $pdo
     * @return self
     */
    public static function createInstance(PDO $pdo): self
    {
        return new static($pdo);
    }
}