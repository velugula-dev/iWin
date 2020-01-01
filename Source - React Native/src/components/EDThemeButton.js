import React, { Component } from "react";
import { Text, TouchableOpacity, StyleSheet } from 'react-native'
import { EDColors } from "../utils/EDColors"
import { EDFonts } from "../utils/EDFontConstants"
import { heightPercentageToDP } from "react-native-responsive-screen";

export default class EDHThemeButton extends Component {
    render() {
        return (
            <TouchableOpacity style={[stylesButton.themeButton, this.props.style]}
                onPress={this.props.onPress}
            >
                <Text style={[stylesButton.themeButtonText, this.props.textStyle]}>{this.props.label}</Text>
            </TouchableOpacity>
        );
    }
}

stylesButton = StyleSheet.create({
    themeButton: {
        paddingTop: 10,
        paddingBottom: 10,
        backgroundColor: EDColors.primary,
        borderRadius: 5,
        marginTop: 10
    },
    themeButtonText: {
        color: EDColors.white,
        textAlign: "center",
        paddingLeft: 10,
        paddingRight: 10,
        fontFamily: EDFonts.medium,
        fontSize: heightPercentageToDP("2.3%")
    }
})