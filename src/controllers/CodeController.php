<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-20
 * Time: 3:48
 */

namespace Xjtuwangke\LaravelCode\Controllers;

use Zend\Barcode\Barcode;
use Endroid\QrCode\QrCode;

/**
 * 用于显示一维码二维码
 * Class CodeController
 */
class CodeController extends \Controller{

    static public function registerRoutes(){
        $class = get_class();
        \Route::get( "code/barcode/{code}" , [ 'as' => "code.barcode.default" , 'uses' => "{$class}@barcode" ] );
        \Route::get( "code/barcode/{code}/{type}" , [ 'as' => "code.barcode" , 'uses' => "{$class}@barcode" ] );
        \Route::get( "code/qrcode/{code}" , [ 'as' => "code.qrcode" , 'uses' => "{$class}@qrcode" ] );
    }

    public function barcode( $code , $type = 'code128' ){

        if( ! in_array( $type , [
            'code128' ,
            'code25' ,
            'code25interleaved' ,
            'code39' ,
            'ean13' ,
            'ean2' ,
            'ean5' ,
            'ean8' ,
            'identcode' ,
            'itf14' ,
            'leitcode' ,
            'planet' ,
            'postnet' ,
            'royalmail' ,
            'upca' ,
            'upce' ,
        ])){
            \App::abort( 404 );
        }

        //Barcode::setBarcodeFont( storage_path( 'pdf/fonts/yahei/normal.ttf' ) );

        $barcodeOptions = array(
            'text' => $code ,
            'factor' => 1 ,
        );
        $rendererOptions = array();
        Barcode::render(
            $type , 'image', $barcodeOptions, $rendererOptions
        );
        $response = \Response::make( '' , 200 );
        $response->header( 'Content-Type' , 'image/png' , true );
        return $response;
    }

    public function qrcode( $code ){
        $qrCode = new QrCode();
        $QR = $qrCode->setText( $code )
            ->setSize( 300 )
            ->setPadding( 10 )
            ->setImageType( QrCode::IMAGE_TYPE_JPEG )
            ->setErrorCorrection( QrCode::LEVEL_HIGH )
            ->getImage();

        $response = \Response::make( '' , 200 );
        $response->header( 'Content-Type' , 'image/jpeg' , true );
        header('Content-type: image/png');

        if( ( $logo = \Config::get( 'laravel-code::config.logo' ) ) && \File::exists( $logo ) ){
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $im = imagecreatetruecolor( $QR_width , $QR_height );
            imagecopy( $im , $QR , 0 , 0 , 0 , 0 , $QR_width , $QR_height );
            imagedestroy( $QR );
            $logo = imagecreatefromstring( file_get_contents( $logo ) );
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($im, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            imagepng( $im );
        }
        else{
            imagepng( $QR );
        }
        return $response;
    }
} 