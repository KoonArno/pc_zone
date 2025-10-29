import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  Modal,
  TextInput
} from "react-native";
import { FontAwesome } from "@expo/vector-icons";
import AsyncStorage from "@react-native-async-storage/async-storage";
import axios from "axios";
import styles from "../styles/ProfileScreenStyles";

const API_URL = "http://192.168.1.33/pc_zone/api/address/get_address.php";
const UPDATE_ADDRESS_URL =
  "http://192.168.1.33/pc_zone/api/address/update_address.php";
const DELETE_ADDRESS_URL =
  "http://192.168.1.33/pc_zone/api/address/delete_address.php";
const SET_DEFAULT_ADDRESS_URL =
  "http://192.168.1.33/pc_zone/api/address/set_default_address.php";
const UPDATE_PROFILE_URL =
  "http://192.168.1.33/pc_zone/api/user/update_profile.php";

const ProfileScreen = ({ navigation }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [loading, setLoading] = useState(false);
  const [userData, setUserData] = useState(null);
  const [addresses, setAddresses] = useState([]);
  const [editModalVisible, setEditModalVisible] = useState(false);
  const [currentAddress, setCurrentAddress] = useState(null);
  const [editProfileModalVisible, setEditProfileModalVisible] = useState(false);
  const [showAllAddresses, setShowAllAddresses] = useState(false);
  const [profileData, setProfileData] = useState({
    username: "",
    email: ""
  });

  const checkLoginStatus = async () => {
    try {
      const userString = await AsyncStorage.getItem("user");
      if (userString !== null) {
        const user = JSON.parse(userString);
        setIsLoggedIn(true);
        setUserData(user);
        setProfileData({ username: user.username, email: user.email });
        fetchUserData(user.user_id);
      } else {
        setIsLoggedIn(false);
        setUserData(null);
      }
    } catch (error) {
      console.error("Error checking login status:", error);
    }
  };

  const fetchUserData = async (userId) => {
    setLoading(true);
    try {
      const response = await axios.get(`${API_URL}?user_id=${userId}`);
      if (response.data.status === "success") {
        setUserData((prevState) => ({
          ...prevState,
          username: response.data.user.username,
          email: response.data.user.email
        }));
        setAddresses(response.data.addresses || []);
      } else {
        console.error("Error fetching user data:", response.data.message);
      }
    } catch (error) {
      console.error("Error fetching user data:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    checkLoginStatus();
    const unsubscribe = navigation.addListener("focus", () => {
      checkLoginStatus();
    });
    return unsubscribe;
  }, [navigation]);

  const handleLogout = async () => {
    try {
      await AsyncStorage.removeItem("user");
      Alert.alert("Logout", "ออกจากระบบเรียบร้อย");
      setIsLoggedIn(false);
      setUserData(null);
      setAddresses([]);
      navigation.navigate("Login");
    } catch (error) {
      console.error("Error during logout:", error);
    }
  };

  const handleAddAddress = () => {
    navigation.navigate("AddAddress");
  };

  const handleEditAddress = (address) => {
    setCurrentAddress(address);
    setEditModalVisible(true);
  };

  const handleUpdateAddress = async (updatedAddress) => {
    try {
      const dataToSend = { ...updatedAddress, user_id: userData.user_id };
      const response = await axios.post(UPDATE_ADDRESS_URL, dataToSend);
      if (response.data.status === "success") {
        fetchUserData(userData.user_id);
        setEditModalVisible(false);
      } else {
        Alert.alert("Error", response.data.message);
      }
    } catch (error) {
      console.error("Error updating address:", error);
    }
  };

  const handleDeleteAddress = async (addressId) => {
    Alert.alert("ยืนยันการลบ", "คุณแน่ใจหรือไม่ว่าต้องการลบที่อยู่นี้?", [
      {
        text: "ยกเลิก",
        style: "cancel"
      },
      {
        text: "ลบ",
        onPress: async () => {
          try {
            const response = await axios.post(DELETE_ADDRESS_URL, {
              address_id: addressId,
              user_id: userData.user_id
            });
            if (response.data.status === "success") {
              fetchUserData(userData.user_id);
              Alert.alert("สำเร็จ", "ลบที่อยู่เรียบร้อยแล้ว");
            } else {
              Alert.alert("Error", response.data.message);
            }
          } catch (error) {
            console.error("Error deleting address:", error);
          }
        }
      }
    ]);
  };

  const handleSetDefaultAddress = async (addressId) => {
    Alert.alert(
      "ยืนยันการตั้งค่า",
      "คุณแน่ใจหรือไม่ว่าต้องการตั้งที่อยู่นี้เป็นที่อยู่หลัก?",
      [
        {
          text: "ยกเลิก",
          style: "cancel"
        },
        {
          text: "ตั้งค่า",
          onPress: async () => {
            try {
              const response = await axios.post(SET_DEFAULT_ADDRESS_URL, {
                address_id: addressId,
                user_id: userData.user_id
              });
              if (response.data.status === "success") {
                fetchUserData(userData.user_id);
                Alert.alert("สำเร็จ", "ตั้งที่อยู่หลักเรียบร้อยแล้ว");
                // Reset the dropdown state when changing default address
                setShowAllAddresses(false);
              } else {
                Alert.alert("Error", response.data.message);
              }
            } catch (error) {
              console.error("Error setting default address:", error);
            }
          }
        }
      ]
    );
  };

  const handleEditProfile = () => {
    setEditProfileModalVisible(true);
  };

  const handleUpdateProfile = async () => {
    try {
      const response = await axios.post(UPDATE_PROFILE_URL, {
        user_id: userData.user_id,
        username: profileData.username,
        email: profileData.email
      });
      if (response.data.status === "success") {
        setUserData((prevState) => ({
          ...prevState,
          username: profileData.username,
          email: profileData.email
        }));
        setEditProfileModalVisible(false);
      } else {
        Alert.alert("Error", response.data.message);
      }
    } catch (error) {
      console.error("Error updating profile:", error);
    }
  };

  const toggleShowAllAddresses = () => {
    setShowAllAddresses(!showAllAddresses);
  };

  // Get default address and other addresses
  const defaultAddress = addresses.find(addr => Number(addr.is_default) === 0);
  const otherAddresses = addresses.filter(addr => Number(addr.is_default) === 1);
  
  // Determine if we have addresses to display
  const hasAddresses = addresses.length > 0;
  const hasMultipleAddresses = addresses.length > 1;

  const renderAddressCard = (address) => {
    const isDefault = Number(address.is_default) === 0;
    return (
      <View 
        key={address.address_id} 
        style={[
          styles.addressCard, 
          isDefault && styles.defaultAddressCard
        ]}
      >
        {isDefault && (
          <View style={styles.defaultAddressBadge}>
            <FontAwesome name="check-circle" size={14} color="#FFFFFF" style={styles.defaultIcon} />
            <Text style={styles.defaultAddressText}>ที่อยู่หลัก</Text>
          </View>
        )}
        <View style={styles.addressHeader}>
          <Text style={styles.addressName}>{address.full_name}</Text>
          <TouchableOpacity 
            style={styles.editAddressButton} 
            onPress={() => handleEditAddress(address)}
          >
            <FontAwesome name="pencil" size={14} color="#FFFFFF" />
          </TouchableOpacity>
        </View>
        <Text style={styles.phoneNumber}>
          <FontAwesome name="phone" size={14} color="#666666" style={styles.phoneIcon} /> {address.phone_number}
        </Text>
        <Text style={styles.addressText}>
          <FontAwesome name="map-marker" size={14} color="#666666" style={styles.locationIcon} /> {address.address_line}, {address.district}, {address.subdistrict}, {address.city}, {address.postal_code}, {address.country}
        </Text>
        <View style={styles.addressActions}>
          <TouchableOpacity 
            style={styles.deleteButton}
            onPress={() => handleDeleteAddress(address.address_id)}
          >
            <FontAwesome name="trash" size={14} color="#FFFFFF" style={styles.actionIcon} />
            <Text style={styles.deleteText}>ลบ</Text>
          </TouchableOpacity>
          
          {/* แสดงปุ่ม "ตั้งเป็นที่อยู่หลัก" เฉพาะที่อยู่ที่ไม่ใช่ที่อยู่หลัก */}
          {Number(address.is_default) === 1 && (
            <TouchableOpacity 
              style={styles.defaultButton}
              onPress={() => handleSetDefaultAddress(address.address_id)}
            >
              <FontAwesome name="star" size={14} color="#FFFFFF" style={styles.actionIcon} />
              <Text style={styles.defaultText}>ตั้งเป็นที่อยู่หลัก</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {isLoggedIn ? (
        <ScrollView contentContainerStyle={styles.scrollContainer}>
          <View style={styles.profileContainer}>
            <View style={styles.profileImageWrapper}>
              <FontAwesome name="user-circle-o" size={80} color="#79B4E3" />
              <TouchableOpacity style={styles.addIcon}>
                <FontAwesome name="plus-circle" size={20} color="#79B4E3" />
              </TouchableOpacity>
            </View>
            <Text style={styles.profileName}>
              {userData?.username || "ชื่อโปรไฟล์"}
            </Text>
            <Text style={styles.profileEmail}>
              {userData?.email || "อีเมล"}
            </Text>

            <TouchableOpacity
              style={styles.editProfileButton}
              onPress={handleEditProfile}
            >
              <Text style={styles.editProfileText}>แก้ไขโปรไฟล์</Text>
            </TouchableOpacity>

            {/* ส่วนแสดงที่อยู่ */}
            <View style={styles.addressesContainer}>
              <View style={styles.sectionHeader}>
                <Text style={styles.sectionTitle}>ที่อยู่ของฉัน</Text>
                <TouchableOpacity 
                  style={styles.addAddressButton} 
                  onPress={handleAddAddress}
                >
                  <FontAwesome name="plus" size={16} color="#FFFFFF" />
                  <Text style={styles.addAddressText}>เพิ่มที่อยู่</Text>
                </TouchableOpacity>
              </View>

              {loading ? (
                <ActivityIndicator size="small" color="#007BFF" />
              ) : hasAddresses ? (
                <View>
                  {/* แสดงที่อยู่หลัก */}
                  {defaultAddress && renderAddressCard(defaultAddress)}
                  
                  {/* ปุ่มแสดงที่อยู่ทั้งหมด */}
                  {hasMultipleAddresses && (
                    <TouchableOpacity 
                      style={styles.showAllAddressesButton} 
                      onPress={toggleShowAllAddresses}
                    >
                      <Text style={styles.showAllAddressesText}>
                        {showAllAddresses ? "ซ่อนที่อยู่อื่นๆ" : `แสดงที่อยู่อื่นๆ (${otherAddresses.length})`}
                      </Text>
                      <FontAwesome 
                        name={showAllAddresses ? "chevron-up" : "chevron-down"} 
                        size={14} 
                        color="#6772E5" 
                      />
                    </TouchableOpacity>
                  )}
                  
                  {/* แสดงที่อยู่อื่นๆ เมื่อกดปุ่ม */}
                  {showAllAddresses && otherAddresses.map(address => renderAddressCard(address))}
                </View>
              ) : (
                <View style={styles.noAddressContainer}>
                  <FontAwesome name="map-o" size={40} color="#CCCCCC" />
                  <Text style={styles.noAddressText}>ยังไม่มีที่อยู่ กรุณาเพิ่มที่อยู่</Text>
                  <TouchableOpacity 
                    style={styles.addFirstAddressButton} 
                    onPress={handleAddAddress}
                  >
                    <Text style={styles.addFirstAddressText}>เพิ่มที่อยู่แรกของคุณ</Text>
                  </TouchableOpacity>
                </View>
              )}
            </View>

            <TouchableOpacity
              style={styles.logoutButton}
              onPress={handleLogout}
            >
              <FontAwesome name="sign-out" size={16} color="#FFFFFF" style={styles.logoutIcon} />
              <Text style={styles.logoutText}>ออกจากระบบ</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      ) : (
        <View style={styles.loginContainer}>
          <FontAwesome name="user-circle-o" size={80} color="#79B4E3" style={styles.loginIcon} />
          <Text style={styles.loginMessage}>กรุณาเข้าสู่ระบบเพื่อใช้งาน</Text>
          <TouchableOpacity
            style={styles.loginButton}
            onPress={() => navigation.navigate("Login")}
          >
            <FontAwesome name="sign-in" size={16} color="#FFFFFF" style={styles.buttonIcon} />
            <Text style={styles.loginText}>เข้าสู่ระบบ</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.registerButton}
            onPress={() => navigation.navigate("Register")}
          >
            <FontAwesome name="user-plus" size={16} color="#6772E5" style={styles.buttonIcon} />
            <Text style={styles.registerText}>สมัครสมาชิก</Text>
          </TouchableOpacity>
        </View>
      )}

      <Modal
        animationType="slide"
        transparent={true}
        visible={editModalVisible}
        onRequestClose={() => setEditModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>แก้ไขที่อยู่</Text>
            <TextInput
              style={styles.input}
              placeholder="ชื่อ-นามสกุล"
              value={currentAddress?.full_name || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, full_name: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="เบอร์โทรศัพท์"
              value={currentAddress?.phone_number || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, phone_number: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="ที่อยู่"
              value={currentAddress?.address_line || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, address_line: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="เขต"
              value={currentAddress?.district || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, district: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="แขวง"
              value={currentAddress?.subdistrict || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, subdistrict: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="จังหวัด"
              value={currentAddress?.city || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, city: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="รหัสไปรษณีย์"
              value={currentAddress?.postal_code || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, postal_code: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="ประเทศ"
              value={currentAddress?.country || ""}
              onChangeText={(text) =>
                setCurrentAddress({ ...currentAddress, country: text })
              }
            />
            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.cancelButton}
                onPress={() => setEditModalVisible(false)}
              >
                <Text style={styles.cancelText}>ยกเลิก</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.saveButton}
                onPress={() => handleUpdateAddress(currentAddress)}
              >
                <Text style={styles.saveText}>บันทึก</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      <Modal
        animationType="slide"
        transparent={true}
        visible={editProfileModalVisible}
        onRequestClose={() => setEditProfileModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>แก้ไขโปรไฟล์</Text>
            <TextInput
              style={styles.input}
              placeholder="ชื่อผู้ใช้"
              value={profileData.username}
              onChangeText={(text) =>
                setProfileData({ ...profileData, username: text })
              }
            />
            <TextInput
              style={styles.input}
              placeholder="อีเมล"
              value={profileData.email}
              onChangeText={(text) =>
                setProfileData({ ...profileData, email: text })
              }
            />
            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.cancelButton}
                onPress={() => setEditProfileModalVisible(false)}
              >
                <Text style={styles.cancelText}>ยกเลิก</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.saveButton}
                onPress={handleUpdateProfile}
              >
                <Text style={styles.saveText}>บันทึก</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
};

export default ProfileScreen;