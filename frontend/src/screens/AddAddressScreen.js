import React, { useState, useEffect } from "react";
import { View, Text, TextInput, TouchableOpacity, Alert, ScrollView, ActivityIndicator, Keyboard } from "react-native";
import { MaterialIcons, Ionicons } from "@expo/vector-icons";
import AsyncStorage from "@react-native-async-storage/async-storage";
import axios from "axios";
import styles from "../styles/AddAddressScreenStyles";

const ADD_ADDRESS_URL = "http://192.168.1.33/pc_zone/api/address/add_address.php";

const AddAddressScreen = ({ navigation }) => {
  const [loading, setLoading] = useState(false);
  const [userData, setUserData] = useState(null);
  const [focusedField, setFocusedField] = useState(null);
  const [addressData, setAddressData] = useState({
    full_name: "",
    phone_number: "",
    address_line: "",
    district: "",
    subdistrict: "",
    city: "",
    postal_code: "",
    country: "ไทย",
    is_default: 1
  });
  const [errors, setErrors] = useState({});

  useEffect(() => {
    checkLoginStatus();
  }, []);

  const checkLoginStatus = async () => {
    try {
      const userString = await AsyncStorage.getItem("user");
      if (userString !== null) {
        const user = JSON.parse(userString);
        setUserData(user);
        // Pre-fill name if available
        if (user.full_name) {
          setAddressData(prev => ({
            ...prev,
            full_name: user.full_name
          }));
        }
      } else {
        Alert.alert("ไม่พบข้อมูลผู้ใช้", "กรุณาเข้าสู่ระบบก่อนเพิ่มที่อยู่");
        navigation.navigate("Login");
      }
    } catch (error) {
      console.error("Error checking login status:", error);
    }
  };

  const validateInputs = () => {
    let isValid = true;
    let newErrors = {};
    
    if (!addressData.full_name.trim()) {
      newErrors.full_name = "กรุณากรอกชื่อเต็ม";
      isValid = false;
    }
    
    if (!addressData.phone_number.trim()) {
      newErrors.phone_number = "กรุณากรอกเบอร์โทรศัพท์";
      isValid = false;
    } else if (!/^\d{9,10}$/.test(addressData.phone_number.trim())) {
      newErrors.phone_number = "เบอร์โทรศัพท์ไม่ถูกต้อง";
      isValid = false;
    }
    
    if (!addressData.address_line.trim()) {
      newErrors.address_line = "กรุณากรอกที่อยู่";
      isValid = false;
    }
    
    if (!addressData.district.trim()) {
      newErrors.district = "กรุณากรอกเขต";
      isValid = false;
    }
    
    if (!addressData.subdistrict.trim()) {
      newErrors.subdistrict = "กรุณากรอกแขวง";
      isValid = false;
    }
    
    if (!addressData.city.trim()) {
      newErrors.city = "กรุณากรอกจังหวัด";
      isValid = false;
    }
    
    if (!addressData.postal_code.trim()) {
      newErrors.postal_code = "กรุณากรอกรหัสไปรษณีย์";
      isValid = false;
    } else if (!/^\d{5}$/.test(addressData.postal_code.trim())) {
      newErrors.postal_code = "รหัสไปรษณีย์ไม่ถูกต้อง";
      isValid = false;
    }
    
    setErrors(newErrors);
    return isValid;
  };

  const handleAddAddress = async () => {
    Keyboard.dismiss();
    
    if (!validateInputs()) {
      Alert.alert("ข้อมูลไม่ถูกต้อง", "กรุณาตรวจสอบข้อมูลที่คุณกรอก");
      return;
    }

    setLoading(true);
    try {
      const dataToSend = {
        ...addressData,
        user_id: userData.user_id
      };

      const response = await axios.post(ADD_ADDRESS_URL, dataToSend);
      if (response.data.status === "success") {
        Alert.alert("สำเร็จ", "เพิ่มที่อยู่เรียบร้อยแล้ว");
        navigation.goBack();
      } else {
        Alert.alert("ผิดพลาด", response.data.message || "ไม่สามารถเพิ่มที่อยู่ได้");
      }
    } catch (error) {
      console.error("Error adding address:", error);
      Alert.alert("ผิดพลาด", "ไม่สามารถเพิ่มที่อยู่ได้ โปรดลองอีกครั้ง");
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (field, value) => {
    setAddressData({
      ...addressData,
      [field]: value
    });
    
    // Clear error when typing
    if (errors[field]) {
      setErrors({
        ...errors,
        [field]: null
      });
    }
  };

  const renderInputField = (label, field, placeholder, keyboardType = "default", multiline = false, icon = null) => {
    return (
      <View style={styles.inputContainer}>
        <Text style={styles.label}>
          {label}
          {field !== "country" && <Text style={styles.requiredLabel}>*</Text>}
        </Text>
        <TextInput
          style={[
            styles.input, 
            focusedField === field && styles.inputFocused,
            multiline && { height: 80, textAlignVertical: 'top' },
            errors[field] && { borderColor: "#e53e3e" }
          ]}
          placeholder={placeholder}
          keyboardType={keyboardType}
          value={addressData[field]}
          onChangeText={(text) => handleInputChange(field, text)}
          onFocus={() => setFocusedField(field)}
          onBlur={() => setFocusedField(null)}
          multiline={multiline}
        />
        {icon && <MaterialIcons name={icon} size={20} style={styles.inputIcon} />}
        {errors[field] && <Text style={{ color: "#e53e3e", fontSize: 12, marginTop: 4 }}>{errors[field]}</Text>}
      </View>
    );
  };

  return (
    <ScrollView style={styles.container} keyboardShouldPersistTaps="handled">
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={22} color="#3182ce" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>เพิ่มที่อยู่ใหม่</Text>
        <View style={styles.placeholder}></View>
      </View>

      <View style={styles.formContainer}>
        <Text style={styles.sectionTitle}>ข้อมูลผู้รับ</Text>
        
        {renderInputField("ชื่อเต็ม", "full_name", "ชื่อ-นามสกุล", "default", false, "person")}
        {renderInputField("เบอร์โทรศัพท์", "phone_number", "เบอร์โทรศัพท์", "phone-pad", false, "phone")}
        
        <Text style={styles.sectionTitle}>ข้อมูลที่อยู่</Text>
        
        {renderInputField("ที่อยู่", "address_line", "บ้านเลขที่ ถนน ซอย", "default", true, "home")}
        {renderInputField("แขวง", "subdistrict", "แขวง")}
        {renderInputField("เขต", "district", "เขต")}
        {renderInputField("จังหวัด", "city", "จังหวัด")}
        {renderInputField("รหัสไปรษณีย์", "postal_code", "รหัสไปรษณีย์", "numeric")}
        {renderInputField("ประเทศ", "country", "ประเทศ")}

        <View style={styles.defaultContainer}>
          <View>
            <Text style={styles.defaultText}>ตั้งเป็นที่อยู่หลัก</Text>
            <Text style={{ fontSize: 12, color: "#4a5568" }}>ใช้เป็นที่อยู่เริ่มต้นเมื่อสั่งซื้อ</Text>
          </View>
          <TouchableOpacity 
            style={[styles.checkBox, addressData.is_default === 0 && styles.checked]}
            onPress={() => handleInputChange("is_default", addressData.is_default === 1 ? 0 : 1)}
          >
            {addressData.is_default === 0 && <Ionicons name="checkmark" size={18} color="#fff" />}
          </TouchableOpacity>
        </View>

        <TouchableOpacity 
          style={styles.submitButton} 
          onPress={handleAddAddress}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Text style={styles.submitText}>บันทึกที่อยู่</Text>
          )}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
};

export default AddAddressScreen;