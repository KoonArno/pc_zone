import React, { useState } from 'react';
import { 
    View, 
    Text, 
    FlatList, 
    Image, 
    TouchableOpacity, 
    Alert,
    ActivityIndicator,
    RefreshControl,
    SafeAreaView
} from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import styles from '../styles/BookmarkScreenStyles';

const API_URL = "http://192.168.1.33/pc_zone/api/bookmark/get_bookmarks.php";
const REMOVE_BOOKMARK_API_URL = "http://192.168.1.33/pc_zone/api/bookmark/remove_bookmark.php";
const IMAGE_BASE_URL = "http://192.168.1.33/pc_zone/image/";

const BookmarkScreen = ({ navigation }) => {
    const [bookmarks, setBookmarks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [userId, setUserId] = useState(null);
    const [isLoggedIn, setIsLoggedIn] = useState(false);

    useFocusEffect(
        React.useCallback(() => {
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
                fetchBookmarks(userData.user_id);
            } else {
                setIsLoggedIn(false);
                setUserId(null);
                setBookmarks([]);
            }
        } catch (error) {
            console.error("Error checking login status:", error);
        } finally {
            setLoading(false);
        }
    };

    const fetchBookmarks = async (userId) => {
        try {
            setRefreshing(true);
            const response = await axios.get(`${API_URL}?user_id=${userId}`);
            
            if (response.data.status === "success") {
                setBookmarks(response.data.bookmarks);
            } else {
                console.error("Error fetching bookmarks:", response.data.message);
            }
        } catch (error) {
            console.error("Error fetching bookmarks:", error);
            Alert.alert("Error", "Could not load bookmarks");
        } finally {
            setRefreshing(false);
            setLoading(false);
        }
    };

    const removeBookmark = async (productId) => {
        try {
            const bookmarkData = {
                user_id: userId,
                product_id: productId
            };
            
            const response = await axios.post(REMOVE_BOOKMARK_API_URL, bookmarkData);
            
            if (response.data.status === "success") {
                setBookmarks(prevBookmarks => 
                    prevBookmarks.filter(item => item.product_id !== productId)
                );
                // Show success feedback
                Alert.alert("สำเร็จ", "นำออกจากรายการโปรดแล้ว");
            } else {
                console.error("Failed to remove bookmark:", response.data.message);
            }
        } catch (error) {
            console.error("Error removing bookmark:", error);
            Alert.alert("Error", "Could not remove product from bookmarks");
        }
    };

    const onRefresh = () => {
        if (userId) {
            fetchBookmarks(userId);
        }
    };

    const navigateToDetail = (productId) => {
        navigation.navigate('DetailScreen', { product_id: productId });
    };

    const renderBookmarkItem = ({ item }) => {
        const formattedPrice = parseInt(item.price).toLocaleString();
        
        return (
            <TouchableOpacity 
                style={styles.productItem}
                onPress={() => navigateToDetail(item.product_id)}
                activeOpacity={0.7}
            >
                <View style={styles.imageContainer}>
                    <Image 
                        source={{ uri: `${IMAGE_BASE_URL}${item.image}` }} 
                        style={styles.productImage}
                        resizeMode="cover"
                    />
                </View>
                
                <View style={styles.productInfo}>
                    <Text style={styles.productName} numberOfLines={2}>
                        {item.product_name}
                    </Text>
                    <Text style={styles.productPrice}>฿{formattedPrice}</Text>
                </View>
                
                <TouchableOpacity 
                    style={styles.removeButton}
                    onPress={() => 
                        Alert.alert(
                            "ยืนยันการลบ",
                            "คุณต้องการนำสินค้านี้ออกจากรายการโปรดใช่หรือไม่?",
                            [
                                { text: "ยกเลิก", style: "cancel" },
                                { text: "ยืนยัน", onPress: () => removeBookmark(item.product_id) }
                            ]
                        )
                    }
                >
                    <View style={styles.removeButtonInner}>
                        <Ionicons name="heart-dislike" size={22} color="#fff" />
                    </View>
                </TouchableOpacity>
            </TouchableOpacity>
        );
    };

    if (loading) {
        return (
            <SafeAreaView style={styles.container}>
                <View style={styles.centerContainer}>
                    <ActivityIndicator size="large" color="#4a6ee0" />
                    <Text style={styles.loadingText}>กำลังโหลด...</Text>
                </View>
            </SafeAreaView>
        );
    }

    if (!isLoggedIn) {
        return (
            <SafeAreaView style={styles.container}>
                <View style={styles.headerGradient}>
                    <LinearGradient
                        colors={['#4a6ee0', '#6a8ff7']}
                        style={styles.gradientHeader}
                    >
                        <Text style={styles.headerTitleLight}>รายการโปรด</Text>
                    </LinearGradient>
                </View>
                
                <View style={styles.centerContainer}>
                    <View style={styles.emptyStateIcon}>
                        <Ionicons name="person-circle-outline" size={80} color="#adbce6" />
                    </View>
                    <Text style={styles.emptyTitle}>ยังไม่ได้เข้าสู่ระบบ</Text>
                    <Text style={styles.emptyText}>กรุณาเข้าสู่ระบบเพื่อดูรายการโปรดของคุณ</Text>
                    <TouchableOpacity 
                        style={styles.loginButton}
                        onPress={() => navigation.navigate('Login')}
                    >
                        <LinearGradient
                            colors={['#4a6ee0', '#6a8ff7']}
                            style={styles.gradientButton}
                        >
                            <Text style={styles.loginButtonText}>เข้าสู่ระบบ</Text>
                            <Ionicons name="arrow-forward" size={20} color="#fff" style={styles.buttonIcon} />
                        </LinearGradient>
                    </TouchableOpacity>
                </View>
            </SafeAreaView>
        );
    }

    return (
        <SafeAreaView style={styles.container}>
            <View style={styles.headerGradient}>
                <LinearGradient
                    colors={['#4a6ee0', '#6a8ff7']}
                    style={styles.gradientHeader}
                >
                    <Text style={styles.headerTitleLight}>รายการโปรด</Text>
                    <Text style={styles.headerSubtitle}>
                        {bookmarks.length > 0 
                            ? `${bookmarks.length} รายการ` 
                            : 'ไม่มีรายการ'}
                    </Text>
                </LinearGradient>
            </View>
            
            {bookmarks.length === 0 ? (
                <View style={styles.centerContainer}>
                    <View style={styles.emptyStateIcon}>
                        <Ionicons name="heart" size={80} color="#e6adbc" />
                    </View>
                    <Text style={styles.emptyTitle}>ไม่มีสินค้าในรายการโปรด</Text>
                    <Text style={styles.emptyText}>คุณสามารถเพิ่มสินค้าที่สนใจได้จากหน้ารายละเอียดสินค้า</Text>
                    <TouchableOpacity 
                        style={styles.shopButton}
                        onPress={() => navigation.navigate('Home')}
                    >
                        <LinearGradient
                            colors={['#4a6ee0', '#6a8ff7']}
                            style={styles.gradientButton}
                        >
                            <Text style={styles.shopButtonText}>ค้นหาสินค้า</Text>
                            <Ionicons name="search" size={20} color="#fff" style={styles.buttonIcon} />
                        </LinearGradient>
                    </TouchableOpacity>
                </View>
            ) : (
                <FlatList
                    data={bookmarks}
                    renderItem={renderBookmarkItem}
                    keyExtractor={item => item.bookmark_id.toString()}
                    contentContainerStyle={styles.listContainer}
                    refreshControl={
                        <RefreshControl
                            refreshing={refreshing}
                            onRefresh={onRefresh}
                            colors={["#4a6ee0"]}
                        />
                    }
                    showsVerticalScrollIndicator={false}
                />
            )}
        </SafeAreaView>
    );
};

export default BookmarkScreen;