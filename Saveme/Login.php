<?php 
namespace App\Saveme;

use Seriti\Tools\Table;

class Login extends Table 
{
    function afterUpdate($id,$edit_type,$form) {
        if($edit_type === 'INSERT') {
            $sql='UPDATE '.TABLE_PREFIX.'logins SET login_date = "'.date('Y-m-d').'" '.
                 'WHERE login_id = "'.$this->db->escapeSql($id).'"';
            $this->db->executeSql($sql,$error_tmp);
        }
    }  

    //configure
    public function setup($param = []) 
    {
        $system = $this->getContainer('system');
        $encrypt_key = $system->configureEncryption(['redirect'=>'/admin/encrypt']);

        $param = ['row_name'=>'Login','col_label'=>'name','encrypt_key'=>$encrypt_key];
        parent::setup($param);

        $this->info['EDIT'] = 'Assign a meaningful title and key words to login so that you will be able to find data using search facility. '.
                              'username and password are encrypted before being stored in the database.'.
                              'Finally you need to click [Submit] button to send login data to server. NB: Username & password are encrypted and not searchable';

        $this->addTableCol(array('id'=>'login_id','type'=>'INTEGER','title'=>'Login ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Login name'));
        $this->addTableCol(array('id'=>'url','type'=>'URL','size'=>32,'title'=>'Login url'));
        $this->addTableCol(array('id'=>'user_name','type'=>'STRING','size'=>32,'title'=>'User name/email','encrypt'=>true,
                                 'hint'=>'This is encrypted and not searchable'));
        $this->addTableCol(array('id'=>'password','type'=>'STRING','size'=>32,'title'=>'password','encrypt'=>true,
                                 'hint'=>'This is encrypted and not searchable'));
        $this->addTableCol(array('id'=>'login_date','type'=>'DATE','title'=>'Create date','edit'=>false));
        $this->addTableCol(array('id'=>'key_words','type'=>'TEXT','title'=>'Keywords','rows'=>3,'required'=>false,
                                 'hint'=>'Keywords are searchable and allow you to effectively group information'));

        $this->addSortorder('login_date DESC','Create date latest','DEFAULT');

        $this->addAction(array('type'=>'check_box','text'=>''));
        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        //$this->addAction(array('type'=>'view','text'=>'view','icon_text'=>'view'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSearch(array('name','url','key_words','login_date'),array('rows'=>2));
    }    
}
?>
