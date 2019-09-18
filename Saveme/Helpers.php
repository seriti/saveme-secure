<?php 
namespace App\Saveme;

use Exception;
use Seriti\Tools\Csv;
use Seriti\Tools\Crypt;


//static functions for saveme module
class Helpers {
    public static function checkImportFormat($format,$line,&$error )  
    {
        $error = '';
        $found = false;
                
        if($format === 'LOGINS') {
            $found = true;
            if($line[0] !== 'Name') $error .= 'First column header['.$line[0].'] NOT = "Name"<br/>';  
            if($line[1] !== 'URL') $error .= 'Second column header['.$line[1].'] NOT = "URL"<br/>'; 
            if($line[2] !== 'User') $error .= 'Third column header['.$line[2].'] NOT = "User"<br/>'; 
            if($line[3] !== 'Password') $error .= 'Fourth column header['.$line[3].'] NOT = "Password"<br/>'; 
            if($line[4] !== 'Date') $error .= 'Fourth column header['.$line[4].'] NOT = "Date"<br/>'; 
            if($line[5] !== 'Keywords') $error .= 'Fourth column header['.$line[5].'] NOT = "Keywords"<br/>'; 
        } 
        
        if($format === 'NOTES') {
            $found = true;
            if($line[0] !== 'Title') $error .= 'First column header['.$line[0].'] NOT = "Title"<br/>';  
            if($line[1] !== 'Date') $error .= 'Second column header['.$line[1].'] NOT = "Date"<br/>'; 
            if($line[2] !== 'Keywords') $error .= 'Third column header['.$line[2].'] NOT = "Keywords"<br/>'; 
            if($line[3] !== 'Text') $error .= 'Fourth column header['.$line[3].'] NOT = "Text"<br/>'; 
        } 
          
        if(!$found) $error .= 'Format['.$format.'] NOT supported!';

        if($error === '') return true; else return false;  
    }

    public static function importData($db,$update = false,$format,$encrypt_key,$line,&$error) 
    {
        $error_tmp = '';
        $error = '';
        $data = [];
        $status = 'NONE';

        if($encrypt_key == '') $error .= 'INVALID encryption key!';
        
        if($format === 'LOGINS') {
            $table_name = TABLE_PREFIX.'logins';
            $key = 'login_id';
            
            $data['name'] = Csv::csvStrip($line[0]);
            $data['url'] = Csv::csvStrip($line[1]);
            $data['user_name'] = Csv::csvStrip($line[2]);
            $data['password'] = Csv::csvStrip($line[3]);
            $data['login_date'] = Csv::csvStrip($line[4]);
            $data['key_words'] = Csv::csvStrip($line[5]);

            $data['user_name'] = Crypt::encryptText($data['user_name'],$encrypt_key);
            $data['password'] = Crypt::encryptText($data['password'],$encrypt_key);

            $unique_sql = 'name = "'.$db->escapeSql($data['name']).'" AND login_date = "'.$db->escapeSql($data['login_date']).'" ';
        }

        if($format === 'NOTES') {
            $table_name = TABLE_PREFIX.'texts';
            $key = 'text_id';

            $data['name'] = Csv::csvStrip($line[0]);
            $data['text_date'] = Csv::csvStrip($line[1]);
            $data['key_words'] = Csv::csvStrip($line[2]);
            $data['text'] = Csv::csvStrip($line[3]);

            $data['text'] = Crypt::encryptText($data['text'],$encrypt_key);

            $unique_sql = 'name = "'.$db->escapeSql($data['name']).'" AND text_date = "'.$db->escapeSql($data['text_date']).'" ';
        } 
        
         
        
        
        if($error === '') {
            $found = false;
            
            //check for existing data
            $sql = 'SELECT * FROM '.$table_name.' WHERE '. $unique_sql.' ';
            $record = $db->readSqlRecord($sql);  
            if($recorder != 0) {
                $found = true;
                $status = 'FOUND';
            }    
    
            
            if($found) {
                if($update) {
                    $where = [$key => $record[$key]];
                    
                    $db->updateRecord($table_name,$data,$where,$error_tmp);
                    if($error_tmp !== '') {
                        $error .= 'Could NOT update record:'.$error_tmp; 
                    } else {
                        $status = 'FOUND_UPDATE';
                    }    
                }  
            } else {  
                $db->insertRecord($table_name,$data,$error_tmp);
                if($error_tmp !== '') {
                    $error .= 'Could NOT create record:'.$error_tmp; 
                } else {
                    $status = 'NEW';
                }    
            }             
        }  
        
        if($error !== '') return false; else return $status; 
    }
    
    
}


?>
