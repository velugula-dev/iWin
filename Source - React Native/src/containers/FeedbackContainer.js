import React, { Component } from "react";
import { Platform, StyleSheet, View, BackHandler } from "react-native";
import { EDColors } from "../utils/EDColors";
import { EDFonts } from "../utils/EDFontConstants";
import BaseContainer from "../containers/BaseContainer"
import { showDialogue } from "../utils/EDAlert";
import { Messages } from "../utils/Messages";
import { strings } from "../locales/i18n";
import { debugLog, FeedbackAnswerType, FeedbackQuestionType, GET_FEEDBACK_QUESTIONS_URL, SAVE_FEEDBACK_ANSWERS_URL } from "../utils/EDConstants";
import Assets from "../assets";
import { apiPost, netStatus } from "../utils/ServiceManager";
import FeedbackQuestionItem from "../components/FeedbackQuestionItem";
import { heightPercentageToDP } from "react-native-responsive-screen";
import EDHThemeButton from "../components/EDThemeButton";
import { NavigationEvents } from "react-navigation";
import EDPlaceholderView from "../components/EDPlaceholderView";
import { connect } from "react-redux";
import Toast, { DURATION } from "react-native-easy-toast";
import MyWebView from "react-native-webview-autoheight";

import { removeSelectedQuizFromRedux } from "../redux/actions/QuizAction";
import { KeyboardAwareFlatList } from "react-native-keyboard-aware-scroll-view";
import WebView from "react-native-webview";


const jsonData = [
    { "question_id": 1, "questionType": FeedbackQuestionType.thumbsUpDown, "question_name": "Did you like the questions today?", "userAnswer": FeedbackAnswerType.thumbsDown },
    { "question_id": 2, "questionType": FeedbackQuestionType.thumbsUpDown, "question_name": "How was the user experience?", "userAnswer": FeedbackAnswerType.thumbsUp },
    { "question_id": 3, "questionType": FeedbackQuestionType.textInput, "question_name": "Do you have any suggestions for how we can make OverUnderz better?", "userAnswer": "Really, a very good application. Cheers!" }
]

class FeedbackContainer extends Component {

    constructor(props) {
        super(props);
        // API DATE
        this.strDateToBeUsed = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date
            : this.props.screenProps.payload.data.notification_date;
        this.strDateTimeToBeUsed = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date_time
            : this.props.screenProps.payload.data.notification_date_time;

        // USER ANSWERS
        this.userFeedbackAnswers = {};
        this.userFeedbackAnswersOriginal = {};

        // VALIDATIONS
        this.shouldAllowToUpdateAnswers = true;
        this.refresIntervalID = undefined;
        this.isForToday = this.props.navigation.state.params
            ? this.props.navigation.state.params.isForToday || false
            : false;

        // WEB VIEW FONT SIZE
        this.fontSizeWebView = 45;//heightPercentageToDP("2.3%");
        this.customStyle =
            "<style>* {max-width: 100%;} body {color: #6B6B6B;font-size:" + this.fontSizeWebView + "px;font-family:Ubuntu-Regular}</style>";
    }

    state = {
        // UTILS
        isLoading: false,
        strOnScreenMessage: '',
        strFeedbackMessage: undefined,

        // FEEDBACK QUESTIONS
        arrayFeedbackQuestions: undefined,
    };

    // RENDER QUESTION ITEM
    renderFeedbackQuestion = (feedbackQuestionToRender) => {
        return <FeedbackQuestionItem
            feedbackQuestion={feedbackQuestionToRender.item}
            index={feedbackQuestionToRender.index}
            onFeedbackAnswerSubmitted={this.onFeedbackAnswerSubmitted}
            areAnswersEditable={this.shouldAllowToUpdateAnswers}
        />
    }

    // ON ANSWER CHANGE EVENT
    onFeedbackAnswerSubmitted = (userAnswer, feedbackQuestion) => {
        this.userFeedbackAnswers[feedbackQuestion.feedback_question_id] = escape(userAnswer)

        if (this.refresIntervalID) {
            clearInterval(this.refresIntervalID)
        }
        this.refresIntervalID = setTimeout(() => {
            this.callSaveFeedbackAnswersAPI()
        }, 2000);
    }

    navigateToPreviousScreen = () => {

        this.props.screenProps.payload.data.notification_date = undefined
        this.props.screenProps.payload.data.notification_date_time = undefined
        this.props.removeSelectedQuizDateFromReduxOnBackButtonFeedbackContainer()

        if (this.isForToday) {
            this.props.navigation.goBack()
        } else {
            this.props.navigation.pop()
        }
    }

    buttonSubmitPressed = () => {
        // if (Object.keys(this.userFeedbackAnswers || {}).length != this.state.arrayFeedbackQuestions.length) {
        //     showDialogue(Messages.answerFeedbackQuestionValidationMessage)
        //     return
        // }
        // showNotImplementedAlert()


        saveAnswersParams = {
            user_id: this.props.userDetails.user_id,
            feedback_question_date: this.strDateTimeToBeUsed,
            feedback_answers: JSON.stringify(this.userFeedbackAnswers)
        }

        apiPost(
            SAVE_FEEDBACK_ANSWERS_URL,
            saveAnswersParams,
            (dictSuccess, message) => {

                debugLog("dictSuccess.message ::: ", message)
            },
            (dictFailure, message) => {
                showDialogue(message || Messages.generalWebServiceError)
                this.setState({ isLoading: false })
            },
            {}
        );
    }

