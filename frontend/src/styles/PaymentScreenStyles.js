import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5"
  },
  header: {
    backgroundColor: "#fff",
    padding: 15,
    alignItems: "center",
    borderBottomWidth: 1,
    borderBottomColor: "#eee"
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333"
  },
  section: {
    backgroundColor: "#fff",
    padding: 20,
    marginTop: 10
  },
  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 15
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333"
  },
  changeButton: {
    backgroundColor: "#f0f8ff",
    paddingVertical: 5,
    paddingHorizontal: 10,
    borderRadius: 15,
    borderWidth: 1,
    borderColor: "#79B4E3"
  },
  changeButtonText: {
    color: "#79B4E3",
    fontWeight: "500",
    fontSize: 14
  },
  selectedAddressContainer: {
    backgroundColor: "#f9f9f9",
    padding: 15,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ddd"
  },
  addressDetailsContainer: {
    paddingVertical: 5
  },
  addressName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 5
  },
  addressText: {
    fontSize: 14,
    color: "#666",
    marginBottom: 5
  },
  defaultBadge: {
    backgroundColor: "#79B4E3",
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
    alignSelf: "flex-start",
    marginTop: 5
  },
  defaultText: {
    color: "white",
    fontSize: 12,
    fontWeight: "500"
  },
  noAddressText: {
    fontSize: 14,
    color: "#666",
    fontStyle: "italic",
    textAlign: "center",
    padding: 20
  },
  paymentImage: {
    width: "100%",
    height: 200,
    borderRadius: 10,
    marginBottom: 10
  },
  uploadButton: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#79B4E3",
    padding: 12,
    borderRadius: 25
  },
  uploadText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold",
    marginLeft: 10
  },
  changeImageButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#79B4E3",
    padding: 8,
    borderRadius: 20,
    alignSelf: "center"
  },
  changeImageText: {
    color: "#fff",
    marginLeft: 5,
    fontWeight: "500"
  },
  orderSummaryRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: "#eee"
  },
  orderSummaryLabel: {
    fontSize: 14,
    color: "#666"
  },
  orderSummaryValue: {
    fontSize: 14,
    color: "#333",
    fontWeight: "500"
  },
  orderTotal: {
    marginTop: 10,
    paddingTop: 10,
    borderTopWidth: 1,
    borderTopColor: "#ddd",
    borderBottomWidth: 0
  },
  orderTotalLabel: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333"
  },
  orderTotalValue: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333"
  },
  bottomContainer: {
    padding: 20,
    backgroundColor: "#fff",
    borderTopWidth: 1,
    borderTopColor: "#eee"
  },
  payButton: {
    backgroundColor: "#79B4E3",
    padding: 15,
    borderRadius: 25,
    alignItems: "center"
  },
  payText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  },
  // Modal styles
  modalContainer: {
    flex: 1,
    justifyContent: "flex-end",
    backgroundColor: "rgba(0,0,0,0.5)"
  },
  modalContent: {
    backgroundColor: "#fff",
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    paddingBottom: 20,
    maxHeight: "70%"
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: "#eee"
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333"
  },
  modalAddressItem: {
    backgroundColor: "#fff",
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: "#eee"
  },
  selectedAddress: {
    backgroundColor: "#f0f8ff"
  },
  productItem: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 15,
    padding: 10,
    backgroundColor: "#f9f9f9",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ddd"
  },
  productImage: {
    width: 80,
    height: 80,
    borderRadius: 8,
    marginRight: 10
  },
  productDetails: {
    flex: 1
  },
  productName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 5
  },
  productPrice: {
    fontSize: 14,
    color: "#666",
    marginBottom: 5
  },
  productQuantity: {
    fontSize: 14,
    color: "#666",
    marginBottom: 5
  },
  productSubtotal: {
    fontSize: 14,
    color: "#333",
    fontWeight: "500"
  },
  noDetailsText: {
    fontSize: 14,
    color: "#666",
    fontStyle: "italic",
    textAlign: "center",
    padding: 20
  }
});

export default styles;