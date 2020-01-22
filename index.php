<form method="post" enctype="multipart/form-data">
    <p><input type="file" name="image" accept="image/*" /></p>
    <input type="submit" name="submit" value="Submit">
</form>

<?php
if (isset($_POST['submit'])) {
 
    //allowed file types
    $arr_file_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
 
    if (!(in_array($_FILES['image']['type'], $arr_file_types))) {
 
        die('Only image is allowed!');
    }
 
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777);
    }
 
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $_FILES['image']['name']);
 
    // optimize image using reSmush.it
    $file = getcwd(). '/uploads/' . $_FILES['image']['name'];
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
    $fp = fopen(getcwd(). '/uploads/'. $name, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
 
    echo "File uploaded successfully.";
}