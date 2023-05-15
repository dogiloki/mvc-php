function renderVar(name){
    let contents=Array.from(document.getElementsByTagName('var-'+name));
    Fetch.get("var/"+name,(data)=>{
        data=JSON.parse(data);
        let value=data[name];
        contents.forEach((content)=>{
            content.textContent=value;
        });
    },{
        "dataType":"json"
    });
}

function renderComponent(name){
    let contents=Array.from(document.getElementsByTagName('component-'+name));
    console.log(contents);
    Fetch.get("component/"+name,(data)=>{
        contents.forEach((content)=>{
            content.innerHTML=data;
        });
    },{
        "dataType":"html"
    });
}