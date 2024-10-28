export default class Util{

    static getMeta(key){
        let element=document.querySelector('meta[name="'+key+'"]');
        if(element==null){
            return null;
        }
        return element.getAttribute('content')??null;
    }

    static createElement(tag,action){
        let element=document.createElement(tag);
        action(element);
        return element;
    }

}