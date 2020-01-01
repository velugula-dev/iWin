import React from 'react'
import { View, Text, StyleSheet, Image, TouchableOpacity, Platform, Keyboard, Modal, Linking } from 'react-native'
import BaseContainer from './BaseContainer';
import { strings, isRTL } from '../locales/i18n';
import Assets from "../assets";
import EDRTLView from '../components/EDRTLView';
import EDRTLTextInput from '../components/EDRTLTextInput';
import { TextFieldTypes, debugLog, LOGIN_URL, REGISTER_URL, UserTypes, SignInType, GET_CMS_DATA, CMSPages } from '../utils/EDConstants';
import { EDColors } from '../utils/EDColors';
import { Messages } from '../utils/Messages';
import EDThemeButton from '../components/EDThemeButton';
import EDButton from '../components/EDButton';
import { EDFonts } from '../utils/EDFontConstants';
import Metrics from '../utils/Metrics';
import { showNotImplementedAlert, showNoInternetAlert, showDialogue } from '../utils/EDAlert';
import Validations from '../utils/Validations';
import { apiPost, netStatus, apiPostForFileUpload } from "../utils/ServiceManager";
import { KeyboardAwareScrollView } from 'react-native-keyboard-aware-scroll-view';
import { connect } from "react-redux";
import { saveUserLoginDetails, getValueFromAsyncStore } from '../utils/AsyncStorageHelper';
import { saveUserDetailsOnLogin } from '../redux/actions/User';
import ImagePicker from "react-native-image-picker";
import { heightPercentageToDP } from 'react-native-responsive-screen';
import ProgressLoader from '../components/ProgressLoader';
import { StackActions, NavigationActions } from "react-navigation";
import MyWebView from "react-native-webview-autoheight";
import EDHThemeButton from '../components/EDThemeButton';

const options = {
    title: strings("General.selectProfilePic"),
    storageOptions: {
        skipBackup: true,
        path: 'images',
        cameraRoll: true,
        waitUntilSaved: true

    },
    chooseFromLibraryButtonTitle: strings("General.choosePhotoTitle"),
    takePhotoButtonTitle: strings("General.capturePhotoTitle"),
    cancelButtonTitle: strings("General.cancel"),
    quality: 0.75,
    maxWidth: 200,
    maxHeight: 200
};

class SignUpContainer extends React.Component {

    constructor(props) {
        super(props);
        this.validationsHelper = new Validations();
        this.userSignUpType = SignInType.email;
        this.cmsData = [];
        this.customStyle =
            "<style>* {max-width: 100%;} body {font-size: 45px;font-family:Ubuntu-Regular}</style>";
    }

    state = {
        strEmailAddress: "",
        strPassword: "",
        strConfirmPassword: "",
        strFirstName: "",
        strLastName: "",
        shouldPerformValidation: false,
        isLoading: false,
        profileImageSource: undefined,
        shouldShowInfoView: false,
        infoText: {}
    };

    componentDidMount() {
        this.callCMSDataAPI()
    }

    callCMSDataAPI() {
        let cmsDataParams = {
            device_type: Platform.OS == "ios" ? "ios" : "android",
        };

        apiPost(
            GET_CMS_DATA,
            cmsDataParams,
            dictSuccess => {
                debugLog("dictSuccess callCMSDataAPI", dictSuccess)
                // if (dictSuccess.cms_data != undefined && dictSuccess.cms_data.length > 0) {

                if (dictSuccess.cms_data != undefined) {
                    this.cmsData = dictSuccess.cms_data || []
                }
            },
            (dictFailure, message) => {
                debugLog("DICT FAILURE CMS DATA :::", dictFailure)
            },
            {}
        );
    }

    buttonSignUpPressed = () => {

        Keyboard.dismiss();
        setTimeout(() => {
            // debugLog("STATE :: ", this.state)


            if (this.state.profileImageSource == undefined) {
                showDialogue(Messages.emptyProfilePicture, [])
                return
            }

            this.setState({ shouldPerformValidation: true });

            if (
                this.state.strEmailAddress.trim() == "" ||
                this.state.strPassword.trim() == "" ||
                this.state.strFirstName.trim() == "" ||
                this.state.strLastName.trim() == "" ||
                this.state.strConfirmPassword.trim() == ""
            ) {
                return;
            }


            if (this.state.strPassword != this.state.strConfirmPassword) {
                return
            }



            if (
                this.validationsHelper.validateEmail(
                    this.state.strEmailAddress,
                    Messages.emptyEmail
                ).trim() == "" &&
                this.validationsHelper.validatePassword(
                    this.state.strPassword,
                    Messages.emptyPassword
                ).trim() == ""
            ) {

                netStatus(status => {
                    if (status) {
                        // CALL SIGN UP API HERE
                        this.callRegisterAPI()
                        // showDialogue("Uncomment here...")
                    } else {
                        showNoInternetAlert()
                    }
                });
            }
        }, 50);


    }

