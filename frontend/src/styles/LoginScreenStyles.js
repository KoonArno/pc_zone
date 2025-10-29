import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
    backgroundColor: "#f5f5f5"
  },
  header: {
    alignItems: "center",
    marginBottom: 30
  },
  appName: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#4e91f2",
    letterSpacing: 1
  },
  welcomeText: {
    fontSize: 16,
    color: "#666",
    marginTop: 5
  },
  formContainer: {
    width: "100%",
    backgroundColor: "#fff",
    borderRadius: 20,
    padding: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    marginBottom: 20,
    color: "#333",
    textAlign: "center"
  },
  input: {
    width: "100%",
    padding: 15,
    marginVertical: 10,
    borderWidth: 1,
    borderColor: "#e0e0e0",
    borderRadius: 12,
    backgroundColor: "#f8f8f8",
    fontSize: 16
  },
  forgotPassword: {
    alignSelf: "flex-end",
    marginTop: 5,
    marginBottom: 15
  },
  forgotPasswordText: {
    color: "#4e91f2",
    fontSize: 14
  },
  button: {
    backgroundColor: "#4e91f2",
    padding: 15,
    borderRadius: 12,
    marginTop: 10,
    width: "100%",
    alignItems: "center"
  },
  buttonDisabled: {
    backgroundColor: "#a0c2f2"
  },
  buttonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  },
  divider: {
    flexDirection: "row",
    alignItems: "center",
    marginVertical: 20
  },
  dividerLine: {
    flex: 1,
    height: 1,
    backgroundColor: "#e0e0e0"
  },
  dividerText: {
    marginHorizontal: 10,
    color: "#999",
    fontSize: 14
  },
  footer: {
    marginTop: 10,
    alignItems: "center"
  },
  linkText: {
    color: "#666",
    fontSize: 16
  },
  linkHighlight: {
    color: "#4e91f2",
    fontWeight: "bold"
  }
});

export default styles;