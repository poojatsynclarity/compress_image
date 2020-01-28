<?php
    class Compress {
        function compress_image($image_path) 
        {
            // optimize image using reSmush.it
            $file = getcwd(). $image_path . $_FILES['image']['name'];
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
            $fp = fopen(getcwd(). $image_path. $name, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            echo "File uploaded successfully.";
        }
    }
     
?>