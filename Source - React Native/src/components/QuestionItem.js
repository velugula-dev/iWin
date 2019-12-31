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
    Modal, TouchableWithoutFeedback

} from "react-native";

import Metrics from "../utils/Metrics";
import { strings, isRTL } from "../locales/i18n";
import EDCard from "../components/EDCard";
import { EDColors } from "../utils/EDColors";
import { debugLog, QuestionType } from "../utils/EDConstants";
import { EDFonts } from "../utils/EDFontConstants";
import EDRTLView from "../components/EDRTLView";
import EDButton from "../components/EDButton";
import { heightPercentageToDP } from "react-native-responsive-screen";
import { RadioGroup, RadioButton } from "react-native-flexi-radio-button";
import SelectMultiple from 'react-native-select-multiple'
import Assets from "../assets";
import { Messages } from "../utils/Messages";
import Moment from "moment";

export default class QuestionItem extends React.Component {


    state = {
        arrayUserAnswers: undefined,
        isCheckboxAnswersGiven: false,
    }


    passSelectedAnswersToContainer = (selectedAnswers, shouldScroll = false) => {
        if (this.props.onAnswerSelected != undefined) {
            this.props.onAnswerSelected(selectedAnswers, this.props.question, this.props.index, shouldScroll)
        }
    }

    onSelect = (index, value) => {
        this.passSelectedAnswersToContainer([value.question_answer_id])
    }

    onSelectionsChange = (selectedAnswers) => {
        // selectedAnswers is array of { label, value }
        questionToRender = this.props.question
        shouldDisableAnswers = questionToRender.userAnswers ? questionToRender.userAnswers.length > 0 : false
        if (shouldDisableAnswers) {
            return
        }

        arrayFinalAnswers = selectedAnswers.map((answerGiven) => {
            return answerGiven.value
        })


        this.passSelectedAnswersToContainer(arrayFinalAnswers)
        this.setState({ arrayUserAnswers: selectedAnswers })
    }


    buttonYesPressed = () => {
        questionAnswered = this.props.question
        answerGiven = questionAnswered.options.filter(value => {
            return value.answer.toLowerCase() == "yes"
        })
        if (answerGiven.length > 0) {
            this.passSelectedAnswersToContainer([answerGiven[0].question_answer_id], true)

        }

    }

    buttonNoPressed = () => {
        questionAnswered = this.props.question
        answerGiven = questionAnswered.options.filter(value => {
            return value.answer.toLowerCase() == "no"
        })
        if (answerGiven.length > 0) {
            this.passSelectedAnswersToContainer([answerGiven[0].question_answer_id], true)
        }
    }



    componentDidMount() {
        questionToRender = this.props.question
        shouldDisableAnswers = questionToRender.userAnswers ? questionToRender.userAnswers.length > 0 : false
        selectedAnswerIndex = questionToRender.userAnswers ? 0 : undefined

        if (questionToRender.question_id == 78) {
        }

        if (shouldDisableAnswers) {
            if (questionToRender.question_type == QuestionType.multi) {
                arrayAnswerIds = questionToRender.userAnswers.split(", ")
                if (questionToRender.question_id == 78) {
                    // debugLog("arrayAnswerIds ::", arrayAnswerIds)
                }
                arrayMultiAnswers = questionToRender.options.filter(option => {
                    return arrayAnswerIds.includes(option.question_answer_id) || arrayAnswerIds.includes(String(option.question_answer_id))
                })
                if (questionToRender.question_id == 78) {
                    // debugLog("arrayMultiAnswers ::", arrayMultiAnswers)
                }
                arrayMultiAnswersFinal = arrayMultiAnswers.map(questionToIterate => {
                    return { label: questionToIterate.answer, value: questionToIterate.question_answer_id }
                })
                if (questionToRender.question_id == 78) {
                    // debugLog("arrayMultiAnswersFinal ::", arrayMultiAnswersFinal)
                }
                if (questionToRender.question_id == 78) {
                    // debugLog("BEFORE SET STATE")
                }
                this.setState({ arrayUserAnswers: arrayMultiAnswersFinal, isCheckboxAnswersGiven: true })
                if (questionToRender.question_id == 78) {
                    // debugLog("AFTER SET STATE")
                }
            } else if (questionToRender.question_type == QuestionType.yesNo) {
                arrayAnswerIds = questionToRender.userAnswers.split(", ")
                // debugLog("arrayAnswerIds ::: ", arrayAnswerIds)
                arrayMultiAnswers = questionToRender.options.filter(option => {
                    return arrayAnswerIds.includes(option.question_answer_id) || arrayAnswerIds.includes(String(option.question_answer_id))
                })
                // debugLog("arrayMultiAnswers ::: ", arrayMultiAnswers)
                arrayMultiAnswersFinal = arrayMultiAnswers.map(questionToIterate => {
                    // debugLog("questionToIterate ::: ", questionToIterate)
                    return { label: questionToIterate.answer, value: questionToIterate.question_answer_id }
                })
                this.setState({ arrayUserAnswers: arrayMultiAnswersFinal })
            }
        } else {
        }
    }

