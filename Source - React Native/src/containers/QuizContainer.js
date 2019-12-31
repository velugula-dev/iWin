import React from "react";
import {
    View,
    Text,
    StyleSheet,
    Image,
    KeyboardAvoidingView,
    Platform,
    AppRegistry,
    Button,
    Dimensions,
    ScrollView,
    TouchableOpacity,
    Modal, TouchableWithoutFeedback, FlatList, Linking

} from "react-native";

import Metrics from "../utils/Metrics";
import BaseContainer from "./BaseContainer";
import { strings, isRTL } from "../locales/i18n";
import Assets from "../assets";
import EDCard from "../components/EDCard";
import { EDColors } from "../utils/EDColors";
import { debugLog, LOGIN_URL, GET_CMS_DATA, GET_QUESTIONS_URL, SUBMIT_ANSWERS_URL, AdMob_IOS_AD_UNIT_ID_INTERSTITIAL_TEST, AdMob_ANDROID_AD_UNIT_ID_BANNER, AdMob_IOS_AD_UNIT_ID_INTERSTITIAL, CMSPages, NotificationTypes, AdMob_ANDROID_AD_UNIT_ID_INTERSTITIAL } from "../utils/EDConstants";
import { EDFonts } from "../utils/EDFontConstants";
import { showNotImplementedAlert, showDialogue, showNoInternetAlert } from "../utils/EDAlert";
import { Card } from "native-base";
import EDRTLView from "../components/EDRTLView";
import EDButton from "../components/EDButton";
import QuestionItem from "../components/QuestionItem";
import { heightPercentageToDP } from "react-native-responsive-screen";
import { connect } from "react-redux";
import { apiPost, netStatus } from "../utils/ServiceManager";
import EDPlaceholderView from "../components/EDPlaceholderView";
import { Messages } from "../utils/Messages";
import {
    AdMobInterstitial
} from 'react-native-admob';
import Hyperlink from 'react-native-hyperlink'
import { NavigationEvents } from "react-navigation";
import { saveSelectedQuizInRedux, removeSelectedQuizFromRedux } from "../redux/actions/QuizAction";
import MyWebView from "react-native-webview-autoheight";
import EDHThemeButton from "../components/EDThemeButton";
import { saveAppInfoInfoInRedux } from "../redux/actions/GlobalActions";
import Permissions from "react-native-permissions";
import moment from "moment";
// import { check, PERMISSIONS, RESULTS, request } from 'react-native-permissions';


class QuizContainer extends React.Component {


    constructor(props) {
        super(props);
        this.userAnswers = {};
        this.isAnswerSubmittedForMidQuestion = false;
        this.todayDate = "";
        this.adTimerDuration = 3;
        this.isInfoViewGlobalMessage = false;
        this.shouldCallCMSAPI = true;
        // this.strDateToBeUsedInAPI = this.props.screenProps.payload.data
        //     ? this.props.screenProps.payload.data.notification_date || ""
        //     : "";
        // this.shouldShowBackArrow = this.strDateToBeUsedInAPI.length > 0;
        this.shouldAllowToGiveAnswersFlagAPI = true
        this.shouldDisplayCorrectAnswers = false
        this.customStyle = "<style>* {max-width: 100%;} body {font-size: 45px;font-family:Ubuntu-Regular}</style>";
        this.strTitle = ""
        this.strDateTimeForAPI = ""
    }

    state = {
        isLoading: false,
        shouldShowInfoView: false,
        infoText: undefined,
        arrayQuestions: undefined,
        strOnScreenMessage: "",
        currentQuestion: 0,
        shouldEnableNextPreviousButton: true
    }

