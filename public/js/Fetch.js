class Fetch{

    static host=document.location.protocol+"//"+window.location.host;

    static get(url,action,array={},resert=false){
        Fetch.#request('GET',url,action,array,resert);
    }

    static post(url,action,array={},resert=false){
        Fetch.#request('POST',url,action,array,resert);
    }

    static put(url,action,array={},resert=false){
        Fetch.#request('PUT',url,action,array,resert);
    }

    static delete(url,action,array={},resert=false){
        Fetch.#request('DELETE',url,action,array,resert);
    }

    static #request(method,url,action,array={},resert=false){
        array.method=method;
        fetch(Fetch.host+"/"+url,array)
        .then(response=>response.text())
        .then(data=>{
            action(data);
        })
        .catch(error=>{
            console.log(error);
            if(resert){
                setTimeout(()=>{
                    Fetch.#request(method,url,action,array,resert);
                },5000);
            }
        });
    }

}