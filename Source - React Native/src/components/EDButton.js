import React, { Component } from "react";
import { Text, TouchableOpacity, StyleSheet, View } from 'react-native'
import { EDColors } from "../utils/EDColors"
import { EDFonts } from "../utils/EDFontConstants"
import EDRTLView from "./EDRTLView";
import { isRTL } from "../locales/i18n";
import { widthPercentageToDP as wp, heightPercentageToDP as hp } from 'react-native-responsive-screen';

export default class EDButton extends Component {
    render() {
        return (

            <EDRTLView pointerEvents={this.props.pointerEvents || "auto"} style={[{ marginVertical: 10, flexDirection: isRTL ? 'row' : 'row-reverse' }, this.props.newStyle]}>
                <TouchableOpacity pointerEvents={this.props.pointerEvents || "auto"} style={[stylesButtonPlain.themeButtonPlain, this.props.buttonStyle]}
                    onPress={this.props.onPress}
                >
                    <Text style={[stylesButtonPlain.themeButtonTextPlain, this.props.textStyle]}>{this.props.label}</Text>
                </TouchableOpacity>
            </EDRTLView>
        );
    }
}

stylesButtonPlain = StyleSheet.create({
    themeButtonPlain: {
        borderRadius: 5,
    },
    themeButtonTextPlain: {
        color: EDColors.primary,
        fontFamily: EDFonts.regular,
        fontSize: hp('2%'),

    }
})