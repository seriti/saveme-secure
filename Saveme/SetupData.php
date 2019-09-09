<?php
namespace App\Saveme;

use Seriti\Tools\SetupModuleData;

class SetupData extends SetupModuledata
{

    public function setupSql()
    {
        $this->tables = ['files','logins','texts'];

        $this->addCreateSql('files',
                            'CREATE TABLE `TABLE_NAME` (
                              `file_id` int(10) unsigned NOT NULL,
                              `title` varchar(255) NOT NULL,
                              `file_name` varchar(255) NOT NULL,
                              `file_name_orig` varchar(255) NOT NULL,
                              `file_text` longtext NOT NULL,
                              `file_date` date NOT NULL,
                              `location_id` varchar(64) NOT NULL,
                              `location_rank` int(11) NOT NULL,
                              `key_words` text NOT NULL,
                              `description` text NOT NULL,
                              `file_size` int(11) NOT NULL,
                              `encrypted` tinyint(1) NOT NULL,
                              `file_name_tn` varchar(255) NOT NULL,
                              `file_ext` varchar(16) NOT NULL,
                              `file_type` varchar(16) NOT NULL,
                              PRIMARY KEY (`file_id`),
                              FULLTEXT KEY `search_idx` (`key_words`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8');  

        $this->addCreateSql('logins',
                            'CREATE TABLE `TABLE_NAME` (
                              `login_id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(64) NOT NULL,
                              `url` varchar(250) NOT NULL,
                              `user_name` text NOT NULL,
                              `password` text NOT NULL,
                              `key_words` text NOT NULL,
                              `login_date` date NOT NULL,
                              PRIMARY KEY (`login_id`),
                              FULLTEXT KEY `search_idx` (`key_words`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8');

        $this->addCreateSql('texts',
                            'CREATE TABLE `TABLE_NAME` (
                              `text_id` int(11) NOT NULL AUTO_INCREMENT,
                              `name` varchar(64) NOT NULL,
                              `key_words` text NOT NULL,
                              `text` longtext NOT NULL,
                              `text_date` date NOT NULL,
                              PRIMARY KEY (`text_id`),
                              FULLTEXT KEY `search_idx` (`key_words`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8');  

       
        //initialisation
        //$this->addInitialSql('');
        

        //updates use time stamp in ['YYYY-MM-DD HH:MM'] format, must be unique and sequential
        //$this->addUpdateSql('YYYY-MM-DD HH:MM','Update TABLE_PREFIX--- SET --- "X"');
    }
}


  
?>
