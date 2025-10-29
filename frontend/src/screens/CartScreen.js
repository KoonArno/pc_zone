import React, { useState, useCallback } from 'react';
import { 
    View, 
    Text, 
    FlatList, 
    Image, 
    TouchableOpacity, 
    ActivityIndicator,
    Alert
} from 'react-native';
import axios from 'axios';
import { useFocusEffect, useNavigation } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import styles from '../styles/CartScreenStyles';

// ใช้ CustomCheckBox แทนที่จะใช้ CheckBox จาก react-native-elements
const CustomCheckBox = ({ checked, onPress, title }) => {
  return (
    <TouchableOpacity 
      style={styles.checkboxContainer} 
      onPress={onPress}
    >
      <View style={[styles.checkbox, checked && styles.checkboxChecked]}>
        {checked && <Text style={styles.checkboxIndicator}>✓</Text>}
      </View>
      {title && <Text style={styles.checkboxTitle}>{title}</Text>}
    </TouchableOpacity>
  );
};

const API_URL = "http://192.168.1.33/pc_zone/api/cart/get_cart.php";
const UPDATE_CART_API_URL = "http://192.168.1.33/pc_zone/api/cart/update_cart.php";
const DELETE_CART_API_URL = "http://192.168.1.33/pc_zone/api/cart/delete_cart_item.php";
const CREATE_ORDER_API_URL = "http://192.168.1.33/pc_zone/api/orders/create_order.php";
const IMAGE_BASE_URL = "http://192.168.1.33/pc_zone/image/";

