class Matrix {
    // на дз - m * n
    constructor(elem, rows = 20, cols = 20) {
        this.elem = elem;
        this.rows = rows;
        this.cols = cols;
        this.cells = [];
    }

    create() {
        for (let i = 0; i < this.rows * this.cols; i++) {
            let div = document.createElement('div');
            if (i % this.cols === 0) {
                div.classList.add('row-start');
            }
            div.setAttribute('data-game', '');
            this.elem.appendChild(div);
            this.cells[i] = '';
        }
        // console.log(this.elem.style.width = 1 + 'px');
        // this.elem.style.width = (this.cols * 20) + 'px';
    }

    getCell(x, y) {
        let num = this._calcNum(x, y);
        return this.cells[num];
    }

    setCell(x, y, val) {
        let num = this._calcNum(x, y);
        this.cells[num] = val;
        // console.log(this.cells[num]);
        this.elem.children[num].setAttribute('data-game', val);
    }

    // пересчитать номер строки и номер столбца в переменную i
    _calcNum(x, y) {
        // неправильно
        // return Math.floor((Math.random() * 400));
        return (y - 1) * this.cols + x - 1;
    }
}

export { Matrix };
