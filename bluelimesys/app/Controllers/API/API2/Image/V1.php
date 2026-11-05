<?php
namespace App\Controllers\API\API2\Image;
use BluelimeSystem;
use BluelimeConfiguration;
class V1 extends \App\Controllers\BaseController
{
    public function get_image(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $type = $POST_VAL['type'] ?? '';
        $image_name = $POST_VAL['image_name'] ?? '';
        $thumbnail = $POST_VAL['thumbnail'] ?? false;

        $type = strtoupper($type);

        $image_folder = $_ENV["IMAGE_PATH_$type"] ?? '';
        $file_name = $image_folder . $image_name;
        return $this->get_image_content($file_name,$thumbnail);
    }

    private function get_image_content($file_name, $thumbnail=false){
        header('Content-type: image');
        if(is_file($file_name) ){
            $im = imagecreatefromstring(file_get_contents($file_name));
            //$im = imagecreatefromjpeg($file_name);
        }
        else
            $im = imagecreatefrompng(APPPATH.'../public/img/image_not_found.png');

        if($thumbnail)
            $im = imagescale($im,500);

        ob_clean();
        imagejpeg($im, null);
        //imagedestroy($im);
        die();
    }

    public function upload_multiple_images(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $images = $POST_VAL['images'] ?? [];
        $type = $POST_VAL['type'] ?? '';
       
        $type = strtoupper($type);
        $uploaded_images = [];
        foreach($images as $image){
            $image_name = $image['image_name'] ?? '';
            $image_content = $image['image_content'] ?? '';

            $image_folder = $_ENV["IMAGE_PATH_$type"] ?? '';
            $file_name = $image_folder . $image_name;
            $file_details = pathinfo($image_name);
            $cnt=1;
            while(is_file($file_name)){
                $file_name = $image_folder . $file_details['filename'] . "_" . $cnt++ . ".".$file_details['extension'];
            }
            if(file_put_contents($file_name, base64_decode($image_content))){
                $uploaded_images[] = pathinfo($file_name)['basename']??'';
            };
        }
        
        return $this->response->setJSON(['uploaded_images'=>$uploaded_images]);
    }
}