    callQuizQuestionsAPI() {

        this.setState({ isLoading: true, arrayQuestions: undefined, currentQuestion: 0, strOnScreenMessage: '' })
        let questionsParams = {
            user_id: this.props.userDetails.user_id,
            exam_date: this.shouldShowBackArrow ? this.strDateTimeForAPI || "" : "" // this.props.selectedQuizDate || ""
        };

        // debugLog("questionsParams ::::", questionsParams)


        apiPost(
            GET_QUESTIONS_URL,
            questionsParams,
            (dictSuccess, message) => {

                this.todayDate = ""
                if (dictSuccess != undefined && dictSuccess.todayDate) {
                    this.todayDate = dictSuccess.todayDate || ""
                }
                this.adTimerDuration = parseInt(dictSuccess.timeForAds) || 3
                this.shouldAllowToGiveAnswersFlagAPI = dictSuccess.isEditableQuiz == 1

                this.shouldDisplayCorrectAnswers = dictSuccess.isAnsAdded == 1

                if (dictSuccess.QuizQuestionList != undefined && dictSuccess.QuizQuestionList.length > 0) {

                    indexOfUnansweredQuestion = 0
                    arrQuestions = dictSuccess.QuizQuestionList

                    debugLog("===== arrQuestions.length =====", arrQuestions.length)
                    if (arrQuestions.length > 0) {
                        firstQuestion = arrQuestions[0]
                        this.strDateToBeUsedInAPI = firstQuestion.question_date
                        debugLog("===== this.strDateToBeUsedInAPI.length =====", this.strDateToBeUsedInAPI)
                        this.todayDate = firstQuestion.question_date
                    }

                    var quizDateForTitle = this.strDateToBeUsedInAPI || ""
                    debugLog("===== quizDateForTitle =====", quizDateForTitle)
                    debugLog("===== quizDateForTitle =====", quizDateForTitle.split(" "))
                    if (this.strDateToBeUsedInAPI == undefined && this.strDateToBeUsedInAPI.length > 0) {
                        if (this.strDateToBeUsedInAPI.split(" ").count > 0) {
                            quizDateForTitle = this.strDateToBeUsedInAPI.split(" ")[0];
                        }
                    }
                    this.strTitle = this.shouldShowBackArrow && quizDateForTitle.length > 0
                        ? "Quiz ( " + quizDateForTitle + " )"
                        : strings("ScreenTitles.quiz")


                    // if (!this.isForSelectedQuiz) {
                    // debugLog("this.shouldAllowToGiveAnswersFlagAPI ::: " + this.shouldAllowToGiveAnswersFlagAPI)
                    if (this.shouldAllowToGiveAnswersFlagAPI) {
                        // debugLog("HERE :::  " + this.shouldAllowToGiveAnswersFlagAPI)
                        arrayUnansweredQuestions = arrQuestions.filter(questionToCheck => {
                            return questionToCheck.userAnswers == undefined || (questionToCheck.userAnswers && questionToCheck.userAnswers.length == 0)
                        })
                        // debugLog("arrayUnansweredQuestions :::  " + arrayUnansweredQuestions)
                        if (arrayUnansweredQuestions.length > 0) {
                            firstUnansweredQuestion = arrayUnansweredQuestions[0]
                            // this.strDateToBeUsedInAPI = firstQuestion.question_date
                            // this.todayDate = firstQuestion.question_date

                            indexOfUnansweredQuestion = arrQuestions.findIndex(question => question.question_id == firstUnansweredQuestion.question_id)
                        }
                    }

                    // debugLog("indexOfUnansweredQuestion ::: " + indexOfUnansweredQuestion)

                    this.setState({ arrayQuestions: arrQuestions, isLoading: false, currentQuestion: indexOfUnansweredQuestion, strOnScreenMessage: message })

                    setTimeout(() => {

                        this.scrollQuestionsList(indexOfUnansweredQuestion)

                    }, 500);



                    if (this.shouldCallCMSAPI) {
                        this.callCMSDataAPI()
                        this.shouldCallCMSAPI = false
                    }
                }
            },
            (dictFailure, message) => {

                debugLog("dictFailure dictFailure", dictFailure)
                this.strTitle = strings("ScreenTitles.quiz")
                this.todayDate = ""
                if (dictFailure != undefined && dictFailure.todayDate) {
                    this.todayDate = dictFailure.todayDate
                }
                this.shouldDisplayCorrectAnswers = false

                // this.setState({ isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                this.setState({ isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                // showDialogue(dictFailure.message || Messages.generalWebServiceError);
                if (this.shouldCallCMSAPI) {
                    this.callCMSDataAPI()
                    this.shouldCallCMSAPI = false
                }
            },
            {}
        );
    }

