class SPA{
    
    static renderVar(names=[], params={}){
        if(typeof names === "string"){
            names=[names];
        }
        Fetch.post("var",(data)=>{
            data=JSON.parse(data);
            for(let name of names){
                let value=data[name];
                let contents=Array.from(document.getElementsByTagName('var-'+name));
                contents.forEach((content)=>{
                    content.textContent=value;
                });
            }
        },{
            "dataType":"json",
            "body":new URLSearchParams(params)
        });
    }

    static renderComponent(name, params={}){
        let contents=Array.from(document.getElementsByTagName('component-'+name));
        Fetch.post("component/"+name,(data)=>{
            contents.forEach((content)=>{
                content.innerHTML=data;
            });
        },{
            "dataType":"html",
            "body":new URLSearchParams(params)
        });
    }
}