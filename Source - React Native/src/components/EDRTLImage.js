
import React from 'react'
import { View, Image } from 'react-native'
import { strings, isRTL } from "../locales/i18n";


export default class EDEDRTLImage extends React.Component {

    render() {
        return (<Image
            source={this.props.source}
            resizeMode='contain'
            style={[{ transform: [{ scaleX: isRTL ? -1 : 1 }] }, this.props.style]}
        />)
    }
}