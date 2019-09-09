<?php 
namespace App\Saveme;

use Seriti\Tools\Upload;
use Seriti\Tools\Secure;

class File extends Upload 
{
  //configure
    public function setup($param = []) 
    {
        if(isset($_GET['mode'])) $mode = Secure::clean('basic',$_GET['mode']); else $mode='';
        if($mode === 'add' or $mode === 'upload' or $mode === 'download') {
            $system = $this->getContainer('system');
            $encrypt_key = $system->configureEncryption(['redirect'=>'/admin/encrypt']);
            $encrypt = true;
        } else {
            $encrypt = false;
            $encrypt_key = '';
        }

        $system = $this->getContainer('system');
        $encrypt_key = $system->configureEncryption(['redirect'=>'/admin/encrypt']);

        //NB: NO FILE PREFIX SET. All other amazon filestorage classes will need a prefix so as not to accidentally overwrite  
        $param = ['row_name'=>'File',
                  'encrypt'=>$encrypt,
                  'show_info'=>true,
                  'encrypt_key'=>$encrypt_key];
        parent::setup($param);

        $this->info['ADD'] = 'If you have Mozilla Firefox or Google Chrome you should be able to drag and drop files directly from your file explorer.'.
                             'Alternatively you can click [Add files] button to select multiple files for download using [Shift] or [Ctrl] keys. '.
                             'Finally you need to click [Upload selected files] button to upload files to server.';
        
        //NB: only need to add non-standard file cols here, or if you need to modify standard file col setup
        $this->addFileCol(array('id'=>'key_words','type'=>'TEXT','title'=>'Key words','cols'=>50,'rows'=>1,'required'=>false,'upload'=>true));

        $this->addSortOrder('file_id DESC','Upload date latest','DEFAULT');

        $this->addAction(array('type'=>'check_box','text'=>''));
        $this->addAction(array('type'=>'edit','text'=>'edit details of','icon_text'=>'edit'));
        $this->addAction(array('type'=>'delete','text'=>'delete','pos'=>'R','icon_text'=>'delete'));

        $this->addSearch(array('file_name_orig','file_date','key_words','encrypted'),array('rows'=>2));
    }
}
?>
