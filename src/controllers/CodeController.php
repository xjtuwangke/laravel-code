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
        $qrCode->setText( $code )
            ->setSize( 300 )
            ->setPadding( 10 )
            ->setImageType( QrCode::IMAGE_TYPE_JPEG )
            ->setErrorCorrection( QrCode::LEVEL_HIGH )
            ->render();
        $response = \Response::make( '' , 200 );
        $response->header( 'Content-Type' , 'image/jpeg' , true );
        return $response;
    }
} 