    callCMSDataAPI() {
        let cmsDataParams = {
            device_type: Platform.OS == "ios" ? "ios" : "android",
            cms_slug: CMSPages.AppInfo
        };

        apiPost(
            GET_CMS_DATA,
            cmsDataParams,
            dictSuccess => {
                // debugLog("dictSuccess callCMSDataAPI", dictSuccess)
                // if (dictSuccess.cms_data != undefined && dictSuccess.cms_data.length > 0) {

                if (dictSuccess.cms_data != undefined) {
                    this.props.saveAppInfoInReduxFromQuizContainer(dictSuccess.cms_data)
                    this.setState({ infoText: dictSuccess.cms_data })
                }
            },
            (dictFailure, message) => {
                showDialogue(message || Messages.generalWebServiceError);
            },
            {}
        );
    }

    onAnswerSelectedInContainer = (selectedAnswers, questionAnswered, questionIndex, shouldScrollToNextQuestion) => {
        // debugLog("questionAnswered ::: ", questionAnswered)
        // debugLog("questionIndex ::: ", questionIndex)

        this.userAnswers[questionAnswered.question_id] = selectedAnswers.join(", ")
        if (shouldScrollToNextQuestion) {
            this.buttonNextPressed()
        }


        // this.userAnswers[questionAnswered.question_id] = selectedAnswers.map(answer => {
        //     return answer.question_answer_id
        // }).join(", ")
        // if (shouldScrollToNextQuestion) {
        //     this.buttonNextPressed()
        // }
    }

    questionInfoButtonHandler = (questionToDisplayInfoFor) => {
        this.isInfoViewGlobalMessage = false;
        this.setState({ shouldShowInfoView: !this.state.shouldShowInfoView })
    }

    renderQuestion = (questionToRender) => {
        // debugLog("question to render :: ", questionToRender)
        return (
            <QuestionItem
                // isForOlderQuiz={this.isForSelectedQuiz}
                isForOlderQuiz={!this.shouldAllowToGiveAnswersFlagAPI}
                question={questionToRender.item}
                index={questionToRender.index}
                onAnswerSelected={this.onAnswerSelectedInContainer}
                totalNumberOfQuestions={this.state.arrayQuestions.length}
                questionInfoButtonHandler={this.questionInfoButtonHandler}
                shouldShowCorrectAnswers={this.shouldDisplayCorrectAnswers} />
        )
    }


    buttonInfoPressed = () => {
        this.isInfoViewGlobalMessage = true;
        this.setState({ shouldShowInfoView: !this.state.shouldShowInfoView })
    }

    dismissInfoView = () => {
        this.setState({ shouldShowInfoView: false })
    }

    scrollQuestionsList = (indexToScroll) => {
        if (this.flatListRef != undefined) {
            this.flatListRef.scrollToIndex({
                animated: true,
                index: indexToScroll
            });
        }
    }

    showInterstitialAd = (onCompletion) => {

        shouldShowAds = false
        this.setState({ shouldEnableNextPreviousButton: false })

        // if (this.state.arrayQuestions.length % 2 == 0) {
        //     debugLog("IF")
        //     shouldShowAds = this.state.currentQuestion == ((this.state.arrayQuestions.length / 2) - 1)
        // } else {
        //     debugLog("ELSE")
        //     shouldShowAds = this.state.currentQuestion == (this.state.arrayQuestions.length / 2)
        // }
        shouldShowAds = this.state.currentQuestion == (parseInt(this.state.arrayQuestions.length / 2) - 1)


        if (shouldShowAds) {
            debugLog("ADS : 0")
            // AdMobInterstitial.setAdUnitID(Platform.OS == "ios" ? AdMob_IOS_AD_UNIT_ID_INTERSTITIAL_TEST : AdMob_IOS_AD_UNIT_ID_INTERSTITIAL_TEST);
            AdMobInterstitial.setAdUnitID(Platform.OS == "ios" ? AdMob_IOS_AD_UNIT_ID_INTERSTITIAL : AdMob_ANDROID_AD_UNIT_ID_INTERSTITIAL);
            AdMobInterstitial.setTestDevices([AdMobInterstitial.simulatorId]);
            AdMobInterstitial.requestAd()
                .then(() => AdMobInterstitial.showAd()
                    .then(() => {
                        debugLog("ADS : 1")
                        this.setState({ shouldEnableNextPreviousButton: true })
                        onCompletion()
                    })
                    .catch((errorShowAds) => {
                        debugLog("ADS : 2")
                        debugLog("SHOW ADS CATCH ::: \(errorShowAds)" + JSON.stringify(errorShowAds), [])
                        this.setState({ shouldEnableNextPreviousButton: true })
                        onCompletion()
                    }))
                .catch((error) => {
                    debugLog("ADS : 3")
                    debugLog("ERROR IN INTERSTITIAL :: \(error)", (error))
                    this.setState({ shouldEnableNextPreviousButton: true })
                    onCompletion()
                });
        } else {
            debugLog("ADS : 4")
            this.setState({ shouldEnableNextPreviousButton: true })
            onCompletion()
        }
    }

