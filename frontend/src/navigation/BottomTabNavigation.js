import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import MaterialIcons from '@expo/vector-icons/MaterialIcons';
import HomeScreen from '../screens/HomeScreen';
import CartScreen from '../screens/CartScreen';
import Favorite from '../screens/BookmarkScreen';
import UserScreen from '../screens/ProfileScreen';
import CheckoutScreen from '../screens/CheckoutScreen';

const Tab = createBottomTabNavigator();

function BottomTabNavigator() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ color, size }) => {
          let iconName;
          if (route.name === 'Home') {
            iconName = 'home-outline';
          } else if (route.name === 'Cart') {
            iconName = 'cart-outline';
          } else if (route.name === 'User') {
            iconName = 'person-outline';
          } else if (route.name === 'Favorite') {
            iconName = 'heart-outline';
          }


          if (route.name === 'Checkout') {
            return <MaterialIcons name="payments" size={size} color={color} />;
          }

          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#1e90ff',
        tabBarInactiveTintColor: 'gray',
        tabBarStyle: { backgroundColor: 'white', paddingBottom: 5, height: 60 },
      })}
    >
      <Tab.Screen 
        name="Home" 
        component={HomeScreen} 
        options={{ headerShown: false }} 
      />
      <Tab.Screen name="Cart" component={CartScreen} options={{ headerShown: false }} />
      <Tab.Screen name="Checkout" component={CheckoutScreen} options={{ headerShown: false }} />
      <Tab.Screen name="Favorite" component={Favorite} options={{ headerShown: false }}/>
      <Tab.Screen name="User" component={UserScreen} options={{ headerShown: false }}/>
    </Tab.Navigator>
  );
}

export default BottomTabNavigator;
