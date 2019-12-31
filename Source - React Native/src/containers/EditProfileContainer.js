import React from "react";
import {
    View,
    StyleSheet,
    TouchableOpacity,
    Keyboard,
    Text, Platform, Linking, Modal
} from "react-native";
import BaseContainer from "./BaseContainer";
import { strings } from "../locales/i18n";
import Assets from "../assets";
import EDRTLTextInput from "../components/EDRTLTextInput";
import { TextFieldTypes, debugLog, SAVE_PROFILE_URL, LOGOUT_URL, StorageKeys, GET_PROFILE_URL, CMSPages, GET_CMS_DATA } from "../utils/EDConstants";
import { EDColors } from "../utils/EDColors";
import { Messages } from "../utils/Messages";
import EDThemeButton from "../components/EDThemeButton";
import Metrics from "../utils/Metrics";
import {
    showNoInternetAlert,
    showDialogue,
    showLogoutAlertWithCompletion
} from "../utils/EDAlert";
import Validations from "../utils/Validations";
import {
    apiPost,
    netStatus,
    apiPostForFileUpload
} from "../utils/ServiceManager";
import { KeyboardAwareScrollView } from "react-native-keyboard-aware-scroll-view";
import { connect } from "react-redux";
import ImagePicker from "react-native-image-picker";
import { StackActions, NavigationActions } from "react-navigation";
import { saveUserLoginDetails, getValueFromAsyncStore } from "../utils/AsyncStorageHelper";
import { LoginManager } from "react-native-fbsdk";
import { GoogleSignin } from 'react-native-google-signin';
import { saveUserDetailsOnLogin } from '../redux/actions/User';
import Image from 'react-native-image-progress';
import * as Progress from 'react-native-progress';
import { removeSelectedQuizFromRedux } from "../redux/actions/QuizAction";
import { NavigationEvents } from "react-navigation";
import EDRTLView from "../components/EDRTLView";
import EDButton from "../components/EDButton";
import { heightPercentageToDP } from "react-native-responsive-screen";
import { EDFonts } from "../utils/EDFontConstants";
import MyWebView from "react-native-webview-autoheight";
import EDHThemeButton from '../components/EDThemeButton';
import Permissions from "react-native-permissions";
import AsyncStorage from "@react-native-community/async-storage"


// CAPTURE OPTIONS
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
    quality: 1.0,
    maxWidth: 200,
    maxHeight: 200


};

class EditProfileContainer extends React.Component {
    constructor(props) {
        super(props);
        this.validationsHelper = new Validations();
        this.cmsData = [];
        this.customStyle =
            "<style>* {max-width: 100%;} body {font-size: 45px;font-family:Ubuntu-Regular}</style>";
    }

    state = {
        // USER ATTRIBUTES
        strEmailAddress: this.props.userDetails.email, // "john.doe@gmail.com",
        strFirstName: this.props.userDetails.first_name,
        strLastName: this.props.userDetails.last_name,
        strInitialEmailAddress: this.props.userDetails.email,
        profileImage: (this.props.userDetails != undefined && this.props.userDetails.profile_pic != undefined && this.props.userDetails.profile_pic.length > 0) ? { uri: this.props.userDetails.profile_pic } : undefined,

        // UTILS
        shouldPerformValidation: false,
        isLoading: false,
        isConnected: true,
        isProfileChange: false,

        infoText: {},
    };

    componentDidMount() {
        this.callCMSDataAPI()
    }

    checkPhotosPermission = () => {

    };

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
        // PERFORM VALIDATIONS
        this.setState({ shouldPerformValidation: true });