    // FEEDBACK MESSAGE HTML
    renderHeaderComponent = () => {
        return (
            <View style={{ flex: 1, backgroundColor: 'red' }}>
                <WebView
                    source={{ html: this.customStyle + this.state.strFeedbackMessage }}
                    startInLoadingState={true}
                    style={{
                        flex: 1,
                        alignSelf: "flex-start",
                        paddingBottom: Platform.OS == "ios" ? 0 : 15,
                        width: "100%"
                    }}
                    //hasIframe={true}
                    scrollEnabled={true}
                />
            </View>

        )
    }

    // SUBMIT BUTTON ( NOT VISIBLE CURRENTLY OWING TO AUTO SAVE MECHANISM BEING IMPLEMENTED )
    renderFooterComponent = () => {
        return (
            <EDHThemeButton
                style={{ marginVertical: 20 }}
                label={"Submit"}
                onPress={this.buttonSubmitPressed}
            />
        )
    }

    // LIFE CYCLE
    onDidFocusFeedbackContainer = () => {
        this.callFeedbackQuestionsAPI();
        BackHandler.addEventListener('hardwareBackPress', this.handleBackPress);
    }

    onDidBlurFeedbackContainer = () => {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackPress);
    }

    // BACK EVENT
    handleBackPress = () => {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackPress);
        this.navigateToPreviousScreen()
        return true;
    }

    // FETCH FEEDBACK QUESTIONS
    callFeedbackQuestionsAPI() {

        this.strDateToBeUsed = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date
            : this.props.screenProps.payload.data.notification_date;

        this.strDateTimeToBeUsed = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date_time
            : this.props.screenProps.payload.data.notification_date_time;

        this.props.screenProps.payload = { data: { notification_date: this.strDateToBeUsed, notification_date_time: this.strDateTimeToBeUsed } }

        netStatus(status => {
            if (status) {

                this.setState({ isLoading: true })
                let feedbackQuestionsParams = {
                    user_id: this.props.userDetails.user_id,
                    feedback_question_date: this.strDateTimeToBeUsed
                };


                apiPost(
                    GET_FEEDBACK_QUESTIONS_URL,
                    feedbackQuestionsParams,
                    (dictSuccess, message) => {

                        this.shouldAllowToUpdateAnswers = dictSuccess.editFeedbackAnsStatus || dictSuccess.editFeedbackAnsStatus == 1
                        if (dictSuccess.feedback_questions_list != undefined) {
                            strFeedbackMessageFetched = dictSuccess.FeedbackMSG ? dictSuccess.FeedbackMSG.message || "" : ''

                            this.setState({ arrayFeedbackQuestions: dictSuccess.feedback_questions_list, isLoading: false, strOnScreenMessage: message, strFeedbackMessage: strFeedbackMessageFetched })
                        }
                    },
                    (dictFailure, message) => {
                        this.setState({ arrayFeedbackQuestions: [], strFeedbackMessage: "", isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                    },
                    {}
                );
            } else {
                this.setState({ strOnScreenMessage: Messages.noInternet })
                // showNoInternetAlert()
            }
        })
    }

    // SAVE FEEDBACK ANSWERS
    callSaveFeedbackAnswersAPI() {
        if (Object.keys(this.userFeedbackAnswers || {}).length > 0) {
            netStatus(status => {
                if (status) {
                    saveAnswersParams = {
                        user_id: this.props.userDetails.user_id,
                        feedback_question_date: this.strDateTimeToBeUsed,
                        feedback_answers: JSON.stringify(this.userFeedbackAnswers)
                    }

                    apiPost(
                        SAVE_FEEDBACK_ANSWERS_URL,
                        saveAnswersParams,
                        (dictSuccess, message) => {

                            if (this.refs.toast) {
                                this.refs.toast.show(message || "Feedback saved successfully!", DURATION.LENGTH_SHORT);
                            }

                        },
                        (dictFailure, message) => {
                            showDialogue(message || Messages.generalWebServiceError)
                        },
                        {}
                    );
                }
            })
        }
    }

    render() {
        return (
            <BaseContainer
                title={strings("ScreenTitles.feedback") + (" ( " + this.strDateToBeUsed + " )")}
                left={Assets.back}
                onLeft={this.navigateToPreviousScreen}
                loading={this.state.isLoading}
            >
                <NavigationEvents onDidFocus={this.onDidFocusFeedbackContainer} onDidBlur={this.onDidBlurFeedbackContainer} />

                <Toast ref="toast" position="center" fadeInDuration={0} />

                {this.state.arrayFeedbackQuestions && this.state.arrayFeedbackQuestions.length > 0
                    ? <View style={{ flex: 1, padding: 20 }}>
                        {this.renderHeaderComponent}
                        <KeyboardAwareFlatList
                            style={{ flex: 1 }}
                            showsVerticalScrollIndicator={false}
                            showsHorizontalScrollIndicator={false}
                            data={this.state.arrayFeedbackQuestions}
                            renderItem={this.renderFeedbackQuestion}
                            keyExtractor={(item, index) => item + index}
                        // ListHeaderComponent={this.renderHeaderComponent}
                        // ListFooterComponent={this.renderFooterComponent}
                        />
                    </View>
                    : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />
                }

            </BaseContainer>
        );
    }
}

export default connect(
    state => {
        return {
            userDetails: state.userOperation,
            selectedQuizDate: state.quizReducer ? (state.quizReducer.question_date || undefined) : undefined
        }
    },
    dispatch => {
        return {
            removeSelectedQuizDateFromReduxOnBackButtonFeedbackContainer: () => [
                dispatch(removeSelectedQuizFromRedux())
            ]
        }
    }
)(FeedbackContainer);