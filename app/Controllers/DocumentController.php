<?php

namespace app\Controllers;

use libs\HTTP\Request;
use libs\Middle\Storage;
use app\Controllers\Controller;
use app\Models\Document;

class DocumentController extends Controller{
    
    private static $disk_files="files";

    public function register(Request $request){
        try{
            $document=new Document();
            $document->setValues($request->all());
            $document->save();
            $document->uploaderFile()->attach(Storage::upload($request->files('file'),self::$disk_files)->id);
            $document->save();
            return json(['status'=>$document->save()]);
        }catch(Exception $ex){
            abort(500);
        }
    }

}

?>