        // INTERNET CHECK
        netStatus(status => {
            if (status) {
                // CALL SIGN UP API HERE
                this.saveProfileAPI();
            } else {
                showNoInternetAlert();
            }
        });
    };

    // IMAGE PICKER
    openImagePicker = () => {

        Permissions.check("photo").then(
            response => {
                //response is an object mapping type to permission
                console.log("response _checkPhotos success", response);

                if (response == "authorized") {
                    console.log("response _checkPhotos success", response);

                    ImagePicker.showImagePicker(options, response => {
                        console.log("Response = ", response);

                        if (response.didCancel) {
                            console.log("User cancelled image picker");
                        } else if (response.error) {
                            console.log("ImagePicker Error: ", response.error);
                            showDialogue(response.error, []);
                        } else if (response.customButton) {
                            console.log("User tapped custom button: ", response.customButton);
                        } else {
                            // console.log("Image source from picker-----<>", response);

                            const source = response;
                            this.state.isProfileChange = true;
                            this.setState({
                                profileImage: source
                            });
                        }
                    });

                } else {
                    showDialogue("Please accept Camera and Gallery permission from Settings", [], "",
                        () => {
                            this.isOpenSetting = true
                            Linking.openURL("app-settings:");
                        })

                    console.log("error _checkPhotos success", response);
                }
            },
            error => {
                console.log("error get success", error);
            }
        );


    };

    // SAVE PROFILE API
    saveProfileAPI() {

        // VALIDATIONS
        Keyboard.dismiss();

        this.setState({ shouldPerformValidation: true });

        if (
            this.state.strEmailAddress.trim() == "" ||
            this.state.strFirstName.trim() == "" ||
            this.state.strLastName.trim() == ""
        ) {
            return;
        }

        if (this.validationsHelper.validateName(this.state.strFirstName) != "" || this.validationsHelper.validateName(this.state.strLastName) != "") {
            return
        }

        if (
            this.validationsHelper.validateEmail(
                this.state.strEmailAddress,
                Messages.emptyEmail
            ).trim() == ""
        ) {

            let params = {
                user_id: this.props.userDetails.user_id,
                first_name: this.state.strFirstName,
                last_name: this.state.strLastName,
                email: this.state.strEmailAddress,
                latitude: "",
                longitude: ""
            };

            !this.state.isProfileChange
                ? (params.profile_pic = this.state.profileImage.uri)
                : "";

            this.setState({ isLoading: true });

            apiPostForFileUpload(
                SAVE_PROFILE_URL,
                params,
                this.state.isProfileChange ? this.state.profileImage : undefined,
                (dictSuccess, message) => {

                    debugLog("dictSuccess :: ", dictSuccess)
                    getValueFromAsyncStore("fcmToken", tokenFetched => {

                        let dictUserDetailsToSave = {
                            user_id: this.props.userDetails.user_id,
                            first_name: this.state.strFirstName,
                            last_name: this.state.strLastName,
                            email: this.props.userDetails.email,
                            password: this.props.userDetails.password || "",
                            push_notification_token: tokenFetched || "1234567890",
                            profile_pic: dictSuccess.profile_pic || undefined,
                            social_media_type: this.props.userDetails.social_media_type,
                            social_media_id: this.props.userDetails.social_media_id
                        };

                        saveUserLoginDetails(
                            dictUserDetailsToSave,
                            successSavingInAsyncStore => {
                                this.setState({ isLoading: false });
                                // SAVE DATA IN GLOBAL STORE - REDUX
                                this.props.saveDetailsOnSuccessfullRegister(dictUserDetailsToSave);

                                // NAVIGATE TO HOME SCREEN
                                showDialogue(message, [], "", () => {
                                    this.props.navigation.navigate("Home");
                                })
                            },
                            errorSavingInAsyncStore => {
                                this.setState({ isLoading: false });
                            }
                        );

                        this.setState({ isLoading: false });
                    }, errrToken => {

                    })
                },
                dictFailure => {
                    debugLog("dictFailure :: ", dictFailure)
                    showDialogue(dictFailure.message || Messages.generalWebServiceError);
                    this.setState({ isLoading: false });
                },
                {}
            );
        }
    }

    // TEXT FIELD CHANGE HANDLERS
    firstNameDidChange = newFirstName => {

        // if (this.validationsHelper.validateName(newFirstName).trim() == "") {
        this.setState({ strFirstName: newFirstName, shouldPerformValidation: false })
        // }
    };

    lastNameDidChange = newLastName => {
        // if (this.validationsHelper.validateName(newLastName).trim() == "") {
        this.setState({ strLastName: newLastName, shouldPerformValidation: false })
        // }
    };

    emailDidChange = newEmail => {
        this.setState({
            strEmailAddress: newEmail,
            shouldPerformValidation: false
        });
    };

    // LOGOUT BUTTON HANDLER
    logoutButtonPressed = () => {
        Keyboard.dismiss();
        setTimeout(() => {

            netStatus(status => {
                if (status) {

                    showLogoutAlertWithCompletion(() => {

                        getValueFromAsyncStore("fcmToken", tokenFetched => {

                            let logoutParams = {
                                user_id: this.props.userDetails.user_id,
                                push_notification_token: tokenFetched || "1234567890124"
                            };
                            this.setState({ isLoading: true });

                            apiPost(
                                LOGOUT_URL,
                                logoutParams,
                                dictResponse => {

                                    this.setState({ isLoading: false });

                                    try {
                                        AsyncStorage.removeItem(StorageKeys.userDetails).then(
                                            () => {
                                                this.props.removeSelectedQuizDateFromReduxOnLogout()

                                                // CLEAR FACEBOOK SESSION
                                                LoginManager.logOut()

                                                // CLEAR GOOGLE+ SESSION
                                                if (GoogleSignin.isSignedIn) {
                                                    GoogleSignin.signOut()
                                                }

                                                // CHANGE THE ROOT SCREEN
                                                this.props.navigation.dispatch(
                                                    StackActions.reset({
                                                        index: 0,
                                                        key: null,
                                                        actions: [NavigationActions.navigate({ routeName: "Login" })]
                                                    })
                                                );
                                            },
                                            () => {
                                                console.log('rejected')
                                            }
                                        )

                                    }
                                    catch (exception) {
                                        showDialogue("Exception in clearing async store ::: ", exception)

                                        this.props.removeSelectedQuizDateFromReduxOnLogout()
                                        this.props.screenProps.payload.data.notification_date = undefined

                                        // CLEAR FACEBOOK SESSION
                                        LoginManager.logOut()

                                        // CLEAR GOOGLE+ SESSION
                                        if (GoogleSignin.isSignedIn) {
                                            GoogleSignin.signOut()
                                        }

                                        // CHANGE THE ROOT SCREEN
                                        this.props.navigation.dispatch(
                                            StackActions.reset({
                                                index: 0,
                                                key: null,
                                                actions: [NavigationActions.navigate({ routeName: "Login" })]
                                            })
                                        );
                                    }


                                },
                                (dictFailure, message) => {
                                    showDialogue(message || Messages.generalWebServiceError);
                                    this.setState({ isLoading: false });
                                },
                                {}
                            );
                        }, errToken => {

                        })




                    })

                } else {
                    showNoInternetAlert()
                }
            })

        }, 50);

    };

    onDidFocusEditProfileContainer = () => {


        netStatus(isConnected => {
            if (isConnected) {
                getProfileParams = {
                    user_id: this.props.userDetails.user_id
                }
                apiPost(GET_PROFILE_URL, getProfileParams,
                    (dictSuccess, message) => {
                        // showDialogue((message + " -- SUCCESS -- ") || "GET PROFILE SUCCESS")
                    },
                    (dictFailure, message) => {
                        // showDialogue((message + " -- FAILURE -- ") || "GET PROFILE FAILURE")
                    },
                    {})
            }
        })
    }

    render() {
        return (
            <BaseContainer
                title={strings("ScreenTitles.profile")}
                right={Assets.logout}
                onRight={this.logoutButtonPressed}
                loading={this.state.isLoading}
            >

                <NavigationEvents onDidFocus={this.onDidFocusEditProfileContainer} />

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


                <KeyboardAwareScrollView
                    enableResetScrollToCoords={true}
                    resetScrollToCoords={{ x: 0, y: 0 }}
                    contentContainerStyle={{}}
                    style={{ flex: 1, marginTop: Metrics.statusbarHeight }}
                    behavior="padding"
                    enabled
                >
                    <View style={{ padding: Metrics.statusbarHeight }}>
                        <View style={{ alignItems: "center", justifyContent: "center" }}>
                            <TouchableOpacity
                                style={{ flex: 1 }}
                                onPress={this.openImagePicker}
                            >
                                {/* <Image source={{ uri: "https://api.adorable.io/avatars/120" }} style={{ */}

                                {/* <Image
                                    source={
                                        this.state.profileImage.uri
                                            ? { uri: this.state.profileImage.uri }
                                            : Assets.userplaceholder
                                    }
                                    style={{
                                        shadowColor: EDColors.text,
                                        shadowOffset: { width: 0, height: 2 },
                                        shadowOpacity: 0.5,
                                        shadowRadius: 2,
                                        borderRadius: 5,
                                        marginTop: 10,
                                        width: Metrics.screenWidth * 0.35,
                                        height: Metrics.screenWidth * 0.35
                                    }}
                                /> */}

                                <Image
                                    source={this.state.profileImage && this.state.profileImage.uri
                                        ? { uri: this.state.profileImage.uri }
                                        : Assets.userplaceholder}
                                    indicator={Progress.Circle}
                                    style={{
                                        shadowColor: EDColors.text,
                                        backgroundColor: EDColors.textDisabled,
                                        shadowOffset: { width: 0, height: 2 },
                                        shadowOpacity: 0.5,
                                        shadowRadius: 2,
                                        borderRadius: 5,
                                        marginTop: 10,
                                        width: Metrics.screenWidth * 0.35,
                                        height: Metrics.screenWidth * 0.35
                                    }} />


                            </TouchableOpacity>
                            <TouchableOpacity
                                style={{
                                    justifyContent: "center",
                                    alignItems: "center",
                                    borderRadius: 5,
                                    backgroundColor: EDColors.primary,
                                    marginLeft: Metrics.screenWidth * 0.35,
                                    marginBottom: 10,
                                    flex: 1,
                                    marginTop: -15,
                                    padding: 20
                                }}
                                onPress={this.openImagePicker}
                            >
                                <Image source={Assets.camera} style={{ alignSelf: 'center', marginLeft: -16, marginTop: -8 }} />
                            </TouchableOpacity>
                            {/* <TouchableOpacity
                                style={{
                                    justifyContent: "center",
                                    alignItems: "center",
                                    flex: 1,
                                    width: 40,
                                    height: 40,
                                    borderRadius: 5,
                                    backgroundColor: EDColors.primary,
                                    marginLeft: Metrics.screenWidth * 0.35,
                                    marginBottom: 10,
                                    marginTop: -15
                                }}
                                onPress={this.openImagePicker}
                            >
                                <Image source={Assets.camera} style={{ marginLeft: -20, marginTop: -15 }} />
                            </TouchableOpacity> */}
                        </View>
                        <View style={{}}>
                            <EDRTLTextInput
                                icon={Assets.name}
                                initialValue={this.state.strFirstName}
                                placeholder={strings("SignUp.firstName")}
                                type={TextFieldTypes.default}
                                onChangeText={this.firstNameDidChange}
                                error={
                                    this.state.shouldPerformValidation
                                        ? this.validationsHelper.validateEmpty(
                                            this.state.strFirstName,
                                            Messages.emptyFirstName
                                        ).length > 0 ? this.validationsHelper.validateEmpty(
                                            this.state.strFirstName,
                                            Messages.emptyFirstName
                                        ) : this.validationsHelper.validateName(this.state.strFirstName, Messages.validFirstName)
                                        : ""
                                }
                            />

                            <EDRTLTextInput
                                icon={Assets.name}
                                initialValue={this.state.strLastName}
                                placeholder={strings("SignUp.lastName")}
                                type={TextFieldTypes.default}
                                onChangeText={this.lastNameDidChange}
                                error={
                                    this.state.shouldPerformValidation
                                        ? this.validationsHelper.validateEmpty(
                                            this.state.strLastName,
                                            Messages.emptyLastName
                                        ).length > 0 ? this.validationsHelper.validateEmpty(
                                            this.state.strLastName,
                                            Messages.emptyLastName
                                        ) : this.validationsHelper.validateName(this.state.strLastName, Messages.validLasttName)
                                        : ""                                    // this.state.shouldPerformValidation
                                    //     ? this.validationsHelper.validateEmpty(
                                    //         this.state.strLastName,
                                    //         Messages.emptyLastName
                                    //     )
                                    //     : ""
                                }
                            />

                            <EDRTLTextInput
                                icon={Assets.mail}
                                opacity={(this.state.strInitialEmailAddress != undefined && this.state.strInitialEmailAddress.trim().length > 0) ? 0.5 : 1.0}
                                pointerEvents={(this.state.strInitialEmailAddress != undefined && this.state.strInitialEmailAddress.trim().length > 0) ? "none" : "auto"}
                                placeholder={strings("SignUp.email")}
                                initialValue={this.state.strEmailAddress}
                                type={TextFieldTypes.email}
                                onChangeText={this.emailDidChange}
                                error={
                                    this.state.shouldPerformValidation
                                        ? this.validationsHelper.validateEmail(
                                            this.state.strEmailAddress,
                                            Messages.emptyEmail
                                        )
                                        : ""
                                }
                            />




                            <EDThemeButton
                                style={{ marginTop: 40 }}
                                label={strings("General.update")}
                                onPress={this.buttonSignUpPressed}
                            />

                            <EDRTLView style={{ alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ marginRight: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>Our</Text>
                                <EDButton newStyle={{ marginVertical: 5 }} label={strings("SignUp.termsOfUse")} onPress={this.buttonTermsOfUsePressed} />
                                <Text style={{ marginHorizontal: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>
                                    and
                        </Text>
                                <EDButton newStyle={{ marginVertical: 0 }} label={strings("SignUp.privacyPolicy")} onPress={this.buttonPrivacyPolicyPressed} />
                            </EDRTLView>

                        </View>
                    </View>
                </KeyboardAwareScrollView>
            </BaseContainer>
        );
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
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: "center",
        backgroundColor: "#F5FCFF"
    }
});

export default connect(
    state => {
        return {
            userDetails: state.userOperation
        };
    },
    dispatch => {
        return {
            saveDetailsOnSuccessfullRegister: userObject => {
                dispatch(saveUserDetailsOnLogin(userObject));
            },
            removeSelectedQuizDateFromReduxOnLogout: () => {
                dispatch(removeSelectedQuizFromRedux())
            }
        };
    }
)(EditProfileContainer);
