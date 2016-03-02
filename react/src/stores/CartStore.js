'use strict';
import BaseStore from './BaseStore';
import {constants} from '../constants';

var squares = [];

function _addSquare(params) {
    squares.push(params.square)
}

class SquareStore extends BaseStore {
    register(payload) {
        switch(payload.action.actionType) {
            case constants.ADD_PRODUCT:
                _addSquare(payload.action.data);
                this.emitChange();
                break;
        }
    }
}

var squareStore = new SquareStore();

export default squareStore;