    openImagePicker = () => {
        ImagePicker.showImagePicker(options, response => {
            console.log("Response = ", response);

            if (response.didCancel) {
                console.log("User cancelled image picker");
            } else if (response.error) {
                console.log("ImagePicker Error: ", response.error);
                showDialogue(response.error, [])
            } else if (response.customButton) {
                console.log("User tapped custom button: ", response.customButton);
            } else {
                // const source = { uri: response.uri };
                this.setState({
                    profileImageSource: response
                });
            }
        });
    }

    callRegisterAPI() {


        this.setState({ isLoading: true });

        getValueFromAsyncStore("fcmToken", (tokenFetched) => {

            let signUpParams = {
                first_name: this.state.strFirstName,
                last_name: this.state.strLastName,
                email: this.state.strEmailAddress,
                password: this.state.strPassword,
                push_notification_token: tokenFetched || "1234567890",
                deviceType: Platform.OS == "ios" ? "ios" : "android",
                social_media_type: "",
                social_media_id: "",
            }

            apiPostForFileUpload(REGISTER_URL, signUpParams, this.state.profileImageSource, (dictSuccess) => {
                let dictUserDetailsToSave = {
                    user_id: dictSuccess.user_id,
                    first_name: this.state.strFirstName,
                    last_name: this.state.strLastName,
                    email: this.state.strEmailAddress,
                    password: this.state.strPassword,
                    push_notification_token: tokenFetched || "1234567890",
                    profile_pic: dictSuccess.profile_pic || undefined,
                    social_media_type: SignInType.email,
                    social_media_id: ""
                };


                saveUserLoginDetails(
                    dictUserDetailsToSave,
                    successSavingInAsyncStore => {
                        this.setState({ isLoading: false });
                        // SAVE DATA IN GLOBAL STORE - REDUX
                        this.props.saveDetailsOnSuccessfullRegister(dictUserDetailsToSave);

                        // NAVIGATE TO HOME SCREEN
                        // this.props.navigation.navigate("Home");

                        // CHANGE THE ROOT SCREEN
                        this.props.navigation.dispatch(
                            StackActions.reset({
                                index: 0,
                                key: null,
                                actions: [NavigationActions.navigate({ routeName: "Home" })]
                            })
                        );

                    },
                    errorSavingInAsyncStore => { }
                );
            }, (errorFailure) => {
                debugLog("errorFailure =====>>>>", errorFailure)
                showDialogue(errorFailure.message || Messages.generalWebServiceError);
                this.setState({ isLoading: false });
            })

        }, errInFetchingToken => {
            this.setState({ isLoading: false });
        })






    }

    buttonSignInPressed = () => {
        this.props.navigation.goBack()

    }

    firstNameDidChange = (newFirstName) => {
        debugLog("newFirstName :::" + newFirstName)
        // if (this.validationsHelper.validateName(newFirstName).trim() == "") {
        this.setState({ strFirstName: newFirstName, shouldPerformValidation: false })
        // }
    }

    lastNameDidChange = (newLastName) => {
        // if (this.validationsHelper.validateName(newLastName).trim() == "") {
        this.setState({ strLastName: newLastName, shouldPerformValidation: false })
        // }
    }

    emailDidChange = (newEmail) => {
        this.setState({ strEmailAddress: newEmail, shouldPerformValidation: false })
    }

    passwordDidChange = (newPassword) => {
        this.setState({ strPassword: newPassword, shouldPerformValidation: false })
    }

    confirmPasswordDidChange = (newConfirmPassword) => {
        this.setState({ strConfirmPassword: newConfirmPassword, shouldPerformValidation: false })
    }

    buttonTermsOfUsePressed = () => {
        arrTermsOfUse = this.cmsData.filter(cmsContentToIterate => {
            return cmsContentToIterate.cms_slug == CMSPages.TermsAndConditions
        })
        if (arrTermsOfUse != undefined && arrTermsOfUse.length > 0) {
            this.state.infoText = arrTermsOfUse[0]
            this.setState({ shouldShowInfoView: true })
        }
    }

    buttonPrivacyPolicyPressed = () => {
        arrPrivacyPolicy = this.cmsData.filter(cmsContentToIterate => {
            return cmsContentToIterate.cms_slug == CMSPages.privacyPolicy
        })
        if (arrPrivacyPolicy != undefined && arrPrivacyPolicy.length > 0) {
            this.state.infoText = arrPrivacyPolicy[0]
            this.setState({ shouldShowInfoView: true })
        }
    }

