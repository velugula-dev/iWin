import React from "react";
import {
    View,
    Text,
    StyleSheet,
    Image,
    KeyboardAvoidingView,
    Platform,
    Keyboard,

    Linking,
    Modal
} from "react-native";
import { strings } from "../locales/i18n";
import Assets from "../assets";
import EDRTLView from "../components/EDRTLView";
import EDRTLImage from "../components/EDRTLImage";
import EDRTLTextInput from "../components/EDRTLTextInput";
import { TextFieldTypes, debugLog, LOGIN_URL, SignInType, REGISTER_URL, GET_CMS_DATA, CMSPages } from "../utils/EDConstants";
import { EDColors } from "../utils/EDColors";
import { Messages } from "../utils/Messages";
import EDThemeButton from "../components/EDThemeButton";
import EDButton from "../components/EDButton";
import { EDFonts } from "../utils/EDFontConstants";
import Metrics from "../utils/Metrics";
import {
    showNoInternetAlert,
    showDialogue,
    showLogoutAlertWithCompletion,
    showNotImplementedAlert,
    showRNUpdateAlert
} from "../utils/EDAlert";
import Validations from "../utils/Validations";
import { apiPost, netStatus, apiPostForFileUpload } from "../utils/ServiceManager";
import { connect } from "react-redux";
import { saveUserLoginDetails, getUserLoginDetails, getValueFromAsyncStore } from "../utils/AsyncStorageHelper";
import ProgressLoader from "../components/ProgressLoader";
import { saveUserDetailsOnLogin } from "../redux/actions/User";
import { saveDropDownInfoInRedux } from '../redux/actions/GlobalActions'


import { heightPercentageToDP } from "react-native-responsive-screen";
import { LoginButton, AccessToken } from 'react-native-fbsdk';
import { LoginManager } from "react-native-fbsdk";
import { GraphRequest, GraphRequestManager } from 'react-native-fbsdk';
import { GoogleSignin, GoogleSigninButton, statusCodes } from 'react-native-google-signin';
import { StackActions, NavigationActions } from "react-navigation";
import firebase from "react-native-firebase";
import { KeyboardAwareScrollView } from "react-native-keyboard-aware-scroll-view";
import MyWebView from "react-native-webview-autoheight";

import EDHThemeButton from '../components/EDThemeButton';
import AsyncStorage from "@react-native-community/async-storage"



class LoginContainer extends React.Component {
    constructor(props) {
        super(props);
        this.validationsHelper = new Validations();
        this.userSignInType = SignInType.email;
        this.cmsData = [];
        this.customStyle =
            "<style>* {max-width: 100%;} body {font-size: 45px;font-family:Ubuntu-Regular}</style>";


    }

