<?php
    class PublishVk{
        public $token;
        public $group_id;
        public $album_id;
        public $user_id;
        public $v;
        public $date;
        public $text;
        private $uploadData;
        public function __construct(){
            define('TIMEZONE', 'Europe/Kiev');
            date_default_timezone_set(TIMEZONE);
            $this->uploadData = '';
            $this->date = strtotime(date("Y-m-d H:m:s")); ;
        }
        public function UploadPhoto($files = []){
            $i = 0;
            foreach($files as $file){
                if($i > 5){
                   return $this->WallPost();
                }
                $file = new CURLFile(realpath($file));
                $post_data = array("photo" => $file);
                $url = json_decode(file_get_contents("https://api.vk.com/method/photos.getWallUploadServer?album_id=".$this->album_id."&group_id=".$this->group_id."&v=".$this->v."&access_token=".$this->token),true);
                if(isset($url['error'])){
                    return 'Error kode: '.$url['error']['error_code'].'<br>'.$url['error']['[error_msg]'];
                }
                $url = $url['response']['upload_url'];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $result  = json_decode(curl_exec($ch),true);
                PublishVk::SavePhoto($result);
                $i++;
            }
            return $this->WallPost();
        }
        
        protected function SavePhoto($params = []){
            $params = array(
                'access_token' => $this->token,
                'album_id'     => $this->album_id,
                'group_id'     => $this->group_id,
                'server'       => $params['server'],
                'photo'        => $params['photo'],
                'hash'         => $params['hash'],
                'v'            => $this->v,
            );
            $safe = json_decode(file_get_contents('https://api.vk.com/method/photos.saveWallPhoto' . '?' .http_build_query($params)), true);
            if(isset($safe['error'])){
                echo 'Error kode: '.$safe['error']['error_code'].'<br>'.$safe['error']['error_msg'];
                die;
            }
            $this->uploadData .= 'photo'.$safe['response'][0]['owner_id'].'_'.$safe['response'][0]['id'].',';
            return true;
        }
        
        public function UploadDocument($files = []){
            $i = 0;
            foreach($files as $file){
                 if($i > 5){
                   return $this->WallPost();
                }
                $name = basename($file);
                $file = new CURLFile(realpath($file));
                $post_data = array("file" => $file);
                $url = json_decode(file_get_contents("https://api.vk.com/method/docs.getUploadServer?group_id=".$this->group_id."&v=".$this->v."&access_token=".$this->token),true);
                if(isset($url['error'])){
                    return 'Error kode: '.$url['error']['error_code'].'<br>'.$url['error']['error_msg'];
                }
                $url = $url['response']['upload_url'];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '1');
                curl_setopt($ch, CURLOPT_HTTPHEADER , ['Content-type: multipart/form-data']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $result  = json_decode(curl_exec($ch),true);
                if(isset($result['error'])){
                    die($result['error']);
                }
                $result['name'] = $name;
                PublishVk::SaveDocs($result);
                $i++;
            }
            return $this->WallPost();
        }
        
        protected function SaveDocs($params){
            $params = array(
                'access_token' => $this->token,
                'file'         => $params['file'],
                'title'        => $params['name'],
                'tags'         => 'DronBoy',
                'v'            => $this->v,
            );
            $safe = json_decode(file_get_contents('https://api.vk.com/method/docs.save' . '?' .http_build_query($params)), true);
            if(isset($safe['error'])){
                echo 'Error kode: '.$safe['error']['error_code'].'<br>'.$safe['error']['error_msg'];
                die;
            }
            $this->uploadData .= 'doc'.$safe['response'][0]['owner_id'].'_'.$safe['response'][0]['id'].',';
        }
        
        public function WallPost(){
            $params = array(
                'access_token' => $this->token,
                'attachments'  => $this->uploadData,
                'owner_id'     => '-'.$this->group_id,
                'v'            => $this->v,
                //'publish_date' => $this->date,
                'message'      => $this->text,
            );
            $res = json_decode(file_get_contents("https://api.vk.com/method/wall.post?".http_build_query($params)),true);
            if(isset($res['error'])){
                return $res['error']['error_code'].'<br>'.$res['error']['error_msg'];
            }else{
                return $res['response']['post_id'];  
            }
        }
    }
?>