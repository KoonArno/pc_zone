import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5",
    justifyContent: "center",
    alignItems: "center",
    padding: 20
  },
  header: {
    alignItems: "center",
    marginBottom: 20
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
  card: {
    backgroundColor: "#fff",
    padding: 25,
    width: "100%",
    borderRadius: 20,
    alignItems: "center",
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
  inputContainer: {
    width: "100%",
    marginVertical: 5
  },
  input: {
    width: "100%",
    padding: 15,
    marginVertical: 5,
    borderWidth: 1,
    borderColor: "#e0e0e0",
    borderRadius: 12,
    backgroundColor: "#f8f8f8",
    fontSize: 16
  },
  inputError: {
    borderColor: "#ff6b6b",
    backgroundColor: "#ffefef"
  },
  errorText: {
    color: "#ff6b6b",
    fontSize: 12,
    marginLeft: 5,
    marginTop: 2
  },
  button: {
    backgroundColor: "#4e91f2",
    padding: 15,
    borderRadius: 12,
    marginTop: 20,
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
  footer: {
    marginTop: 20,
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
