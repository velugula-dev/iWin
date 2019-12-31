import React from "react";
import { View, Image, Platform } from "react-native";
import { EDColors } from "../utils/EDColors";
import Assets from "../assets";
import EDThemeButton from "../components/EDThemeButton";
import { strings, isRTL } from "../locales/i18n";
import EDRTLView from "../components/EDRTLView";
import { getUserLoginDetails, getValueFromAsyncStore, saveUserLoginDetails } from "../utils/AsyncStorageHelper";
import { debugLog, LOGIN_URL, SignInType } from "../utils/EDConstants";
import ProgressLoader from "../components/ProgressLoader";
import { showNoInternetAlert, showDialogue } from "../utils/EDAlert";
import { saveUserDetailsOnLogin } from "../redux/actions/User";
import { connect } from "react-redux";
import { apiPost, netStatus } from "../utils/ServiceManager";
import { StackActions, NavigationActions } from "react-navigation";
import NavigationService from "../../NavigationService";
import { saveSelectedQuizInRedux } from "../redux/actions/QuizAction";
import AsyncStorage from "@react-native-community/async-storage"

class SplashContainer extends React.Component {
  constructor(props) {
    super(props);
  }

  state = {
    strEmailAddress: "",
    strPassword: "",
    isLoading: false,
  };

  componentDidMount() {



    getUserLoginDetails(fetchedDetails => {
      this.performAutoLogin(fetchedDetails)
    }, errorInFetchingUserDetails => {
      setTimeout(() => {
        this.props.navigation.dispatch(
          StackActions.reset({
            index: 0,
            key: null,
            actions: [NavigationActions.navigate({ routeName: "Login" })]
          })
        );
        // this.props.navigation.navigate("Login")
      }, 2000);
    })

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

  callLoginWithSocialMediaAPI = (socialMediaID, onFailure, typeSocialMedia, emailFetched) => {

    if (emailFetched == undefined || emailFetched == null || emailFetched.length == 0) {
      showDialogue("Email fetched is undefined :::: ", socialMediaID, " ::::: ", typeSocialMedia)
      return
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

            apiPost(
              LOGIN_URL,
              apiParams,
              dictResponse => {

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
                    this.props.saveDetailsOnSuccessfullAutoLoginInRedux(dictUserDetailsToSave);

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
                this.props.navigation.dispatch(
                  StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Login" })]
                  })
                );
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


          saveUserLoginDetails(
            dictUserDetailsToSave,
            successSavingInAsyncStore => {
              this.setState({ isLoading: false });
              // SAVE DATA IN GLOBAL STORE - REDUX
              this.props.saveDetailsOnSuccessfullAutoLoginInRedux(dictUserDetailsToSave);

              // // NAVIGATE TO HOME SCREEN
              // this.props.navigation.navigate("Home");

              // CHANGE THE ROOT SCREEN

              if (this.props.screenProps && this.props.screenProps.isFromNotification) {


                strQuizDate = undefined
                if (this.props.screenProps.payload && this.props.screenProps.payload.data) {
                  strQuizDate = this.props.screenProps.payload.data.notification_date
                }


                // this.props.saveQuizDetailsFromSplashInRedux({ question_date: strQuizDate, totalquestions: 4 })

                NavigationService.reloadStackForQuizScreen(this.props.navigation, this.props.screenProps.payload)
              } else {
                this.props.navigation.dispatch(
                  StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home" })]
                  })
                );
              }
            },
            errorSavingInAsyncStore => { }
          );

        },
        (dictFailure, message) => {
          // showDialogue(message || Messages.generalWebServiceError);
          this.props.navigation.dispatch(
            StackActions.reset({
              index: 0,
              key: null,
              actions: [NavigationActions.navigate({ routeName: "Login" })]
            })
          );
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
  buttonLoginPressed = () => {
    this.props.navigation.navigate("Login");
  };

  buttonSkipPressed = () => {
  };

  render() {
    return (
      <View style={{ flex: 1 }}>
        {this.state.isLoading ? <ProgressLoader /> : null}
        <View
          style={{ flex: 7, alignItems: "center", justifyContent: "center" }}
        >
          <Image source={Assets.logo} />
        </View>
        <View
          style={{ flex: 3, alignItems: "center", justifyContent: "center" }}
        >
          <EDRTLView
            style={{
              marginTop: 10,
              alignItems: "center",
              justifyContent: "center"
            }}
          >
            {/* <EDThemeButton
              textStyle={{ color: EDColors.primary, fontSize: 18 }}
              style={{
                width: "35%",
                borderColor: EDColors.primary,
                borderWidth: 1,
                marginRight: isRTL ? 0 : 20,
                marginLeft: isRTL ? 20 : 0,
                marginTop: 0,
                backgroundColor: EDColors.white
              }}
              label={strings("Login.signIn")}
              onPress={this.buttonLoginPressed}
            />
            <EDThemeButton
              textStyle={{ fontSize: 18 }}
              style={{
                borderColor: EDColors.primary,
                borderWidth: 1,
                width: "35%",
                marginTop: 0
              }}
              label={strings("General.skip")}
              onPress={this.buttonSkipPressed}
            /> */}
          </EDRTLView>
        </View>
        <View
          style={{ flex: 4, alignItems: "center", justifyContent: "flex-end" }}
        >
          <Image
            resizeMethod="resize"
            resizeMode="center"
            style={{}}
            source={Assets.buildings}
          />
        </View>
      </View>
    );
  }
}

export default connect(

  state => {
    return {

    }
  },
  dispatch => {
    return {
      saveDetailsOnSuccessfullAutoLoginInRedux: userObject => {
        dispatch(saveUserDetailsOnLogin(userObject));
      },
      saveQuizDetailsFromSplashInRedux: (selectedQuizData) => {
        dispatch(saveSelectedQuizInRedux(selectedQuizData))
      }

    }

  }

)(SplashContainer);
