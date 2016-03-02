'use strict';

export var capitalize = function (string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

export var camelCase = function (constant) {
    return constant.split('_').map(function (word, i) {
        if (i == 0) return word.toLowerCase();
        else return capitalize(word); 
    }).join('');
}
