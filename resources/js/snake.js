require('./bootstrap');
import { Matrix } from './snake/matrix';
import { Helpers } from './snake/helpers';
import { Fruit } from './snake/fruit';
import { Wall } from './snake/wall';
import { Snake } from './snake/snake';



window.onload = function () {

    let div = document.querySelector('#fields');
    div.style.borderTop = '1px solid #000';
    div.style.borderLeft = '1px solid #000';

    // console.dir(div);
    let matrix = new Matrix(div, 20, 30);
    matrix.create();

    (new Fruit(matrix, [
        [5, 5]
    ])).show();

    let wall = new Wall(matrix, [
        [3, 7],
        [4, 7],
        [5, 7],
        [6, 7],
        [7, 7],
    ]);
    wall.show();

    let wall1 = new Wall(matrix, [
        [10, 10],
        [10, 11],
        [10, 12],
        [10, 13],
        [10, 14],
    ]);
    wall1.show();

    let wall2 = new Wall(matrix, [
        [25, 10],
        [26, 11],
        [27, 12],
        [28, 13],
        [29, 14],
    ]);
    wall2.show();



    // matrix.setCell(1, 1, 'fruit');

    let snake = new Snake(matrix, [
        [10, 10],
        [9, 10],
        [8, 10],
    ], 'right');
    snake.show();

    let score = 0;

    document.onkeydown = function(e) {
        switch (e.keyCode) {
            case 37:
                if (snake.course !== 'right') {
                    snake.newCourse = 'left';
                }
                break;
            case 38:
                if (snake.course !== 'down') {
                    snake.newCourse = 'up';
                }
                break;
            case 39:
                if (snake.course !== 'left') {
                    snake.newCourse = 'right';
                }
                break;
            case 40:
                if (snake.course !== 'up') {
                    snake.newCourse = 'down';
                }
                break;
        }
    }

    let timer = setInterval(() => {
        snake.move();

        if (!snake.alive) {
            clearInterval(timer);
            // alert('gameover');
            location.reload();
        }
        if (snake.eaten) {
            score++;
            console.log(score);
            let x, y;
            do {
                x = Helpers.random(1, matrix.cols);
                y = Helpers.random(1, matrix.rows);
            } while (matrix.getCell(x,y) !== '')

            (new Fruit(matrix, [
                [x, y]
            ])).show();

        }

    }, 100);
}
