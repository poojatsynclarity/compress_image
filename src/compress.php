<?php
    class Compress {
        public $flag = true;
        public $size_flag = true;

        function compress_image_without_curl($source)
        {
            // Get image info 
            $imgInfo = getimagesize($source);
            $mime = $imgInfo['mime']; 
            // Create a new image from file 
            switch($mime){ 
                case 'image/jpeg': 
                    $image = imagecreatefromjpeg($source); 
                    break; 
                case 'image/png': 
                    $image = imagecreatefrompng($source); 
                    break; 
                default: 
                    $image = imagecreatefromjpeg($source); 
            } 
            // Save image 
            if(imagejpeg($image, $source, 92))
            {
                echo "Image compressed Successfully!";
            }
        }

        function compress_image($image_path) 
        {
            $ext = pathinfo($image_path, PATHINFO_EXTENSION);
            $arr_file_types = ['png','jpg', 'jpeg'];
            try
            {
                if (!(in_array($ext, $arr_file_types))) {
                    $this->flag = false;
                    throw new Exception("Only image is allowed!");
                }
            }
            catch (Exception $e) {
                echo 'ErrorMessage: ' .$e->getMessage(), "\n";
            }

            if (!$this->flag) {
                return;
            }

            $size = filesize($image_path);
            try
            {
                if(!($size < 5242880 ))
                {
                    $this->size_flag = false;
                    throw new Exception("The image should be less than 5MB!");
                }
            }
            catch (Exception $e) {
                echo 'ErrorMessage: ' .$e->getMessage(), "\n";
            }
            if(!$this->size_flag)
            {
                return;
            }

            if  (!in_array  ('curl', get_loaded_extensions()))  
            {
                $this->compress_image_without_curl($image_path);
                $this->flag = false;
            }
              
            if (!$this->flag) {
                return;
            }
            
            // optimize image using reSmush.it
            $file = $image_path;
            $mime = mime_content_type($file);
            $info = pathinfo($file);
            $name = $info['basename'];
            $output = new CURLFile($file, $mime, $name);
            $data = array(
                "files" => $output,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=92');
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            $result = curl_error($ch);
            }
            curl_close ($ch);
        
            $arr_result = json_decode($result);
        
            // store the optimized version of the image
            $ch = curl_init($arr_result->dest);
            $fp = fopen($image_path, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            if($arr_result)
            {
                echo "File uploaded successfully.";
            }
            
        }
    }     
?>