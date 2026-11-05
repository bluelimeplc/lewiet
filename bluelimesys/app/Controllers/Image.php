<?php

namespace App\Controllers;

class Image extends BaseController
{
    public function index()
    {
        $image = $this->request->getGet('image');
        $thumbnail = $this->request->getGet('thumbnail');
        if (!empty($image)) {
            $file_path = $_ENV['IMAGE_PATH_PATHOLOGY'];
            $this->get_image_content($file_path . $image, $thumbnail);
        }
    }

    public function progresspicture(){
        $image = $this->request->getGet('image');
        $thumbnail = $this->request->getGet('thumbnail');
        $file_path = $_ENV['PROGRESS_IMAGE_PATH'];
        $this->get_image_content($file_path.$image, $thumbnail);
    }
    public function cashierdocument(){
        $image = $this->request->getGet('image');
        $thumbnail = $this->request->getGet('thumbnail');
        $file_path = $_ENV['CASHIER_DOCUMENT_PATH'];
        $this->get_image_content($file_path.$image, $thumbnail);
    }

    public function manual_card(){
        $image = $this->request->getGet('image');
        $thumbnail = $this->request->getGet('thumbnail');
        $file_path = $_ENV['MANUAL_CARD_PATH'];
        $this->get_image_content($file_path.$image, $thumbnail);        
    }

    private function get_image_content($file_name, $thumbnail=false){
        if (!is_file($file_name)) {
            $file_name = APPPATH.'../public/img/image_not_found.png';
        }

        $image_info = getimagesize($file_name);
        $mime_type = $image_info['mime'] ?? 'image/jpeg';

        switch ($mime_type) {
            case 'image/jpeg':
                $im = imagecreatefromjpeg($file_name);
                break;
            case 'image/png':
                $im = imagecreatefrompng($file_name);
                break;
            case 'image/gif':
                $im = imagecreatefromgif($file_name);
                break;
            default:
                $im = imagecreatefrompng(APPPATH.'../public/img/image_not_found.png');
                break;
        }

        if ($thumbnail) {
            $im = imagescale($im, 200);
        }

        ob_start();
        imagejpeg($im, null);
        $image_data = ob_get_clean();
        imagedestroy($im);

        $this->response->setContentType($mime_type);
        $this->response->setBody($image_data);
        $this->response->send();
        exit();
    }
}