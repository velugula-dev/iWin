
import { TYPE_SAVE_SELECTED_QUIZ, TYPE_REMOVE_SELECTED_QUIZ } from "../actions/QuizAction";

const initialStateQuiz = {
}



export function quizOperations(state = initialStateQuiz, action) {
    switch (action.type) {
        case TYPE_SAVE_SELECTED_QUIZ: {
            return Object.assign({}, state, action.value);
        }
        case TYPE_REMOVE_SELECTED_QUIZ: {
            return Object.assign({}, state, {
                question_date: undefined,
                totalquestions: undefined
            })
        }

        default:
            return state
    }
}
