class Features {

    constructor() {
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

}

