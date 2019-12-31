import React, { Component } from "react";
import { StyleSheet, Text, View, Image } from "react-native";
import { EDColors } from "../utils/EDColors";
import { EDFonts } from "../utils/EDFontConstants";
import Validations from "../utils/Validations";
import BaseContainer from "../containers/BaseContainer"
import { showDialogue } from "../utils/EDAlert";
import { Messages } from "../utils/Messages";
import { strings } from "../locales/i18n";
import { TextFieldTypes, FORGOT_PASSWORD_URL, debugLog, DEFAULT_ALERT_TITLE } from "../utils/EDConstants";
import Assets from "../assets";
import EDRTLTextInput from "../components/EDRTLTextInput";
import EDThemeButton from "../components/EDThemeButton";
import { apiPost } from "../utils/ServiceManager";
import { KeyboardAwareScrollView } from "react-native-keyboard-aware-scroll-view";

export default class ForgotPasswordContainer extends Component {

    constructor(props) {
        super(props);
        this.validationsHelper = new Validations();
    }

    state = {
        // USER EMAIL
        strEmailAddress: "",

        // UTILS
        shouldPerformValidation: false,
        isLoading: false
    };

    // EMAIL CHANGE HANDLER
    emailDidChange = (newEmail) => {
        this.setState({ strEmailAddress: newEmail })
    }

    // BACK EVENT
    navigateToPreviousScreen = () => {
        this.props.navigation.goBack()
    }

    // SUBMIT BUTTON EVENT
    buttonSendEmailPressed = () => {

        // VALIDATIONS
        this.setState({ shouldPerformValidation: true });

        if (this.state.strEmailAddress.trim() == "") {
            return
        }

        if (this.validationsHelper.validateEmail(this.state.strEmailAddress, Messages.emptyEmail).trim() == "") {

            this.setState({ isLoading: true });

            let dictForgotPassword = { email: this.state.strEmailAddress }
            // CALL FORGOT PASSWORD API HERE...
            apiPost(FORGOT_PASSWORD_URL, dictForgotPassword, (responseSuccess, responseMessage) => {

                showDialogue(responseMessage, [], DEFAULT_ALERT_TITLE, this.navigateToPreviousScreen)
                this.setState({ isLoading: false });
            }, (responseFailure) => {
                showDialogue((responseFailure && responseFailure.message) || Messages.generalWebServiceError)
                this.setState({ isLoading: false });
            }, {})
        }


    }

    render() {
        return (
            <BaseContainer
                title={strings("ScreenTitles.forgotPassword")}
                left={Assets.back}
                onLeft={this.navigateToPreviousScreen}
                loading={this.state.isLoading}
            >

                <KeyboardAwareScrollView style={{ flex: 1, padding: 20 }}>


                    <View style={{ flex: 1 }}>
                        {/* EMAIL TEXT INPUT */}
                        <EDRTLTextInput placeholder={strings("Login.email")} type={TextFieldTypes.email} onChangeText={this.emailDidChange}
                            error={
                                this.state.shouldPerformValidation
                                    ? this.validationsHelper.validateEmail(
                                        this.state.strEmailAddress,
                                        Messages.emptyEmail
                                    )
                                    : undefined
                            } />
                    </View>
                    <View style={{
                        flex: 6,
                        justifyContent: "space-evenly",
                        alignItems: "center",
                    }}
                    >
                        {/* LOCK IMAGE & INFO TEXT*/}
                        <Image source={Assets.passwordlock} style={{ marginVertical: 40 }} />
                        <View style={{ alignItems: "center", justifyContent: "center" }}>
                            <Text style={styles.messageStyle}>
                                {Messages.forgotPassword.firstLine}
                            </Text>
                            <Text style={styles.messageStyle}>
                                {Messages.forgotPassword.secondLine}
                            </Text>
                        </View>
                    </View>

                    {/* BOTTOM SECTION */}
                    <View style={{ flex: 2, marginHorizontal: 20, justifyContent: "center" }}>
                        {/* SUBMIT BUTTON */}
                        <View>
                            <EDThemeButton label={strings("ForgotPassword.sendMail")} onPress={this.buttonSendEmailPressed} />
                        </View>
                    </View>
                </KeyboardAwareScrollView>

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
