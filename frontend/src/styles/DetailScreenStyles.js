import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingBottom: 10
  },
  productImage: {
    width: "100%",
    height: 250, // ปรับให้ใหญ่ขึ้นเล็กน้อย
    resizeMode: "cover",
    borderBottomLeftRadius: 12,
    borderBottomRightRadius: 12
  },
  detailsContainer: {
    padding: 20,
    flex: 1,
    backgroundColor: "#fff",
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    marginTop: -20, // ให้ดูเหมือน Card ลอยขึ้นมา
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.1,
    shadowRadius: 5,
    elevation: 5 // เงาสำหรับ Android
  },
  productTitle: {
    fontSize: 22,
    fontWeight: "bold",
    color: "#333",
    textAlign: "left",
    marginBottom: 8
  },
  productDescriptionContainer: {
    marginTop: 10
  },
  productDescription: {
    fontSize: 16,
    color: "#555",
    lineHeight: 24,
    textAlign: "justify",
    marginTop: 8
  },
  footer: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingVertical: 16,
    paddingHorizontal: 20,
    borderTopWidth: 1,
    borderColor: "#ddd",
    backgroundColor: "#f8f8f8",
    borderTopLeftRadius: 12,
    borderTopRightRadius: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: -3 },
    shadowOpacity: 0.1,
    shadowRadius: 5,
    elevation: 5
  },
  priceText: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#1e90ff",
    flex: 1
  },
  cartButton: {
    backgroundColor: "#1e90ff",
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 12,
    shadowColor: "#1e90ff",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
    elevation: 3, // เงาสำหรับ Android
    marginRight: 12
  },
  cartButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold",
    textAlign: "center"
  },
  heartButton: {
    justifyContent: "center",
    alignItems: "center",
    padding: 8,
    width: 44,
    height: 44
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20
  },
  emptyCartText: {
    fontSize: 18,
    color: "#555",
    marginBottom: 20,
    textAlign: "center"
  },
  loginButton: {
    backgroundColor: "#1e90ff",
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 12,
    shadowColor: "#1e90ff",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
    elevation: 3
  },
  loginButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold"
  }
});

export default styles;
