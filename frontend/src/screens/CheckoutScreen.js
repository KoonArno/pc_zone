import React, { useState, useCallback } from 'react';
import { 
    View, 
    Text, 
    FlatList, 
    TouchableOpacity, 
    ActivityIndicator,
    Alert,
    Image
} from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { format } from 'date-fns';
import styles from '../styles/CheckoutScreenStyles';

const API_URL = "http://192.168.1.33/pc_zone/api";
const GET_ORDERS_API_URL = `${API_URL}/orders/get_orders.php`;
const GET_ORDER_DETAILS_API_URL = `${API_URL}/orders/get_order_details.php`;
const UPDATE_ORDER_STATUS_API_URL = `${API_URL}/orders/update_order_status.php`;
const IMAGE_BASE_URL = "http://192.168.1.33/pc_zone/image/";

const CheckoutScreen = ({ navigation }) => {
    const [orders, setOrders] = useState([]);
    const [expandedOrderId, setExpandedOrderId] = useState(null);
    const [orderDetails, setOrderDetails] = useState({});
    const [orderAddress, setOrderAddress] = useState({});
    const [loading, setLoading] = useState(true);
    const [detailsLoading, setDetailsLoading] = useState(false);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [userId, setUserId] = useState(null);

    // แก้ไข useFocusEffect ให้ทำงานทุกครั้งที่เข้ามาที่หน้านี้
useFocusEffect(
    useCallback(() => {
        // รีเซ็ต state เพื่อให้มีการโหลดข้อมูลใหม่
        setOrders([]);
        setExpandedOrderId(null);
        setOrderDetails({});
        setOrderAddress({});
        setLoading(true);
        
        // ตรวจสอบสถานะการล็อกอิน
        checkLoginStatus();
        
        // ฟังก์ชัน cleanup เมื่อออกจากหน้านี้
        return () => {
            // ทำความสะอาดหรือยกเลิกการร้องขอที่อาจเกิดขึ้น (ถ้าจำเป็น)
        };
    }, [])
);

    const checkLoginStatus = async () => {
        try {
            const user = await AsyncStorage.getItem("user");
            if (user !== null) {
                const userData = JSON.parse(user);
                setIsLoggedIn(true);
                setUserId(userData.user_id);
                fetchOrders(userData.user_id);
            } else {
                setIsLoggedIn(false);
                setUserId(null);
                setOrders([]);
            }
        } catch (error) {
            console.error("Error checking login status:", error);
        }
    };

    const fetchOrders = async (userId) => {
        setLoading(true);
        try {
            const response = await axios.get(`${GET_ORDERS_API_URL}?user_id=${userId}`);
            if (response.data.status === "success") {
                setOrders(response.data.orders);
            } else {
                console.warn("Failed to get orders:", response.data.message);
                setOrders([]);
            }
        } catch (error) {
            console.error("Error fetching orders:", error);
            Alert.alert("Error", "Could not load orders");
            setOrders([]);
        } finally {
            setLoading(false);
        }
    };

    const fetchOrderDetails = async (orderId) => {
        setDetailsLoading(true);
        try {
            const response = await axios.get(`${GET_ORDER_DETAILS_API_URL}?order_id=${orderId}`);
            if (response.data.status === "success") {
                setOrderDetails(prev => ({
                    ...prev,
                    [orderId]: response.data.order_items
                }));
            } else {
                console.warn("Failed to get order details:", response.data.message);
            }
        } catch (error) {
            console.error("Error fetching order details:", error);
            Alert.alert("Error", "Could not load order details");
        } finally {
            setDetailsLoading(false);
        }
    };

    const fetchOrderAddress = async (orderId) => {
    try {
        const response = await axios.get(`http://192.168.1.33/pc_zone/api/orders/get_order_address.php?order_id=${orderId}`);
        if (response.data.status === "success") {
            setOrderAddress(prev => ({
                ...prev,
                [orderId]: response.data.address
            }));
        } else {
            console.warn("Failed to get order address:", response.data.message);
        }
    } catch (error) {
        console.error("Error fetching order address:", error);
        Alert.alert("Error", "Could not load order address");
    }
};

    const handlePayment = (orderId) => {
        navigation.navigate('Payment', {
            order_id: orderId,
            user_id: userId
        });
    };

    const handleReceiveOrder = async (orderId) => {
        try {
            const response = await axios.post(UPDATE_ORDER_STATUS_API_URL, {
                order_id: orderId,
                order_status: 'done'
            });
            
            if (response.data.status === "success") {
                setOrders(orders.map(order => 
                    order.order_id === orderId 
                        ? {...order, order_status: 'done'} 
                        : order
                ));
                Alert.alert("สำเร็จ", "ขอบคุณที่ยืนยันการรับสินค้า");
            } else {
                Alert.alert("เกิดข้อผิดพลาด", response.data.message);
            }
        } catch (error) {
            console.error("Error updating order status:", error);
            Alert.alert("เกิดข้อผิดพลาด", "ไม่สามารถอัปเดตสถานะคำสั่งซื้อได้");
        }
    };

    const toggleOrderDetails = (orderId) => {
        if (expandedOrderId === orderId) {
            setExpandedOrderId(null);
        } else {
            setExpandedOrderId(orderId);
            if (!orderDetails[orderId]) {
                fetchOrderDetails(orderId);
            }
            if (!orderAddress[orderId]) {
                fetchOrderAddress(orderId);
            }
        }
    };

    const formatDate = (dateString) => {
        try {
            const date = new Date(dateString);
            return format(date, 'dd/MM/yyyy HH:mm');
        } catch (error) {
            return dateString;
        }
    };

    const getStatusStyle = (status, paymentStatus) => {
        if (status === 'processing') {
            if (!paymentStatus) {
                return styles.statusProcessing; // รอชำระเงิน
            } else if (paymentStatus === 'pending') {
                return styles.statusPending; // ชำระแล้วรอยืนยัน
            } else if (paymentStatus === 'completed') {
                return styles.statusPreparing; // กำลังจัดสินค้า
            }
        } else if (status === 'shipping') {
            if (paymentStatus === 'completed') {
                return styles.statusShipping; // กำลังจัดส่ง
            }
        } else if (status === 'shipped') {  // เพิ่มสถานะ shipped
            if (paymentStatus === 'completed') {
                return styles.statusShipped; // จัดส่งแล้ว
            }
        } else if (status === 'done') {
            if (paymentStatus === 'completed') {
                return styles.statusDelivered; // จัดส่งแล้ว
            }
        } else if (status === 'rejected') {  // เพิ่มเงื่อนไขสำหรับสถานะ rejected
            return styles.statusRejected; // คำสั่งซื้อถูกปฏิเสธ
        }
        return styles.statusDefault; // สถานะเริ่มต้น
    };
    
    const getStatusText = (status, paymentStatus) => {
        if (status === 'processing') {
            if (!paymentStatus) {
                return 'รอชำระเงิน';
            } else if (paymentStatus === 'pending') {
                return 'ชำระแล้วรอยืนยัน';
            } else if (paymentStatus === 'completed') {
                return 'กำลังจัดสินค้า';
            }
        } else if (status === 'shipping') {
            if (paymentStatus === 'completed') {
                return 'กำลังจัดส่ง';
            }
        } else if (status === 'shipped') {  // เพิ่มสถานะ shipped
            if (paymentStatus === 'completed') {
                return 'จัดส่งแล้ว';
            }
        } else if (status === 'done') {
            if (paymentStatus === 'completed') {
                return 'รับสินค้าแล้ว';
            }
        } else if (status === 'rejected') {  // เพิ่มเงื่อนไขสำหรับสถานะ rejected
            return 'คำสั่งซื้อถูกปฏิเสธ';
        }
        return status;
    };

    const renderOrderItem = ({ item }) => (
        <View style={styles.orderItem}>
            <View style={styles.orderHeader}>
                <Text style={styles.orderId}>คำสั่งซื้อ #{item.order_id}</Text>
                <Text style={[styles.orderStatus, getStatusStyle(item.order_status, item.payment_status)]}>
                    {getStatusText(item.order_status, item.payment_status)}
                </Text>
            </View>
    
            <View style={styles.orderDetails}>
                <Text style={styles.orderDate}>วันที่: {formatDate(item.created_at)}</Text>
                <Text style={styles.orderPrice}>ยอดรวม: ฿ {parseInt(item.total_price).toLocaleString()}</Text>
            </View>
    
            {item.order_status === 'processing' && !item.payment_status ? (
                <TouchableOpacity
                    style={styles.payButton}
                    onPress={() => handlePayment(item.order_id)}
                >
                    <Text style={styles.payButtonText}>ชำระเงิน</Text>
                </TouchableOpacity>
            ) : item.order_status === 'shipped' && item.payment_status === 'completed' ? (
                <TouchableOpacity
                    style={styles.receiveButton}
                    onPress={() => handleReceiveOrder(item.order_id)}
                >
                    <Text style={styles.receiveButtonText}>ยืนยันรับสินค้า</Text>
                </TouchableOpacity>
            ) : item.order_status === 'rejected' ? (  // เพิ่มเงื่อนไขสำหรับสถานะ rejected
                <View style={styles.rejectedContainer}>
                    <Text style={styles.rejectedText}>คำสั่งซื้อถูกปฏิเสธเนื่องจากปัญหาการชำระเงิน</Text>
                </View>
            ) : (
                <View style={[
                    styles.paidContainer,
                    item.order_status === 'processing' && item.payment_status === 'pending' ? styles.pendingPaymentContainer : null,
                    item.order_status === 'processing' && item.payment_status === 'completed' ? styles.preparingContainer : null,
                    item.order_status === 'shipping' && item.payment_status === 'completed' ? styles.shippingContainer : null,
                    item.order_status === 'shipped' && item.payment_status === 'completed' ? styles.shippedContainer : null,
                    item.order_status === 'done' && item.payment_status === 'completed' ? styles.deliveredContainer : null,
                ]}>
                    <Text style={[
                        styles.paidText,
                        item.order_status === 'processing' && item.payment_status === 'pending' ? styles.pendingPaymentText : null,
                        item.order_status === 'processing' && item.payment_status === 'completed' ? styles.preparingText : null,
                        item.order_status === 'shipping' && item.payment_status === 'completed' ? styles.shippingText : null,
                        item.order_status === 'shipped' && item.payment_status === 'completed' ? styles.shippedText : null,
                        item.order_status === 'done' && item.payment_status === 'completed' ? styles.deliveredText : null,
                    ]}>
                        {getStatusText(item.order_status, item.payment_status)}
                    </Text>
                </View>
            )}
    
            <TouchableOpacity
                style={styles.detailsButton}
                onPress={() => toggleOrderDetails(item.order_id)}
            >
                <Text style={styles.detailsButtonText}>
                    {expandedOrderId === item.order_id ? "ซ่อนรายละเอียด" : "ดูรายละเอียด"}
                </Text>
            </TouchableOpacity>
    
            {expandedOrderId === item.order_id && (
                <View style={styles.orderItemsContainer}>
                    {/* แสดงที่อยู่จัดส่ง */}
                    {!(item.order_status === 'processing' && (!item.payment_status || item.payment_status === 'pending')) && orderAddress[item.order_id] && (
                        <View style={styles.addressContainer}>
                            <Text style={styles.addressHeader}>ที่อยู่จัดส่ง</Text>
                            <Text style={styles.addressText}>{orderAddress[item.order_id].full_name}</Text>
                            <Text style={styles.addressText}>{orderAddress[item.order_id].phone_number}</Text>
                            <Text style={styles.addressText}>
                                {orderAddress[item.order_id].address_line}, {orderAddress[item.order_id].subdistrict}, {orderAddress[item.order_id].district}, {orderAddress[item.order_id].city}, {orderAddress[item.order_id].postal_code}
                            </Text>
                        </View>
                    )}
    
                    <Text style={styles.orderItemsHeader}>รายการสินค้า</Text>
    
                    {detailsLoading ? (
                        <ActivityIndicator size="small" color="#007BFF" style={styles.detailsLoading} />
                    ) : !orderDetails[item.order_id] ? (
                        <Text style={styles.noDetailsText}>ไม่พบข้อมูลรายการสินค้า</Text>
                    ) : (
                        orderDetails[item.order_id].map((orderItem, index) => (
                            <View key={index} style={styles.orderItemRow}>
                                <Image
                                    source={{ uri: `${IMAGE_BASE_URL}${orderItem.image}` }}
                                    style={styles.productImage}
                                />
                                <View style={styles.orderItemInfo}>
                                    <Text style={styles.orderItemName}>{orderItem.product_name}</Text>
                                    <Text style={styles.orderItemPrice}>฿ {parseInt(orderItem.price).toLocaleString()}</Text>
                                </View>
                                <Text style={styles.orderItemQuantity}>x{orderItem.quantity}</Text>
                                <Text style={styles.orderItemTotal}>
                                    ฿ {parseInt(orderItem.price * orderItem.quantity).toLocaleString()}
                                </Text>
                            </View>
                        ))
                    )}
    
                    <View style={styles.orderTotalContainer}>
                        <Text style={styles.orderTotalLabel}>ยอดรวมทั้งสิ้น:</Text>
                        <Text style={styles.orderTotalAmount}>฿ {parseInt(item.total_price).toLocaleString()}</Text>
                    </View>
                </View>
            )}
        </View>
    );

    return (
        <View style={styles.container}>
            {!isLoggedIn ? (
                <View style={styles.centerContainer}>
                    <Text style={styles.emptyText}>กรุณาเข้าสู่ระบบเพื่อดูรายการสั่งซื้อ</Text>
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
            ) : orders.length === 0 ? (
                <View style={styles.centerContainer}>
                    <Text style={styles.emptyText}>ไม่มีรายการสั่งซื้อ</Text>
                    <TouchableOpacity 
                        style={styles.shopButton}
                        onPress={() => navigation.navigate('Home')}
                    >
                        <Text style={styles.shopButtonText}>ไปช้อปปิ้งเลย</Text>
                    </TouchableOpacity>
                </View>
            ) : (
                <>
                    <Text style={styles.header}>รายการสั่งซื้อของคุณ</Text>
                    <FlatList
                        data={orders}
                        keyExtractor={item => item.order_id.toString()}
                        renderItem={renderOrderItem}
                        contentContainerStyle={styles.listContainer}
                    />
                </>
            )}
        </View>
    );
};

export default CheckoutScreen;