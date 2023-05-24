class Wire{

    static TYPE_WIRES=Object.freeze({
        MODEL:{
            id:Symbol(),
            text:"model",
        },
        CLICK:{
            id:Symbol(),
            text:"click",
        },
    });

    constructor(element,type,content){
        this.element=element;
        this.type=type;
        this.content=content;
    }

    getValue(){
        let value=null;
        switch(this.element.tagName){
            case "INPUT":
            case "TEXTAREA":
            case "SELEXT":{
                value=this.element.value;
                break;
            }
        }
        return value;
    }

    setValue(value){
        switch(this.element.tagName){
            case "INPUT":
            case "TEXTAREA":
            case "SELEXT":{
                this.element.value=value;
                break;
            }
        }
    }

}