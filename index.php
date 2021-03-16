<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
</head>
<body>
<?php
$arrayPecas = [
    [
        'name' => 'torre',
        'piece' => 'tower',
        'icon' => 'fas fa-chess-rook'
    ],
    [
        'name' => 'cavalo',
        'piece' => 'horse',
        'icon' => 'fas fa-chess-knight'
    ],
    [
        'name' => 'bispo',
        'piece' => 'bishop',
        'icon' => 'fas fa-chess-bishop'
    ],
    [
        'name' => 'rainha',
        'piece' => 'queen',
        'icon' => 'fas fa-chess-queen'
    ],
    [
        'name' => 'rei',
        'piece' => 'king',
        'icon' => 'fas fa-chess-king'
    ],
    [
        'name' => 'bispo',
        'piece' => 'bishop',
        'icon' => 'fas fa-chess-bishop'
    ],
    [
        'name' => 'cavalo',
        'piece' => 'horse',
        'icon' => 'fas fa-chess-knight'
    ],
    [
        'name' => 'torre',
        'piece' => 'tower',
        'icon' => 'fas fa-chess-rook'
    ],
];

$arrayTabuleiro = [];

$totalTabuleiro = 64;

//faz o indice do array principal
$posPecas = 0;
//faz o indice do array de peças
$iPecas = 0;
//conta
$countLines = 0;

for ($i=0;$i<$totalTabuleiro;$i++){
    $countLines++;
    if($countLines < 9 || $countLines < 8){
        $arrayTabuleiro[$posPecas][$countLines]['name'] = $arrayPecas[$iPecas]['name'];
        $arrayTabuleiro[$posPecas][$countLines]['name'] .= $posPecas == 0 ? ' preto' : ' branco';
        $arrayTabuleiro[$posPecas][$countLines]['piece'] = $arrayPecas[$iPecas]['piece'];
        $arrayTabuleiro[$posPecas][$countLines]['icon'] = $arrayPecas[$iPecas]['icon'];
        $arrayTabuleiro[$posPecas][$countLines]['player'] = $posPecas == 0 ? 1 : 2;
        $iPecas++;
    }
    if($posPecas == 1 || $posPecas == 6){
        $arrayTabuleiro[$posPecas][$countLines]['name'] = 'peão';
        $arrayTabuleiro[$posPecas][$countLines]['name'] .= $posPecas == 1 ? ' preto' : ' branco';
        $arrayTabuleiro[$posPecas][$countLines]['piece'] = 'pawn';
        $arrayTabuleiro[$posPecas][$countLines]['icon'] = 'fas fa-chess-pawn';
        $arrayTabuleiro[$posPecas][$countLines]['player'] = $posPecas == 1 ? 1 : 2;
    }
    if($posPecas > 1 && $posPecas < 6){
        $arrayTabuleiro[$posPecas][$countLines]['name'] = null;
        $arrayTabuleiro[$posPecas][$countLines]['piece'] = null;
        $arrayTabuleiro[$posPecas][$countLines]['player'] = null;
    }
    if($countLines == 8){
        $countLines = 0;
        $iPecas = 0;
        $posPecas++;
    }
}
//print_r($arrayTabuleiro);
echo '<div class="table">';
foreach ($arrayTabuleiro as $item){
    echo '<div>';
    foreach ($item as $data){
        if(!$data['player']){
            echo '<span>'.$data['name'].'</span>';
        }else{
            echo '<span><i class="'.$data['icon'].'" data-player="'.$data['player'].'" data-piece="'.$data['piece'].'"></i></span>';
        }
    }
    echo '</div>';
}
echo '</div>';

?>

