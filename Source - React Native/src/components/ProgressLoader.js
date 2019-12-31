import React from 'react'
import { View, StyleSheet, Dimensions } from 'react-native'
import { Spinner } from 'native-base';
import { EDColors } from '../utils/EDColors';
import Metrics from '../utils/Metrics';

export default class ProgressLoader extends React.Component {
    render() {
        return (
            <View style={STYLES.container}>
                <View style={STYLES.containerOpac}></View>
                <Spinner style={STYLES.spinner} color={EDColors.primary} />
            </View>)
    }
}

const STYLES = StyleSheet.create({
    container: {
        position: 'absolute',
        width: Metrics.screenWidth,
        height: Metrics.screenHeight,
        zIndex: 997
    },
    containerOpac: {
        position: 'absolute',
        width: Metrics.screenWidth,
        height: Metrics.screenHeight,
        backgroundColor: 'rgba(0,0,0,0.25)',
        zIndex: 998
    },
    spinner: {
        flex: 1,
        alignSelf: 'center',
        zIndex: 1000
    }
})