/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow
 */

import React, { Component } from 'react';
import { Platform, StyleSheet, Text, View } from 'react-native';
import { AppNavigator } from "./src/components/RootNavigator";
import firebase from "react-native-firebase";

import { createStore, combineReducers, bindActionCreators, connect } from "redux";
import { Provider } from "react-redux";
import { userOperations } from "./src/redux/reducers/UserReducer";
import { generalOperations } from "./src/redux/reducers/GeneralReducer";
import { navigationOperation } from "./src/redux/reducers/NavigationReducer";
import KeyboardManager from "react-native-keyboard-manager";
import { strings } from "./src/locales/i18n";
import NavigationService from "./NavigationService";
import { notificationAlert, showDialogue } from "./src/utils/EDAlert";
import { debugLog } from "./src/utils/EDConstants";
import { quizOperations } from './src/redux/reducers/QuizReducer';
import AsyncStorage from "@react-native-community/async-storage"


const rootReducer = combineReducers({
  userOperation: userOperations,
  navigationReducer: navigationOperation,
  generalReducer: generalOperations,
  quizReducer: quizOperations
});

const globalStore = createStore(rootReducer);

type Props = {};

export default class App extends Component<Props> {

  constructor(props) {
    super(props);
    this.isOpenedFromNotification = false;
    this.notificationPayload = undefined;
  }
  state = {
    stateRefresh: false // This is used to refresh the state to allow notification screen to be rendered if app is in background
  };

  componentDidMount() {

    this.checkPermission();
    this.createNotificationListeners();

    console.disableYellowBox = true

    if (Platform.OS == "ios") {
      KeyboardManager.setEnable(true);
      KeyboardManager.setEnableAutoToolbar(true);
      KeyboardManager.setToolbarPreviousNextButtonEnable(true);
      KeyboardManager.setShouldToolbarUsesTextFieldTintColor(true);
      KeyboardManager.setShouldShowToolbarPlaceholder(true);
      KeyboardManager.setShouldResignOnTouchOutside(true);
      KeyboardManager.setToolbarDoneBarButtonItemText(strings("General.done"));
    }
  }

  //Remove listeners allocated in createNotificationListeners()
  componentWillUnmount() {
    this.notificationListener();
    this.notificationOpenedListener();
  }

  //1
  async checkPermission() {
    const enabled = await firebase.messaging().hasPermission();
    if (enabled) {
      this.getToken();
    } else {
      this.requestPermission();
    }
  }

  //3
  async getToken() {
    let fcmToken = await AsyncStorage.getItem("fcmToken");
    // showDialogue("fcmToken 123 :: " + fcmToken)
    if (!fcmToken) {
      fcmToken = await firebase.messaging().getToken();
      if (fcmToken) {
        // user has a device token
        await AsyncStorage.setItem("fcmToken", fcmToken);
      }
    }
  }

  //2
  async requestPermission() {
    try {
      await firebase.messaging().requestPermission();
      // User has authorised
      this.getToken();
    } catch (error) {
      // User has rejected permissions
      // showDialogue("PERMISSION REJECTED")
      console.log("permission rejected");
    }
  }

  async createNotificationListeners() {
    /*
     * Triggered when a particular notification has been received in foreground
     * */
    this.notificationListener = firebase
      .notifications()
      .onNotification(notification => {
        console.log("===== foreground =====");
        const { title, body } = notification;
        this.isOpenedFromNotification = false;
        this.setState({
          openedFromNotification: this.state.stateRefresh ? false : true
        });
        // this.showAlert(title, body);
        notificationAlert(title || "", body || "", () => {
          this.notificationPayload = notification
          NavigationService.reloadStackForQuizScreen("Home", notification)

          // NavigationService.navigate("NotificationsContainer");
        });
      });

    /*
     * If your app is in background, you can listen for when a notification is clicked / tapped / opened as follows:
     * */
    this.notificationOpenedListener = firebase
      .notifications()
      .onNotificationOpened(notificationOpen => {

        this.notificationPayload = notificationOpen.notification
        console.log("===== background =====");

        console.log(
          "before notificationOpenedListener",
          this.isOpenedFromNotification
        );
        const { title, body } = notificationOpen.notification;
        this.isOpenedFromNotification = true;
        this.setState({
          openedFromNotification: this.state.stateRefresh ? false : true
        });
        NavigationService.reloadStackForQuizScreen("Home", notificationOpen.notification)
        // NavigationService.navigate("NotificationsContainer");
        console.log(
          "after notificationOpenedListener",
          this.isOpenedFromNotification
        );

        // this.showAlert(title, body);
      });

    /*
     * If your app is closed, you can check if it was opened by a notification being clicked / tapped / opened as follows:
     * */
    const notificationOpen = await firebase
      .notifications()
      .getInitialNotification();
    if (notificationOpen) {

      this.notificationPayload = notificationOpen.notification

      console.log("===== closed =====");
      console.log("before notificationOpen", this.isOpenedFromNotification);

      this.isOpenedFromNotification = true;
      this.setState({
        openedFromNotification: this.state.stateRefresh ? false : true
      });

      // HENIT: COMMENTED BELOW LINE
      // NavigationService.reloadStackForQuizScreen("Home", notificationOpen.notification)


      console.log("navigate to notification screen");
    }
    /*
     * Triggered for data only payload in foreground
     * */
    this.messageListener = firebase.messaging().onMessage(message => {
      //process data message
      console.log(JSON.stringify(message));
    });
    console.log("after notificationOpen", this.isOpenedFromNotification);
  }

  render() {
    return (
      <Provider store={globalStore}>

        <View style={{ flex: 1 }}>
          {/* {this.isOpenedFromNotification != undefined ? (
          ) : null} */}

          <AppNavigator
            ref={navigatorRef => {
              NavigationService.setTopLevelNavigator(navigatorRef);
            }}
            screenProps={{ isFromNotification: this.isOpenedFromNotification || false, payload: this.notificationPayload || {} }} // screenProps are used by top level navigator to send data to any screen under them as props.
          />

        </View>
      </Provider>
    );
  }
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F5FCFF',
  },
  welcome: {
    fontSize: 20,
    textAlign: 'center',
    margin: 10,
  },
  instructions: {
    textAlign: 'center',
    color: '#333333',
    marginBottom: 5,
  },
});
