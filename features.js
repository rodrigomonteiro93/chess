class Features {

    constructor(testing) {
        this.$testing = testing
    }

    addPieceRemoved(piece, player){
        let playerRemoved
        player === 1 ? playerRemoved = 2 : playerRemoved = 1
        const ul = document.querySelector('.list-pieces-'+playerRemoved)
        ul.classList.add('show')
        let li = document.createElement('li')
        let i = document.createElement('i')
        let getPiece;
        if(piece === 'pawn'){
            getPiece = pawn
        }else{
            getPiece = pieces.filter(function(item){
                return (item.piece === piece);
            });
        }
        if(getPiece){
            let classI = getPiece[0].icon.split(' ')
            i.classList.add(classI[0])
            i.classList.add(classI[1])
            li.appendChild(i)
            ul.appendChild(li)
        }
    }

    toast(msg){
        const toast = document.querySelector('.toast')
        if(toast){ toast.remove() }
        const toastElement = document.createElement('span')
        toastElement.classList.add('toast')
        toastElement.textContent = msg
        const body = document.querySelector('body')
        body.append(toastElement)
    }
}

