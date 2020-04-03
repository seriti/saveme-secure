<?php 
namespace App\Saveme;

use Seriti\Tools\Table;
use Seriti\Tools\ENCRYPT_ROUTE;

class Text extends Table 
{
    function afterUpdate($id,$edit_type,$form) {
        if($edit_type === 'INSERT') {
            $sql='UPDATE '.TABLE_PREFIX.'texts SET text_date = "'.date('Y-m-d').'" '.
                 'WHERE text_id = "'.$this->db->escapeSql($id).'"';
            $this->db->executeSql($sql,$error);
            if($error !== '') die($error);
        }
    }  

    //configure
    public function setup($param = []) 
    {
        $system = $this->getContainer('system');
        $encrypt_key = $system->configureEncryption(['redirect'=>ENCRYPT_ROUTE]);

        $param = ['row_name'=>'Note','col_label'=>'name','encrypt_key'=>$encrypt_key];
        parent::setup($param);

        $this->addMessage('To encrypt/decrypt locally with javascript use <a href="javascript:open_popup(\'jscrypt\',900,500)">Jscrypt tool</a>');  

        $this->info['EDIT']='Assign a meaningful title and key words to note so that you will be able to find data using search facility. '.
                           'All text is encrypted by default before being stored in database. If you wish to save super sensitive information '.
                           'then use the Jscrypt menu option to manually add a second layer of encryption with any key you desire and '.
                           'then cut/paste cipher text into this page. '.
                           'Finally you need to click [Submit] button to send note data to server. NB: As text is encrypted it is not searchable!';                         

        $this->addTableCol(array('id'=>'text_id','type'=>'INTEGER','title'=>'Note ID','key'=>true,'key_auto'=>true,'list'=>false));
        $this->addTableCol(array('id'=>'name','type'=>'STRING','title'=>'Note title'));
        $this->addTableCol(array('id'=>'text_date','type'=>'DATE','title'=>'Create date','edit'=>false));
        $this->addTableCol(array('id'=>'key_words','type'=>'TEXT','title'=>'Keywords','cols'=>60,'rows'=>3,'required'=>false,'hint'=>'(Keywords are searchable and allow you to effectively group information)'));
        $this->addTableCol(array('id'=>'text','type'=>'TEXT','title'=>'Text','cols'=>60,'rows'=>10,'encrypt'=>true,'hint'=>'(All this text is encrypted before being stored in database, and not searchable)'));

        $this->addSortOrder('text_date DESC','Create date latest','DEFAULT');

        $this->addAction(array('type'=>'check_box','text'=>''));
        $this->addAction(array('type'=>'edit','text'=>'edit','icon_text'=>'edit'));
        $this->addAction(array('type'=>'view','text'=>'view','icon_text'=>'view'));
        $this->addAction(array('type'=>'delete','text'=>'delete','icon_text'=>'delete','pos'=>'R'));

        $this->addSearch(array('name','key_words','text_date'),array('rows'=>2));
    }    
}
?>
