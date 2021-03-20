(async function (){
    class Chess extends Features{

        constructor(testing) {
            super(testing);
            this.$Y = false
            this.$X = false
            this.arrayOptions = []
            this.$PlayerActive = Math.floor(Math.random() * 2 + 1)
            this.$Target = false
            this.$winner = false
            this.$testing = testing
            this.$table = document.querySelector('.table')
            this.$directions = this.createDirection()
            this.createTable()
        }

        async createTable(){
            this.$table.append(...await this.getPieces(1));
            this.$table.append(await this.getPawn(1));
            this.$table.append(await this.getEmpty());
            this.$table.append(await this.getEmpty());
            this.$table.append(await this.getEmpty());
            this.$table.append(await this.getEmpty());
            this.$table.append(await this.getPawn(2));
            this.$table.append(...await this.getPieces(2));

            this.toast(this.$PlayerActive)
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
                this.toast(this.$PlayerActive)
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
        getPieces(player){
            let div = document.createElement('div');
            return pieces.map((item, index) => {
                let span = document.createElement('span');
                let i = document.createElement('i');
                let classI = item.icon.split(' ')
                i.classList.add(classI[0])
                i.classList.add(classI[1])
                i.setAttribute('data-player', player)
                i.setAttribute('data-piece', item.piece)
                i.addEventListener('click', this.bindShowMovement.bind(this))
                span.appendChild(i)
                div.appendChild(span);
                return div;
            });
        }
        getPawn(player){
            let div = document.createElement('div')
            for(let $i = 0;$i<=7;$i++){
                let span = document.createElement('span')
                let i = document.createElement('i')
                let classI = pawn[0].icon.split(' ')
                i.classList.add(classI[0])
                i.classList.add(classI[1])
                i.setAttribute('data-player', player)
                i.setAttribute('data-piece', pawn[0].piece)
                i.addEventListener('click', this.bindShowMovement.bind(this))
                span.appendChild(i)
                div.appendChild(span);
            }
            return div
        }
        getEmpty(){
            let div = document.createElement('div')
            for(let $i = 0;$i<=7;$i++){
                let span = document.createElement('span')
                div.appendChild(span);
            }
            return div
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
            if(e.childNodes.length){
                this.addPieceRemoved(e.children.item(0).getAttribute('data-piece'), this.$PlayerActive)
            }
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
    const testing = false
    new Chess(testing)
}())