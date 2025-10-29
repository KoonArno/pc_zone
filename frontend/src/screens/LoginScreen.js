import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';
import styles from '../styles/LoginScreenStyles';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_URL = "http://192.168.1.33/pc_zone/api/auth/login.php";

const LoginScreen = ({ navigation }) => {
  const [identifier, setIdentifier] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
  useEffect(() => {
    const checkLoginStatus = async () => {
      const user = await AsyncStorage.getItem("user");
      if (user) {
        navigation.replace("Main");
      }
    };
    checkLoginStatus();
  }, []);

  const validateInputs = () => {
    if (!identifier || !password) {
      Alert.alert('แจ้งเตือน', 'กรุณากรอกชื่อผู้ใช้หรืออีเมล และรหัสผ่าน');
      return false;
    }
    
    return true;
  };

  const handleLogin = async () => {
    if (!validateInputs()) return;
    
    setIsLoading(true);
    
    try {
      const response = await axios.post(API_URL, {
        identifier: identifier,
        password: password
      });
      
      console.log(response.data);
      
      if (response.data.status === "success") {
        // บันทึกข้อมูลผู้ใช้ด้วย AsyncStorage
        await AsyncStorage.setItem("user", JSON.stringify(response.data.user));
        
        Alert.alert('✅ สำเร็จ', 'ยินดีต้อนรับ ' + response.data.user.username);
        
        // นำทางไปยังหน้า Main (BottomTabNavigator)
        navigation.replace('Main');
      } else {
        Alert.alert('❌ ผิดพลาด', response.data.message || 'ชื่อผู้ใช้/อีเมล หรือรหัสผ่านไม่ถูกต้อง');
      }
    } catch (error) {
      console.error("Login Error:", error);
      Alert.alert(
        '❌ เข้าสู่ระบบไม่สำเร็จ', 
        error.response?.data?.message || 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
      );
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.appName}>PC ZONE</Text>
        <Text style={styles.welcomeText}>ยินดีต้อนรับกลับมา</Text>
      </View>
      
      <View style={styles.formContainer}>
        <Text style={styles.title}>เข้าสู่ระบบ</Text>
        
        <TextInput 
          style={styles.input} 
          placeholder="Username or Email" 
          value={identifier} 
          onChangeText={setIdentifier} 
          autoCapitalize="none"
        />
        
        <TextInput 
          style={styles.input} 
          placeholder="Password" 
          value={password} 
          onChangeText={setPassword} 
          secureTextEntry 
        />
        
        <TouchableOpacity style={styles.forgotPassword}>
          <Text style={styles.forgotPasswordText}>ลืมรหัสผ่าน?</Text>
        </TouchableOpacity>
        
        <TouchableOpacity 
          style={[styles.button, isLoading && styles.buttonDisabled]} 
          onPress={handleLogin}
          disabled={isLoading}
        >
          <Text style={styles.buttonText}>
            {isLoading ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ'}
          </Text>
        </TouchableOpacity>
        
        <View style={styles.divider}>
          <View style={styles.dividerLine} />
          <Text style={styles.dividerText}>หรือ</Text>
          <View style={styles.dividerLine} />
        </View>
        
        <View style={styles.footer}>
          <TouchableOpacity onPress={() => navigation.navigate('Register')}>
            <Text style={styles.linkText}>ยังไม่มีบัญชีผู้ใช้? <Text style={styles.linkHighlight}>สมัครที่นี่</Text></Text>
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};

export default LoginScreen;