import React, { useEffect, useState } from 'react';
import { View, Text, TextInput, FlatList, Image, TouchableOpacity } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import styles from '../styles/HomeScreenStyles';
import { Ionicons } from '@expo/vector-icons';
import { FontAwesome5 } from '@expo/vector-icons';
import Entypo from '@expo/vector-icons/Entypo';
import { SvgXml } from 'react-native-svg'; // นำเข้า SvgXml จาก react-native-svg

// XML ของ SVG สำหรับแผ่นรองเมาส์ - ใช้ SVG ที่ได้รับมา
const mousepadSvgXml = `
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve">
<g>
	<g xmlns="http://www.w3.org/2000/svg">
		<g id="Page-1_28_">
			<g id="_x30_29---Gaming-Mouse-Pad">
				<path id="Shape_168_" d="m34.133 512h443.733c18.852 0 34.134-15.282 34.134-34.133v-324.267c0-18.851-15.282-34.133-34.133-34.133h-213.334v-8.533c0-4.713 3.82-8.533 8.533-8.533h8.533c14.106-.079 25.521-11.494 25.6-25.6 0-14.138-11.462-25.6-25.6-25.6h-51.2c-2.301.006-4.503-.939-6.084-2.611-1.583-1.562-2.466-3.698-2.449-5.922 0-4.713 3.82-8.533 8.533-8.533h8.533c14.138 0 25.6-11.462 25.6-25.6.001-4.714-3.819-8.535-8.532-8.535s-8.533 3.821-8.533 8.533c0 2.263-.899 4.434-2.499 6.034s-3.771 2.499-6.034 2.499h-8.534c-14.138 0-25.6 11.462-25.6 25.6.07 14.109 11.491 25.53 25.6 25.6h51.2c4.713 0 8.533 3.821 8.533 8.533.003 2.298-.941 4.496-2.611 6.076-1.556 1.592-3.696 2.48-5.922 2.458h-8.533c-14.138 0-25.6 11.462-25.6 25.6v8.533h-213.334c-18.851.001-34.133 15.283-34.133 34.134v324.267c0 18.851 15.282 34.133 34.133 34.133zm-17.066-358.4c0-9.426 7.641-17.067 17.067-17.067h213.333v18.637c-10.192 3.603-17.022 13.22-17.067 24.03v8.687c-4.119-.065-8.117 1.39-11.23 4.087l-51.2 44.8c-3.702 3.235-5.829 7.909-5.837 12.826v16.486.137 117.777c0 51.841 42.026 93.867 93.867 93.867s93.867-42.026 93.867-93.867v-117.76-.137c0-5.484 0-10.98 0-16.486-.015-4.907-2.141-9.571-5.837-12.8l-51.2-44.8c-3.107-2.713-7.106-4.184-11.23-4.13v-8.687c-.045-10.81-6.875-20.427-17.067-24.03v-18.637h213.333c9.426 0 17.067 7.641 17.067 17.067v324.267c0 9.426-7.641 17.067-17.067 17.067h-443.733c-9.426 0-17.067-7.641-17.067-17.067v-324.267zm162.133 229.811c13.056 26.3 36.087 43.255 76.8 43.255s63.744-16.956 76.8-43.255v.589c-.081 36.58-25.885 68.055-61.739 75.307-9.941 2.005-20.182 2.005-30.123 0-35.853-7.252-61.657-38.727-61.738-75.307zm76.8 26.189c-71.603 0-76.8-60.442-76.8-159.992l51.2-44.809v93.867c0 14.138 11.462 25.6 25.6 25.6 14.139 0 25.6-11.462 25.6-25.6v-93.866l51.2 44.783c0 99.575-5.197 160.017-76.8 160.017zm-8.533-121.037c5.496 2.094 11.57 2.094 17.067 0v10.103c0 4.713-3.821 8.533-8.533 8.533-4.713 0-8.533-3.82-8.533-8.533v-10.103zm17.066-49.63v25.6c0 4.713-3.821 8.533-8.533 8.533-4.713 0-8.533-3.82-8.533-8.533v-34.133c0-4.713 3.821-8.533 8.533-8.533 4.713 0 8.533 3.82 8.533 8.533zm0-32.563c-5.496-2.094-11.57-2.094-17.067 0v-27.17c0-4.713 3.821-8.533 8.533-8.533 4.713 0 8.533 3.821 8.533 8.533v27.17z" fill="white" data-original="#000000">
				</path>
				<path id="Shape_167_" d="m42.667 196.267c4.713 0 8.533-3.821 8.533-8.533v-17.067h17.067c4.713 0 8.533-3.821 8.533-8.533 0-4.713-3.82-8.533-8.533-8.533h-25.6c-4.713 0-8.533 3.821-8.533 8.533v25.6c-.001 4.712 3.82 8.533 8.533 8.533z" fill="white" data-original="#000000">
				</path>
			</g>
		</g>
	</g>
</g>
</svg>
`;

