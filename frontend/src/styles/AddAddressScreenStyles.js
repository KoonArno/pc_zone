import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f7f9fc"
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 16,
    paddingVertical: 18,
    backgroundColor: "#ffffff",
    borderBottomWidth: 1,
    borderBottomColor: "#e0e6ed",
    elevation: 2,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    marginTop: 50,
  },
  backButton: {
    padding: 8,
    borderRadius: 8,
    backgroundColor: "#f0f7ff"
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#2d3748"
  },
  placeholder: {
    width: 40
  },
  formContainer: {
    padding: 20,
    backgroundColor: "#ffffff",
    margin: 16,
    borderRadius: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 5
  },
  label: {
    fontSize: 15,
    fontWeight: "600",
    color: "#4a5568",
    marginBottom: 8,
    marginTop: 16
  },
  input: {
    borderWidth: 1,
    borderColor: "#e2e8f0",
    padding: 14,
    borderRadius: 8,
    fontSize: 16,
    backgroundColor: "#f9fafc"
  },
  defaultContainer: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    marginTop: 24,
    marginBottom: 16,
    padding: 12,
    backgroundColor: "#f0f9ff",
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: "#3182ce"
  },
  defaultText: {
    fontSize: 16,
    color: "#2c5282",
    fontWeight: "500"
  },
  checkBox: {
    width: 26,
    height: 26,
    borderWidth: 2,
    borderColor: "#3182ce",
    borderRadius: 6,
    alignItems: "center",
    justifyContent: "center"
  },
  checked: {
    backgroundColor: "#3182ce"
  },
  submitButton: {
    backgroundColor: "#3182ce",
    padding: 16,
    borderRadius: 10,
    marginTop: 24,
    alignItems: "center",
    shadowColor: "#3182ce",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 6,
    elevation: 3
  },
  submitText: {
    color: "#ffffff",
    fontSize: 16,
    fontWeight: "700",
    letterSpacing: 0.5
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#2d3748",
    marginBottom: 16,
    marginTop: 8,
    paddingBottom: 8,
    borderBottomWidth: 1,
    borderBottomColor: "#e2e8f0"
  },
  inputFocused: {
    borderColor: "#3182ce",
    borderWidth: 2,
    shadowColor: "#3182ce",
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.1,
    shadowRadius: 3
  },
  inputIcon: {
    position: "absolute",
    right: 12,
    top: 14,
    color: "#a0aec0"
  },
  inputContainer: {
    position: "relative",
    marginBottom: 10
  },
  requiredLabel: {
    color: "#e53e3e",
    fontWeight: "400",
    marginLeft: 4
  }
});

export default styles;