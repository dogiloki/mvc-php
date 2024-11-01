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
            "eccLevel"=>EccLevel::H,
            "imageBase64"=>false,
            "addLogoSpace"=>true,
            "logoSpaceWidth"=>15,
            "logoSpaceHeight"=>15,
            "scale"=>7,
            "imageTransparent"=>false,
            "drawCircularModules"=>false,
            "circleRadius"=>0.45,
            "keepAsSquare"=>[QRMatrix::M_FINDER,QRMatrix::M_FINDER_DOT]
        ]);
    }

    public function render($data,$image){
        $qr=new QR($this->qr_options);
        $qr->addByteSegment($data);
        $this->qr_code=$qr;
        $output=new QRImage($this->qr_options,$this->qr_code->getMatrix());
        $this->qr_output=$output->dump(null,$image);
        return $this;
    }

    public function save($name="qr.png"){
        file_put_contents(Config::filesystem('storage.qr')."/".$name,$this->qr_output);
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