const API_URL = "http://192.168.1.33/pc_zone/api/products/get_products.php";
const IMAGE_BASE_URL = "http://192.168.1.33/pc_zone/image/";

// ข้อมูลหมวดหมู่
const categories = [
    { id: '1', name: 'คีย์บอร์ด', icon: 'keyboard', iconType: 'Entypo', dbType: 'keyboard' },
    { id: '2', name: 'เมาส์', icon: 'mouse', iconType: 'FontAwesome5', dbType: 'mouse' },
    { id: '3', name: 'มอนิเตอร์', icon: 'desktop-outline', iconType: 'Ionicons', dbType: 'monitor' },
    { id: '4', name: 'ไมโครโฟน', icon: 'mic-outline', iconType: 'Ionicons', dbType: 'mic' },
    { id: '5', name: 'แผ่นรองเมาส์', icon: mousepadSvgXml, iconType: 'SVG', dbType: 'mouse_pad' },
    { id: '6', name: 'หูฟัง', icon: 'headset-outline', iconType: 'Ionicons', dbType: 'headset' },
];

// Mapping Object สำหรับแปลงค่า type จากฐานข้อมูลเป็นชื่อหมวดหมู่
const typeMapping = {
    keyboard: 'คีย์บอร์ด',
    mouse: 'เมาส์',
    monitor: 'มอนิเตอร์',
    mic: 'ไมโครโฟน',
    mousepad: 'แผ่นรองเมาส์',
    headset: 'หูฟัง',
};

