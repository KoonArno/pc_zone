import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F8F6F4",
    padding: 10,
    paddingTop: 40
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20
  },
  header: {
    fontSize: 22,
    fontWeight: "bold",
    marginBottom: 10,
    color: "#333",
    textAlign: "center"
  },
  selectAllContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    marginBottom: 10
  },
  cartSummary: {
    fontSize: 14,
    color: "#666"
  },
  cartItem: {
    flexDirection: "row",
    backgroundColor: "#fff",
    padding: 10,
    marginVertical: 5,
    borderRadius: 10,
    alignItems: "center"
  },
  image: {
    width: 60,
    height: 60,
    borderRadius: 10,
    backgroundColor: "#f0f0f0"
  },
  itemDetails: {
    flex: 1,
    marginLeft: 10
  },
  itemName: {
    fontSize: 16,
    fontWeight: "bold"
  },
  itemPrice: {
    fontSize: 14,
    color: "#007BFF"
  },
  footer: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 15,
    borderTopWidth: 1,
    borderTopColor: "#eee",
    paddingHorizontal: 10,
    backgroundColor: "#fff"
  },
  priceContainer: {
    flex: 1
  },
  selectedLabel: {
    fontSize: 14,
    color: "#666"
  },
  selectedTotal: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#007BFF",
    marginBottom: 5
  },
  payButton: {
    backgroundColor: "#007BFF",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 8
  },
  disabledButton: {
    backgroundColor: "#cccccc"
  },
  payText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  },
  emptyCartText: {
    fontSize: 18,
    marginBottom: 20,
    color: "#666"
  },
  shopButton: {
    backgroundColor: "#007BFF",
    paddingVertical: 12,
    paddingHorizontal: 25,
    borderRadius: 8
  },
  shopButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  },
  quantityControl: {
    flexDirection: "row",
    alignItems: "center",
    marginTop: 10
  },
  quantityButton: {
    backgroundColor: "#f0f0f0",
    width: 30,
    height: 30,
    borderRadius: 15,
    justifyContent: "center",
    alignItems: "center"
  },
  quantityButtonText: {
    fontSize: 16,
    fontWeight: "bold"
  },
  quantityText: {
    fontSize: 16,
    marginHorizontal: 10
  },
  removeButton: {
    marginLeft: "auto",
    paddingHorizontal: 10,
    paddingVertical: 5,
    backgroundColor: "#ff4d4d",
    borderRadius: 5
  },
  removeButtonText: {
    color: "#fff",
    fontSize: 14
  },
  loginButton: {
    backgroundColor: "#007BFF",
    paddingVertical: 12,
    paddingHorizontal: 25,
    borderRadius: 8
  },
  loginButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  },
  checkboxContainer: {
    flexDirection: "row",
    alignItems: "center",
    marginRight: 10
  },
  checkbox: {
    width: 20,
    height: 20,
    borderWidth: 1,
    borderColor: "#007BFF",
    borderRadius: 4,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#fff"
  },
  checkboxChecked: {
    backgroundColor: "#007BFF"
  },
  checkboxIndicator: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "bold"
  },
  checkboxTitle: {
    marginLeft: 8,
    fontSize: 14
  }
});

export default styles;
