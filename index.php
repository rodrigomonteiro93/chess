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
        newY;
        constructor() {
            this.$Y = ''
            this.$X = ''
            this.arrayOptions = []
            this.$PlayerActive = Math.floor(Math.random() * 2 + 1)
            this.$Target = ''
            this.$winner = ''
            this.$testing = true
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
                elem.addEventListener('click', this.bindGoMovement.bind(this))
            })
        }

        bindGoMovement(e) {
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
            }
            return move
        }
        moveTower() {

            this.$table.childNodes.forEach((itemY) => {
                const y = itemY.childNodes.item(this.$X)
                if(!y.childNodes.length){
                    const positions = this.getPositions(y)
                    //options.push([this.$X, positions[1], (y.children)])
                    this.setOption([positions[0], positions[1]])
                }
            })

            return this.arrayOptions
        }

        movePawn() {
            let newY = ''
            this.$Target.getAttribute('data-player') === '1' ? newY = this.$Y + 1 : newY = this.$Y - 1

            const movementOptionEl = this.$table.children.item(newY).children
            const movementOptionElement = movementOptionEl.item(this.$X)

            let options = []
            this.$table.children.item(parseInt(this.$Y)).childNodes.forEach((item, x) => {
                if(x === (this.$X - 1) || x === (parseInt(this.$X) + 1)){
                    options.push([x, newY, (item.children)])
                }
            })
            if(options.length){
                this.checkAttack(options)
            }

            if(!this.checkNextMovement(movementOptionElement)){
                const positions = this.getPositions(movementOptionElement);
                this.setOption([positions[0], positions[1]])
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
            arrayCheck.forEach((item) => {
                const elem = this.$table.children.item(item[1]).children.item(item[0])
                if(elem.childNodes.length && elem.children.item(0).getAttribute('data-player') !== this.$PlayerActive.toString()){
                    this.setOption([item[0], item[1], 'attack'])
                }
            })
        }

        setOption(option){
            this.arrayOptions.push(option)
            //save X, Y, class(optional)
        }

        checkNextMovement(movementOptionElement){
            return (movementOptionElement.children.length)
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