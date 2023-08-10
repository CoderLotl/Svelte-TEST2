<?php
namespace App\Model\Utilities;

class Log
{
    public static function WriteLog($path, $content)
    {
        $file = fopen($path, 'a+');
        fwrite($file, $content . "\n");
        fclose($file);
        if(file_exists($path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>