<?php
namespace App\Saveme;

use Seriti\Tools\Dashboard AS DashboardTool;

class Dashboard extends DashboardTool
{
     

    //configure
    public function setup($param = []) 
    {
        $this->col_count = 2;  

        $login_user = $this->getContainer('user'); 

        //(block_id,col,row,title)
        $this->addBlock('ADD',1,1,'Capture new data');
        $this->addItem('ADD','Add a new Login',['link'=>"login?mode=add"]);
        $this->addItem('ADD','Add a new Note',['link'=>"text?mode=add"]);
        $this->addItem('ADD','Add new Files/Documents/Images',['link'=>"file?mode=add"]);
        
        if($login_user->getAccessLevel() === 'GOD') {
            $this->addBlock('IMPORT',1,2,'Import data');
            $this->addItem('IMPORT','Import logins and Notes',['link'=>'import','icon'=>'setup']);

            $this->addBlock('CONFIG',1,3,'Module Configuration');
            $this->addItem('CONFIG','Setup Database',['link'=>'setup_data','icon'=>'setup']);
        }    
        
    }

}

?>
