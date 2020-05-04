import { Elem } from './elem';

class Snake extends Elem {

    constructor(matrix, cords, course) {
        super(matrix, cords);
        this.course = course;
        this.alive = true;
        this.value = 'snake';
        this.newCourse = course;
        this.eaten = false;
    }
    move() {
        if (!this.alive) {
            return;
        }
        this.eaten = false;
        this.course = this.newCourse;

        let head = this.cords[0].slice();
        // console.log(head);
        // return;

        switch (this.course) {
            case 'right':
                head[0]++;
                break;
            case 'left':
                head[0]--;
                break;
            case 'up':
                head[1]--;
                break;
            case 'down':
                head[1]++;
                break;
        }

        if (!this._checkAlive(head)) {
            this.alive = false;
            return;
        }

        let find = this.matrix.getCell(head[0], head[1]);
        if (find === 'wall' || find === 'snake') {
            this.alive = false;
            return;
        }

        if (find === 'fruit') {
            this.eaten = true;
        } else {
            let tail = this.cords.pop();
            this.matrix.setCell(tail[0], tail[1], '');
        }

        this.cords.unshift(head);
        this.matrix.setCell(head[0], head[1], 'snake');
        // console.log(this.cords);
    }

    _checkAlive(head) {
        return head[0] >= 1 && head[0] <= this.matrix.cols &&
            head[1] >= 1 && head[1] <= this.matrix.rows;
    }
}

export { Snake };
