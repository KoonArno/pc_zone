import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  FlatList,
  SafeAreaView,
  ScrollView,
  Image,
  Modal
} from "react-native";
import axios from "axios";
import * as ImagePicker from "expo-image-picker";
import { Ionicons } from "@expo/vector-icons";
import * as FileSystem from "expo-file-system";
import styles from "../styles/PaymentScreenStyles";
import AsyncStorage from '@react-native-async-storage/async-storage';

const PROCESS_PAYMENT_URL =
  "http://192.168.1.33/pc_zone/api/payment/process_payment.php";
const ADDRESS_API_URL =
  "http://192.168.1.33/pc_zone/api/address/get_address.php";
const ORDERS_API_URL = "http://192.168.1.33/pc_zone/api/orders/get_orders.php";
const GET_ORDER_DETAILS_API_URL =
  "http://192.168.1.33/pc_zone/api/orders/get_order_details.php";

const PaymentScreen = ({ navigation, route }) => {
  const [addresses, setAddresses] = useState([]);
  const [selectedAddress, setSelectedAddress] = useState(null);
  const [loading, setLoading] = useState(true);
  const [checkoutLoading, setCheckoutLoading] = useState(false);
  const [paymentImage, setPaymentImage] = useState(null);
  const [paymentImageData, setPaymentImageData] = useState(null);
  const [totalPrice, setTotalPrice] = useState(0);
  const [orderDetails, setOrderDetails] = useState(null);
  const [orderItems, setOrderItems] = useState([]);
  const [addressModalVisible, setAddressModalVisible] = useState(false);
  const [userId, setUserId] = useState(null);

  useEffect(() => {
    // Get user ID from AsyncStorage
    getUserId();
  }, []);

  useEffect(() => {
    // Only fetch data after we have the user ID
    if (userId) {
      getAddress();
      getOrderDetails();
    }
  }, [userId]);

  useEffect(() => {
    if (orderDetails?.order_id) {
      getOrderItems(orderDetails.order_id);
    }
  }, [orderDetails]);

  const getUserId = async () => {
    try {
      const user = await AsyncStorage.getItem("user");
      if (user !== null) {
        const userData = JSON.parse(user);
        setUserId(userData.user_id);
      } else {
        // Handle case where user is not logged in
        Alert.alert("Error", "กรุณาเข้าสู่ระบบก่อนชำระเงิน", [
          { text: "ตกลง", onPress: () => navigation.navigate('Login') }
        ]);
      }
    } catch (error) {
      console.error("Error retrieving user data:", error);
      Alert.alert("Error", "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้");
    }
  };

  const getOrderItems = async (orderId) => {
    try {
      const response = await axios.get(
        `${GET_ORDER_DETAILS_API_URL}?order_id=${orderId}`
      );
      if (response.data.status === "success") {
        setOrderItems(response.data.order_items);
      } else {
        Alert.alert("Error", "ไม่พบรายการสินค้า");
      }
    } catch (error) {
      console.error("Error fetching order items:", error);
      Alert.alert("Error", "เกิดข้อผิดพลาดในการโหลดรายการสินค้า");
    }
  };

  const getOrderDetails = async () => {
    try {
      // Use route params if available, otherwise use the latest order
      if (route.params?.order_id) {
        const response = await axios.get(`${GET_ORDER_DETAILS_API_URL}?order_id=${route.params.order_id}`);
        if (response.data.status === "success") {
          // Find the order details from orders API
          const ordersResponse = await axios.get(`${ORDERS_API_URL}?user_id=${userId}`);
          if (ordersResponse.data.status === "success") {
            const order = ordersResponse.data.orders.find(o => o.order_id === route.params.order_id);
            if (order) {
              setOrderDetails(order);
              setTotalPrice(parseFloat(order.total_price));
            }
          }
        }
      } else {
        const response = await axios.get(`${ORDERS_API_URL}?user_id=${userId}`);
        if (
          response.data.status === "success" &&
          response.data.orders.length > 0
        ) {
          const latestOrder = response.data.orders[0]; // Gets the most recent order
          setOrderDetails(latestOrder);
          setTotalPrice(parseFloat(latestOrder.total_price));
        } else {
          // If no orders found, fallback to route params
          setTotalPrice(route.params?.totalPrice || 0);
        }
      }
    } catch (error) {
      console.error("Error fetching order details:", error);
      // Fallback to route params
      setTotalPrice(route.params?.totalPrice || 0);
    }
  };

  const getAddress = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${ADDRESS_API_URL}?user_id=${userId}`);
      if (response.data.status === "success") {
        setAddresses(response.data.addresses);
        // แก้ไขให้รองรับทั้งกรณีที่ is_default เป็นตัวเลขและข้อความ
        const defaultAddress = response.data.addresses.find(
          (addr) => addr.is_default === 0 || addr.is_default === "0"
        );
        setSelectedAddress(defaultAddress || response.data.addresses[0]);
      } else {
        setAddresses([]);
        Alert.alert("Error", "ไม่พบข้อมูลที่อยู่", [
          { text: "ลองใหม่", onPress: getAddress }
        ]);
      }
    } catch (error) {
      console.error("Error fetching addresses:", error);
      Alert.alert("Error", "เกิดข้อผิดพลาดในการโหลดที่อยู่", [
        { text: "ลองใหม่", onPress: getAddress }
      ]);
    } finally {
      setLoading(false);
    }
  };

  const pickImage = async () => {
    let result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 1 // ตั้งค่า quality เป็น 1 เพื่อให้รูปภาพเป็นต้นฉบับ
    });

    if (!result.canceled) {
        setPaymentImage(result.assets[0].uri);
        setPaymentImageData(result.assets[0]); // Hold image data
    }
  };

  const handleCheckout = async () => {
    if (!userId) {
      Alert.alert("Error", "กรุณาเข้าสู่ระบบก่อนชำระเงิน");
      return;
    }

    if (!selectedAddress) {
      Alert.alert("Error", "กรุณาเลือกที่อยู่จัดส่ง");
      return;
    }

    if (!paymentImageData) {
      Alert.alert("Error", "กรุณาอัปโหลดหลักฐานการชำระเงิน");
      return;
    }

    setCheckoutLoading(true);

    try {
      // แปลงรูปเป็น Base64
      const base64 = await FileSystem.readAsStringAsync(paymentImageData.uri, {
        encoding: FileSystem.EncodingType.Base64
      });

      // ส่งข้อมูลทั้งหมดพร้อมรูปภาพไปยัง API
      const response = await axios.post(PROCESS_PAYMENT_URL, {
        user_id: userId,
        total_price: totalPrice,
        address_id: selectedAddress.address_id,
        image: base64, // ส่งรูปภาพเป็น Base64
        order_id: orderDetails?.order_id, // ส่ง order_id ไปด้วย
        clear_cart: true // ล้างตะกร้า (optional)
      });

      if (response.data.status === "success") {
        Alert.alert("สำเร็จ", "บันทึกการชำระเงินเรียบร้อยแล้ว", [
          {
            text: "ตกลง",
            onPress: () => {
              // คุณสามารถใช้ response.data.order_id ได้หากจำเป็น
              navigation.goBack(); // ย้อนกลับไปหน้าก่อนหน้า
            }
          }
        ]);
      } else {
        Alert.alert("Error", response.data.message || "การชำระเงินล้มเหลว");
      }
    } catch (error) {
      console.error("Error during checkout:", error);
      Alert.alert("Error", "เกิดข้อผิดพลาดในการชำระเงิน: " + error.message);
    } finally {
      setCheckoutLoading(false);
    }
  };

  const renderAddressItem = (address) => {
    return (
      <View style={styles.addressDetailsContainer}>
        <Text style={styles.addressName}>{address.full_name}</Text>
        <Text style={styles.addressText}>{address.phone_number}</Text>
        <Text style={styles.addressText}>
          {address.address_line}, {address.subdistrict}, {address.district},{" "}
          {address.city}, {address.postal_code}
        </Text>
        {(address.is_default === 0 || address.is_default === "0") && (
          <View style={styles.defaultBadge}>
            <Text style={styles.defaultText}>ที่อยู่หลัก</Text>
          </View>
        )}
      </View>
    );
  };

  const AddressModal = () => {
    return (
      <Modal
        animationType="slide"
        transparent={true}
        visible={addressModalVisible}
        onRequestClose={() => setAddressModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>เลือกที่อยู่จัดส่ง</Text>
              <TouchableOpacity onPress={() => setAddressModalVisible(false)}>
                <Ionicons name="close" size={24} color="#333" />
              </TouchableOpacity>
            </View>

            <FlatList
              data={addresses}
              keyExtractor={(item) => item.address_id.toString()}
              renderItem={({ item }) => (
                <TouchableOpacity
                  style={[
                    styles.modalAddressItem,
                    selectedAddress?.address_id === item.address_id &&
                      styles.selectedAddress
                  ]}
                  onPress={() => {
                    setSelectedAddress(item);
                    setAddressModalVisible(false);
                  }}
                >
                  {renderAddressItem(item)}
                </TouchableOpacity>
              )}
            />
          </View>
        </View>
      </Modal>
    );
  };

  // If user ID is not yet available, show loading state
  if (!userId) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>ชำระเงิน</Text>
        </View>
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
          <ActivityIndicator size="large" color="#79B4E3" />
          <Text style={{ marginTop: 10 }}>กำลังโหลดข้อมูล...</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>ชำระเงิน</Text>
      </View>

      <ScrollView>
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>ที่อยู่จัดส่ง</Text>
            <TouchableOpacity
              style={styles.changeButton}
              onPress={() => setAddressModalVisible(true)}
            >
              <Text style={styles.changeButtonText}>เปลี่ยน</Text>
            </TouchableOpacity>
          </View>

          {loading ? (
            <ActivityIndicator size="large" color="#79B4E3" />
          ) : selectedAddress ? (
            <View style={styles.selectedAddressContainer}>
              {renderAddressItem(selectedAddress)}
            </View>
          ) : (
            <Text style={styles.noAddressText}>ไม่พบข้อมูลที่อยู่</Text>
          )}
        </View>

        {/* ส่วนแสดงรายละเอียดสินค้า */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>รายการสินค้า</Text>
          {orderItems.length > 0 ? (
            orderItems.map((item, index) => (
              <View key={index} style={styles.productItem}>
                <Image
                  source={{
                    uri: `http://192.168.1.33/pc_zone/image/${item.image}`
                  }}
                  style={styles.productImage}
                />
                <View style={styles.productDetails}>
                  <Text style={styles.productName}>{item.product_name}</Text>
                  <Text style={styles.productPrice}>
                    ฿ {parseInt(item.price).toLocaleString()}
                  </Text>
                  <Text style={styles.productQuantity}>
                    จำนวน: {item.quantity}
                  </Text>
                  <Text style={styles.productSubtotal}>
                    ฿ {parseInt(item.price * item.quantity).toLocaleString()}
                  </Text>
                </View>
              </View>
            ))
          ) : (
            <Text style={styles.noDetailsText}>ไม่พบรายการสินค้า</Text>
          )}
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>อัปโหลดหลักฐานการชำระเงิน</Text>
          {paymentImage ? (
            <View>
              <Image
                source={{ uri: paymentImage }}
                style={styles.paymentImage}
              />
              <TouchableOpacity
                style={styles.changeImageButton}
                onPress={pickImage}
              >
                <Ionicons name="refresh" size={18} color="#fff" />
                <Text style={styles.changeImageText}>เปลี่ยนรูป</Text>
              </TouchableOpacity>
            </View>
          ) : (
            <TouchableOpacity style={styles.uploadButton} onPress={pickImage}>
              <Ionicons name="cloud-upload-outline" size={24} color="#fff" />
              <Text style={styles.uploadText}>เลือกไฟล์</Text>
            </TouchableOpacity>
          )}
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>สรุปคำสั่งซื้อ</Text>
          <View style={styles.orderSummaryRow}>
            <Text style={styles.orderSummaryLabel}>ราคาสินค้า</Text>
            <Text style={styles.orderSummaryValue}>{totalPrice} บาท</Text>
          </View>
          <View style={styles.orderSummaryRow}>
            <Text style={styles.orderSummaryLabel}>ค่าจัดส่ง</Text>
            <Text style={styles.orderSummaryValue}>ฟรี</Text>
          </View>
          <View style={[styles.orderSummaryRow, styles.orderTotal]}>
            <Text style={[styles.orderSummaryLabel, styles.orderTotalLabel]}>
              รวมทั้งสิ้น
            </Text>
            <Text style={[styles.orderSummaryValue, styles.orderTotalValue]}>
              {totalPrice} บาท
            </Text>
          </View>
        </View>
      </ScrollView>

      <View style={styles.bottomContainer}>
        <TouchableOpacity
          style={styles.payButton}
          onPress={handleCheckout}
          disabled={checkoutLoading}
        >
          {checkoutLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.payText}>ยืนยันการชำระเงิน</Text>
          )}
        </TouchableOpacity>
      </View>

      <AddressModal />
    </SafeAreaView>
  );
};

export default PaymentScreen;