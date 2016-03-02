'use strict';

var dispatcher = require('./lib/dispatcher'),
    constants  = require('./constants'),
    string     = require('./utils').string;

function _registerActions (actions, cb) {
    Object.keys(actions).forEach(function (constant) {
        var name = string.camelCase(constant);
        if (cb && cb instanceof Function) {
            exports[name] = cb.call(null, constant);
        }
    });
}

_registerActions(constants, function (constant) {
    return function (data) {
        dispatcher.handleViewAction({
            actionType: constant,
            data: data
        });
        return true;
    };
});
