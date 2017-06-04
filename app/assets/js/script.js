window.addEventListener('load', function () {

    Element.prototype.insertAfter || (Element.prototype.insertAfter = function(node){
        this.parentNode.insertBefore(node, this.nextSibling);
    });

    Element.prototype.removeAllChilds || (Element.prototype.removeAllChilds = function(){
        while(this.firstChild) this.removeChild(this.firstChild);
    });

    var $button = document.getElementById('add-etu-button'),
        $template,
        $nodes = Array.prototype.slice.call(document.getElementsByClassName('etudiant-register'));

    if($nodes.length){
        $template = $nodes[0].cloneNode(true);
    } else {
        return;
    }

    var $hook = $nodes[$nodes.length - 1],
        $toOverload = Array.prototype.slice.call(document.getElementsByClassName('need-overload')),
        $container = $nodes[0].parentNode,
        nbEtu = $nodes.length;

    $toOverload.forEach(function($node){
        var $deleteButton = document.createElement('A');
        $deleteButton.href = '#';
        $deleteButton.textContent = 'supprimer';
        $deleteButton.classList.add('col-md-1');
        $deleteButton.addEventListener('click', function (e) {
            e.preventDefault();
            $container.removeChild($node);
            $nodes = Array.prototype.slice.call(document.getElementsByClassName('etudiant-register'));
            nbEtu = $nodes.length;
            $hook = $nodes[nbEtu - 1];
        });
        $node.classList.remove('need-overload');
        $node.appendChild($deleteButton);
    });



    $button.addEventListener('click', function (e) {
        e.preventDefault();

        var $newEtuRow = $template.cloneNode(true),
            $etuNumber = $newEtuRow.getElementsByClassName('etudiant-number')[0],
            $chef = $newEtuRow.querySelector('input[type=radio]'),
            $deleteButton = document.createElement('A');



        $deleteButton.href = '#';
        $deleteButton.textContent = 'supprimer';
        $deleteButton.classList.add('col-md-1');

        $deleteButton.addEventListener('click', function (e) {
            e.preventDefault();
            $container.removeChild($newEtuRow);
            $nodes = Array.prototype.slice.call(document.getElementsByClassName('etudiant-register'));
            nbEtu = $nodes.length;
            $hook = $nodes[nbEtu - 1];
        });

        $newEtuRow.appendChild($deleteButton);
        $hook.insertAfter($newEtuRow);

        $nodes = Array.prototype.slice.call(document.getElementsByClassName('etudiant-register'));
        nbEtu = $nodes.length;
        $chef.value = nbEtu;
        $etuNumber.textContent = nbEtu;
        $hook = $nodes[nbEtu - 1];
    });

});