    buttonPreviousPressed = () => {

        if (this.state.currentQuestion == 0) {
            return
        }

        this.scrollQuestionsList(this.state.currentQuestion - 1)
        this.setState({ currentQuestion: this.state.currentQuestion - 1 })
    }

    buttonNextPressed = () => {




        questionToSubmitAnswerFor = this.state.arrayQuestions[this.state.currentQuestion]
        this.setState({ shouldEnableNextPreviousButton: false })
        if (questionToSubmitAnswerFor.userAnswers && questionToSubmitAnswerFor.userAnswers.length > 0) {

            debugLog("==1==")
            this.showInterstitialAd(() => {
                newIndexToScroll = this.state.currentQuestion == this.state.arrayQuestions.length - 1
                    ? 0
                    : Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1)

                this.scrollQuestionsList(newIndexToScroll)

                var question_date = this.strDateToBeUsedInAPI
                var localDateTimeFromMoment = moment(new Date()).local().format("HH:mm:ss")
                var question_date_time = question_date + " " + localDateTimeFromMoment
                debugLog("==4== " + question_date_time)

                if (newIndexToScroll == 0 && this.state.arrayQuestions.length == 1) {
                    this.props.navigation.navigate("Feedback", { isForToday: !this.shouldShowBackArrow, question_date: this.strDateToBeUsedInAPI, question_date_time: question_date_time, totalquestion: 4 })
                }

                this.setState({ currentQuestion: newIndexToScroll })
            })


        } else {
            debugLog("==2==")

            strUserAnswers = this.userAnswers[questionToSubmitAnswerFor.question_id]
            // if ((strUserAnswers == undefined || strUserAnswers.length == 0) && !this.isForSelectedQuiz) {
            if ((strUserAnswers == null || strUserAnswers === undefined || strUserAnswers == "undefined" || strUserAnswers == undefined || strUserAnswers.length == 0) && this.shouldAllowToGiveAnswersFlagAPI && questionToSubmitAnswerFor.question_ans_rest_status == 0) {
                this.setState({ shouldEnableNextPreviousButton: true })
                showDialogue(Messages.selectAnswer)
                return
            }

            if (this.shouldShowBackArrow && !this.shouldAllowToGiveAnswersFlagAPI) {
                this.showInterstitialAd(() => {
                    debugLog("==3==")
                    newIndexToScroll = this.state.currentQuestion == this.state.arrayQuestions.length - 1
                        ? 0
                        : Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1)

                    this.scrollQuestionsList(newIndexToScroll)
                    this.setState({ currentQuestion: newIndexToScroll, shouldEnableNextPreviousButton: true })
                })

                return
            }

            if (!this.shouldAllowToGiveAnswersFlagAPI || questionToSubmitAnswerFor.question_ans_rest_status == 1) {
                this.showInterstitialAd(() => {
                    debugLog("==4==")
                    newIndexToScroll = this.state.currentQuestion == this.state.arrayQuestions.length - 1
                        ? 0
                        : Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1)

                    this.scrollQuestionsList(newIndexToScroll)
                    debugLog("==4==" + newIndexToScroll + " - " + this.strDateToBeUsedInAPI + + " - " + this.strDateTimeForAPI)

                    var question_date = this.strDateToBeUsedInAPI
                    var localDateTimeFromMoment = moment(new Date()).local().format("HH:mm:ss")
                    var question_date_time = question_date + " " + localDateTimeFromMoment
                    debugLog("==4== " + question_date_time)

                    if (newIndexToScroll == 0 && this.state.arrayQuestions.length == 1) {
                        this.props.navigation.navigate("Feedback", { isForToday: !this.shouldShowBackArrow, question_date: this.strDateToBeUsedInAPI, question_date_time: question_date_time, totalquestion: 4 })
                    }

                    this.setState({ currentQuestion: newIndexToScroll, shouldEnableNextPreviousButton: true })
                })

