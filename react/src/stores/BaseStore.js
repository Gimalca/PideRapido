'use strict';

import EventEmitter from 'events';
import {event} from '../constants/actions';
import {dispatcher} from '../lib/Dispatcher';

class BaseStore extends EventEmitter {

  constructor() {
      super();
      this.register = this.register.bind(this);
      dispatcher.register(this.register);
  }

  register(payload) {
  }

  emitChange() {
    this.emit(events.CHANGE);
  }

  addChangeListener(callback) {
      this.on(events.CHANGE, callback);
  }

  removeChangeListener(callback) {
    this.removeListener(events.CHANGE, callback);
  }

}

export default BaseStore;
