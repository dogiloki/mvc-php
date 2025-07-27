import Fetch from './Fetch.js';
import Util from './Util.js';

export default class XHR{

    static request({
        method="GET",
        url=null,
        uri="",
        headers={},
        load=()=>{},
        error=()=>{},
        abort=()=>{},
        progress=()=>{},
        data={},
        query={},
        files={},
        action=()=>{},
        resert=false
    }){
        url??=Fetch.host+"/"+uri;
        data.method=method;
        let xhr=new XMLHttpRequest();
        let form_data=new FormData();
        let query_params="?"+Object.keys(query)
        .map(key=>encodeURIComponent(key)+"="+encodeURIComponent(query[key]))
        .join("&");
        form_data.append('_token',Util.getMeta('_token'));
        form_data.append('_method',method);
        xhr.open(method,url+query_params);
        for(let index1 in data){
            form_data.append(index1,data[index1]);
        }
        for(let index2 in files){
            form_data.append(index2,files[index2]);
        }
        for(let index3 in headers){
            xhr.setRequestHeader(index3,headers[index3]);
        }
        xhr.addEventListener('load',function(){
            load(xhr);
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
        action(xhr);
        xhr.send(form_data);
    }

}