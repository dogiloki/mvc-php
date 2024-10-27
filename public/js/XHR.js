import Fetch from './Fetch.js';

export default class XHR{

    static request({
        method="GET",
        url:url=null,
        uri="",
        load=()=>{},
        error=()=>{},
        abort=()=>{},
        progress=()=>{},
        data={},
        files={},
        resert=false
    }){
        url??=Fetch.host+"/"+uri;
        console.log(url);
        data.method=method;
        let xhr=new XMLHttpRequest();
        let form_data=new FormData();
        for(let index1 in data){
            form_data.append(index1,data[index1]);
        }
        for(let index2 in files){
            form_data.append(index2,files[index2]);
        }
        xhr.addEventListener('load',function(){
            load(xhr.responseText);
        });
        xhr.addEventListener('error',function(){
            if(resert){
                setTimeout(()=>{
                    XHR.request({
                        method:method,
                        url:rul,
                        uri:uri,
                        load:load,
                        error:error,
                        abort:abort,
                        data:data,
                        files:files,
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
            progress(evt,(evt.loaded/evt.total)*100);
        });

        xhr.open(method,url);
        xhr.send(form_data);
    }

}