import { Elem } from './elem';

class Wall extends  Elem {
    constructor(matrix, cords) {
        super(matrix, cords);
        this.value = 'wall';
    }
}

export { Wall };
