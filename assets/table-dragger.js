import tableDragger from 'table-dragger'
console.log('ilan');
var dragger = tableDragger(document.querySelector("#table"), { mode: "row", onlyBody: true });

dragger.on('drop', function(from, to, el){
    var object = {};
    for (let index = Math.min(from, to) - 1; index < Math.max(from, to); index++) {
        const newOrder = index + 1;
        const id = el.children[1].children[index].children[0].value;
        object[id] = newOrder;
        el.children[1].children[index].children[0].innerText = newOrder;
    }
    $.ajax({
        url: '/admin/customer/statut/changeOrder',
        type: 'POST',
        data: {from: from, to: to},
    })
})