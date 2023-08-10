<?php
namespace App\Model\Classes;

use Exception;
use PDO;
use App\Model\Utilities\Log;

class DataAccess
{
    /**
     * Finds if there's any occurrence where the given columns and values match at the given table.
     * Receives 2 arrays with the params of columns and values to search, the table, and the DB path.
     * @param string $table String containing the table's name.
     * @param mixed $columns Array with the names of the columns.
     * @param mixed $values Array with the values to search.
     * @param string $path The path to the DB.
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
            foreach ($columns as $column)
            {
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

    public function Update($table, $columns, $values, $conditions, $path)
    {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try
        {
            if (!is_array($columns) || !is_array($values) || count($columns) !== count($values))
            {
                throw new Exception("Invalid input: Columns and values must be arrays, and be of the same length.");
            }
    
            $setClauses = [];
            foreach ($columns as $column)
            {
                $setClauses[] = $column . ' = :' . $column;
            }
    
            $setClause = implode(', ', $setClauses);
    
            $whereClauses = [];
            foreach ($conditions as $conditionColumn => $conditionValue)
            {
                $whereClauses[] = $conditionColumn . ' = :' . $conditionColumn . '_condition';
            }
    
            $whereClause = implode(' AND ', $whereClauses);
    
            $statement = $pdo->prepare("UPDATE $table SET $setClause WHERE $whereClause");
    
            foreach ($columns as $index => $column)
            {
                $statement->bindParam(':' . $column, $values[$index], PDO::PARAM_STR);
            }
            
            foreach ($conditions as $conditionColumn => $conditionValue)
            {
                $statement->bindParam(':' . $conditionColumn, $conditionValue, PDO::PARAM_STR);
            }
            
            $statement->execute();
    
            $rowCount = $statement->rowCount();
            return $rowCount > 0;
        }        
        catch(Exception $e)
        {
            die($e);
        }
    }

    /**
     * Gets a single column from a given table where the given columns and values match.
     * Receives 2 arrays with the params of columns and values to search, the table, and the DB path.     
     * @param string $table
     * @param string $fromColumn Column to search for.
     * @param mixed $whereColumns Array with the names of the columns.
     * @param mixed $whereValues Array with the values to search.
     * @param string $path The path to the DB.
     * 
     * @return string|false Returns either a string with the found values or false.
     */
    public function GetSingleColumn($table, $fromColumn, $whereColumns, $whereValues, $path)
    {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
        try
        {
            if (!is_array($whereColumns) || !is_array($whereValues) || count($whereColumns) !== count($whereValues))
            {
                throw new Exception("Invalid input: Columns and values must be arrays, and be of the same length.");
            }

            $whereClauses = [];
            foreach ($whereColumns as $column)
            {
                $whereClauses[] = $column . ' = :' . $column;
            }

            $whereClause = implode(' AND ', $whereClauses);

            $statement = $pdo->prepare("SELECT $fromColumn FROM $table WHERE $whereClause");

            foreach ($whereColumns as $index => $column)
            {
                $statement->bindParam(':' . $column, $whereValues[$index], PDO::PARAM_STR);
            }

            $statement->execute();
            $resultRaw = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if($resultRaw)
            {
                $result = '';
    
                for($i = 0; $i < count($resultRaw); $i++)
                {
                    $result .= $resultRaw[$i][$fromColumn];
                    if( ($i + 1) < count($resultRaw))
                    {
                        $result .= ', ';
                    }
                } 
    
                return $result;
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