                return
            }

            // CALL API FOR SUBMITTING USER ANSWERS HERE...
            netStatus(status => {
                if (status) {
                    this.setState({ isLoading: true })

                    this.showInterstitialAd(() => {

                        nextQuestionID = this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? "" : this.state.arrayQuestions[this.state.currentQuestion + 1].question_id

                        let submitAnswersParams = {
                            user_id: this.props.userDetails.user_id,
                            question_id: questionToSubmitAnswerFor.question_id,
                            question_answer_id: strUserAnswers,
                            latitude: "",
                            longitude: "",
                            current_quiz_question: nextQuestionID
                        }

                        apiPost(
                            SUBMIT_ANSWERS_URL,
                            submitAnswersParams,
                            (dictSuccess, message) => {




                                questionToSubmitAnswerFor.userAnswers = strUserAnswers
                                arrayQuestionsUpdated = this.state.arrayQuestions
                                arrayQuestionsUpdated[this.state.currentQuestion] = questionToSubmitAnswerFor

                                if (this.state.currentQuestion == this.state.arrayQuestions.length - 1) {

                                    // SAVE THE SELECTED QUIZ IN REDUX
                                    strQuestionDate = ""
                                    if (arrQuestions.length > 0) {
                                        strQuestionDate = arrQuestions[0].question_date || ""
                                    }
                                    // this.props.saveQuizDetailsInRedux({ question_date: strQuestionDate, totalquestions: dictSuccess.questionsCount || 0 })

                                    // NAVIGATE TO FEEDBACK SCREEN FROM HERE..
                                    this.scrollQuestionsList(0)
                                    var question_date = this.strDateToBeUsedInAPI
                                    var localDateTimeFromMoment = moment(new Date()).local().format("HH:mm:ss")
                                    var question_date_time = question_date + " " + localDateTimeFromMoment
                                    this.setState({ isLoading: false, arrayQuestions: arrayQuestionsUpdated, currentQuestion: this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? 0 : this.state.currentQuestion + 1 })
                                    this.props.navigation.navigate("Feedback", { isForToday: !this.shouldShowBackArrow, question_date: this.strDateToBeUsedInAPI, question_date_time: question_date_time, totalquestion: 4 })

                                } else {

                                    if (this.state.currentQuestion == ((this.state.arrayQuestions.length / 2) - 1)) {
                                        setTimeout(() => {
                                            this.scrollQuestionsList(Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1))
                                            this.setState({ isLoading: false, arrayQuestions: arrayQuestionsUpdated, currentQuestion: this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? 0 : this.state.currentQuestion + 1 })
                                        }, this.adTimerDuration * 1000);
                                    } else {
                                        this.scrollQuestionsList(Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1))
                                        this.setState({ isLoading: false, arrayQuestions: arrayQuestionsUpdated, currentQuestion: this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? 0 : this.state.currentQuestion + 1 })
                                    }

                                }

                            },
                            (dictFailure, message) => {
                                debugLog("====== dictFailure SUBMIT ANSWER =======", dictFailure, message)
                                debugLog("====== this.state.currentQuestion =======", this.state.currentQuestion)
                                showDialogue(message || Messages.generalWebServiceError)

                                if (dictFailure.hasOwnProperty('isQuestionDisableError')) {
                                    debugLog("====== 1 =======")

                                    if (dictFailure.isQuestionDisableError) {
                                        // questionToSubmitAnswerFor.userAnswers = strUserAnswers
                                        questionToSubmitAnswerFor.question_ans_rest_status = 1
                                        arrayQuestionsUpdated = this.state.arrayQuestions
                                        arrayQuestionsUpdated[this.state.currentQuestion] = questionToSubmitAnswerFor

                                    }

                                    if (this.state.currentQuestion == ((this.state.arrayQuestions.length / 2) - 1)) {
                                        setTimeout(() => {
                                            this.scrollQuestionsList(Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1))
                                            this.setState({ isLoading: false, arrayQuestions: arrayQuestionsUpdated, currentQuestion: this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? 0 : this.state.currentQuestion + 1 })
                                        }, this.adTimerDuration * 1000);
                                    } else {
                                        this.scrollQuestionsList(Math.min(this.state.arrayQuestions.length - 1, this.state.currentQuestion + 1))
                                        this.setState({ isLoading: false, arrayQuestions: arrayQuestionsUpdated, currentQuestion: this.state.currentQuestion == this.state.arrayQuestions.length - 1 ? 0 : this.state.currentQuestion + 1 })
                                    }

                                } else {
                                    this.setState({ isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                                }


                                // showDialogue(dictFailure.message || Messages.generalWebServiceError);
                            },
                            {}
                        );
                    })



                } else {
                    showNoInternetAlert()
                }
            })
        }
    }


    _onViewableItemsChanged = ({ viewableItems, changed }) => {
        console.log("Visible items are", viewableItems);
        console.log("Changed in this iteration", changed);
    };

    _viewabilityConfig = {
        itemVisiblePercentThreshold: 50
    };

    checkPhotosPermission = () => {

        // check("ios.permission.PHOTO_LIBRARY").then(result => {
        //     switch (result) {
        //         case RESULTS.UNAVAILABLE:
        //             console.log(
        //                 'This feature is not available (on this device / in this context)',
        //             );
        //             this.requestPermissionPhotos();
        //             break;
        //         case RESULTS.DENIED:
        //             console.log(
        //                 'The permission has not been requested / is denied but requestable',
        //             );
        //             this.requestPermissionPhotos();
        //             break;
        //         case RESULTS.GRANTED:
        //             console.log('The permission is granted');
        //             break;
        //         case RESULTS.BLOCKED:
        //             this.requestPermissionPhotos();
        //             console.log('The permission is denied and not requestable anymore');
        //             break;
        //     }
        // })
        //     .catch(error => {
        //         console.log("response _checkPhotos error", error);
        //     });

        Permissions.check("photo").then(
            response => {
                //response is an object mapping type to permission
                console.log("response _checkPhotos success", response);

                if (response == "authorized") {
                    console.log("response _checkPhotos success", response);
                } else {
                    this.requestPermissionPhotos();
                    console.log("error _checkPhotos success", response);
                }
            },
            error => {
                console.log("error get success", error);
            }
        );
    };

    requestPermissionPhotos = () => {

        // request("ios.permission.PHOTO_LIBRARY").then(result => {
        //     switch (result) {
        //         case RESULTS.UNAVAILABLE:
        //             console.log(
        //                 'This feature is not available (on this device / in this context)',
        //             );
        //             break;
        //         case RESULTS.DENIED:
        //             console.log(
        //                 'The permission has not been requested / is denied but requestable',
        //             );
        //             break;
        //         case RESULTS.GRANTED:
        //             console.log('The permission is granted');
        //             break;
        //         case RESULTS.BLOCKED:
        //             console.log('The permission is denied and not requestable anymore');
        //             break;
        //     }
        // });



        Permissions.request("photo").then(response => {
            // Returns once the user has chosen to 'allow' or to 'not allow' access
            // Response is one of: 'authorized', 'denied', 'restricted', or 'undetermined'

            // if (response == "authorized") {
            //   this.setLanguage();
            // } else {
            // }
        });
    };








    componentDidMount() {
        // netStatus(status => {
        //     if (status) {
        //         this.callQuizQuestionsAPI()
        //     } else {
        //         this.setState({ strOnScreenMessage: Messages.noInternet })
        //         showNoInternetAlert();
        //     }
        // });
        this.checkPhotosPermission()
    }

    navigateToPreviousScreen = () => {
        // REMOVE SELECTED DATE FROM REDUX
        this.props.removeSelectedQuizDateFromReduxOnBackButtonQuizContainer()
        this.props.screenProps.payload.data.notification_date = undefined
        this.props.navigation.pop()
    }

    onDidBlurQuizContainer = () => {
    }

    componentWillUnmount() {
        // REMOVE SELECTED DATE FROM REDUX
        this.props.removeSelectedQuizDateFromReduxOnBackButtonQuizContainer()
        if (this.props.screenProps != undefined && this.props.screenProps.payload != undefined && this.props.screenProps.payload.data != undefined && this.props.screenProps.payload.data.notification_date != undefined) {
            this.props.screenProps.payload.data.notification_date = undefined
        }
    }

    onDidFocusQuizContainer = () => {
        debugLog("THIS PROPS ::: ", this.props)


        // this.isForSelectedQuiz = this.props.selectedQuizDate != undefined && this.props.selectedQuizDate.length > 0;
        this.strDateToBeUsedInAPI = (this.props.screenProps.payload.data && this.props.screenProps.payload.data.notification_date)
            ? this.props.screenProps.payload.data.notification_date || ""
            : this.props.navigation.state.params
                ? this.props.navigation.state.params.question_date || ""
                : "";
        this.strDateTimeForAPI = (this.props.screenProps.payload.data && this.props.screenProps.payload.data.notification_date_time)
            ? this.props.screenProps.payload.data.notification_date_time || ""
            : this.props.navigation.state.params
                ? this.props.navigation.state.params.question_date_time || ""
                : "";
        this.shouldShowBackArrow = (this.props.screenProps.payload.data && this.props.screenProps.payload.data.notification_type &&
            (this.props.screenProps.payload.data.notification_type.toLowerCase() == NotificationTypes.questionsPosted || this.props.screenProps.payload.data.notification_type.toLowerCase() == NotificationTypes.custom))
            ? false
            : this.strDateToBeUsedInAPI.length > 0;

        if (this.props.screenProps.payload.data && this.props.screenProps.payload.data.notification_type) {
            this.strDateToBeUsedInAPI = ""
            this.strDateTimeForAPI = ""
        }
        this.props.screenProps.payload = { data: { notification_date: this.strDateToBeUsedInAPI, notification_date_time: this.strDateTimeForAPI } }


        netStatus(status => {
            if (status) {
                debugLog("===== onDidFocusQuizContainer =====")

                this.callQuizQuestionsAPI()
            } else {
                this.setState({ strOnScreenMessage: Messages.noInternet })
                showNoInternetAlert();
            }
        });
    }

    networkStatusChangeHandler = (status) => {
        if (status) {
            debugLog("===== networkStatusChangeHandler =====")
            if (this.state.arrayQuestions != undefined && this.state.arrayQuestions.length == 0) {
                this.callQuizQuestionsAPI()
            }
        } else {
            this.setState({ isLoading: false })
        }
    }

    render() {
        currentQuestion = (this.state.arrayQuestions ? this.state.arrayQuestions[this.state.currentQuestion] : undefined)

        return (

            <BaseContainer
                title={this.strTitle}
                loading={this.state.isLoading}
                // right={this.state.infoText ? Assets.infoicon : null}
                // onRight={this.buttonInfoPressed}
                isLeftString={!this.shouldShowBackArrow}
                left={this.shouldShowBackArrow ? Assets.back : this.todayDate || ""}
                onLeft={this.navigateToPreviousScreen}
                networkStatus={this.networkStatusChangeHandler}
            >

                <NavigationEvents onDidFocus={this.onDidFocusQuizContainer} onDidBlur={this.onDidBlurQuizContainer} />

                {(this.state.shouldShowInfoView)
                    ? <Modal visible={this.state.shouldShowInfoView}
                        animationType="slide"
                        transparent={true}
                        onRequestClose={this.dismissInfoView}>


                        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.25)', justifyContent: "center" }}>

                            <View style={{
                                width: Metrics.screenWidth - 40,
                                height: Metrics.screenHeight - 2 * (heightPercentageToDP("10%") + 10),
                                marginHorizontal: 20, marginVertical: heightPercentageToDP("10%") + 10,
                                backgroundColor: "white",
                                justifyContent: 'center',
                                borderRadius: 8,
                            }}>

                                {/* <View style={{
                                backgroundColor: "#fff",
                                padding: 10,
                                marginLeft: 20,
                                marginRight: 20,
                                borderRadius: 6,
                                width: Dimensions.get("window").width - 40,
                                height: Dimensions.get("window").height - 80,
                                marginTop: 20,
                                marginBottom: 20,
                                justifyContent: 'center'
                            }}> */}



                                <Text style={{ marginHorizontal: 20, marginTop: 20, marginBottom: 10, textAlign: 'center', fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2.3%") }}>{this.isInfoViewGlobalMessage ? this.state.infoText.cms_title : "Question Info"}</Text>



                                {/* <ScrollView style={{ marginVertical: 10, marginHorizontal: 10, flex: 1 }}>
                                    <Hyperlink linkStyle={{ color: EDColors.primary }} linkDefault={true}>
                                        <Text style={{ margin: 20, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("2.1%") }}>{(this.isInfoViewGlobalMessage ? this.state.infoText.cms_contents : currentQuestion.question_detail) + "\n\n" + "Read more at http://www.google.com"}</Text>
                                    </Hyperlink>
                                </ScrollView> */}


                                <MyWebView
                                    source={{ html: this.customStyle + (this.isInfoViewGlobalMessage ? this.state.infoText.cms_contents : currentQuestion.question_detail) }}
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

                                {/* <EDButton newStyle={{ alignSelf: 'center' }} buttonStyle={{ marginBottom: 20 }}
                                    label={strings("General.dismiss")}
                                    onPress={this.dismissInfoView}
                                /> */}

                            </View>
                        </View>
                        {/* </TouchableOpacity> */}

                    </Modal>
                    : null}





                {this.state.arrayQuestions != undefined
                    ?
                    (this.state.arrayQuestions.length > 0
                        ? <FlatList
                            style={{ flex: 1 }}
                            initialScrollIndex={this.state.currentQuestion}
                            scrollEnabled={false}
                            pagingEnabled={true}
                            getItemLayout={(data, index) => {
                                return { length: Metrics.screenWidth, offset: Metrics.screenWidth * index, index }
                            }
                            }
                            ref={(ref) => { this.flatListRef = ref; }}
                            initialNumToRender={this.state.currentQuestion}
                            showsVerticalScrollIndicator={false}
                            showsHorizontalScrollIndicator={false}
                            horizontal={true}
                            data={this.state.arrayQuestions}
                            renderItem={this.renderQuestion}
                            keyExtractor={(item, index) => item + index}
                        />
                        : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />
                    )
                    : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />}


                {this.state.arrayQuestions != undefined
                    ? <EDRTLView pointerEvents={this.state.isAnswered ? "none" : "auto"} opacity={this.state.isAnswered ? 0.5 : 1} style={{ alignItems: 'center', justifyContent: 'center' }}>

                        {this.state.currentQuestion == 0
                            ? null
                            : <EDButton
                                label={strings("General.previous")}
                                pointerEvents={this.state.shouldEnableNextPreviousButton ? "auto" : "none"}
                                onPress={this.buttonPreviousPressed}
                                containerStyle={{ width: Metrics.screenWidth * 0.4 }}
                                buttonStyle={{ opacity: this.state.shouldEnableNextPreviousButton ? 1.0 : 0.5, borderColor: EDColors.primary, borderWidth: 1, borderRadius: 10, marginHorizontal: 10, color: EDColors.primary }}
                                textStyle={{ color: EDColors.primary, marginVertical: 15, marginHorizontal: 40 }}
                            />
                        }

                        <EDButton
                            label={this.state.currentQuestion >= (this.state.arrayQuestions && this.state.arrayQuestions.length - 1) ? strings("General.submit") : strings("General.next")}
                            pointerEvents={this.state.shouldEnableNextPreviousButton ? "auto" : "none"}
                            onPress={this.buttonNextPressed}
                            containerStyle={{ width: Metrics.screenWidth * 0.4 }}
                            buttonStyle={{ opacity: this.state.shouldEnableNextPreviousButton ? 1.0 : 0.5, marginHorizontal: 10, backgroundColor: EDColors.primary, color: EDColors.white }}
                            textStyle={{ color: EDColors.white, marginVertical: 15, marginHorizontal: 40 }}
                        />
                    </EDRTLView>
                    : null}

            </BaseContainer >

        );
    }

}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: "center",
        backgroundColor: "#F5FCFF"
    },

});

export default connect(
    state => {
        return {
            userDetails: state.userOperation,
            selectedQuizDate: state.quizReducer ? (state.quizReducer.question_date || undefined) : undefined,
        }
    },
    dispatch => {
        return {
            saveQuizDetailsInRedux: (selectedQuizData) => {
                dispatch(saveSelectedQuizInRedux(selectedQuizData))
            },
            removeSelectedQuizDateFromReduxOnBackButtonQuizContainer: () => {
                dispatch(removeSelectedQuizFromRedux())
            },
            saveAppInfoInReduxFromQuizContainer: (appInfoObject) => {
                dispatch(saveAppInfoInfoInRedux(appInfoObject))
            }
        }
    }
)(QuizContainer);