<script>
(async function (){
    class Chess {

        constructor() {
            this.$Y = false
            this.$X = false
            this.arrayOptions = []
            this.$PlayerActive = Math.floor(Math.random() * 2 + 1)
            this.$Target = false
            this.$winner = false
            this.$testing = false
            this.$directions = this.createDirection()
            this.$table = document.querySelector('.table')
            this.items = document.querySelectorAll('.table div span i')
            this.items.forEach(item => {
                item.addEventListener('click', this.bindShowMovement.bind(this))
            })
        }

        async bindShowMovement(e) {
            if(this.checkPlay(e)){
                this.clearActives()
                //Get positions in table
                const positions = this.getPositions(e.currentTarget.parentElement);
                this.$X = positions[0]
                this.$Y = positions[1]
                const returnCheckMovement = await this.checkMovement(e)
                if(returnCheckMovement.length){
                    e.target.parentElement.classList.add('target')
                    this.createEvent()
                }
            }
        }

        createEvent(){
            this.arrayOptions.forEach((item) => {
                const elem = this.$table.children.item(item[1]).children.item(item[0])
                elem.classList.add('active')
                if(item[2]){
                    elem.classList.add('attack')
                }
                elem.addEventListener('click', this.bindMovement.bind(this))
            })
        }

        bindMovement(e) {
            if (e.target.classList.contains('active')) {
                this.checkPiece(e.target) ? this.endGame() : null
                e.target.innerHTML = this.$Target.parentElement.innerHTML
                this.$Target.parentElement.innerHTML = ''
                e.target.classList.remove('active')
                this.$Target = e.target.children.item(0)
                this.$Target.addEventListener('click', this.bindShowMovement.bind(this))
                this.$PlayerActive = (this.$PlayerActive) === 1 ? 2 : 1
                this.clearActives()
            }
        }

        async checkMovement(e) {
            this.$Target = e.target
            this.arrayOptions = []
            const peace = this.$Target.getAttribute('data-piece')
            let move = false
            switch (peace){
                case 'pawn':
                    move = await this.movePawn()
                    break
                case 'tower':
                    move = await this.moveTower()
                    break
                case 'bishop':
                    move = await this.moveBishop()
                    break
                case 'queen':
                    move = await this.moveQueen()
                    break
                case 'horse':
                    move = await this.moveHorse()
                    break
                case 'king':
                    move = await this.moveKing()
                    break
            }
            return move
        }

        async moveKing() {
            await this.setMovementX(true)
            await this.setMovementY(true)
            await this.setMovementZ(true)
            return this.arrayOptions
        }

        async moveHorse() {
            await this.setMovementL()
            return this.arrayOptions
        }

        async moveBishop() {
            await this.setMovementZ()
            return this.arrayOptions
        }

        async moveQueen() {
            await this.setMovementY()
            await this.setMovementX()
            await this.setMovementZ()
            return this.arrayOptions
        }

        async moveTower() {
            await this.setMovementY()
            await this.setMovementX()
            return this.arrayOptions
        }

        setMovementL() {
            this.$directions.l.pos1 = []
            this.$directions.l.pos2 = []
            this.$directions.l.pos3 = []
            this.$directions.l.pos4 = []
            let item
            //top
            this.$table.childNodes.forEach((itemY, index) => {
                if(index === this.$Y - 2){
                    let item = itemY.childNodes.item(this.$X - 1)
                    if(item && this.$X > 0){
                        if(item.childNodes.length){
                            let arrTopLef = []
                            arrTopLef.push([this.$X - 1, this.$Y - 2])
                            this.checkAttack(arrTopLef)
                        }else{
                            this.setOption([this.$X - 1, this.$Y - 2])
                        }
                    }
                    item = itemY.childNodes.item(this.$X + 1)
                    if(item && this.$X < 7){
                        if(item.childNodes.length){
                            let arrTopRig = []
                            arrTopRig.push([this.$X + 1, this.$Y - 2])
                            this.checkAttack(arrTopRig)
                        }else{
                            this.setOption([this.$X + 1, this.$Y - 2])
                        }
                    }
                }
                //line
                if(index === this.$Y){
                    //left
                    console.log(this.$X)
                    if(this.$X > 1){
                        if(this.$Y > 0){
                            item = this.$table.childNodes.item(index - 1).childNodes.item(this.$X - 2)
                            if(item.childNodes.length){
                                let arrLefTop = []
                                arrLefTop.push([this.$X - 2, index - 1])
                                this.checkAttack(arrLefTop)
                            }else{
                                this.setOption([this.$X - 2, index - 1])
                            }
                        }
                        if(this.$Y < 7) {
                            item = this.$table.childNodes.item(index + 1).childNodes.item(this.$X - 2)
                            if (item.childNodes.length) {
                                let arrLefBot = []
                                arrLefBot.push([this.$X - 2, index + 1])
                                this.checkAttack(arrLefBot)
                            } else {
                                this.setOption([this.$X - 2, index + 1])
                            }
                        }
                    }
                    //right
                    if(this.$X < 6){
                        if(this.$Y > 0){
                            item = this.$table.childNodes.item(index - 1).childNodes.item(this.$X + 2)
                            if(item.childNodes.length){
                                let arrLefTop = []
                                arrLefTop.push([this.$X + 2, index - 1])
                                this.checkAttack(arrLefTop)
                            }else{
                                this.setOption([this.$X + 2, index - 1])
                            }
                        }
                        if(this.$Y < 7) {
                            item = this.$table.childNodes.item(index + 1).childNodes.item(this.$X + 2)
                            console.log(item, 'aa')
                            if (item.childNodes.length) {
                                let arrLefBot = []
                                arrLefBot.push([this.$X + 2, index + 1])
                                this.checkAttack(arrLefBot)
                            } else {
                                this.setOption([this.$X + 2, index + 1])
                            }
                        }
                    }
                }
                //bottom
                if(index === this.$Y + 2){
                    let item = itemY.childNodes.item(this.$X - 1)
                    if(item && this.$X > 0){
                        if(item.childNodes.length){
                            let arrBotLef = []
                            arrBotLef.push([this.$X - 1, this.$Y + 2])
                            this.checkAttack(arrBotLef)
                        }else{
                            this.setOption([this.$X - 1, this.$Y + 2])
                        }
                    }
                    item = itemY.childNodes.item(this.$X + 1)
                    if(item && this.$X < 7){
                        if(item.childNodes.length){
                            let arrBotRig = []
                            arrBotRig.push([this.$X + 1, this.$Y + 2])
                            this.checkAttack(arrBotRig)
                        }else{
                            this.setOption([this.$X + 1, this.$Y + 2])
                        }
                    }
                }
            })
            return this.arrayOptions
        }

        setMovementZ(limit) {
            this.$directions.z.pos1.val = this.$X
            this.$directions.z.pos1.max = []
            this.$directions.z.pos2.val = this.$X
            this.$directions.z.pos2.max = []
            this.$directions.z.pos3.val = this.$X
            this.$directions.z.pos3.max = []
            this.$directions.z.pos4.val = this.$X
            this.$directions.z.pos4.max = []

            for(let $i=(this.$Y-1);$i>=0;$i--){
                const item = this.$table.childNodes.item($i)
                //top left
                if(this.$directions.z.pos1.val > 0 && !this.$directions.z.pos1.max.length){
                    this.$directions.z.pos1.val =  parseInt(this.$directions.z.pos1.val) - 1
                    const pos1 = item.childNodes.item(this.$directions.z.pos1.val)

                    if(pos1.childNodes.length){
                        this.$directions.z.pos1.max.push([this.$directions.z.pos1.val, $i])
                        this.checkAttack(this.$directions.z.pos1.max)
                    }else{
                        if(!this.$directions.z.pos1.max.length){
                            this.setOption([this.$directions.z.pos1.val, $i])
                        }
                    }
                }
                //top right
                if(this.$directions.z.pos2.val < 7 && !this.$directions.z.pos2.max.length){
                    this.$directions.z.pos2.val =  parseInt(this.$directions.z.pos2.val) + 1
                    const pos2 = item.childNodes.item(this.$directions.z.pos2.val)
                    if(pos2.childNodes.length){
                        this.$directions.z.pos2.max.push([this.$directions.z.pos2.val, $i])
                        //console.log(this.$directions.z.pos2.max)
                        this.checkAttack(this.$directions.z.pos2.max)
                    }else{
                        this.setOption([this.$directions.z.pos2.val, $i])
                    }
                }
                if(limit && $i < this.$Y){
                    $i = 0
                }
            }
            for(let $i=(this.$Y+1);$i<=7;$i++){
                const item = this.$table.childNodes.item($i)
                //bottom left
                if(this.$directions.z.pos3.val > 0 && !this.$directions.z.pos3.max.length){
                    this.$directions.z.pos3.val =  parseInt(this.$directions.z.pos3.val) - 1
                    const pos3 = item.childNodes.item(this.$directions.z.pos3.val)
                    if(pos3.childNodes.length){
                        this.$directions.z.pos3.max.push([this.$directions.z.pos3.val, $i])
                        this.checkAttack(this.$directions.z.pos3.max)
                    }else{
                        if(!this.$directions.z.pos3.max.length){
                            this.setOption([this.$directions.z.pos3.val, $i])
                        }
                    }
                }
                //bottom right
                if(this.$directions.z.pos4.val < 7 && !this.$directions.z.pos4.max.length){
                    this.$directions.z.pos4.val =  parseInt(this.$directions.z.pos4.val) + 1
                    const pos4 = item.childNodes.item(this.$directions.z.pos4.val)
                    if(pos4.childNodes.length){
                        this.$directions.z.pos4.max.push([this.$directions.z.pos4.val, $i])
                        //console.log(this.$directions.z.pos4.max)
                        this.checkAttack(this.$directions.z.pos4.max)
                    }else{
                        this.setOption([this.$directions.z.pos4.val, $i])
                    }
                }
                if(limit && $i > this.$Y){
                    $i = 7
                }
            }
            return this.arrayOptions
        }

        setMovementX(limit) {
            this.$directions.x.max = []
            this.$directions.x.min = []
            //search min e max
            this.$table.childNodes.item(this.$Y).childNodes.forEach((itemX, index) => {
                let positions = this.getPositions(itemX)
                if(limit){
                    this.$directions.x.min = [(this.$X > 0) ? this.$X - 1 : this.$X, positions[1]];
                    this.$directions.x.max = [(this.$X < 7) ? this.$X + 1 : this.$X, positions[1]];
                }else{
                    if(itemX.childNodes.length && index < this.$X){
                        this.$directions.x.min = [positions[0], positions[1]]
                    }
                    if(itemX.childNodes.length && positions[0] > this.$X){
                        !this.$directions.x.max.length ? this.$directions.x.max = [positions[0], positions[1]] : null
                    }
                }
            })
            //set default
            if(!this.$directions.x.min.length){
                this.$directions.x.min = [0, this.$Y]
            }else{
                let arrMin = []
                arrMin.push([this.$directions.x.min[0], this.$directions.x.min[1]])
                this.checkAttack(arrMin)
            }
            if(!this.$directions.x.max.length){
                this.$directions.x.max = [7, this.$Y]
            }else{
                let arrMax = []
                arrMax.push([this.$directions.x.max[0], this.$directions.x.max[1]])
                this.checkAttack(arrMax)
            }
            //set positions X
            this.$table.childNodes.item(this.$Y).childNodes.forEach((itemX, index) => {
                let positions = this.getPositions(itemX)
                //check prev(s)
                if(index < this.$X && index >= this.$directions.x.min[0]){
                    if(index === this.$directions.x.min[0]){
                        if((itemX.childNodes.length && itemX.childNodes.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()) || !itemX.childNodes.length){
                            this.setOption([positions[0], positions[1]])
                        }
                    }else{
                        this.setOption([positions[0], positions[1]])
                    }
                }
                //check next(s)
                if(index > this.$X && index <= this.$directions.x.max[0]){
                    if(index === this.$directions.x.max[0]){
                        if((itemX.childNodes.length && itemX.childNodes.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()) || !itemX.childNodes.length){
                            this.setOption([positions[0], positions[1]])
                        }
                    }else{
                        this.setOption([positions[0], positions[1]])
                    }
                }
            })
        }

        setMovementY(limit){
            this.$directions.y.max = []
            this.$directions.y.min = []
            //search min e max
            this.$table.childNodes.forEach((itemY, index) => {
                let y = itemY.childNodes.item(this.$X)
                let positions = this.getPositions(y)
                if(limit){
                    if(this.$table.childNodes.item(this.$Y - 1)){
                        this.$directions.y.min = [this.$X, this.$Y - 1];
                    }else{
                        this.$directions.y.min = [this.$X, this.$Y];
                    }
                    if(this.$table.childNodes.item(this.$Y + 1)){
                        this.$directions.y.max = [this.$X, this.$Y + 1];
                    }else{
                        this.$directions.y.max = [this.$X, this.$Y];
                    }
                }else{
                    if(y.childNodes.length && positions[1] < this.$Y){
                        if(this.$PlayerActive === 1){
                            this.$directions.y.min = [positions[0], positions[1]]
                        }else{
                            this.$directions.y.min = [positions[0], positions[1]]
                        }
                    }
                    if(y.childNodes.length && positions[1] > this.$Y){
                        !this.$directions.y.max.length ? this.$directions.y.max = [positions[0], positions[1]] : null
                    }
                }
            })
            //set default
            if(!this.$directions.y.min.length){
                this.$directions.y.min = [this.$X, 0]
            }else{
                let arrMin = []
                arrMin.push([this.$directions.y.min[0], this.$directions.y.min[1]])
                this.checkAttack(arrMin)
            }
            if(!this.$directions.y.max.length){
                this.$directions.y.max = [this.$X, 7]
            }else{
                let arrMax = []
                arrMax.push([this.$directions.y.max[0], this.$directions.y.max[1]])
                this.checkAttack(arrMax)
            }
            //set positions Y
            this.$table.childNodes.forEach((item) => {
                let y = item.childNodes.item(this.$X)
                let positions = this.getPositions(y)
                //check prev(s)
                if(positions[1] < this.$Y && positions[1] >= this.$directions.y.min[1]){
                    if(positions[1] === this.$directions.y.min[1]){
                        if((y.childNodes.length && y.childNodes.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()) || !y.childNodes.length){
                            this.setOption([positions[0], positions[1]])
                        }
                    }else{
                        this.setOption([positions[0], positions[1]])
                    }
                }
                //check next(s)
                if(positions[1] > this.$Y && positions[1] <= this.$directions.y.max[1]){
                    if(positions[1] === this.$directions.y.max[1]){
                        if((y.childNodes.length && y.childNodes.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()) || !y.childNodes.length){
                            this.setOption([positions[0], positions[1]])
                        }
                    }else{
                        this.setOption([positions[0], positions[1]])
                    }
                }
            })
            return this.arrayOptions
        }

        movePawn() {
            if(this.$Y === 0 || this.$Y === 7){ return false }
            let pos1, pos2, next, initial
            if(this.$Target.getAttribute('data-player') === '1'){
                pos1 = this.$Y + 1
                pos2 = this.$Y + 2
                initial = 1
            }else{
                pos1 = this.$Y - 1
                pos2 = this.$Y - 2
                initial = 6
            }

            next = this.$table.childNodes.item(pos1).childNodes.item(this.$X)
            if(!next.childNodes.length){
                this.setOption([this.$X, pos1])
                //double movement
                if(this.$Y === initial){
                    next = this.$table.childNodes.item(pos2).childNodes.item(this.$X)
                    if(!next.childNodes.length){
                        this.setOption([this.$X, pos2])
                    }
                }
            }
            //check attack
            if(this.$X !== 7){
                const nextCheckAttach2 = this.$table.children.item(pos1).children.item(this.$X + 1)
                if(nextCheckAttach2.childNodes.length){
                    let checkAttack = []
                    checkAttack.push([parseInt(this.$X + 1), pos1])
                    this.checkAttack(checkAttack)
                }else{

                }
            }
            if(this.$X !== 0){
                const nextCheckAttach1 = this.$table.children.item(pos1).children.item(this.$X - 1)
                if(nextCheckAttach1.childNodes.length){
                    let checkAttack = []
                    checkAttack.push([parseInt(this.$X - 1), pos1])
                    this.checkAttack(checkAttack)
                }
            }

            return this.arrayOptions
        }

        getPositions(el){
            //el = span
            let x = [...el.parentNode.children].indexOf(el)
            let y = [...el.parentElement.parentNode.children].indexOf(el.parentNode)
            return [x, y]
        }

        checkAttack(arrayCheck){
            let callback = false
            console.log(arrayCheck)
            arrayCheck.forEach((item) => {
                let elem = this.$table.children.item(item[1]).children.item(item[0])
                //console.log(elem)
                if(elem.childNodes.length && elem.children.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()){
                    this.setOption([item[0], item[1], 'attack'])
                    callback = true
                }
            })
            return callback
        }

        setOption(option){
            this.arrayOptions.push(option)
            //save X, Y, class(optional)
        }

        checkNextMovement(movementOptionElement){
            return (movementOptionElement.children.length)
        }
        createDirection(){
            return {
                'y' : {
                    'min' : [],
                    'max' : []
                },
                'x' : {
                    'min' : [],
                    'max' : []
                },
                'z' : {
                    'pos1' : {
                        'val' : 0,
                        'max' : []
                    },
                    'pos2' : {
                        'val' : 0,
                        'max' : []
                    },
                    'pos3' : {
                        'val' : 0,
                        'max' : []
                    },
                    'pos4' : {
                        'val' : 0,
                        'max' : []
                    }
                },
                'l' : {
                    'pos1' : [],
                    'pos2' : [],
                    'pos3' : [],
                    'pos4' : []
                },
            }
        }

        clearActives(){
            const actives = document.querySelectorAll('span.active')
            actives.forEach((active) =>{
                active.classList.remove('active')
            })
            const target = document.querySelector('span.target')
            if(target){
                target.classList.remove('target')
            }
            return true
        }

        checkPlay(e){
            return (e.target.getAttribute('data-player') === this.$PlayerActive.toString() || this.$testing)
        }

        checkPiece(e){
            return (e.childNodes.length && e.children.item(0).getAttribute('data-piece') === 'king')
        }

        endGame(){
            this.$winner = this.$PlayerActive
            if (confirm(`O jogador ganhador foi: ${this.$winner}! Deseja jogar novamente?`)) {
                document.location.reload(true)
            }else{
                this.$table.innerHTML = `<strong>Obrigado pela partida.</strong>`
            }
        }

    }

//initial
    new Chess()



}())
</script>

</body>
</html>