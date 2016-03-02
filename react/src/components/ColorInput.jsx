'use strict';

import React from 'react';

class ColorInput  extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            text: ''
        };

        this.handleChange = (evt) => {
            var text = evt.target.value;
            this.setState({
            text: text
            })
        };

        this.handleClick = (evt) => {
            this.props.onAdd(this.state.text);
            this.setState({
                text: ''
            });
        }
    }
  render() {
    return (<div>
        <input onChange={this.handleChange} 
            type='text' placeholder='Elija el color' 
            value={this.state.text} 
            />
            <button onClick={this.handleClick} >Agregar</button>
           </div>);
  }
}

export default ColorInput
