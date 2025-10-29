import { StyleSheet } from "react-native";

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f8f9fa"
  },
  headerGradient: {
    elevation: 4,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3
  },
  gradientHeader: {
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 18
  },
  headerTitleLight: {
    fontSize: 22,
    fontWeight: "bold",
    color: "#ffffff"
  },
  headerSubtitle: {
    fontSize: 14,
    color: "rgba(255,255,255,0.8)",
    marginTop: 4
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 30
  },
  listContainer: {
    padding: 16,
    paddingTop: 20,
    paddingBottom: 30
  },
  productItem: {
    flexDirection: "row",
    backgroundColor: "white",
    borderRadius: 15,
    marginVertical: 8,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.15,
    shadowRadius: 3,
    elevation: 3,
    position: "relative",
    overflow: "hidden"
  },
  imageContainer: {
    borderRadius: 12,
    overflow: "hidden",
    elevation: 2,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2
  },
  productImage: {
    width: 90,
    height: 90,
    borderRadius: 12
  },
  productInfo: {
    flex: 1,
    marginLeft: 16,
    justifyContent: "space-between",
    paddingRight: 36
  },
  productName: {
    fontSize: 16,
    fontWeight: "600",
    color: "#444",
    lineHeight: 22
  },
  productPrice: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#4a6ee0",
    marginTop: 8
  },
  removeButton: {
    position: "absolute",
    top: 12,
    right: 12,
    zIndex: 2
  },
  removeButtonInner: {
    backgroundColor: "#ff4757",
    borderRadius: 30,
    width: 36,
    height: 36,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 3
  },
  emptyStateIcon: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: "#f2f6ff",
    justifyContent: "center",
    alignItems: "center",
    marginBottom: 20
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 10
  },
  emptyText: {
    fontSize: 16,
    color: "#777",
    textAlign: "center",
    lineHeight: 22
  },
  loadingText: {
    marginTop: 12,
    color: "#666",
    fontSize: 16
  },
  loginButton: {
    width: "100%",
    borderRadius: 30,
    marginTop: 30,
    overflow: "hidden",
    elevation: 3
  },
  gradientButton: {
    flexDirection: "row",
    paddingVertical: 14,
    paddingHorizontal: 25,
    justifyContent: "center",
    alignItems: "center"
  },
  loginButtonText: {
    color: "white",
    fontSize: 16,
    fontWeight: "bold"
  },
  shopButton: {
    width: "100%",
    borderRadius: 30,
    marginTop: 30,
    overflow: "hidden",
    elevation: 3
  },
  shopButtonText: {
    color: "white",
    fontSize: 16,
    fontWeight: "bold"
  },
  buttonIcon: {
    marginLeft: 8
  }
});

export default styles;
