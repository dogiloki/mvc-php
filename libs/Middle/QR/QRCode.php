<?php

namespace libs\Middle\QR;

use chillerlan\QRCode\QRCode as QR;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRCodeOutputException;
use libs\Middle\QR\QRImage;
use libs\Config;

class QRCode{

    private $qr_options;
    private $qr_code;
    private $qr_output;

    public function __construct($qr_options=null){
        $this->qr_options=$qr_options??new QROptions([
            "version"=>QR::VERSION_AUTO,
            'outputType'=>QR::OUTPUT_IMAGE_PNG,
            "eccLevel"=>EccLevel::H,
            "imageBase64"=>false,
            "addLogoSpace"=>true,
            "logoSpaceWidth"=>20,
            "logoSpaceHeight"=>20,
            "scale"=>4,
            "imageTransparent"=>true,
            "drawCircularModules"=>false,
            "circleRadius"=>0.45,
            "keepAsSquare"=>[QRMatrix::M_FINDER,QRMatrix::M_FINDER_DOT]
        ]);
    }

    public function render($data,$image=null){
        $qr=new QR($this->qr_options);
        if($image==null){
            $this->qr_options->addLogoSpace=true;
            $this->qr_output=$qr->render($data);
        }else{
            $qr->addByteSegment($data);
            $output=new QRImage($this->qr_options,$qr->getMatrix());
            $this->qr_output=$output->dump(null,$image);
        }
        $this->qr_code=$qr;
        return $this;
    }

    public function save($name="qr.png"){
        return file_put_contents(Config::filesystem('storage.qr')."/".$name,$this->qr_output);
    }

    public function code(){
        return $this->qr_code;
    }

    public function output(){
        return $this->qr_output;
    }

    public function options(){
        return $this->qr_options;
    }

}

?>