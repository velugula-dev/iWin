import React, { Component } from "react";
import { Platform, StyleSheet, Text, View, Image } from "react-native";
import { EDColors } from "../utils/EDColors";
import { EDFonts } from "../utils/EDFontConstants";
import Validations from "../utils/Validations";
import BaseContainer from "../containers/BaseContainer"
import { showDialogue, showNotImplementedAlert } from "../utils/EDAlert";
import { Messages } from "../utils/Messages";
import { strings } from "../locales/i18n";
import { TextFieldTypes, FORGOT_PASSWORD_URL, debugLog, DEFAULT_ALERT_TITLE } from "../utils/EDConstants";
import Assets from "../assets";
import EDRTLTextInput from "../components/EDRTLTextInput";
import EDThemeButton from "../components/EDThemeButton";
import { apiPost } from "../utils/ServiceManager";

export default class FeedbackContainer extends Component {

    constructor(props) {
        super(props);
    }

    state = {
        isLoading: false
    };


    navigateToPreviousScreen = () => {
        this.props.navigation.goBack()
    }

    render() {
        return (
            <BaseContainer
                title={strings("ScreenTitles.feedback")}
                left={Assets.back}
                onLeft={this.navigateToPreviousScreen}
                loading={this.state.isLoading}
            >

                <View style={{ flex: 1, padding: 20, backgroundColor: 'red' }}>

                    <View style={{ flex: 1, backgroundColor: 'yellow' }}>
                    </View>


                </View>

            </BaseContainer>
        );
    }
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    messageStyle: {
        fontFamily: EDFonts.regular,
        fontSize: 14,
        color: EDColors.text,
        textAlign: 'center'
    }
});
