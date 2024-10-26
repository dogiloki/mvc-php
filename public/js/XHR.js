import Fetch from './Fetch.js';

class XHR{

    static request({
        method="GET",
        url="",
        load=()=>{},
        error=()=>{},
        abort=()=>{},
        progress=()=>{},
        data={},
        files={},
        resert=false
    }){
        array.method=method;
        let xhr=new XMLHttpRequest();
        let form_data=new FormData();
        for(let index1 in data){
            form_data.append(index1,data[index]);
        }
        for(let index2 in files){
            form_data.append(index2,files[index]);
        }
        xhr.addEventListener('load',function(){
            load(xhr.responseText);
        });
        xhr.addEventListener('error',function(){
            if(resert){
                setTimeout(()=>{
                    XHR.request({
                        method:method,
                        url:url,
                        load:load,
                        error:error,
                        abort:abort,
                        array:array,
                        resert:resert
                    });
                },5000);
            }
            error(xhr.responseText);
        });
        xhr.addEventListener('abort',function(){
            abort(xhr.responseText);
        });
        xhr.upload.addEventListener('progress',function(evt){
            progress(evt);
        });

        xhr.open(method,Fetch.host+"/"+url);
        xhr.send(form_data);
    }

}