<?php 
namespace App\Saveme;

use Seriti\Tools\Date;
use Seriti\Tools\Form;
use Seriti\Tools\Doc;
use Seriti\Tools\Calc;
use Seriti\Tools\Secure;
use Seriti\Tools\DbInterface;
use Seriti\Tools\IconsClassesLinks;
use Seriti\Tools\MessageHelpers;
use Seriti\Tools\ContainerHelpers;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_TEMP;

use App\Saveme\Helpers;

use Psr\Container\ContainerInterface;

//NB: legacy code, does not use Wizard class
class ImportWizard
{
    use IconsClassesLinks;
    use MessageHelpers;
    use ContainerHelpers;
   
    protected $container;
    protected $container_allow = ['system','user'];

    protected $db;
    protected $debug = false;

    protected $mode = '';
    protected $errors = array();
    protected $errors_found = false; 
    protected $messages = array();

    protected $user_id;
    protected $encrypt_key;

    protected $file_formats = array('LOGINS'=>'Logins CSV format','NOTES'=>'Text notes CSV format');
    protected $upload_dir = BASE_UPLOAD.UPLOAD_TEMP;
    protected $max_size = 1000000;

    public function __construct(DbInterface $db, ContainerInterface $container) 
    {
        $this->db = $db;
        $this->container = $container;
               
        if(defined('\Seriti\Tools\DEBUG')) $this->debug = \Seriti\Tools\DEBUG;

        $system = $this->getContainer('system');
        $this->encrypt_key = $system->configureEncryption(['redirect'=>'/admin/encrypt']);
    }

    public function process()
    {
        $html = '';
        $error = '';
        $count = 0;
        $update_contact = false;

        $this->mode = 'select_file';
        if(isset($_GET['mode'])) $this->mode = Secure::clean('basic',$_GET['mode']);

        if($this->mode === 'import') {

            $file_format = Secure::clean('alpha',$_POST['file_format']);
            if(!array_key_exists($file_format,$this->file_formats)) {
                $this->addError('Selected file format['.$file_format.'] INVALID!');
            }  
            
            if(isset($_POST['update_data']) and $_POST['update_data'] === 'YES') {
                $update_data = true;
            } else {
                $update_data = false;
            }  
              
              
            $file_options = array();
            $file_options['upload_dir'] = $this->upload_dir;
            $file_options['allow_ext'] = array('csv','txt');
            $file_options['max_size'] = $max_size;
            $save_name = 'import_data';
            $file_name = Form::uploadFile('import_file',$save_name,$file_options,$error);
            if($error !== '') {
                if($error !== 'NO_FILE') {
                    $this->addError('Import file: '.$error);
                } else {
                    $this->addError('NO file selected for import! Please click [Browse] button and select a valid format file');
                }    
            } else {
                $import_file_path = $this->upload_dir.$file_name;
                if(!file_exists($import_file_path)) {
                    $this->addError('Import File['.$import_file_path.'] does not exist');
                }   
            }  

            if($this->errors_found) {
                $this->mode = 'select_file'; 
            } else {
                //Convert file encoding to UTF8
                /*
                $contents = file_get_contents($import_file_path);
                $encoding = 'ISO-8859-1'; 
                if($file_format === 'LOGINS') $encoding = 'ISO-8859-1';
                if($file_format === 'NOTES') $encoding = 'UTF-8'; 
                $contents = mb_convert_encoding($contents,'UTF-8',$encoding); 
                file_put_contents($import_file_path,$contents);
                */

                $handle = fopen($import_file_path,'r');
                $error_file = false;
                $i = 0;
                $insert = 0;
                $found = 0;
                $update = 0;
                while(($line = fgetcsv($handle,0,",",'"')) !== FALSE) {
                    $i++;
                    $value_num = count($line);
                  
                    if($i == 1) {
                        Helpers::checkImportFormat($file_format,$line,$error);
                        
                        if($error !== '') {
                            $this->addError($file_format." file format errors:\r\n".$error);
                            $error_file = true;
                        }  
                    } 
                  
                    if(!$error_file and $i > 1 and $value_num > 3) {  
                        $status = Helpers::importData($this->db,$update_data,$file_format,$this->encrypt_key,$line,$error);
                        
                        if($error !== '') {
                            $this->addError($error);
                        } else {
                            if(substr($status,0,5) === 'FOUND') $found++;  
                            if($status === 'FOUND_UPDATE') $update++;
                            if($status === 'NEW') $insert++;
                        }    
                    }
                }  
                fclose($handle);
                
                $this->addMessage('Imported <strong>'.$insert.'</strong> NEW '.$file_format);
                $this->addMessage('Found <strong>'.$found.'</strong> Existing/Duplicate '.$file_format.', '.
                                  'updated <strong>'.$update.'</strong> '.$file_format.'');
            }  
        }

        if($this->mode === 'select_file') {
            $html .= '<div id="edit_div">'.
                     '<form method="post" id="import_csv_file" action="?mode=import" enctype="multipart/form-data">';
    
            $html .= '<div class="row">';
            $list_param = [];
            $list_param['class'] = 'form-control edit_input';     
            $html .= '<div class="'.$this->classes['col_label'].'">Select file format:</div><div class="col-sm-6">'.
                     Form::arrayList($this->file_formats,'file_format',$file_format,true,$list_param).
                     '</div>';
            $html .= '</div>';
    
            $html .= '<div class="row">';
            $html .= '<div class="'.$this->classes['col_label'].'">Select file:</div><div class="col-sm-6">'.
                     Form::fileInput('import_file','',$list_param).
                     '</div>';     
            $html .= '</div>';
    
            $html .= '<div class="row">';
            $html .= '<div class="'.$this->classes['col_label'].'">Update matching records?:</div><div class="col-sm-6">'.
                     Form::checkBox('update_data','YES',$update_data,'edit_input').
                     '</div>';     
            $html .= '</div>';
    
            $html .= '<div class="row">';
            $html .= '<div class="col-sm-6"><input type="submit" class="btn btn-primary" value="Import selected contact file"></div>';
            $html .= '</div>';
    
            $html .= '</form></div>';
        }

        $html = $this->viewMessages().$html;
            
        return $html;
    }
}
?>