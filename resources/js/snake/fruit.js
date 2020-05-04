import { Elem } from './elem';

class Fruit extends  Elem {
    constructor(matrix, cords) {
        super(matrix, cords);
        this.value = 'fruit';
    }
}

export { Fruit };
