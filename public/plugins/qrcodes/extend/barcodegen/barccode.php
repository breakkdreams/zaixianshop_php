<?php

class Barccode
{
    private $imgUrl='';         // 保存的位置
    private $imgText='';         // 内容
    private $imgformat; //图片格式
    private $imgbcgtype;  //编码类型

    /**设置路径
     * @param $url图片生成的路径
     */
    public function  imgUrl($url)
    {
        $this->imgUrl=$url;
    }

    /**
     * 一维码内容
     */
    public function imgText($text)
    {
        $this->imgText=$text;
    }

    /**生成
     * @throws BCGArgumentException
     * @throws BCGDrawException
     */
    public function createBarccode($imgText,$imgUrl='')
    {
        require_once('class/BCGFontFile.php');
        require_once('class/BCGColor.php');
        require_once('class/BCGDrawing.php');

        require_once('class/BCGcode39.barcode.php');
        $font = new BCGFontFile(dirname(__FILE__) . '/font/Arial.ttf', 18);
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);
        $drawException = null;
        try {
            $code = new BCGcode39();
            $code->setScale(2);
            $code->setThickness(30);
            $code->setForegroundColor($color_black);
            $code->setBackgroundColor($color_white);
            $code->setFont($font);
            $code->parse($imgText);
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        $drawing = new BCGDrawing($imgUrl, $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        header('Content-Type: image/png');
//        header('Content-Disposition: inline; filename="999.png"');
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    }
}

?>