<?php

namespace App\Service\Image;

/**
 * Service
 */
final class ImageService {


    public function __construct()
    {
       
    }

    public function imageString($img_file){
        // $img_file = '../uploads/user_profile/'.$userRow['profile_image'];
        $imgData = base64_encode(file_get_contents($img_file));
        $src = 'data: '.mime_content_type($img_file).';base64,'.$imgData;

        return $src;
       
    }

   

}