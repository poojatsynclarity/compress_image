<?php
    class Compress {
        function compress_image($image_path) 
        {
            if  (in_array  ('curl', get_loaded_extensions())) 
            {
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
                curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=80');
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
                echo "File uploaded successfully.";
         
            }  
            else 
            {
                echo "CURL is not available on your web server! To optimize the image the CURL must be installed on your web server.";
            }
            
        }
    }
     
?>