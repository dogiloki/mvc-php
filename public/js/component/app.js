var components=[];

document.addEventListener("DOMContentLoaded",()=>{

    // Obtener componentes
    let colletion=Array.from(document.getElementsByTagName('*'));
    colletion.forEach((element)=>{
        if(element.tagName.startsWith("COMPONENT:")){
            components.push(new Component(element.tagName.substring(10),element));
        }
    });

});