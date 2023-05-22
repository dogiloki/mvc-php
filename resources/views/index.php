<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{urlPublic('css/normalize.css')}}">
</head>
<body>
    <h1>Hola mundo</h1>
    <p>Bienvenido</p>
    @php
        $user=\app\Models\User::all();
    @endphp
    <component:vista :variable="{{base64_encode(serialize($user))}}">
    </component:vista>
</body>
</html>

<script src="{{urlPublic('js/fetch.js')}}"></script>

<script>

document.addEventListener('DOMContentLoaded',()=>{

    // Obtener componentes
    let components=[];
    let colletion=Array.from(document.getElementsByTagName('*'));
    colletion.forEach((element)=>{
        if(element.tagName.startsWith("COMPONENT:")){
            components.push({
                "name":element.tagName.substring(10),
                "node":element,
                "vars":getVars(element),
                "wires":[],
            });
        }
    });
    function getVars(node){
        let element=node;
        let params={};
        let attributes=Array.from(element.attributes);
        attributes.forEach((attribute)=>{
            let name=attribute.name;
            if(name.startsWith(":")){
                params[name]=attribute.value;
            }
        });
        return params;
    }
    for(let component of components){
        render(component);
    }

    function render(component){
        component.wires.map((wire)=>{
            wire.value=wire.getValue();
            return wire;
        })
        Fetch.post("component/"+component.name,(data)=>{
            let doc=new DOMParser().parseFromString(data,"text/html");
            let collection_old=Array.from(component.node.getElementsByTagName('*'));
            let collection_new=Array.from(doc.body.getElementsByTagName('*'));
            if(collection_old.length==0){
                component.node.innerHTML=data;
                getWires(component,true);
            }else{
                collection_new.forEach((element_new,index)=>{
                    let element_old=component.node.querySelector("#"+component.name+"-"+index);
                    if(element_old!=null && element_new.textContent!=element_old.textContent){
                        component.node.replaceChild(element_new,element_old);
                    }
                });
                getWires(component,false);
            }
        },{
            "body":new URLSearchParams({json:JSON.stringify(component)})
        });
    }

    function getWires(component,addEvent=true){
        let collection=component.node.getElementsByTagName('*');
        for(let a=0; a<collection.length; a++){
            let element=collection[a];
            element.setAttribute("id",component.name+"-"+a);
            let attributes=Array.from(element.attributes);
            attributes.forEach((attribute)=>{
                if(attribute.name.startsWith("wire:")){
                    let wire=attribute.name.substring(5);
                    let listener=wire;
                    let attrib=attribute.value;
                    //let listener=wire.match(/^([^()]+)/)[1];
                    //let attrib=wire.match(/"([^"]+)"/)[1];
                    if(addEvent){
                        addEventWire(component,element,listener,attrib);
                    }
                }
            });
        }
    }

    function addEventWire(component,element,listener,attrib){
        let wire={
            "element":element,
            "listener":listener,
            "attrib":attrib,
            getValue:()=>{
                return element.value??element.innerHTML??element.src??element.href??element.data;
            }
        };
        //console.log(this.content.querySelector((element.tagName)+"[wire\\:"+listener+"=\""+attrib+"\"]"));
        element.addEventListener(listener,()=>{
            component.vars[attrib]=wire.getValue();
            render(component);
        });
        component.wires.push(wire);
    }
    console.log(components);

});

</script>