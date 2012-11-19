<?php
namespace KFI\UploadBundle\Services;

class Mime{
    static protected $types = array(
        'image' => array('gif','png','jpg','jpeg'),
        'audio' => array('aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'),
        'video' => array('asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv'),
        'document' => array('doc','docx','pages','odt','rtf','pdf'),
        'spreadsheet' => array('xls','numbers','ods'),
        'interactive' => array('ppt','key','odp','swf'),
        'text' => array('txt'),
        'archive' => array('tar','bz2','gz','cab','dmg','rar','sea','sit','sqx','zip'),
        'code' => array('css','html','php','js'),
    );

    static protected $mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tif|tiff' => 'image/tiff',
        'ico' => 'image/x-icon',
        'asf|asx|wax|wmv|wmx' => 'video/asf',
        'avi' => 'video/avi',
        'mov|qt' => 'video/quicktime',
        'mpeg|mpg|mpe|mp4' => 'video/mpeg',
        'txt|c|cc|h' => 'text/plain',
        'rtx' => 'text/richtext',
        'css' => 'text/css',
        'htm|html' => 'text/html',
        'mp3|m4a' => 'audio/mpeg',
        'ra|ram' => 'audio/x-realaudio',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'mid|midi' => 'audio/midi',
        'wma' => 'audio/wma',
        'rtf' => 'application/rtf',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'doc|docx' => 'application/msword',
        'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
        'wri' => 'application/vnd.ms-write',
        'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
        'mdb' => 'application/vnd.ms-access',
        'mpp' => 'application/vnd.ms-project',
        'swf' => 'application/x-shockwave-flash',
        'class' => 'application/java',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'gz|gzip' => 'application/x-gzip',
        'exe' => 'application/x-msdownload',
        // openoffice formats
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
    );

    public static function getType( $fileName ) {
        $ext = substr(strrchr($fileName,'.'),1);
        foreach ( self::$types as $type => $exts )
            if ( in_array($ext, $exts) )
                return $type;
        return 'misc';
    }

    public static function getMimeType( $filename){
        foreach (self::$mimes as $ext_preg => $mime_match) {
            $ext_preg = '!\.(' . $ext_preg . ')$!i';
            if (preg_match($ext_preg, $filename, $ext_matches)) {
                return $mime_match;
            }
        }
        return 'application/octet-stream';
    }
}