    dismissInfoView = () => {
        this.setState({ shouldShowInfoView: false })
    }

    render() {
        return (

            <KeyboardAwareScrollView enableResetScrollToCoords={true} resetScrollToCoords={{ x: 0, y: 0 }} contentContainerStyle={{}} style={{ flex: 1, marginTop: Metrics.statusbarHeight }} behavior="padding" enabled >

                {this.state.isLoading ? <ProgressLoader /> : null}

                {(this.state.shouldShowInfoView)
                    ? <Modal visible={this.state.shouldShowInfoView}
                        animationType="slide"
                        transparent={true}
                        onRequestClose={this.dismissInfoView}>


                        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.25)', justifyContent: "center" }}>

                            <View style={{
                                width: Metrics.screenWidth - 40,
                                // height: Metrics.screenHeight - 2 * (heightPercentageToDP("10%") + 10),
                                height: Metrics.screenHeight - 2 * (30),
                                marginHorizontal: 20, marginVertical: heightPercentageToDP("10%") + 10,
                                backgroundColor: "white",
                                justifyContent: 'center',
                                borderRadius: 8,
                            }}>


                                <Text style={{ marginHorizontal: 20, marginTop: 20, marginBottom: 10, textAlign: 'center', fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2.3%") }}>{this.state.infoText.cms_title}</Text>

                                <MyWebView
                                    source={{ html: this.customStyle + this.state.infoText.cms_contents }}
                                    ref={(ref) => { this.webview = ref; }}
                                    startInLoadingState={true}
                                    style={{
                                        flex: 1,
                                        alignSelf: "center",
                                        paddingBottom: Platform.OS == "ios" ? 0 : 15,
                                        backgroundColor: 'transparent'
                                    }}
                                    width="90%"
                                    dataDetectorTypes={["link"]}
                                    onLoadStart={() => {
                                        debugLog("::::: onLoadStart :::::")
                                    }}
                                    onLoadEnd={() => {
                                        debugLog("::::: onLoadEnd :::::")
                                    }}
                                    onError={(errorInWebView) => {
                                        debugLog("::::: errorInWebView :::::", JSON.stringify(errorInWebView))
                                    }}
                                    //hasIframe={true}
                                    scrollEnabled={true}
                                    onNavigationStateChange={(event) => {
                                        debugLog("::::: event :::::", JSON.stringify(event))
                                        if (event.url != undefined && event.navigationType === 'click') {
                                            this.webview.stopLoading();
                                            Linking.openURL(event.url);
                                        }
                                    }}

                                />

                                <EDHThemeButton style={{ margin: 20 }}
                                    label={strings("General.dismiss")}
                                    onPress={this.dismissInfoView}
                                />


                            </View>
                        </View>
                        {/* </TouchableOpacity> */}

                    </Modal>
                    : null}

                < View style={{ padding: Metrics.statusbarHeight }}>

                    <View style={{ alignItems: 'center', justifyContent: 'center' }}>
                        <TouchableOpacity style={{ flex: 1 }} onPress={this.openImagePicker}>
                            <Image source={this.state.profileImageSource ? { uri: this.state.profileImageSource.uri } : Assets.userplaceholder} style={{
                                shadowColor: EDColors.text,
                                shadowOffset: { width: 0, height: 2 },
                                shadowOpacity: 0.5,
                                shadowRadius: 2, borderRadius: 5, marginTop: 10, width: Metrics.screenWidth * 0.35, height: Metrics.screenWidth * 0.35
                            }} />
                        </TouchableOpacity>
                        <TouchableOpacity style={{ justifyContent: 'center', alignItems: 'center', flex: 1, width: 40, height: 40, borderRadius: 5, backgroundColor: EDColors.primary, marginLeft: (Metrics.screenWidth * 0.35), marginBottom: 10, marginTop: -15 }} onPress={this.openImagePicker}>
                            <Image source={Assets.camera} style={{}} />
                        </TouchableOpacity>
                    </View>
                    <View style={{}}>

                        <EDRTLTextInput
                            icon={Assets.name}
                            placeholder={strings("SignUp.firstName")}
                            initialValue={this.state.strFirstName}
                            type={TextFieldTypes.default}
                            onChangeText={this.firstNameDidChange}
                            error={
                                // this.state.shouldPerformValidation
                                //     ? this.validationsHelper.validateEmpty(
                                //         this.state.strFirstName,
                                //         Messages.emptyFirstName
                                //     )
                                //     : ""
                                this.state.shouldPerformValidation
                                    ? this.validationsHelper.validateEmpty(
                                        this.state.strFirstName,
                                        Messages.emptyFirstName
                                    ).length > 0 ? this.validationsHelper.validateEmpty(
                                        this.state.strFirstName,
                                        Messages.emptyFirstName
                                    ) : this.validationsHelper.validateName(this.state.strFirstName, Messages.validFirstName)
                                    : ""} />

                        <EDRTLTextInput
                            icon={Assets.name}
                            initialValue={this.state.strLastName}
                            placeholder={strings("SignUp.lastName")}
                            type={TextFieldTypes.default}
                            onChangeText={this.lastNameDidChange}
                            error={
                                // this.state.shouldPerformValidation
                                //     ? this.validationsHelper.validateEmpty(
                                //         this.state.strLastName,
                                //         Messages.emptyLastName
                                //     )
                                //     : ""
                                this.state.shouldPerformValidation
                                    ? this.validationsHelper.validateEmpty(
                                        this.state.strLastName,
                                        Messages.emptyLastName
                                    ).length > 0 ? this.validationsHelper.validateEmpty(
                                        this.state.strLastName,
                                        Messages.emptyLastName
                                    ) : this.validationsHelper.validateName(this.state.strLastName, Messages.validLasttName)
                                    : ""} />


                        <EDRTLTextInput
                            icon={Assets.mail}
                            initialValue={this.state.strEmailAddress}
                            placeholder={strings("SignUp.email")}
                            type={TextFieldTypes.email}
                            onChangeText={this.emailDidChange}
                            error={
                                this.state.shouldPerformValidation
                                    ? this.validationsHelper.validateEmail(
                                        this.state.strEmailAddress,
                                        Messages.emptyEmail
                                    )
                                    : ""
                            } />

                        <EDRTLTextInput
                            icon={Assets.lock}
                            placeholder={strings("SignUp.password")}
                            initialValue={this.state.strPassword}
                            type={TextFieldTypes.password}
                            onChangeText={this.passwordDidChange}
                            error={
                                this.state.shouldPerformValidation
                                    ? this.validationsHelper.validatePassword(
                                        this.state.strPassword,
                                        Messages.emptyPassword
                                    )
                                    : ""
                            } />

                        <EDRTLTextInput
                            icon={Assets.lock}
                            placeholder={strings("SignUp.confirmpassword")}
                            initialValue={this.state.strConfirmPassword}
                            type={TextFieldTypes.password}
                            onChangeText={this.confirmPasswordDidChange}
                            error={
                                this.state.shouldPerformValidation
                                    ?
                                    (this.validationsHelper.validatePassword(
                                        this.state.strConfirmPassword,
                                        Messages.confirmPassword
                                    ).length > 0 ? this.validationsHelper.validatePassword(
                                        this.state.strConfirmPassword,
                                        Messages.confirmPassword
                                    ) : (this.state.strPassword != this.state.strConfirmPassword ? Messages.confirmPasswordMismatch : ""))
                                    : ""
                            } />

                        <EDRTLView style={{ alignItems: 'center' }}>
                            <Text style={{ marginRight: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>By signing up, you agree to our</Text>
                            <EDButton newStyle={{ marginVertical: 5 }} label={strings("SignUp.termsOfUse")} onPress={this.buttonTermsOfUsePressed} />
                            <Text style={{ marginLeft: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>
                                and
                        </Text>
                        </EDRTLView>
                        <EDRTLView style={{ alignItems: 'center' }}>
                            <EDButton newStyle={{ marginVertical: 0 }} label={strings("SignUp.privacyPolicy")} onPress={this.buttonPrivacyPolicyPressed} />
                        </EDRTLView>

                        <EDThemeButton style={{ marginTop: 10 }} label={strings("SignUp.signUp")} onPress={this.buttonSignUpPressed} />

                        <EDRTLView style={{ marginTop: 10, alignItems: 'center', justifyContent: 'center' }}>
                            <Text style={{ color: EDColors.text, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("2%") }}>{strings("SignUp.haveAccount")}</Text>
                            <EDButton label={strings("SignUp.signIn")} onPress={this.buttonSignInPressed} />
                        </EDRTLView>

                    </View>
                </View >

            </KeyboardAwareScrollView >
        )
    }
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: 'center',
        backgroundColor: '#F5FCFF',
    }
});

export default connect(

    state => {
        return {

        }
    },
    dispatch => {
        return {
            saveDetailsOnSuccessfullRegister: userObject => {
                dispatch(saveUserDetailsOnLogin(userObject));
            },
        }

    }

)(SignUpContainer);