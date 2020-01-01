export const TYPE_SAVE_SELECTED_QUIZ = "TYPE_SAVE_SELECTED_QUIZ";
export const TYPE_REMOVE_SELECTED_QUIZ = "TYPE_REMOVE_SELECTED_QUIZ";

export function saveSelectedQuizInRedux(data) {
    return {
        type: TYPE_SAVE_SELECTED_QUIZ,
        value: data
    };
}

export function removeSelectedQuizFromRedux(data) {
    return {
        type: TYPE_REMOVE_SELECTED_QUIZ,
        value: data
    };
}
