import tableDragger from 'table-dragger'

var dragger = tableDragger(document.querySelector("#table"), { dragHandler: ".handle",mode: "row", onlyBody: true });

dragger.on('drop', function(from, to, el){
    var object = {};
    for (let index = Math.min(from, to) - 1; index < Math.max(from, to); index++) {
        const newOrder = index + 1;
        const id = el.children[1].children[index].children[0].textContent;
        object[id] = newOrder;
        el.children[1].children[index].children[1].innerText = newOrder;
    }
    
    $.ajax({
        url: 'changeOrder',
        type: 'POST',
        data: {object: object},
    })
})