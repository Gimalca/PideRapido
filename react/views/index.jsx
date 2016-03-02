'use strict'

import React from 'react';
import ColorInput from '../src/components/ColorInput';

var Cuadro =  React.createClass({
  handleClick: function (evt) {
    this.props.handleClick(this.props.index);
  },
  render: function () {
    var estilo = {
      background: this.props.color
    };
    
    return <div onClick={this.handleClick} style={estilo} className='cuadro'>
    </div>;
  }
})

var Container = React.createClass({
  getInitialState: function () {
    return {
      squares: []
    };
  },
  handleAddSquare: function (color) {
    this.state.squares.push(color);

    this.setState({
        squares: this.state.squares
    });
  },
  removeSquare: function (index) {
    this.state.squares.splice(index, 1);
    this.setState({
      squares: this.state.squares
    });
  },
  render: function () {
    var squares = this.state.squares.map(function (color, i) {
      return <Cuadro index={i} key={i} color={color} handleClick={this.removeSquare} />;
    }.bind(this));
    return <div>
      <ColorInput onAdd={this.handleAddSquare} />
      <div>
        {squares}
      </div>
    </div>;
  }
});

React.render(<Container />, document.getElementById('container'));
