<?php
namespace App\Model\Classes;

use Exception;
use PDO;

class DataAccess
{
    /**
     * Receives 2 arrays with the params of columns and values to search, the table, and the DB path.
     * @param mixed $table String containing the table's name.
     * @param mixed $columns Array with the names of the columns.
     * @param mixed $values Array with the values to search.
     * @param mixed $path The path to the DB.
     * 
     * @return bool
     */
    public function Find($table, $columns, $values, $path)
    {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try
        {
            if (!is_array($columns) || !is_array($values) || count($columns) !== count($values))
            {
                throw new Exception("Invalid input: Columns and values must be arrays, and be of the same length.");
            }

            $whereClauses = [];
            foreach ($columns as $column) {
                $whereClauses[] = $column . ' = :' . $column;
            }

            $whereClause = implode(' AND ', $whereClauses);

            $statement = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $whereClause");

            foreach ($columns as $index => $column)
            {
                $statement->bindParam(':' . $column, $values[$index], PDO::PARAM_STR);
            }
            
            $statement->execute();

            if($statement->fetchColumn() > 0)
            {                
                return true;
            }
            else
            {
                return false;
            }            
        }        
        catch(Exception $e)
        {
            die($e);
        }
    }
}

?>