const CartScreen = ({ route, navigation }) => {
  const [cartItems, setCartItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [totalPrice, setTotalPrice] = useState(0);
  const [selectedTotal, setSelectedTotal] = useState(0);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [processingOrder, setProcessingOrder] = useState(false);
  const [userId, setUserId] = useState(null);
  const [selectedItems, setSelectedItems] = useState({});
  const [selectAll, setSelectAll] = useState(false);

  const nav = useNavigation();

  useFocusEffect(
    useCallback(() => {
      checkLoginStatus();
    }, [])
  );

  const checkLoginStatus = async () => {
    try { 
      const user = await AsyncStorage.getItem("user");
      if (user !== null) {
        const userData = JSON.parse(user);
        setIsLoggedIn(true);
        setUserId(userData.user_id);
        fetchCartItems(userData.user_id);
      } else {
        setIsLoggedIn(false);
        setUserId(null);
        setCartItems([]);
        setTotalPrice(0);
        setSelectedTotal(0);
        setSelectedItems({});
      }
    } catch (error) {
      console.error("Error checking login status:", error);
    }
  };

  const fetchCartItems = async (userIdValue) => {
    setLoading(true);
    try {
      const response = await axios.get(`${API_URL}?user_id=${userIdValue}`);
      if (response.data.status === "success") {
        setCartItems(response.data.cart_items);
        setTotalPrice(response.data.total_price);
        
        // เริ่มต้นด้วยไม่มีสินค้าที่เลือก
        const initialSelectedState = {};
        response.data.cart_items.forEach(item => {
          initialSelectedState[item.id] = false;
        });
        setSelectedItems(initialSelectedState);
        setSelectedTotal(0);
        setSelectAll(false);
      } else {
        console.warn("Failed to get cart items:", response.data.message);
        setCartItems([]);
        setTotalPrice(0);
        setSelectedTotal(0);
        setSelectedItems({});
      }
    } catch (error) {
      console.error("Error fetching cart items:", error);
      Alert.alert("Error", "Could not load cart items");
      setCartItems([]);
      setTotalPrice(0);
      setSelectedTotal(0);
      setSelectedItems({});
    } finally {
      setLoading(false);
    }
  };

  const updateCartItem = async (id, quantity) => {
    if (!userId) {
      Alert.alert("Error", "User not logged in");
      return;
    }
    
    try {
      const response = await axios.post(UPDATE_CART_API_URL, {
        id: id,
        user_id: userId,
        quantity: quantity
      });
      
      if (response.data.status === "success") {
        fetchCartItems(userId);
      } else {
        console.warn("Failed to update cart:", response.data.message);
        Alert.alert("Error", "Could not update cart item");
      }
    } catch (error) {
      console.error("Error updating cart:", error);
      Alert.alert("Error", "Could not update cart item");
    }
  };

  const removeCartItem = async (id) => {
    if (!userId) {
      Alert.alert("Error", "User not logged in");
      return;
    }
    
    try {
      const response = await axios.post(DELETE_CART_API_URL, {
        id: id,
        user_id: userId
      });
      
      if (response.data.status === "success") {
        fetchCartItems(userId);
      } else {
        console.warn("Failed to remove cart item:", response.data.message);
        Alert.alert("Error", "Could not remove cart item");
      }
    } catch (error) {
      console.error("Error removing cart item:", error);
      Alert.alert("Error", "Could not remove cart item");
    }
  };

  const increaseQuantity = (item) => {
    updateCartItem(item.id, item.quantity + 1);
  };

  const decreaseQuantity = (item) => {
    if (item.quantity > 1) {
      updateCartItem(item.id, item.quantity - 1);
    } else {
      Alert.alert(
        "ลบสินค้า",
        "คุณต้องการลบสินค้านี้ออกจากรถเข็นหรือไม่?",
        [
          { text: "ยกเลิก", style: "cancel" },
          { text: "ลบ", style: "destructive", onPress: () => removeCartItem(item.id) }
        ]
      );
    }
  };

  // ฟังก์ชันจัดการการเลือกสินค้า
  const toggleItemSelection = (itemId) => {
    const updatedSelectedItems = { ...selectedItems };
    updatedSelectedItems[itemId] = !updatedSelectedItems[itemId];
    setSelectedItems(updatedSelectedItems);
    
    // คำนวณราคารวมใหม่
    calculateSelectedTotal(updatedSelectedItems);
    
    // ตรวจสอบว่าเลือกทั้งหมดหรือไม่
    const allSelected = cartItems.every(item => updatedSelectedItems[item.id]);
    setSelectAll(allSelected);
  };

  // ฟังก์ชันเลือกทั้งหมด/ยกเลิกการเลือกทั้งหมด
  const toggleSelectAll = () => {
    const newSelectAll = !selectAll;
    setSelectAll(newSelectAll);
    
    const updatedSelectedItems = {};
    cartItems.forEach(item => {
      updatedSelectedItems[item.id] = newSelectAll;
    });
    setSelectedItems(updatedSelectedItems);
    
    // คำนวณราคารวมใหม่
    calculateSelectedTotal(updatedSelectedItems);
  };

  // ฟังก์ชันคำนวณราคารวมของสินค้าที่เลือก
  const calculateSelectedTotal = (selectedItemsState) => {
    let newTotal = 0;
    cartItems.forEach(item => {
      if (selectedItemsState[item.id]) {
        newTotal += parseInt(item.price) * item.quantity;
      }
    });
    setSelectedTotal(newTotal);
  };

  const createOrder = async () => {
    if (!userId) {
      Alert.alert("ข้อผิดพลาด", "คุณยังไม่ได้เข้าสู่ระบบ");
      return;
    }
    
    // ตรวจสอบว่ามีสินค้าที่เลือกหรือไม่
    const selectedCartItems = cartItems.filter(item => selectedItems[item.id]);
    
    if (selectedCartItems.length === 0) {
      Alert.alert("ข้อผิดพลาด", "กรุณาเลือกสินค้าที่ต้องการสั่งซื้อ");
      return;
    }
  
    setProcessingOrder(true);
    try {
      const orderData = {
        user_id: userId,
        total_price: selectedTotal,
        cart_items: selectedCartItems.map(item => ({
          product_id: parseInt(item.product_id) || 0,
          quantity: item.quantity
        })).filter(item => item.product_id > 0)
      };
  
      if (orderData.cart_items.length === 0) {
        Alert.alert("ข้อผิดพลาด", "ไม่พบข้อมูลสินค้าที่ถูกต้อง");
        setProcessingOrder(false);
        return;
      }
  
      console.log("Sending order data:", JSON.stringify(orderData));
  
      const response = await axios.post(CREATE_ORDER_API_URL, orderData);
      
      if (response.data.status === "success") {
        Alert.alert(
          "สำเร็จ",
          "การสั่งซื้อของคุณได้รับการดำเนินการแล้ว",
          [
            { 
              text: "ตกลง", 
              onPress: () => {
                fetchCartItems(userId);
                nav.navigate('Checkout');
              } 
            }
          ]
        );
      } else {
        Alert.alert("ข้อผิดพลาด", response.data.message || "ไม่สามารถดำเนินการสั่งซื้อได้");
      }
    } catch (error) {
      console.error("Error creating order:", error.response?.data || error.message);
      Alert.alert("ข้อผิดพลาด", "ไม่สามารถดำเนินการสั่งซื้อได้ กรุณาลองใหม่อีกครั้ง");
    } finally {
      setProcessingOrder(false);
    }
  };

  return (
    <View style={styles.container}>
      {!isLoggedIn ? (
        <View style={styles.centerContainer}>
          <Text style={styles.emptyCartText}>กรุณาเข้าสู่ระบบเพื่อดูรถเข็น</Text>
          <TouchableOpacity 
            style={styles.loginButton}
            onPress={() => navigation.navigate('Login')}
          >
            <Text style={styles.loginButtonText}>เข้าสู่ระบบ</Text>
          </TouchableOpacity>
        </View>
      ) : loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color="#007BFF" />
        </View>
      ) : cartItems.length === 0 ? (
        <View style={styles.centerContainer}>
          <Text style={styles.emptyCartText}>ไม่มีสินค้าในรถเข็น</Text>
          <TouchableOpacity 
            style={styles.shopButton}
            onPress={() => navigation.navigate('Home')}
          >
            <Text style={styles.shopButtonText}>ไปช้อปปิ้งต่อ</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <>
          <Text style={styles.header}>รถเข็นของคุณ</Text>
          
          {/* เพิ่มส่วนควบคุมการเลือกทั้งหมด */}
          <View style={styles.selectAllContainer}>
            <CustomCheckBox
              checked={selectAll}
              onPress={toggleSelectAll}
              title="เลือกทั้งหมด"
            />
            <Text style={styles.cartSummary}>
              สินค้าทั้งหมด: {cartItems.length} รายการ
            </Text>
          </View>
          
          <FlatList
            data={cartItems}
            keyExtractor={item => item.id.toString()}
            renderItem={({ item }) => (
              <View style={styles.cartItem}>
                {/* ใช้ CustomCheckBox แทน */}
                <CustomCheckBox
                  checked={selectedItems[item.id] || false}
                  onPress={() => toggleItemSelection(item.id)}
                />
                
                <Image 
                  source={{ uri: `${IMAGE_BASE_URL}${item.image}` }} 
                  style={styles.image}
                />
                <View style={styles.itemDetails}>
                  <Text style={styles.itemName}>{item.product_name}</Text>
                  <Text style={styles.itemPrice}>฿ {parseInt(item.price).toLocaleString()}</Text>
                  
                  <View style={styles.quantityControl}>
                    <TouchableOpacity 
                      style={styles.quantityButton}
                      onPress={() => decreaseQuantity(item)}
                    >
                      <Text style={styles.quantityButtonText}>-</Text>
                    </TouchableOpacity>
                    
                    <Text style={styles.quantityText}>{item.quantity}</Text>
                    
                    <TouchableOpacity 
                      style={styles.quantityButton}
                      onPress={() => increaseQuantity(item)}
                    >
                      <Text style={styles.quantityButtonText}>+</Text>
                    </TouchableOpacity>
                    
                    <TouchableOpacity 
                      style={styles.removeButton}
                      onPress={() => {
                        Alert.alert(
                          "ลบสินค้า",
                          "คุณต้องการลบสินค้านี้ออกจากรถเข็นหรือไม่?",
                          [
                            { text: "ยกเลิก", style: "cancel" },
                            { text: "ลบ", style: "destructive", onPress: () => removeCartItem(item.id) }
                          ]
                        );
                      }}
                    >
                      <Text style={styles.removeButtonText}>ลบ</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              </View>
            )}
          />
          <View style={styles.footer}>
            <View style={styles.priceContainer}>
              <Text style={styles.selectedLabel}>ที่เลือก:</Text>
              <Text style={styles.selectedTotal}>฿ {parseInt(selectedTotal).toLocaleString()}</Text>
            </View>
            <TouchableOpacity 
              style={[styles.payButton, processingOrder && styles.disabledButton]}
              onPress={createOrder}
              disabled={processingOrder}
            >
              {processingOrder ? (
                <ActivityIndicator size="small" color="#FFFFFF" />
              ) : (
                <Text style={styles.payText}>ยืนยันคำสั่งซื้อ</Text>
              )}
            </TouchableOpacity>
          </View>
        </>
      )}
    </View>
  );
};

export default CartScreen;