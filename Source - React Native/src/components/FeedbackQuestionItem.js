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
    Modal, TouchableWithoutFeedback,
    TextInput

} from "react-native";

import Metrics from "../utils/Metrics";
import { strings, isRTL } from "../locales/i18n";
import EDCard from "../components/EDCard";
import { EDColors } from "../utils/EDColors";
import { debugLog, QuestionType, FeedbackAnswerType, FeedbackQuestionType } from "../utils/EDConstants";
import { EDFonts } from "../utils/EDFontConstants";
import EDRTLView from "../components/EDRTLView";
import EDButton from "../components/EDButton";
import { heightPercentageToDP } from "react-native-responsive-screen";
import { RadioGroup, RadioButton } from "react-native-flexi-radio-button";
import SelectMultiple from 'react-native-select-multiple'
import Assets from "../assets";
import { Messages } from "../utils/Messages";


export default class FeedbackQuestionItem extends React.Component {

    state = {
        isThumbsUpAnswered: undefined,
        strSuggestionText: undefined,
        suggestionFieldHeight: heightPercentageToDP("15.0%")
    }

    buttonThumbsUpPressed = () => {
        this.setState({ isThumbsUpAnswered: true })
        this.passOnUserInputForFeedbackToContainer(FeedbackAnswerType.thumbsUp)
    }

    buttonThumbsDownPressed = () => {
        this.setState({ isThumbsUpAnswered: false })
        this.passOnUserInputForFeedbackToContainer(FeedbackAnswerType.thumbsDown)
    }

    onChangeSuggestionText = (suggestionText) => {
        this.setState({ strSuggestionText: suggestionText })
        // this.passOnUserInputForFeedbackToContainer(suggestionText)
    }

    onEndEditingFeedbackTextInput = (suggestionTextEndEditingEvent) => {
        suggestionText = suggestionTextEndEditingEvent.nativeEvent.text || ""
        this.passOnUserInputForFeedbackToContainer(suggestionText)
    }

    passOnUserInputForFeedbackToContainer(userInput) {
        if (this.props.onFeedbackAnswerSubmitted != undefined) {
            this.props.onFeedbackAnswerSubmitted(userInput, this.props.feedbackQuestion)
        }
    }

    onContentSizeChange = (contentSizeChangeEvent) => {
        this.setState({
            suggestionFieldHeight: Math.max(contentSizeChangeEvent.nativeEvent.contentSize.height, heightPercentageToDP("15.0%"))
        });
    }

    componentDidMount() {
        if (this.props != undefined, this.props.feedbackQuestion != undefined) {
            userFeedbackQuestion = this.props.feedbackQuestion
            submittedAnswer = unescape(userFeedbackQuestion.feedback_user_answer || "")
            if (userFeedbackQuestion.feedback_question_type == FeedbackQuestionType.thumbsUpDown && submittedAnswer.length > 0) {
                this.setState({ isThumbsUpAnswered: submittedAnswer.toLowerCase() == FeedbackAnswerType.thumbsUp.toLowerCase() })
            } else if (userFeedbackQuestion.feedback_question_type == FeedbackQuestionType.textInput) {
                this.setState({ strSuggestionText: submittedAnswer })
            }
        }
    }


    renderThumbsUI = () => {
        questionToRender = this.props.feedbackQuestion
        questionIndex = this.props.index
        shouldDisableFeedbackAnswers = this.props.isForOldQuestion
        return (
            <View opacity={this.props.areAnswersEditable ? 1.0 : 0.5} pointerEvents={this.props.areAnswersEditable ? "auto" : "none"} style={{ flex: 1, justifyContent: 'space-between' }}>
                <EDRTLView style={{ flex: 1 }}>
                    <Text style={{ marginLeft: questionToRender.question_detail ? 10 : 0, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, fontFamily: EDFonts.regular }}>{String(questionIndex + 1) + ".  "}</Text >
                    <Text style={{ flex: 1, marginLeft: 0, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, fontFamily: EDFonts.regular }}>{questionToRender.feedback_question}</Text >
                </EDRTLView>
                <EDRTLView style={{ marginVertical: 20, alignItems: 'center', justifyContent: 'center' }}>
                    <TouchableOpacity onPress={shouldDisableFeedbackAnswers ? undefined : this.buttonThumbsUpPressed} style={{ marginRight: 20 }}>
                        <Image style={{}} source={(this.state.isThumbsUpAnswered != undefined && this.state.isThumbsUpAnswered) ? Assets.thumbupselected : Assets.thumbupdeselected} />
                    </TouchableOpacity>
                    <TouchableOpacity onPress={shouldDisableFeedbackAnswers ? undefined : this.buttonThumbsDownPressed} style={{ marginLeft: 20 }}>
                        <Image style={{}} source={(this.state.isThumbsUpAnswered != undefined && !this.state.isThumbsUpAnswered) ? Assets.thumbdownselected : Assets.thumbdowndeselected} />
                    </TouchableOpacity>

                </EDRTLView>
            </View >
        )
    }

    renderTextInputUI = () => {
        questionToRender = this.props.feedbackQuestion
        questionIndex = this.props.index
        shouldDisableFeedbackAnswers = this.props.isForOldQuestion
        return (
            <View opacity={this.props.areAnswersEditable ? 1.0 : 0.5} pointerEvents={this.props.areAnswersEditable ? "auto" : "none"} style={{ flex: 1, justifyContent: 'space-between' }}>
                <EDRTLView style={{ flex: 1 }}>
                    <Text style={{ marginLeft: questionToRender.question_detail ? 10 : 0, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, fontFamily: EDFonts.regular }}>{String(questionIndex + 1) + ".  "}</Text >
                    <Text style={{ flex: 1, marginLeft: 0, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, fontFamily: EDFonts.regular }}>{questionToRender.feedback_question}</Text >
                </EDRTLView>
                <TextInput
                    maxLength={500}
                    style={{
                        margin: 20,
                        height: this.state.suggestionFieldHeight,
                        flex: 1,
                        fontFamily: EDFonts.regular,
                        fontSize: heightPercentageToDP("2.3%"),
                        color: EDColors.text,
                        textAlignVertical: 'center',
                        borderColor: EDColors.text,
                        borderWidth: 1.0,
                        borderRadius: 8,
                        paddingVertical: 10,
                        paddingHorizontal: 10
                    }}
                    autoCapitalize={"sentences"}
                    placeholder={"Enter your suggestions..."}
                    onChangeText={this.onChangeSuggestionText}
                    value={this.state.strSuggestionText}
                    multiline={true}
                    onContentSizeChange={this.onContentSizeChange}
                    // onSubmitEditing={this.onEndEditingFeedbackTextInput}
                    onEndEditing={this.onEndEditingFeedbackTextInput}
                />
            </View>
        )
    }

    render() {
        questionToRender = this.props.feedbackQuestion
        questionIndex = this.props.index
        return (
            questionToRender.feedback_question_type == FeedbackQuestionType.thumbsUpDown
                ? this.renderThumbsUI()
                : this.renderTextInputUI()

        )
    }

}