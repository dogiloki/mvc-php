<?php

namespace libs\Middle\QR;

use chillerlan\QRCode\Output\QRGdImage;

class QRImage extends QRGdImage{

    public function dump($file=null,$image=null){
        $this->options->returnResource=true;

        // No es necesario guardar el resultado de dump(), esta en: $this->image
        parent::dump($file);

        $logo=imagecreatefrompng($image);

        $width=imagesx($logo);
        $height=imagesy($logo);

        $logo_width=($this->options->logoSpaceWidth-2)*$this->options->scale;
        $logo_height=($this->options->logoSpaceHeight-2)*$this->options->scale;

        $logo_size=$this->matrix->size()*$this->options->scale;

        imagecopyresampled($this->image,$logo,($logo_size-$logo_width)/2,($logo_size-$logo_height)/2,0,0,$logo_width,$logo_height,$width,$height);

        $data=$this->dumpImage();

        $this->saveToFile($data,$file);

        if($this->options->imageBase64){
            $data=$this->toBase64DataURI($data,'image/'.$this->options->outputType);
        }

        return $data;

    }

}

?>