    renderOptions = (questionToRender) => {
        shouldDisableAnswers = (questionToRender.userAnswers) ? questionToRender.userAnswers.length > 0 : false

        if (!shouldDisableAnswers && questionToRender.question_ans_rest_status != undefined) {
            shouldDisableAnswers = questionToRender.question_ans_rest_status == 1
        }

        if (this.props.isForOlderQuiz) {
            shouldDisableAnswers = true
        }


        debugLog("shouldDisableAnswers HERE :::" + shouldDisableAnswers + " ==== ID ===== " + questionToRender.question_id)
        selectedAnswerIndex = questionToRender.userAnswers != undefined ? 0 : undefined
        // debugLog("selectedAnswerIndex HERE 1111 :::", selectedAnswerIndex)
        if (shouldDisableAnswers) {
            // debugLog("HERE 77 :::")
            if (questionToRender.question_type != QuestionType.multi) {
                selectedAnswerIndex = questionToRender.options.findIndex(answer => (answer.question_answer_id == questionToRender.userAnswers || answer.question_answer_id == parseInt(questionToRender.userAnswers)))
                // debugLog("HERE 88 :::", selectedAnswerIndex)
            } else {
            }
        }

        switch (questionToRender.question_type) {
            case QuestionType.single:
                return (
                    <RadioGroup
                        color={EDColors.text}
                        selectedIndex={selectedAnswerIndex}
                        onSelect={(index, value) => this.onSelect(index, value)}
                        style={{
                        }}
                    >
                        {questionToRender.options.map((value, index, array) => {
                            return <RadioButton
                                disabled={shouldDisableAnswers}
                                style={{ flexDirection: isRTL ? "row-reverse" : "row" }}
                                value={questionToRender.options[index]}
                                color={EDColors.primary}
                            >
                                <Text style={{ fontSize: heightPercentageToDP("2%"), color: EDColors.text, fontFamily: EDFonts.regular }}>{value.answer}</Text>
                            </RadioButton>
                        })}
                    </RadioGroup>
                )

            case QuestionType.multi:
                if (questionToRender.question_id == 78) {
                    // debugLog("this.state.arrayUserAnswers for question id 46 ::", this.state.arrayUserAnswers)
                }
                return (
                    <View>
                        <SelectMultiple
                            labelStyle={{ fontSize: heightPercentageToDP("2%"), color: EDColors.text, fontFamily: EDFonts.regular }}
                            selectedLabelStyle={{ fontSize: heightPercentageToDP("2%"), color: EDColors.primary, fontFamily: EDFonts.regular }}
                            rowStyle={{ borderBottomWidth: 0, padding: 0, paddingVertical: 15, paddingRight: 15 }}
                            items={questionToRender.options.map((questionToIterate) => {
                                // debugLog("value :: MULTI", value)
                                return { label: questionToIterate.answer, value: questionToIterate.question_answer_id }
                            })}
                            selectedItems={this.state.arrayUserAnswers}
                            onSelectionsChange={this.onSelectionsChange} />

                    </View>
                )

                break;

            case QuestionType.yesNo:
                // selectedAnswerIndex = selectedAnswerIndex == -1 ? undefined : selectedAnswerIndex
                correctAnswerString = (selectedAnswerIndex != undefined && selectedAnswerIndex != -1) ? questionToRender.options[selectedAnswerIndex].answer : ""
                // debugLog("correctAnswerString " + correctAnswerString)
                // debugLog("shouldDisableAnswers ", shouldDisableAnswers)
                return (
                    <View>
                        <EDRTLView pointerEvents={shouldDisableAnswers ? "none" : "auto"} style={{ marginTop: 40, opacity: shouldDisableAnswers ? 0.5 : 1, alignItems: 'center', justifyContent: 'center' }}>

                            <EDButton
                                label={strings("General.yes")}
                                onPress={this.buttonYesPressed}
                                containerStyle={{ width: Metrics.screenWidth * 0.4 }}
                                buttonStyle={{ marginHorizontal: 10, backgroundColor: EDColors.androidStatusBarColor, color: EDColors.white }}
                                textStyle={{ color: EDColors.white, marginVertical: 15, marginHorizontal: 40, fontSize: heightPercentageToDP("2.3%") }}
                            />

                            <EDButton
                                label={strings("General.no")}
                                onPress={this.buttonNoPressed}
                                containerStyle={{ width: Metrics.screenWidth * 0.4 }}
                                buttonStyle={{ marginHorizontal: 10, backgroundColor: EDColors.googleColor, color: EDColors.white }}
                                textStyle={{ color: EDColors.white, marginVertical: 15, marginHorizontal: 40, fontSize: heightPercentageToDP("2.3%") }}
                            />
                        </EDRTLView>

                        {shouldDisableAnswers
                            ? <EDRTLView style={{ marginTop: 40, alignItems: 'center', justifyContent: 'center' }}>
                                <Text style={{ textAlignVertical: 'center', flex: questionToRender.question_type == QuestionType.yesNo ? 1 : 0, textAlign: 'right', marginRight: 5, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, marginVertical: 10, fontFamily: EDFonts.regular }}>Your answer: </Text>
                                <Text style={{ textAlignVertical: 'center', marginVertical: 10, flex: 1, fontSize: heightPercentageToDP("2.3%"), color: EDColors.primary, fontFamily: EDFonts.medium }}>{(selectedAnswerIndex != undefined && selectedAnswerIndex != -1) ? questionToRender.options[selectedAnswerIndex].answer : "N/A"}</Text>
                            </EDRTLView>
                            : null}

                    </View>

                )

            default:
                break;
        }
    }

