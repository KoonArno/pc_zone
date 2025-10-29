import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f8f9fa",
    padding: 15,
    marginTop: 40
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20
  },
  header: {
    fontSize: 20,
    fontWeight: "bold",
    marginBottom: 15,
    color: "#333"
  },
  listContainer: {
    paddingBottom: 20
  },
  orderItem: {
    backgroundColor: "#fff",
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    elevation: 2,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4
  },
  orderHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 10
  },
  orderId: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333"
  },
  orderStatus: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 15,
    fontSize: 14
  },
  statusProcessing: {
    backgroundColor: "#ffd166",
    color: "#333"
  },
  statusDone: {
    backgroundColor: "#06d6a0",
    color: "#fff"
  },
  statusRejected: {
    color: '#FF0000', // สีแดงเพื่อแสดงว่าถูกปฏิเสธ
    fontWeight: 'bold',
  },
  statusDefault: {
    backgroundColor: "#adb5bd",
    color: "#fff"
  },
  orderDetails: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 15
  },
  orderDate: {
    color: "#666",
    fontSize: 14
  },
  orderPrice: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333"
  },
  payButton: {
    backgroundColor: "#007BFF",
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  disabledButton: {
    backgroundColor: "#7fb7ec"
  },
  payButtonText: {
    color: "#fff",
    fontWeight: "bold",
    fontSize: 16
  },
  // ปุ่มใหม่สำหรับการยืนยันได้รับสินค้า
  receiveButton: {
    backgroundColor: "#8bc34a", // สีเขียว
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  receiveButtonText: {
    color: "#fff",
    fontWeight: "bold",
    fontSize: 16
  },
  paidContainer: {
    backgroundColor: "#e6f7f2",
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  paidText: {
    color: "#06d6a0",
    fontWeight: "bold",
    fontSize: 16
  },
  detailsButton: {
    paddingVertical: 10,
    alignItems: "center",
    borderTopWidth: 1,
    borderTopColor: "#eee"
  },
  detailsButtonText: {
    color: "#007BFF",
    fontSize: 14,
    fontWeight: "500"
  },
  orderItemsContainer: {
    marginTop: 10,
    paddingTop: 10,
    borderTopWidth: 1,
    borderTopColor: "#eee"
  },
  orderItemsHeader: {
    fontSize: 16,
    fontWeight: "bold",
    marginBottom: 10,
    color: "#333"
  },
  detailsLoading: {
    marginVertical: 20
  },
  noDetailsText: {
    fontStyle: "italic",
    color: "#888",
    textAlign: "center",
    marginVertical: 10
  },
  orderItemRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: "#f2f2f2"
  },
  orderItemInfo: {
    flex: 2
  },
  orderItemName: {
    fontSize: 14,
    marginBottom: 5
  },
  orderItemPrice: {
    fontSize: 13,
    color: "#666"
  },
  orderItemQuantity: {
    flex: 0.5,
    fontSize: 14,
    textAlign: "center"
  },
  orderItemTotal: {
    flex: 1,
    fontSize: 14,
    fontWeight: "bold",
    textAlign: "right"
  },
  orderTotalContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 15,
    paddingTop: 10,
    borderTopWidth: 1,
    borderTopColor: "#eee"
  },
  orderTotalLabel: {
    fontSize: 16,
    fontWeight: "bold"
  },
  orderTotalAmount: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#007BFF"
  },
  emptyText: {
    fontSize: 16,
    color: "#666",
    textAlign: "center",
    marginBottom: 20
  },
  loginButton: {
    backgroundColor: "#007BFF",
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 8
  },
  loginButtonText: {
    color: "#fff",
    fontWeight: "bold",
    fontSize: 16
  },
  shopButton: {
    backgroundColor: "#06d6a0",
    paddingVertical: 12,
    paddingHorizontal: 30,
    borderRadius: 8
  },
  shopButtonText: {
    color: "#fff",
    fontWeight: "bold",
    fontSize: 16
  },
  productImage: {
    width: 50,
    height: 50,
    borderRadius: 6,
    marginRight: 10
  },
  orderItemRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: "#f2f2f2"
  },
  orderItemInfo: {
    flex: 1,
    marginRight: 10
  },
  orderItemName: {
    fontSize: 14,
    marginBottom: 5
  },
  orderItemPrice: {
    fontSize: 13,
    color: "#666"
  },
  orderItemQuantity: {
    width: 40,
    fontSize: 14,
    textAlign: "center"
  },
  orderItemTotal: {
    width: 80,
    fontSize: 14,
    fontWeight: "bold",
    textAlign: "right"
  },
  statusDelivered: {
    backgroundColor: "#8bc34a",
    color: "#fff"
  },
  deliveredContainer: {
    backgroundColor: "#8bc34a"
  },
  deliveredText: {
    color: "#33691e",
    fontWeight: "bold",
    fontSize: 16
  },
  statusShipping: {
    backgroundColor: "#3498db", // สีน้ำเงิน
    color: "#fff"
  },
  shippingContainer: {
    backgroundColor: "#80deea", // สีพื้นหลังน้ำเงินอ่อน
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  shippingText: {
    color: "#3498db", // สีน้ำเงินสำหรับข้อความ
    fontWeight: "bold",
    fontSize: 16
  },
  statusPreparing: {
    backgroundColor: "#9292D1",
    color: "#fff"
  },
  preparingContainer: {
    backgroundColor: "#e8d6f2",
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  preparingText: {
    color: "#9b59b6",
    fontWeight: "bold",
    fontSize: 16
  },
  addressContainer: {
    marginTop: 15,
    padding: 10,
    backgroundColor: "#f9f9f9",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ddd"
  },
  addressHeader: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 10
  },
  addressText: {
    fontSize: 14,
    color: "#666",
    marginBottom: 5
  },
  statusShipped: {
    backgroundColor: "#ff9800", // สีส้ม
    color: "#fff"
  },
  shippedContainer: {
    backgroundColor: "#ffe0b2", // สีพื้นหลังส้มอ่อน
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 10
  },
  shippedText: {
    color: "#e65100", // สีส้มเข้มสำหรับข้อความ
    fontWeight: "bold",
    fontSize: 16
  },
  rejectedContainer: {
    padding: 10,
    backgroundColor: '#FFEBEE', // สีพื้นหลังสำหรับสถานะ rejected
    borderRadius: 5,
    marginTop: 10,
},
rejectedText: {
    color: '#FF0000', // สีแดงเพื่อแสดงว่าถูกปฏิเสธ
    fontWeight: 'bold',
    textAlign: 'center',
},
});

export default styles;
