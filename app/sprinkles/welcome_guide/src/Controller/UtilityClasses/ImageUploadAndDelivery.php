<?php

namespace UserFrosting\Sprinkle\WelcomeGuide\Controller\UtilityClasses;

class ImageUploadAndDelivery
{

    public static function getFullImagePath($filename)
    {
        return "../app/sprinkles/welcome_guide/uploads/images/" . $filename;
    }

    public static function deleteImageFile($filename)
    {
        $fileToBeDeleted = ImageUploadAndDelivery::getFullImagePath($filename);
        unlink($fileToBeDeleted);
    }

    private static function uploadImageFileToServer($file)
    {
        //generate unique file name
        $new_filename = md5(uniqid(rand(), true) . microtime());
        $file->setName($new_filename);
        // Validate file upload
        $file->addValidations(array(
            // Ensure file is an image
            //new \Upload\Validation\Mimetype('image/png', 'image/jpeg', 'image/gif', 'image/bmp'),
            new \Upload\Validation\Mimetype(array('image/png', 'image/jpeg', 'image/gif', 'image/bmp')),
            // Ensure file is no larger than 5M
            new \Upload\Validation\Size('5M')
        ));
        // Access data about the file that has been uploaded
        $file_data = array(
            'name' => $file->getNameWithExtension(),
            'extension' => $file->getExtension(),
            'mime' => $file->getMimetype(),
            'size' => $file->getSize(),
            'md5' => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );
        // Try to upload file
        try {
            // Success!
            $file->upload();
            return $file_data;
        } catch (\Exception $e) {
            // Fail!
            $errors = $file->getErrors();
            return $errors;
        }
    }

    public static function uploadImageAndRemovePreviousOne($nameOfFileInPostObject, $nameOfPreviousImageFile, $data)
    {
        if (!empty($nameOfPreviousImageFile) && !empty($data[$nameOfFileInPostObject . '_remove']) && $data[$nameOfFileInPostObject . '_remove']) {
            ImageUploadAndDelivery::deleteImageFile($nameOfPreviousImageFile);
            return null;
        }
        if (!empty($_FILES[$nameOfFileInPostObject]) && !empty($_FILES[$nameOfFileInPostObject]['name'])) {
            $storage = new \Upload\Storage\FileSystem(ImageUploadAndDelivery::getFullImagePath(''));
            //upload new file
            $file1 = new \Upload\File($nameOfFileInPostObject, $storage);
            $succUp = ImageUploadAndDelivery::uploadImageFileToServer($file1);
            if (isset($succUp['name'])) {
                if (isset($nameOfPreviousImageFile)) {
                    //delete previous file
                    ImageUploadAndDelivery::deleteImageFile($nameOfPreviousImageFile);
                }
                return $succUp['name'];
            }
        }
        return $nameOfPreviousImageFile;
    }
}