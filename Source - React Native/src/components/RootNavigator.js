import { createStackNavigator, createAppContainer, createBottomTabNavigator } from "react-navigation";
import React from "react";
import SplashContainer from "../containers/SplashContainer";
import LoginContainer from "../containers/LoginContainer";
import SignUpContainer from "../containers/SignUpContainer";

import HistoryContainer from "../containers/HistoryContainer";
import QuizContainer from "../containers/QuizContainer";
import EditProfileContainer from "../containers/EditProfileContainer";
import ResultsContainer from "../containers/ResultsContainer";
import ForgotPasswordContainer from "../containers/ForgotPasswordContainer";
import FeedbackContainer from "../containers/FeedbackContainer";



import Assets from "../assets";
import { debugLog } from "../utils/EDConstants";
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Image,
  Platform,
  Text,
  StatusBar
} from "react-native";
import { EDColors } from "../utils/EDColors";


const QUIZ_STACK_CONTAINER = createStackNavigator(
  {
    QuizContainer: {
      screen: QuizContainer
    }
  },
  {
    initialRouteName: "QuizContainer",
    headerMode: "none"
  }
);

const QUESTIONS_STACK_CONTAINER = createStackNavigator(
  {
    Questions: {
      screen: QuizContainer
    }
  },
  {
    initialRouteName: "Questions",
    headerMode: "none"
  }
);


const RESULTS_STACK_CONTAINER = createStackNavigator(
  {
    Results: {
      screen: ResultsContainer
    }
  },
  {
    initialRouteName: "Results",
    headerMode: "none"
  }
);

const FEEDBACK_STACK_CONTAINER = createStackNavigator(
  {
    Feedback: {
      screen: FeedbackContainer
    }
  },
  {
    initialRouteName: "Feedback",
    headerMode: "none"
  }
);


const PROFILE_STACK_CONTAINER = createStackNavigator(
  {

    EditProfileContainer: {
      screen: EditProfileContainer
    }
  },
  {
    initialRouteName: "EditProfileContainer",
    headerMode: "none"
  }
);

const QUIZ_DETAILS_TAB_NAVIGATOR = createBottomTabNavigator(
  {
    Questions: {
      screen: QuizContainer
    },
    Results: {
      screen: ResultsContainer
    },
    Feedback: {
      screen: FeedbackContainer
    }
  },
  {
    defaultNavigationOptions: ({ navigation }) => ({
      tabBarIcon: ({ focused, tintColor }) => {
        const { routeName } = navigation.state;
        var styleToApply = {
          height: 25,
          width: 25
        };
        if (routeName === "Questions") {
          iconName = focused ? Assets.questionsSelected : Assets.questions;
        } else if (routeName === "Results") {
          iconName = focused ? Assets.resultsSelected : Assets.results;
        } else if (routeName === "Feedback") {
          iconName = focused ? Assets.feedbackSelected : Assets.feedback;
        }
        return <Image style={styleToApply} source={iconName} />;
      }
    }),
    initialRouteName: "Questions",
    tabBarPosition: "bottom",
    tabBarOptions: {
      activeTintColor: EDColors.primary,
      inactiveTintColor: EDColors.text,
      showLabel: true
    }
  }
);

const HISTORY_STACK_CONTAINER = createStackNavigator(
  {
    HistoryContainer: {
      screen: HistoryContainer
    },


  },
  {
    initialRouteName: "HistoryContainer",
    headerMode: "none"
  }
);

const HOME_TAB_NAVIGATOR = createBottomTabNavigator(
  {
    Quiz: {
      screen: QUIZ_STACK_CONTAINER
    },
    History: {
      screen: HISTORY_STACK_CONTAINER
    },
    Profile: {
      screen: PROFILE_STACK_CONTAINER
    }
  },
  {
    defaultNavigationOptions: ({ navigation }) => ({
      // tabBarOnPress: tabBarOnPress(navigation),
      tabBarIcon: ({ focused, tintColor }) => {
        const { routeName } = navigation.state;
        var styleToApply = {
          height: 25,
          width: 25
        };
        // debugLog("ROUTE NAME ::: ", routeName)
        // var iconName = "";
        if (routeName === "Quiz") {
          iconName = focused ? Assets.quizSelected : Assets.quiz;
        } else if (routeName === "History") {
          iconName = focused ? Assets.historySelected : Assets.history;
        } else if (routeName === "Profile") {
          iconName = focused ? Assets.userSelected : Assets.user;
        }
        return <Image style={styleToApply} source={iconName} />;
      }
    }),
    initialRouteName: "Quiz",
    tabBarPosition: "bottom",
    tabBarOptions: {
      activeTintColor: EDColors.primary,
      inactiveTintColor: EDColors.text,
      style: {
      },
      showLabel: true
    }
  }
);


const RootNavigator = createStackNavigator(
  {
    Splash: {
      screen: SplashContainer
    },
    Login: {
      screen: LoginContainer
    },
    Register: {
      screen: SignUpContainer
    },
    Home: {
      screen: HOME_TAB_NAVIGATOR
    },
    ForgotPassword: {
      screen: ForgotPasswordContainer
    },
    Feedback: {
      screen: FeedbackContainer
    },
    QuizDetails: {
      screen: QUIZ_DETAILS_TAB_NAVIGATOR
    }

  },
  {
    initialRouteName: "Splash",
    headerMode: "none"
  }
);

export const AppNavigator = createAppContainer(RootNavigator);
