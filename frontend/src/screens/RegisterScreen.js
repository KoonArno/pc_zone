import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert} from 'react-native';
import styles from '../styles/RegisterScreenStyles';
import axios from 'axios';

const API_URL = "http://192.168.1.33/pc_zone/api/auth/Register.php";

const RegisterScreen = ({ navigation }) => {
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  
  // Specific error states for each field
  const [errors, setErrors] = useState({
    username: false,
    email: false
  });
  const [errorMessages, setErrorMessages] = useState({
    username: '',
    email: ''
  });

  const validateInputs = () => {
    if (!username || !email || !password || !confirmPassword) {
      Alert.alert('แจ้งเตือน', 'กรุณากรอกข้อมูลให้ครบทุกช่อง');
      return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      Alert.alert('แจ้งเตือน', 'กรุณากรอกอีเมลให้ถูกต้อง');
      return false;
    }
    
    if (password.length < 6) {
      Alert.alert('แจ้งเตือน', 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
      return false;
    }
    
    if (password !== confirmPassword) {
      Alert.alert('แจ้งเตือน', 'รหัสผ่านไม่ตรงกัน');
      return false;
    }
    
    return true;
  };

  const handleRegister = async () => {
    if (!validateInputs()) return;
  
    // Reset errors before submitting
    setErrors({ username: false, email: false });
    setErrorMessages({ username: '', email: '' });
  
    setIsLoading(true);
  
    try {
      const response = await axios.post(API_URL, { 
        username, 
        email, 
        password 
      });
  
      const responseData = response.data;
      console.log(responseData);
  
      // Check for specific error types in the response
      if (responseData.status === 'error') {
        if (responseData.field === 'username') {
          setErrors(prev => ({ ...prev, username: true }));
          setErrorMessages(prev => ({ ...prev, username: responseData.message }));
        } else if (responseData.field === 'email') {
          setErrors(prev => ({ ...prev, email: true }));
          setErrorMessages(prev => ({ ...prev, email: responseData.message }));
        } else {
          Alert.alert('แจ้งเตือน', responseData.message || 'ไม่สามารถสมัครสมาชิกได้');
        }
        return;
      }
  
      // Success case
      if (responseData.status === 'success') {
        Alert.alert('✅ สำเร็จ', responseData.message || 'สมัครสมาชิกสำเร็จ');
        navigation.navigate('Login');
      }
    } catch (error) {
      Alert.alert('❌ สมัครสมาชิกไม่สำเร็จ', 'เกิดข้อผิดพลาดในการสมัครสมาชิก');
    } finally {
      setIsLoading(false);
    }
  };

  // Clear errors when user types in the input fields
  const handleUsernameChange = (text) => {
    setUsername(text);
    if (errors.username) {
      setErrors(prev => ({ ...prev, username: false }));
      setErrorMessages(prev => ({ ...prev, username: '' }));
    }
  };

  const handleEmailChange = (text) => {
    setEmail(text);
    if (errors.email) {
      setErrors(prev => ({ ...prev, email: false }));
      setErrorMessages(prev => ({ ...prev, email: '' }));
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.appName}>PC ZONE</Text>
        <Text style={styles.welcomeText}>ยินดีต้อนรับสู่การเป็นสมาชิก</Text>
      </View>
      
      <View style={styles.card}>
        <Text style={styles.title}>สมัครสมาชิก</Text>
        
        <View style={styles.inputContainer}>
          <TextInput 
            style={[
              styles.input, 
              errors.username && styles.inputError
            ]} 
            placeholder="ชื่อผู้ใช้" 
            value={username} 
            onChangeText={handleUsernameChange}
            autoCapitalize="none"
          />
          {errors.username && (
            <Text style={styles.errorText}>{errorMessages.username}</Text>
          )}
        </View>
        
        <View style={styles.inputContainer}>
          <TextInput 
            style={[
              styles.input, 
              errors.email && styles.inputError
            ]} 
            placeholder="อีเมล" 
            value={email} 
            onChangeText={handleEmailChange}
            keyboardType="email-address"
            autoCapitalize="none"
          />
          {errors.email && (
            <Text style={styles.errorText}>{errorMessages.email}</Text>
          )}
        </View>
        
        <TextInput 
          style={styles.input} 
          placeholder="รหัสผ่าน" 
          value={password} 
          onChangeText={setPassword}
          secureTextEntry 
        />
        
        <TextInput 
          style={styles.input} 
          placeholder="ยืนยันรหัสผ่าน" 
          value={confirmPassword} 
          onChangeText={setConfirmPassword}
          secureTextEntry 
        />
        
        <TouchableOpacity 
          style={[styles.button, isLoading && styles.buttonDisabled]} 
          onPress={handleRegister}
          disabled={isLoading}
        >
          <Text style={styles.buttonText}>
            {isLoading ? 'กำลังสมัคร...' : 'สมัครสมาชิก'}
          </Text>
        </TouchableOpacity>
        
        <View style={styles.footer}>
          <TouchableOpacity onPress={() => navigation.navigate('Login')}>
            <Text style={styles.linkText}>มีบัญชีอยู่แล้ว? <Text style={styles.linkHighlight}>เข้าสู่ระบบ</Text></Text>
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};

export default RegisterScreen;