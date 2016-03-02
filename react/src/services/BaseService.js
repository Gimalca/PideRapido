'use strict';

import {dispatcher} from '../lib/Dispatcher';

class BaseService {

    constructor() {
        super();
        this.register = this.register.bind(this)
        dispatcher.register(this.register);
    }

    register(payload) {
    
    }
}