const HomeScreen = ({ navigation }) => {
    const [product, setProduct] = useState([]);
    const [search, setSearch] = useState('');
    const [filteredProduct, setFilteredProduct] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState(null);

    useEffect(() => {
        fetchProduct();
    }, []);

    // ฟังก์ชันสำหรับสับเปลี่ยนลำดับรายการสินค้าแบบสุ่ม (Fisher-Yates shuffle algorithm)
    const shuffleArray = (array) => {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]]; // สลับตำแหน่ง
        }
        return shuffled;
    };

    const fetchProduct = async () => {
        try {
            const response = await axios.get(API_URL);
            console.log("API Response:", response.data);

            if (response.data.status === "success" && Array.isArray(response.data.product)) {
                const updatedProduct = response.data.product.map(product => ({
                    ...product,
                    image: product.image ? `${IMAGE_BASE_URL}${product.image}` : "https://via.placeholder.com/100x150",
                    // แปลง type จากฐานข้อมูลเป็นชื่อหมวดหมู่ที่ต้องการแสดงผล
                    displayType: typeMapping[product.type] || product.type
                }));

                // สุ่มลำดับสินค้าก่อนแสดงผล
                const shuffledProducts = shuffleArray(updatedProduct);

                setProduct(shuffledProducts);
                setFilteredProduct(shuffledProducts);
            } else {
                console.warn("No products found");
            }
        } catch (error) {
            console.error("Error fetching Product:", error);
        }
    };

    const handleSearch = () => {
        if (search.trim() === '') {
            setFilteredProduct(product);
        } else {
            const results = product.filter(product =>
                product.product_name.toLowerCase().includes(search.toLowerCase()) ||
                (product.description && product.description.toLowerCase().includes(search.toLowerCase()))
            );
            // สุ่มลำดับผลลัพธ์การค้นหา
            setFilteredProduct(shuffleArray(results));
        }
    };

    const filterByCategory = (categoryId) => {
        // ถ้ากดหมวดหมู่ที่เลือกอยู่แล้ว ให้ยกเลิกการกรองและแสดงสินค้าทั้งหมดแบบสุ่ม
        if (selectedCategory === categoryId) {
            setSelectedCategory(null);
            setFilteredProduct(shuffleArray([...product]));
        } else {
            // ถ้ากดหมวดหมู่ใหม่ ให้กรองและแสดงสินค้าเฉพาะหมวดหมู่นั้น แบบสุ่ม
            setSelectedCategory(categoryId);
            const category = categories.find(cat => cat.id === categoryId);
            const results = product.filter(product =>
                product.type === category.dbType
            );
            setFilteredProduct(shuffleArray(results));
        }
    };

    const onSearchChange = (text) => {
        setSearch(text);
        if (text.trim() === '') {
            // ถ้ามีการเลือกหมวดหมู่อยู่ ให้แสดงเฉพาะสินค้าในหมวดหมู่นั้น แบบสุ่ม
            if (selectedCategory) {
                const category = categories.find(cat => cat.id === selectedCategory);
                const results = product.filter(product =>
                    product.type === category.dbType
                );
                setFilteredProduct(shuffleArray(results));
            } else {
                // ถ้าไม่มีการเลือกหมวดหมู่ ให้แสดงสินค้าทั้งหมด แบบสุ่ม
                setFilteredProduct(shuffleArray([...product]));
            }
        } else {
            handleSearch();
        }
    };

    // ฟังก์ชันสำหรับการแสดงไอคอนตามประเภท
    const renderCategoryIcon = (category) => {
        if (category.iconType === 'SVG') {
            return <SvgXml xml={category.icon} width="24" height="24" />;
        } else if (category.iconType === 'FontAwesome5') {
            return <FontAwesome5 name={category.icon} size={24} color="white" />;
        } else if (category.iconType === 'Entypo') {
            return <Entypo name={category.icon} size={24} color="white" />;
        } else {
            return <Ionicons name={category.icon} size={24} color="white" />;
        }
    };

    return (
        <View style={styles.container}>
            {/* ช่องค้นหาสินค้าแบบใหม่ตามรูปภาพ */}
            <View style={styles.searchBar}>
                <TextInput
                    style={styles.searchInput}
                    placeholder="ค้นหาสินค้า..."
                    value={search}
                    onChangeText={onSearchChange}
                />
                <Ionicons name="search-outline" size={20} color="#888" />
            </View>

            {/* หมวดหมู่สินค้า */}
            <View style={styles.categoryContainer}>
                {categories.map((category) => (
                    <TouchableOpacity
                        key={category.id}
                        style={[
                            styles.categoryItem,
                            selectedCategory === category.id && { backgroundColor: '#74b9ff' }
                        ]}
                        onPress={() => filterByCategory(category.id)}
                    >
                        {renderCategoryIcon(category)}
                        <Text style={styles.categoryText}>{category.name}</Text>
                    </TouchableOpacity>
                ))}
            </View>

            {/* แสดงรายการสินค้า */}
            <FlatList
                data={filteredProduct}
                keyExtractor={(item) => item.product_id.toString()}
                numColumns={2}
                columnWrapperStyle={styles.productGrid}
                renderItem={({ item }) => (
                    <TouchableOpacity
                        style={styles.productCard}
                        onPress={() => navigation.navigate('DetailScreen', { product_id: item.product_id })}
                    >
                        <Image
                            source={{ uri: item.image }}
                            style={styles.productImage}
                            resizeMode="cover"
                        />
                        <View style={styles.productInfo}>
                            <Text
                                style={styles.productTitle}
                                numberOfLines={2}
                                ellipsizeMode="tail"
                            >
                                {item.product_name}
                            </Text>
                        </View>
                        <Text style={styles.priceText}>฿{item.price}</Text>
                    </TouchableOpacity>
                )}
            />
        </View>
    );
};

export default HomeScreen;