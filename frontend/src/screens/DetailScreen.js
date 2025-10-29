import React, { useEffect, useState } from 'react';
import { 
    View, 
    Text, 
    Image, 
    TouchableOpacity, 
    StyleSheet,
    Alert,
    ActivityIndicator,
    Modal,
    StatusBar,
    Dimensions
} from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Ionicons } from '@expo/vector-icons';
import styles from '../styles/DetailScreenStyles';

const API_URL = "http://192.168.1.33/pc_zone/api/products/get_product.php";
const CART_API_URL = "http://192.168.1.33/pc_zone/api/cart/add_to_cart.php";
const BOOKMARK_API_URL = "http://192.168.1.33/pc_zone/api/bookmark/add_to_bookmark.php";
const REMOVE_BOOKMARK_API_URL = "http://192.168.1.33/pc_zone/api/bookmark/remove_bookmark.php";
const CHECK_BOOKMARK_API_URL = "http://192.168.1.33/pc_zone/api/bookmark/check_bookmark.php";
const IMAGE_BASE_URL = "http://192.168.1.33/pc_zone/image/";

const DetailScreen = ({ route, navigation }) => {
    const { product_id } = route.params;  
    const [product, setProduct] = useState(null);
    const [isAddingToCart, setIsAddingToCart] = useState(false);
    const [isBookmarked, setIsBookmarked] = useState(false);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [loading, setLoading] = useState(true);
    const [userId, setUserId] = useState(null);
    const [imageModalVisible, setImageModalVisible] = useState(false);

    useFocusEffect(
        React.useCallback(() => {
            checkLoginStatus();
            fetchProductDetail();
        }, [])
    );

    const checkLoginStatus = async () => {
        try {
            const user = await AsyncStorage.getItem("user");
            if (user !== null) {
                const userData = JSON.parse(user);
                setIsLoggedIn(true);
                setUserId(userData.user_id);
                checkBookmarkStatus(userData.user_id, product_id);
            } else {
                setIsLoggedIn(false);
                setUserId(null);
            }
        } catch (error) {
            console.error("Error checking login status:", error);
        } finally {
            setLoading(false);
        }
    };

    const fetchProductDetail = async () => {
        try {
            const response = await axios.get(`${API_URL}?product_id=${product_id}`);
            if (response.data.status === "success") {
                setProduct(response.data.product);
            } else {
                console.warn("Product not found");
            }
        } catch (error) {
            console.error("Error fetching product details:", error);
            Alert.alert("Error", "Could not load product details");
        }
    };

    const checkBookmarkStatus = async (userId, productId) => {
        try {
            const response = await axios.get(`${CHECK_BOOKMARK_API_URL}?user_id=${userId}&product_id=${productId}`);
            if (response.data.status === "success") {
                setIsBookmarked(response.data.bookmarked);
            }
        } catch (error) {
            console.error("Error checking bookmark status:", error);
        }
    };

    const addToCart = async () => {
        if (!isLoggedIn) {
            Alert.alert(
                "กรุณาเข้าสู่ระบบ", 
                "คุณต้องเข้าสู่ระบบก่อนที่จะเพิ่มสินค้าในรถเข็น",
                [
                    { text: "ยกเลิก", style: "cancel" },
                    { text: "เข้าสู่ระบบ", onPress: () => navigation.navigate('Login') }
                ]
            );
            return;
        }
    
        if (!product || !userId) return;
            
        setIsAddingToCart(true);
        
        try {
            const cartData = {
                user_id: userId,
                product_id: product.product_id,
                quantity: 1
            };
            
            const response = await axios.post(CART_API_URL, cartData);
            
            if (response.data.status === "success") {
                Alert.alert(
                    "Success",
                    "Product added to cart successfully!",
                    [
                        { 
                            text: "Continue Shopping", 
                            style: "cancel" 
                        },
                        { 
                            text: "View Cart", 
                            onPress: () => navigation.navigate('Cart', { refresh: true }) 
                        }
                    ]
                );
            } else {
                Alert.alert("Error", response.data.message || "Failed to add product to cart");
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
            Alert.alert("Error", "Could not add product to cart");
        } finally {
            setIsAddingToCart(false);
        }
    };
    
    const toggleBookmark = async () => {
        if (!isLoggedIn) {
            Alert.alert(
                "กรุณาเข้าสู่ระบบ", 
                "คุณต้องเข้าสู่ระบบก่อนที่จะเพิ่มสินค้าในรายการโปรด",
                [
                    { text: "ยกเลิก", style: "cancel" },
                    { text: "เข้าสู่ระบบ", onPress: () => navigation.navigate('Login') }
                ]
            );
            return;
        }
    
        if (!product || !userId) return;
        
        try {
            let response;
            const bookmarkData = {
                user_id: userId,
                product_id: product.product_id
            };
            
            if (isBookmarked) {
                // Remove bookmark
                response = await axios.post(REMOVE_BOOKMARK_API_URL, bookmarkData);
            } else {
                // Add bookmark
                response = await axios.post(BOOKMARK_API_URL, bookmarkData);
            }
            
            if (response.data.status === "success") {
                // Toggle bookmark state
                setIsBookmarked(!isBookmarked);
                // No alert shown as requested
            } else {
                console.error("Bookmark toggle failed:", response.data.message);
            }
        } catch (error) {
            console.error("Error toggling bookmark:", error);
        }
    };

    const openImageModal = () => {
        setImageModalVisible(true);
    };

    const closeImageModal = () => {
        setImageModalVisible(false);
    };

    if (loading) {
        return (
            <View style={styles.container}>
                <ActivityIndicator size="large" color="#007BFF" />
            </View>
        );
    }

    if (!product) {
        return (
            <View style={styles.container}>
                <Text>Loading...</Text>
            </View>
        );
    }

    let descriptionData;
    try {
        descriptionData = JSON.parse(product.description);
    } catch (error) {
        descriptionData = product.description;
    }

    return (
        <View style={styles.container}>
            <TouchableOpacity activeOpacity={0.9} onPress={openImageModal}>
                <Image 
                    source={{ uri: `${IMAGE_BASE_URL}${product.image}` }} 
                    style={styles.productImage} 
                />
            </TouchableOpacity>

            <View style={styles.detailsContainer}>
                <Text style={styles.productTitle}>
                    {product.product_name}
                </Text>

                <View style={styles.productDescriptionContainer}>
                    {typeof descriptionData === "object" 
                        ? Object.entries(descriptionData).map(([key, value], index) => (
                            <Text key={index} style={styles.productDescription}>
                                {key}: {value}
                            </Text>
                          ))
                        : <Text style={styles.productDescription}>{descriptionData}</Text>
                    }
                </View>
            </View>

            <View style={styles.footer}>
                <Text style={styles.priceText}>
                    ฿{parseInt(product.price).toLocaleString()}
                </Text>

                <TouchableOpacity 
                    style={styles.cartButton}
                    onPress={addToCart}
                    disabled={isAddingToCart}
                >
                    <Text style={styles.cartButtonText}>
                        {isAddingToCart ? 'กำลังเพิ่ม...' : 'เพิ่มไปยังรถเข็น'}
                    </Text>
                </TouchableOpacity>

                <TouchableOpacity 
                    style={styles.heartButton}
                    onPress={toggleBookmark}
                >
                    <Ionicons 
                        name={isLoggedIn && isBookmarked ? "heart" : "heart-outline"} 
                        size={28} 
                        color="#ff4757" 
                    />
                </TouchableOpacity>
            </View>

            {/* Full-screen Image Modal */}
            <Modal
                visible={imageModalVisible}
                transparent={true}
                animationType="fade"
                onRequestClose={closeImageModal}
            >
                <StatusBar backgroundColor="#000" barStyle="light-content" />
                <View style={imageModalStyles.modalContainer}>
                    <TouchableOpacity 
                        style={imageModalStyles.closeButton} 
                        onPress={closeImageModal}
                    >
                        <Ionicons name="close" size={30} color="#fff" />
                    </TouchableOpacity>
                    
                    <Image 
                        source={{ uri: `${IMAGE_BASE_URL}${product?.image}` }}
                        style={imageModalStyles.fullImage}
                        resizeMode="contain"
                    />
                </View>
            </Modal>
        </View>
    );
};

const imageModalStyles = StyleSheet.create({
    modalContainer: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.95)',
        justifyContent: 'center',
        alignItems: 'center',
    },
    fullImage: {
        width: Dimensions.get('window').width,
        height: Dimensions.get('window').height,
    },
    closeButton: {
        position: 'absolute',
        top: 40,
        right: 20,
        zIndex: 10,
        backgroundColor: 'rgba(0,0,0,0.5)',
        borderRadius: 20,
        padding: 8,
    }
});

export default DetailScreen;