import React from 'react'
import { View } from 'react-native'
import { strings, isRTL } from "../locales/i18n";

export default class EDRTLView extends React.Component {
    render() {
        return (<View pointerEvents={this.props.pointerEvents || "auto"} opacity={this.props.opacity || 1} style={[{
            flexDirection: isRTL ? 'row-reverse' : 'row'
        }, this.props.style]}>
            {this.props.children}
        </View>)
    }
}