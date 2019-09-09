<?php 
namespace App\Saveme;

use Exception;

use Seriti\Tools\Amazon;
use Seriti\Tools\Calc;
use Seriti\Tools\Form;
use Seriti\Tools\Validate;
use Seriti\Tools\Secure;
use Seriti\Tools\Image;
use Seriti\Tools\DbInterface;
use Seriti\Tools\IconsClassesLinks;
use Seriti\Tools\ContainerHelpers;
use Seriti\Tools\MessageHelpers;
use Seriti\Tools\TableStructures;
use Seriti\Tools\STORAGE;
use Seriti\Tools\BASE_UPLOAD;
use Seriti\Tools\UPLOAD_DOCS;

use Psr\Container\ContainerInterface;

class Search
{
    use IconsClassesLinks;
    use ContainerHelpers;
    use TableStructures;
    use MessageHelpers;

    protected $errors = array();
    protected $errors_found = false; 
    protected $messages = array();

    protected $container_allow = ['s3','mail','user','system'];
    protected $container;
    protected $db;

    public $search_text = '';
    public $search_type = 'STD';

    public function __construct(DbInterface $db, ContainerInterface $container)
    {
        $this->db = $db;
        $this->container = $container;

        //$this->setup($param);
    }

    public function process()
    {
        $html = '';
        $error = '';
        $result = '';
        $count = 0;
        
        $this->search_type = 'STD';

        //only used for local storage
        $upload_dir = BASE_UPLOAD.UPLOAD_DOCS;

        $this->search_text = $_GET['search_text'];
        Validate::text('Search text',1,64,$this->search_text,$error);
        if($error !== '') $this->addError($error);

        if(isset($_GET['search_type'])) {
            $this->search_type = Secure::clean('alpha',$_GET['search_type']);
        } else {
            $this->search_type = 'STD';
        }
        if($this->search_type !== 'STD' and $this->search_type !== 'BOOL') $this->search_type = 'STD';
        
        if(!$this->errors_found) {
            //search all files
            $sql = 'SELECT file_id,file_name_orig,file_name_tn,file_date,file_size,file_type FROM '.TABLE_PREFIX.'files ';
            if($this->search_type === 'STD') {
                $sql .= 'WHERE file_name_orig LIKE "%'.$this->db->escapeSql($this->search_text).'%" OR '.
                              'key_words LIKE "%'.$this->db->escapeSql($this->search_text).'%" ';
            } else {
                $sql .= 'WHERE MATCH(key_words) AGAINST("'.$this->db->escapeSql($this->search_text).'" IN BOOLEAN MODE) ';  
            }                
            $sql .= 'ORDER BY file_name_orig LIMIT 100 ';
            $files = $this->db->readSqlArray($sql);
            if($files != 0) {
                $count += count($files);

                if(STORAGE === 'amazon') $s3 = $this->getContainer('s3');
            }    
            
            //search texts
            $sql='SELECT text_id,name,text_date FROM '.TABLE_PREFIX.'texts ';
            if($this->search_type === 'STD') {
                $sql .= 'WHERE name LIKE "%'.$this->db->escapeSql($this->search_text).'%" OR '.
                              'key_words LIKE "%'.$this->db->escapeSql($this->search_text).'%" ';
            } else {
                $sql .= 'WHERE MATCH(key_words) AGAINST("'.$this->db->escapeSql($this->search_text).'" IN BOOLEAN MODE) ';  
            }                
            $sql .= 'ORDER BY name LIMIT 100 ';
            $texts = $this->db->readSqlArray($sql);
            if($texts != 0) $count += count($texts);
             
            //search logins
            $sql = 'SELECT login_id,name,url,login_date FROM '.TABLE_PREFIX.'logins ';
            if($this->search_type === 'STD') {
                $sql .='WHERE name LIKE "%'.$this->db->escapeSql($this->search_text).'%" OR '.
                             'url LIKE "%'.$this->db->escapeSql($this->search_text).'%" OR '.
                             'key_words LIKE "%'.$this->db->escapeSql($this->search_text).'%" ';
            } else {
                $sql  .= 'WHERE MATCH(key_words) AGAINST("'.$this->db->escapeSql($this->search_text).'" IN BOOLEAN MODE) ';  
            }                
            $sql .= 'ORDER BY name LIMIT 100 ';
            $logins = $this->db->readSqlArray($sql);
            if($logins != 0) $count += count($logins);
        }


        //slap together html for search results display
        if($logins!=0) {   
            $result_html .= '<h2>All Logins</h2><ul>';
            
            foreach($logins as $login_id => $login) {
                $link = '<a href="login?mode=view&id='.$login_id.'" >'.$login['name'].' : created['.$login['login_date'].']';      
                $result_html .= '<li>'.$link.'</a></li>';
            }  
            $result_html .= '</ul>';
        }

        if($texts != 0) {   
            $result_html .= '<h2>All Text notes</h2><ul>';
            
            foreach($texts as $text_id =>$text) {
                $link = '<a href="text?mode=view&id='.$text_id.'" >'.$text['name'].' : created['.$text['text_date'].']';      
                $result_html .= '<li>'.$link.'</a></li>';
            }  
            $result_html .= '</ul>';
        }

        if($files != 0) {
            $result_html .= '<h2>All Files</h2><ul>';
                
            foreach($files as $file_id => $file) {
                if($file['file_type'] === 'Images') {
                    $image_link = '<a href="file?mode=view_image&id='.$file_id.'">';
                    if($file['file_name_tn'] != '') {
                        if(STORAGE === 'amazon') {
                            $url = $s3->getS3Url($file['file_name_tn']);
                        }    
                        if(STORAGE === 'local') {
                            $path = $upload_dir.$file['file_name_tn'];
                            $url = Image::getImage('SRC',$path,$error);
                        }    
                            
                        $link =$image_link.'<img src="'.$url.'" border="0" height="60">';    
                    } else { 
                        $link = $image_link;
                    }
                } else {
                    $link = '<a id="file'.$file_id.'" href="file?mode=download&id='.$file_id.'" onclick="link_download(\'file'.$file_id.'\')">'.
                            '<img src="'.BASE_URL.'images/disk.png" border="0" title="Download document">';  
                }  
                $link .= $file['file_name_orig'].' : uploaded['.$file['file_date'].'] & Size['.Calc::displayBytes($file['file_size'],1).']';      
                $result_html .= '<li>'.$link.'</a></li>';
            } 
            $result_html .= '</ul>';  
        }
            
        if($count === 0) $result_html .= '<h2>NO data found that matches your search terms! Please broaden your search</h2>';


        $html .= $this->viewMessages();
        $html .= $this->viewSearch();
        $html .= $result_html;

        return $html;
    } 

    protected function viewSearch() 
    {
        $html = '';

        $html .= '<div id="search_div">';

        $html .= '<form method="get" id="full_search" name="full_search" action="search" style="display:inline">'.
                    '&nbsp;<input type="submit" class="btn btn-primary" value="Search">&nbsp;'.
                    '<input type="text" name="search_text" value="'.$this->search_text.'">'.
                    '<input type="hidden" name="mode" value="search">';

        if($this->search_type === 'STD') $check = 'checked'; else $check = '';
        $html .=    '<input type="radio" name="search_type" value="STD" '.$check.'>Standard text in name or keywords ';
        if($this->search_type === 'BOOL') $check = 'checked'; else $check = '';
        $html .=    '<input type="radio" name="search_type" value="BOOL" '.$check.' alt="wtf">Boolean keyword search ';
        $html .=    '(<b>+</b>xyz must occur; <b>-</b>xyz must not occur; xyz<b>*</b> wildcard.)';
        $html .= '</form>';

        $html .= '</div>';

        return $html;
    }
}
?>
                                                
