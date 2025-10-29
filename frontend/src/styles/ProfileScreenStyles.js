import { StyleSheet } from "react-native";

const colors = {
  primary: "#6772E5",
  secondary: "#79B4E3",
  accent: "#5469D4",
  background: "#F7F9FC",
  cardBg: "#FFFFFF",
  text: "#333333",
  textSecondary: "#666666",
  textLight: "#999999",
  border: "#E5E9F2",
  borderLight: "#F0F2F7",
  success: "#4CAF50",
  danger: "#F44336",
  defaultBadge: "#5469D4"
};

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background
  },
  scrollContainer: {
    flexGrow: 1,
    paddingBottom: 30
  },
  profileContainer: {
    marginTop: 20,
    padding: 20
  },
  profileImageWrapper: {
    alignSelf: "center",
    marginVertical: 20,
    position: "relative"
  },
  addIcon: {
    position: "absolute",
    bottom: 0,
    right: 0,
    backgroundColor: colors.cardBg,
    borderRadius: 10,
    padding: 2
  },
  profileName: {
    textAlign: "center",
    fontSize: 22,
    fontWeight: "600",
    color: colors.text,
    marginTop: 10
  },
  profileEmail: {
    textAlign: "center",
    fontSize: 16,
    color: colors.textSecondary,
    marginBottom: 15
  },
  editProfileButton: {
    backgroundColor: colors.cardBg,
    borderWidth: 1,
    borderColor: colors.primary,
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderRadius: 8,
    alignSelf: "center",
    marginBottom: 20,
    flexDirection: "row",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 1
  },
  editProfileText: {
    color: colors.primary,
    fontSize: 14,
    fontWeight: "500"
  },
  addressesContainer: {
    backgroundColor: colors.cardBg,
    borderRadius: 12,
    padding: 20,
    marginBottom: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2
  },
  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 15,
    paddingBottom: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "600",
    color: colors.text
  },
  addAddressButton: {
    backgroundColor: colors.primary,
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 6
  },
  addAddressText: {
    color: colors.cardBg,
    fontSize: 12,
    fontWeight: "500",
    marginLeft: 5
  },
  addressCard: {
    backgroundColor: colors.background,
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    borderWidth: 1,
    borderColor: colors.border,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 1
  },
  defaultAddressCard: {
    borderColor: colors.primary,
    borderWidth: 2,
    backgroundColor: colors.background
  },
  defaultAddressBadge: {
    backgroundColor: colors.defaultBadge,
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
    alignSelf: "flex-start",
    marginBottom: 8
  },
  defaultIcon: {
    marginRight: 4
  },
  defaultAddressText: {
    color: colors.cardBg,
    fontSize: 12,
    fontWeight: "500"
  },
  addressHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 5
  },
  addressName: {
    fontSize: 16,
    fontWeight: "600",
    color: colors.text
  },
  editAddressButton: {
    backgroundColor: colors.secondary,
    width: 26,
    height: 26,
    borderRadius: 13,
    justifyContent: "center",
    alignItems: "center"
  },
  phoneNumber: {
    fontSize: 14,
    color: colors.textSecondary,
    marginBottom: 6,
    flexDirection: "row",
    alignItems: "center"
  },
  phoneIcon: {
    marginRight: 5
  },
  addressText: {
    fontSize: 14,
    color: colors.textSecondary,
    lineHeight: 20,
    marginBottom: 12
  },
  locationIcon: {
    marginRight: 5
  },
  addressActions: {
    flexDirection: "row",
    justifyContent: "flex-end",
    borderTopWidth: 1,
    borderTopColor: colors.borderLight,
    paddingTop: 10
  },
  deleteButton: {
    backgroundColor: colors.danger,
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 6,
    flexDirection: "row",
    alignItems: "center",
    marginRight: 10
  },
  defaultButton: {
    backgroundColor: colors.primary,
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 6,
    flexDirection: "row",
    alignItems: "center"
  },
  actionIcon: {
    marginRight: 5
  },
  deleteText: {
    color: colors.cardBg,
    fontSize: 12,
    fontWeight: "500"
  },
  defaultText: {
    color: colors.cardBg,
    fontSize: 12,
    fontWeight: "500"
  },
  noAddressContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 30
  },
  noAddressText: {
    color: colors.textLight,
    textAlign: "center",
    marginVertical: 10
  },
  addFirstAddressButton: {
    backgroundColor: colors.primary,
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderRadius: 8,
    marginTop: 10
  },
  addFirstAddressText: {
    color: colors.cardBg,
    fontSize: 14,
    fontWeight: "500"
  },
  logoutButton: {
    backgroundColor: colors.danger,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginTop: 10,
    flexDirection: "row",
    justifyContent: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
    elevation: 2
  },
  logoutIcon: {
    marginRight: 8
  },
  logoutText: {
    color: "white",
    fontSize: 16,
    fontWeight: "500"
  },
  loginContainer: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    padding: 20
  },
  loginIcon: {
    marginBottom: 20
  },
  loginMessage: {
    fontSize: 18,
    color: colors.text,
    marginBottom: 20,
    textAlign: "center"
  },
  loginButton: {
    backgroundColor: colors.primary,
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 8,
    width: "100%",
    alignItems: "center",
    marginBottom: 15,
    flexDirection: "row",
    justifyContent: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2
  },
  buttonIcon: {
    marginRight: 8
  },
  loginText: {
    color: "white",
    fontSize: 16,
    fontWeight: "500"
  },
  registerButton: {
    backgroundColor: "transparent",
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.primary,
    width: "100%",
    alignItems: "center",
    flexDirection: "row",
    justifyContent: "center"
  },
  registerText: {
    color: colors.primary,
    fontSize: 16,
    fontWeight: "500"
  },
  modalContainer: {
    flex: 1,
    justifyContent: "center",
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    padding: 20
  },
  modalContent: {
    backgroundColor: colors.cardBg,
    borderRadius: 12,
    padding: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.2,
    shadowRadius: 15,
    elevation: 5
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: "600",
    color: colors.text,
    marginBottom: 20,
    textAlign: "center"
  },
  input: {
    backgroundColor: colors.background,
    paddingHorizontal: 15,
    paddingVertical: 12,
    borderRadius: 8,
    marginBottom: 15,
    borderWidth: 1,
    borderColor: colors.border,
    fontSize: 16
  },
  modalButtons: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 10
  },
  cancelButton: {
    backgroundColor: colors.background,
    paddingVertical: 12,
    paddingHorizontal: 15,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.border,
    flex: 1,
    marginRight: 10,
    alignItems: "center"
  },
  cancelText: {
    color: colors.textSecondary,
    fontSize: 16,
    fontWeight: "500"
  },
  saveButton: {
    backgroundColor: colors.primary,
    paddingVertical: 12,
    paddingHorizontal: 15,
    borderRadius: 8,
    flex: 1,
    marginLeft: 10,
    alignItems: "center"
  },
  saveButton: {
    backgroundColor: colors.primary,
    paddingVertical: 12,
    paddingHorizontal: 15,
    borderRadius: 8,
    flex: 1,
    marginLeft: 10,
    alignItems: "center"
  },
  saveText: {
    color: colors.cardBg,
    fontSize: 16,
    fontWeight: "500"
  },
  errorText: {
    color: colors.danger,
    fontSize: 14,
    marginBottom: 10
  },
  formGroup: {
    marginBottom: 15
  },
  formLabel: {
    fontSize: 14,
    color: colors.text,
    marginBottom: 5,
    fontWeight: "500"
  },
  formInput: {
    backgroundColor: colors.background,
    paddingHorizontal: 15,
    paddingVertical: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.border,
    fontSize: 16
  },
  textArea: {
    height: 100,
    textAlignVertical: "top"
  },
  checkboxContainer: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 15
  },
  checkbox: {
    borderWidth: 1,
    borderColor: colors.border,
    width: 20,
    height: 20,
    justifyContent: "center",
    alignItems: "center",
    borderRadius: 4,
    marginRight: 10
  },
  checkboxChecked: {
    backgroundColor: colors.primary,
    borderColor: colors.primary
  },
  checkboxText: {
    fontSize: 14,
    color: colors.textSecondary
  },
  successMessage: {
    backgroundColor: colors.success,
    padding: 15,
    borderRadius: 8,
    marginBottom: 15
  },
  successText: {
    color: colors.cardBg,
    fontSize: 14,
    fontWeight: "500"
  },
  errorMessage: {
    backgroundColor: colors.danger,
    padding: 15,
    borderRadius: 8,
    marginBottom: 15
  },
  placeholderImage: {
    backgroundColor: colors.borderLight,
    width: 100,
    height: 100,
    borderRadius: 50,
    justifyContent: "center",
    alignItems: "center"
  },
  placeholderText: {
    color: colors.textLight,
    fontSize: 14
  },
  loadingOverlay: {
    position: "absolute",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: "rgba(255, 255, 255, 0.7)",
    justifyContent: "center",
    alignItems: "center",
    zIndex: 999
  },
  loadingText: {
    marginTop: 10,
    color: colors.text,
    fontSize: 16,
    fontWeight: "500"
  },
  showAllAddressesButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    borderRadius: 6,
    marginVertical: 10,
    borderWidth: 1,
    borderColor: colors.border,
    backgroundColor: colors.background
  },
  showAllAddressesText: {
    color: colors.primary,
    fontSize: 14,
    fontWeight: "500",
    marginRight: 5
  },
  dropdownIcon: {
    marginLeft: 5
  },
});
