import React, { Component } from "react";
import { StyleSheet, Text, View, Button, TouchableOpacity, TextInput, Image, Platform } from "react-native";

import { EDColors } from '../utils/EDColors'
import { EDFonts } from '../utils/EDFontConstants'
import { TextFieldTypes } from "../utils/EDConstants";
import EDRTLView from "./EDRTLView";
import Assets from "../assets";
import EDRTLImage from "./EDRTLImage";
import { isRTL } from "../locales/i18n";
import { heightPercentageToDP } from "react-native-responsive-screen";

export default class EDEDRTLTextInput extends Component {


    constructor(props) {
        super(props);
    }

    state = {
        showPassword: false
    };


    fieldKeyboardType() {
        if (this.props.type === TextFieldTypes.email) {
            return "email-address"
        } else if (this.props.type === TextFieldTypes.password) {
            return "default"
        } else if (this.props.type === TextFieldTypes.amount || this.props.type === TextFieldTypes.phone) {
            return "number-pad";
        } else if (this.props.type === TextFieldTypes.description) {
            return "default"
        }
    }

    shouldAutoCapitalise() {
        if (this.props.type === TextFieldTypes.email) {
            return "none";
        } else if (this.props.type === TextFieldTypes.password) {
            return "none";
        }

    }

    callTextChangeHandler = (textToSend) => {
        if (this.props.callBackFromParent != undefined) {
            this.props.callBackFromParent(textToSend, this.props.type)
        }
    }

    showHidePassword = () => {
        this.setState({ showPassword: !this.state.showPassword })
    }

    render() {
        return (
            <View opacity={this.props.opacity || 1.0} pointerEvents={this.props.pointerEvents || "auto"} style={[this.props.style, { marginTop: 10 }]}>
                <EDRTLView style={{ alignItems: 'center' }}>
                    {this.props.icon
                        ? <EDRTLImage style={{ width: 15, height: 15 }} source={this.props.icon || Assets.menu} />
                        : null}
                    <TextInput
                        value={this.props.initialValue}
                        style={[styles.textFieldStyle, { textAlign: ((isRTL ? 'right' : 'left')) }]}
                        placeholder={this.props.placeholder || ""}
                        tintColor={EDColors.primary}
                        autoCapitalize={this.shouldAutoCapitalise()}
                        keyboardType={this.fieldKeyboardType()}
                        autoCorrect={false}
                        selectionColor={EDColors.primary}
                        onChangeText={this.props.onChangeText || undefined}
                        secureTextEntry={this.props.type == TextFieldTypes.password && !this.state.showPassword}
                        direction={isRTL ? 'rtl' : 'ltr'}
                        maxLength={this.props.type == TextFieldTypes.phone ? 10 : 60}
                    />
                    {this.props.type == TextFieldTypes.password
                        ? <TouchableOpacity style={{}} onPress={this.showHidePassword}>
                            <Image source={this.state.showPassword ? Assets.eyeopen : Assets.eyeclose} />
                        </TouchableOpacity>
                        : null}

                </EDRTLView>

                <View style={{ backgroundColor: this.props.error ? EDColors.error : EDColors.primary, height: 1 }}></View>

                {this.props.error
                    ? <EDRTLView style={{}}>
                        <Text style={styles.errorTextStyle}>{this.props.error}</Text>
                    </EDRTLView>
                    : null}
            </View>

        );
    }
}
const styles = StyleSheet.create({
    textFieldStyle: {
        marginHorizontal: 0,
        flex: 1,
        height: heightPercentageToDP("6.7%"),
        fontFamily: EDFonts.regular,
        fontSize: heightPercentageToDP("2.3%"),
        color: EDColors.text,

    },
    errorTextStyle: {
        fontSize: heightPercentageToDP("1.7%"),
        fontFamily: EDFonts.regular,
        color: EDColors.error,
    }
})