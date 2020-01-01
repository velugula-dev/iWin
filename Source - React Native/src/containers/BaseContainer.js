import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import NavBar from '../components/NavBar';
import { Container } from 'native-base';
import ProgressLoader from '../components/ProgressLoader';
import { netStatusEvent } from "../utils/ServiceManager";

export default class BaseContainer extends React.Component {

    componentDidMount() {

        // CHECK FOR INTERNET
        netStatusEvent(status => {
            if (status) {
                console.log("connected from base ", status);
                // alert("internet found from base container");
            } else {
                console.log("not connected from base", status);
                // alert("Sorry no internet from base container");
            }
            if (this.props.networkStatus != undefined) {
                this.props.networkStatus(status);
            }
        });
    }

    render() {
        return (<View style={{ flex: 1 }}>
            {/* HEADER VIEW */}
            <NavBar
                title={this.props.title}
                left={this.props.left}
                onLeft={this.props.onLeft}
                right={this.props.right}
                onRight={this.props.onRight}
                left={this.props.left}
                isLeftString={this.props.isLeftString}
            />

            {/* CHILDREN */}
            <View style={styles.container}>
                {this.props.children}
            </View>

            {/* LOADING VIEW */}
            {this.props.loading ? <ProgressLoader /> : null}

        </View>)
    }
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    children: {

    }
});