    didPressQuestionInfoButton = () => {
        if (this.props.questionInfoButtonHandler != undefined) {
            this.props.questionInfoButtonHandler(this.props.question)
        }
    }

    render() {
        index = this.props.index
        totalQuestions = this.props.totalNumberOfQuestions || 40
        questionToRender = this.props.question
        var strUTC = (questionToRender.answer_restrict_time)
        // debugLog("===== strUTC ===== " + strUTC)

        var stillUtc = Moment.utc(strUTC).toDate();
        // var strLocalDateTime = Moment(stillUtc).local().format('YYYY-MM-DD HH:mm');
        var strLocalDateTime = Moment(stillUtc).local().format('MMM DD, YYYY hh:mm A');
        
        var strExpiryToDisplay = (questionToRender.question_ans_rest_status == 1 ? "  (Expired at: " : "  (Expires at: ") + strLocalDateTime + ")"


        correctAnswers = questionToRender.options.filter(answerToCheck => {
            return answerToCheck.is_correct_answer == true || answerToCheck.is_correct_answer == 1
        }).map(correctAnswers => {
            return correctAnswers.answer || ""
        }).join(", ")


        if (correctAnswers == undefined || correctAnswers.length == 0) {
            // correctAnswers = Messages.notAvailable
        }
        return (
            <View style={{ flex: 1, margin: 20, width: Metrics.screenWidth - 40 }}>

                {/* QUESTION TEXT */}
                <ScrollView showsVerticalScrollIndicator={false} style={{}}>
                    <View style={{ flexDirection: 'row', justifyContent: 'space-between' }}>
                        {questionToRender.question_detail
                            ? <TouchableOpacity style={{}} onPress={this.didPressQuestionInfoButton}>
                                <Image source={Assets.infoiconblue} />
                            </TouchableOpacity>
                            : null}
                        <Text style={{ flexDirection: "row", marginLeft: questionToRender.question_detail ? 10 : 0, flex: 1, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, fontFamily: EDFonts.medium }}>{questionToRender.question_name}
                            <Text style={{ marginLeft: 5, fontSize: heightPercentageToDP("1.9%"), color: EDColors.textDisabled, fontFamily: EDFonts.medium, marginTop: 5 }}>{strExpiryToDisplay}</Text>
                        </Text>
                        <Text style={{ fontSize: heightPercentageToDP("2.3%"), color: EDColors.textDisabled, fontFamily: EDFonts.medium, marginLeft: 10 }}>{(index + 1)}/{totalQuestions}</Text>
                    </View>

                    {/* COMMENTED ON OCT 04 - 2019 */}
                    {/* <Text style={{ fontSize: heightPercentageToDP("2.3%"), color: EDColors.textDisabled, fontFamily: EDFonts.medium, marginTop: 5 }}>Expires at: {strLocalDateTime}</Text> */}

                    {/* OPTIONS */}
                    <View pointerEvents={(this.props.isForOlderQuiz || questionToRender.question_ans_rest_status == 1) ? "none" : "auto"}
                    // COMMENTED ON SEP 27 - HN
                    // opacity={(this.props.isForOlderQuiz || questionToRender.question_ans_rest_status == 1) ? 0.5 : 1.0}
                    >
                        {this.renderOptions(questionToRender)}
                        {/* CORRECT ANSWER(S) */}
                        {correctAnswers != undefined && correctAnswers.length > 0 && this.props.shouldShowCorrectAnswers
                            ? <EDRTLView style={{ justifyContent: 'center' }}>

                                <Text style={{ textAlignVertical: 'center', flex: questionToRender.question_type == QuestionType.yesNo ? 1 : 0, textAlign: 'right', marginRight: 5, fontSize: heightPercentageToDP("2.3%"), color: EDColors.text, marginVertical: 10, fontFamily: EDFonts.regular }}>Correct answer: </Text>
                                <Text style={{ textAlignVertical: 'center', marginVertical: 10, flex: 1, fontSize: heightPercentageToDP("2.3%"), color: EDColors.primary, fontFamily: EDFonts.medium }}>{correctAnswers.replace(/(\r\n|\n|\r)/gm, "")}</Text>

                            </EDRTLView>
                            : null}
                    </View>
                </ScrollView>


            </View>
        )
    }
}