    state = {
        strEmailAddress: "",
        strPassword: "",
        shouldPerformValidation: false,
        isLoading: false,
        infoText: {},

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


    callLoginWithSocialMediaAPI = (socialMediaID, onFailure, typeSocialMedia, emailFetched) => {

        if (emailFetched == undefined || emailFetched == null || emailFetched.length == 0) {
            debugLog("Can not fetch email from social media")
            // return
        }

        netStatus(status => {
            if (status) {
                this.setState({ isLoading: true })

                getValueFromAsyncStore("fcmToken", res => {

                    console.log("Response", res);
                    if (res != "" && res != null && res != undefined) {
                        let apiParams = {
                            social_media_id: socialMediaID,
                            push_notification_token: res || "1234567890",
                            device_type: Platform.OS == "ios" ? "ios" : "android",
                            social_media_type: typeSocialMedia,
                            email: emailFetched || ""
                        };

                        console.log("apiParams ====>>>", apiParams);

                        apiPost(
                            LOGIN_URL,
                            apiParams,
                            dictResponse => {

                                console.log("dictResponse ====>>>", dictResponse);
                                let dictSuccess = dictResponse.login
                                let dictUserDetailsToSave = {
                                    user_id: dictSuccess.user_id,
                                    first_name: dictSuccess.first_name,
                                    last_name: dictSuccess.last_name,
                                    email: dictSuccess.email,
                                    password: "",
                                    push_notification_token: "1234567890",
                                    profile_pic: dictSuccess.profile_pic || undefined,
                                    social_media_type: this.userSignInType,
                                    social_media_id: socialMediaID
                                };


                                saveUserLoginDetails(
                                    dictUserDetailsToSave,
                                    successSavingInAsyncStore => {
                                        this.setState({ isLoading: false });
                                        // SAVE DATA IN GLOBAL STORE - REDUX
                                        this.props.saveDetailsOnSuccessfullLoginInRedux(dictUserDetailsToSave);

                                        // // NAVIGATE TO HOME SCREEN
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

                            },
                            dictFailure => {
                                if (onFailure != undefined) {
                                    onFailure()
                                }
                                // showDialogue(dictFailure.message || Messages.generalWebServiceError);
                                // this.setState({ isLoading: false });
                            },
                            {}
                        );
                    } else {
                    }

                }, errorInGettingToken => {
                    this.setState({ isLoading: false })

                })
            } else {
                this.setState({ isLoading: false })
                showNoInternetAlert();
            }
        });
    }

    performAutoLogin(userDetails) {
        var email = userDetails.email || ""
        var password = userDetails.password || ""
        this.userSignInType = userDetails.social_media_type
        if (userDetails.social_media_type == SignInType.google || userDetails.social_media_type == SignInType.facebook) {
            this.callLoginWithSocialMediaAPI(userDetails.social_media_id, undefined,
                userDetails.social_media_type == SignInType.google ? SignInType.google : SignInType.facebook, email)
        } else {
            this.state.strEmailAddress = email
            this.state.strPassword = password
            this.callLoginAPI()
        }
    }

    componentDidMount() {

        this.callCMSDataAPI()
        // this.callDropDownAPI()

        // CLEAR FACEBOOK SESSION
        // LoginManager.logOut()

        getUserLoginDetails(fetchedDetails => {
            this.performAutoLogin(fetchedDetails)
        }, errorInFetchingUserDetails => {
            debugLog("errorInFetchingUserDetails from AsyncStorage ::::", errorInFetchingUserDetails)
        })

        GoogleSignin.configure({
            scopes: ['https://www.googleapis.com/auth/drive.readonly'], // what API you want to access on behalf of the user, default is email and profile
            hostedDomain: '', // specifies a hosted domain restriction
            loginHint: '147852', // [iOS] The user's ID, or email address, to be prefilled in the authentication UI if possible. [See docs here](https://developers.google.com/identity/sign-in/ios/api/interface_g_i_d_sign_in.html#a0a68c7504c31ab0b728432565f6e33fd)
            forceConsentPrompt: true, // [Android] if you want to show the authorization prompt at each login.
            iosClientId: '179211789786-62qo1jj68nkvilt01892j6njur6goi01.apps.googleusercontent.com', // [iOS] optional, if you want to specify the client ID of type iOS (otherwise, it is taken from GoogleService-Info.plist)
        });


    }

    buttonSignUpPressed = () => {
        this.props.navigation.navigate("Register");
    };

    buttonForgotPasswordPressed = () => {
        this.props.navigation.navigate("ForgotPassword");
    };

    // buttonFacebookPressed = () => {
    //     showRNUpdateAlert()
    // }

    buttonFacebookPressed = () => {
        // Attempt a login using the Facebook login dialog asking for default permissions.

        Keyboard.dismiss()
        setTimeout(() => {
            netStatus(status => {
                if (status) {

                    this.setState({ isLoading: true })
                    this.userSignInType = SignInType.facebook
                    LoginManager.setLoginBehavior(Platform.OS == "ios" ? 'browser' : 'native_with_fallback');
                    LoginManager.logInWithPermissions(["public_profile", "email"]).then(
                        // LoginManager.logInWithReadPermissions(["public_profile", "email"]).then(
                        function (result) {
                            if (result.isCancelled) {
                                console.log("Login cancelled");
                            } else {
                                console.log(
                                    "Login success with permissions: " +
                                    result.grantedPermissions.toString()
                                );
                                let infoRequest = new GraphRequest('/me', {
                                    httpMethod: 'GET',
                                    version: 'v3.2',
                                    parameters: {
                                        'fields': {
                                            'string': 'id, name, first_name, middle_name, last_name, picture.type(large), email,gender'
                                        }
                                    }
                                }, (err, userInfo) => {
                                    if (err != undefined) {
                                        console.log("ERROR IN FETCHING INFO USING GRAPH PATH", err.toString())
                                    }
                                    if (userInfo != undefined) {
                                        console.log("RESPONSE IN FETCHING INFO USING GRAPH PATH", JSON.stringify(userInfo))


                                        if (userInfo.id != undefined) {

                                            getValueFromAsyncStore("fcmToken", tokenFetched => {

                                                this.callLoginWithSocialMediaAPI(userInfo.id, () => {

                                                    let signUpParams = {
                                                        first_name: userInfo.first_name || "",
                                                        last_name: userInfo.last_name || "",
                                                        email: userInfo.email || "",
                                                        password: "",
                                                        push_notification_token: tokenFetched || "1234567890",
                                                        device_type: Platform.OS == "ios" ? "ios" : "android",
                                                        social_media_type: this.userSignInType,
                                                        social_media_id: userInfo.id,
                                                        profile_pic: (userInfo.picture != undefined && userInfo.picture.data != undefined && userInfo.picture.data.url != undefined) ? userInfo.picture.data.url : ("http://graph.facebook.com/" + userInfo.id + "/picture?type=large")
                                                    }

                                                    console.log("signUpParams ====>>>", signUpParams);

                                                    this.callRegisterViaSocialMediaAPI(signUpParams)
                                                }, SignInType.facebook, userInfo.email || "")
                                            }, errorFCMToken => {
                                                this.setState({ isLoading: false })
                                                debugLog("Unable to get FCM Token From Async Store")
                                                // showDialogue("Unable to get FCM Token From Async Store", [])
                                            })



                                        } else {
                                            this.setState({ isLoading: false })
                                            showDialogue("Unable to get user's facebook id")
                                        }
                                    }
                                });
                                // Start the graph request.
                                new GraphRequestManager().addRequest(infoRequest).start();

                            }
                        }.bind(this),
                        function (error) {
                            this.setState({ isLoading: false })
                            console.log("Login fail with error: " + error);
                        }.bind(this)
                    );

                } else {
                    showNoInternetAlert();
                }
            });
        }, 50);

    }

    buttonGooglePressed = () => {

        Keyboard.dismiss()
        setTimeout(() => {

            netStatus(status => {
                if (status) {


                    this.userSignInType = SignInType.google
                    GoogleSignin.hasPlayServices()
                        .then(function () {

                            debugLog("HAS PLAY SERVICES : YES")
                            GoogleSignin.signIn()
                                .then(function (userDetailsFetched) {

                                    debugLog("userDetailsFetched :::: ", userDetailsFetched)

                                    // accessToken: "ya29.GlyLBuadKvGC_hEoIurKZxKPyRW7-SivVWBElCu6UxmbvdICo34v_hbrQLAKYiTwOGWt1TacYBM80iLYBuu0DAoyvl4zfl7gD045R05QuAXoJlcx8N0O7kP4IHrOJg"
                                    // accessTokenExpirationDate: 3599.9987100362778
                                    // email: "henit.evince@gmail.com"
                                    // familyName: "Evince"
                                    // givenName: "Henit"
                                    // id: "110923141198903761048"
                                    // idToken: "eyJhbGciOiJSUzI1NiIsImtpZCI6IjhhYWQ2NmJkZWZjMWI0M2Q4ZGIyN2U2NWUyZTJlZjMwMTg3OWQzZTgiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiIxNzkyMTE3ODk3ODYtNjJxbzFqajY4bmt2aWx0MDE4OTJqNm5qdXI2Z29pMDEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiIxNzkyMTE3ODk3ODYtNjJxbzFqajY4bmt2aWx0MDE4OTJqNm5qdXI2Z29pMDEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTA5MjMxNDExOTg5MDM3NjEwNDgiLCJlbWFpbCI6Imhlbml0LmV2aW5jZUBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6IkZUcS1rbnAwRnN5RWNDNEttdDZnY2ciLCJuYW1lIjoiSGVuaXQgRXZpbmNlIiwicGljdHVyZSI6Imh0dHBzOi8vbGg2Lmdvb2dsZXVzZXJjb250ZW50LmNvbS8tb0pUcERlQVZLaG8vQUFBQUFBQUFBQUkvQUFBQUFBQUFBQUEvQUt4cndjYXN1clY4UzRWYmlMN1cwTDlTdFNHU0cwTWRXdy9zOTYtYy9waG90by5qcGciLCJnaXZlbl9uYW1lIjoiSGVuaXQiLCJmYW1pbHlfbmFtZSI6IkV2aW5jZSIsImxvY2FsZSI6ImVuLUdCIiwiaWF0IjoxNTQ2OTIzMDA2LCJleHAiOjE1NDY5MjY2MDZ9.aLzqQXSi2ZEQSrDdWZGiBc5cHXo9uPK_QMC0ZBLF3x81uxFbhAnmbmKKc6qvPLkOfRlajXp6aqO92VqkqJTSzzDoOaQ_dsCWBbWG8JPAQPuTpVgxrVBQXMVr9DO4XaXXI1GWVqzYdFq8uEUd-baNufgwLBBWklWadXioQvRWp-Hkhms_NnB4WxzVItHjEl2oCXN2BETiuK8m8N9atmGpmq3gWCLM6zcpIPL-jWht5zayRGk2BF0xJgPYhGjOFOUDBGGNDik70koV85fRxwkvS7vGUKHaEkWUEIXN2uNUTKRZ331owzUa6mH5x86OFFthxjwx8SJaKxBgLw93tzHKtw"
                                    // name: "Henit Evince"
                                    // photo: "https://lh6.googleusercontent.com/-oJTpDeAVKho/AAAAAAAAAAI/AAAAAAAAAAA/AKxrwcasurV8S4VbiL7W0L9StSGSG0MdWw/s120/photo.jpg"
                                    // serverAuthCode: null

                                    // userInfo = Platform.OS == "ios" ? userDetailsFetched : userDetailsFetched.user
                                    userInfo = userDetailsFetched.user
                                    if (userInfo.id != undefined) {


                                        this.callLoginWithSocialMediaAPI(userInfo.id, () => {


                                            getValueFromAsyncStore("fcmToken", tokenFetched => {
                                                let signUpParams = {
                                                    first_name: userInfo.givenName || "",
                                                    last_name: userInfo.familyName || "",
                                                    email: userInfo.email || "",
                                                    password: "",
                                                    push_notification_token: tokenFetched || "1234567890",
                                                    device_type: Platform.OS == "ios" ? "ios" : "android",
                                                    social_media_type: this.userSignInType,
                                                    social_media_id: userInfo.id,
                                                    profile_pic: userInfo.photo
                                                }


                                                this.callRegisterViaSocialMediaAPI(signUpParams)
                                            }, errorFCMToken => {
                                                this.setState({ isLoading: false })
                                            })

                                        }, SignInType.google, userInfo.email || "")
                                    } else {
                                        this.setState({ isLoading: false })
                                        showDialogue("Unable to get google plus id")
                                    }
                                }.bind(this), function (errorUserInfo) {

                                    debugLog("errorUserInfo :::: ", errorUserInfo)


                                    if (errorUserInfo.code == -5) {
                                        debugLog("user cancelled the login flow")
                                    }
                                    else {
                                        // some other error happened
                                        if (errorUserInfo.code == 12501 || errorUserInfo.code == "12501") {
                                            debugLog("user cancelled the login flow with error ::" + JSON.stringify(errorUserInfo))
                                        }
                                        // showDialogue("Play Services not available." + JSON.stringify(errorUserInfo))
                                    }
                                }.bind(this)
                                );
                        }.bind(this), function (errorPlayServices) {
                            debugLog("ERROR IN PLAY SERVICES:", errorPlayServices)
                        }.bind(this)
                        );
                } else {
                    this.setState({ isLoading: false })
                    debugLog("NO INTERNET :::: ")
                    showNoInternetAlert();
                }
            });
        }, 50);

    }




    callRegisterViaSocialMediaAPI(signUpParams) {


        this.setState({ isLoading: true });

        // let signUpParams = {
        //     first_name: this.state.strFirstName,
        //     last_name: this.state.strLastName,
        //     email: this.state.strEmailAddress,
        //     password: this.state.strPassword,
        //     push_notification_token: "1234567890",
        //     deviceType: Platform.OS == "ios" ? "ios" : "android",
        //     social_media_type: "",
        //     social_media_id: "",
        // }

        apiPostForFileUpload(REGISTER_URL, signUpParams, undefined, (dictSuccess) => {
            let dictUserDetailsToSave = {
                user_id: dictSuccess.user_id,
                first_name: signUpParams.first_name || "",
                last_name: signUpParams.last_name || "",
                email: signUpParams.email || "",
                password: signUpParams.password || "",
                push_notification_token: "1234567890",
                profile_pic: signUpParams.profile_pic || undefined,
                social_media_type: signUpParams.social_media_type,
                social_media_id: signUpParams.social_media_id
            };


            saveUserLoginDetails(
                dictUserDetailsToSave,
                successSavingInAsyncStore => {
                    this.setState({ isLoading: false });
                    // SAVE DATA IN GLOBAL STORE - REDUX
                    this.props.saveDetailsOnSuccessfullLoginInRedux(dictUserDetailsToSave);

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
    }



    buttonLoginPressed = () => {


        Keyboard.dismiss()
        setTimeout(() => {

            this.setState({
                shouldPerformValidation: true
            });

            this.userSignInType = SignInType.email
            if (
                this.state.strEmailAddress.trim() == "" ||
                this.state.strPassword.trim() == ""
            ) {
                return;
            }
            // if (
            //     this.validationsHelper
            //         .validateEmail(this.state.strEmailAddress, Messages.emptyEmail)
            //         .trim() == "" &&
            //     this.validationsHelper
            //         .validatePassword(this.state.strPassword, Messages.emptyPassword)
            //         .trim() == ""
            // ) {
            if (
                this.validationsHelper
                    .validateEmail(this.state.strEmailAddress, Messages.emptyEmail)
                    .trim() == ""
            ) {
                netStatus(status => {
                    if (status) {

                        // CALL LOGIN API HERE
                        this.callLoginAPI()
                    } else {
                        showNoInternetAlert();
                    }
                });
            }

        }, 50);


        // currentLocale = "en"
        // I18n.locale = "en";

    };

    callLoginAPI() {
        getValueFromAsyncStore("fcmToken", (tokenFetched) => {
            let loginParams = {
                email: this.state.strEmailAddress,
                password: this.state.strPassword,
                device_type: Platform.OS == "ios" ? "ios" : "android",
                push_notification_token: tokenFetched || "1234567890124"
            };
            this.setState({ isLoading: true });

            apiPost(
                LOGIN_URL,
                loginParams,
                dictResponse => {

                    let dictSuccess = dictResponse.login || {}
                    let dictUserDetailsToSave = {
                        user_id: dictSuccess.user_id,
                        first_name: dictSuccess.first_name,
                        last_name: dictSuccess.last_name,
                        email: this.state.strEmailAddress,
                        password: this.state.strPassword,
                        push_notification_token: tokenFetched || "1234567890",
                        profile_pic: dictSuccess.profile_pic || undefined,
                        social_media_type: SignInType.email,
                        social_media_id: ""
                    };

                    // SAVE USER DETAILS IN ASYNC STORE
                    saveUserLoginDetails(
                        dictUserDetailsToSave,
                        successSavingInAsyncStore => {
                            this.setState({ isLoading: false });
                            // SAVE DATA IN GLOBAL STORE - REDUX
                            this.props.saveDetailsOnSuccessfullLoginInRedux(dictUserDetailsToSave);

                            // // NAVIGATE TO HOME SCREEN
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

                },
                (dictFailure, message) => {
                    showDialogue(message || Messages.generalWebServiceError);
                    this.setState({ isLoading: false });
                },
                {}
            );
        }, errInFetchingToken => {
            this.setState({ isLoading: false })
            debugLog("Unable to get FCM Token from async store")
            // showDialogue("Unable to get FCM Token from async store", [])
        })

    }

    passwordDidChange = newPassword => {
        this.setState({ strPassword: newPassword, shouldPerformValidation: false });
    };

    emailDidChange = newEmail => {
        this.setState({
            strEmailAddress: newEmail,
            shouldPerformValidation: false
        });
    };

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
            <KeyboardAwareScrollView
                contentContainerStyle={{}}
                style={{ flex: 1 }}
                behavior="padding"
                enabled
            >

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

                {/* PROGRESS LOADER */}
                {this.state.isLoading ? <ProgressLoader /> : null}




                <View style={{ flex: 1, padding: Metrics.statusbarHeight }}>


                    <View
                        style={{
                            alignItems: "center",
                            justifyContent: "center",
                        }}
                    >
                        <Image resizeMethod="scale" resizeMode="cover" style={{}} source={Assets.logo} />
                    </View>
                    <View style={{}}>
                        <EDRTLTextInput
                            icon={Assets.mail}
                            placeholder={strings("Login.email")}
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
                        <EDRTLTextInput
                            icon={Assets.lock}
                            placeholder={strings("Login.password")}
                            type={TextFieldTypes.password}
                            onChangeText={this.passwordDidChange}
                            error={
                                this.state.shouldPerformValidation && this.state.strPassword.trim() == ""
                                    ? Messages.emptyPassword
                                    : ""
                            }
                        />

                        <EDButton
                            label={strings("Login.forgotPassword")}
                            onPress={this.buttonForgotPasswordPressed}
                        />

                        <EDHThemeButton
                            label={strings("Login.signIn")}
                            onPress={this.buttonLoginPressed}
                        />

                        <EDRTLView
                            style={{
                                marginTop: 10,
                                alignItems: "center",
                                justifyContent: "center"
                            }}
                        >
                            <Text
                                style={{
                                    color: EDColors.text,
                                    fontFamily: EDFonts.regular,
                                    fontSize: heightPercentageToDP("2%")
                                }}
                            >
                                {strings("Login.noAccount")}
                            </Text>
                            <EDButton
                                label={strings("Login.signUp")}
                                onPress={this.buttonSignUpPressed}
                            />
                        </EDRTLView>

                        <EDRTLView style={{ marginTop: 20, alignItems: 'center' }}>
                            <View style={{ height: 1, backgroundColor: EDColors.textSecondary, flex: 1 }}></View>
                            <Text style={{ marginHorizontal: 20, color: EDColors.primary, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>OR</Text>
                            <View style={{ height: 1, backgroundColor: EDColors.textSecondary, flex: 1 }}></View>
                        </EDRTLView>

                        <EDRTLView style={{ marginTop: 10, alignItems: 'center', justifyContent: 'space-between' }}>
                            <EDButton
                                label={strings("Login.facebook")}
                                onPress={this.buttonFacebookPressed}
                                buttonStyle={{ backgroundColor: EDColors.fbColor, color: EDColors.white }}
                                textStyle={{ color: EDColors.white, marginVertical: 15, marginHorizontal: 10 }}
                            />

                            <EDButton
                                label={strings("Login.google")}
                                onPress={this.buttonGooglePressed}
                                buttonStyle={{ backgroundColor: EDColors.googleColor, color: EDColors.white }}
                                textStyle={{ color: EDColors.white, marginVertical: 15, marginHorizontal: 10 }}
                            />
                        </EDRTLView>
                    </View>

                    <EDRTLView style={{ alignItems: 'center' }}>
                        <Text style={{ marginRight: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>By logging in, you agree to our</Text>
                        <EDButton newStyle={{ marginVertical: 5 }} label={strings("SignUp.termsOfUse")} onPress={this.buttonTermsOfUsePressed} />
                        <Text style={{ marginLeft: 5, color: EDColors.text, fontSize: heightPercentageToDP("1.8%"), fontFamily: EDFonts.regular }}>
                            and
                        </Text>
                    </EDRTLView>
                    <EDRTLView style={{ alignItems: 'center' }}>
                        <EDButton newStyle={{ marginVertical: 0 }} label={strings("SignUp.privacyPolicy")} onPress={this.buttonPrivacyPolicyPressed} />
                    </EDRTLView>

                </View>

            </KeyboardAwareScrollView>
        );
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

        }
    },
    dispatch => {
        return {
            saveDetailsOnSuccessfullLoginInRedux: userObject => {
                dispatch(saveUserDetailsOnLogin(userObject));
            },
        }

    }

)(LoginContainer);