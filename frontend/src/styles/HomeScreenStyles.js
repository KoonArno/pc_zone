import { StyleSheet } from 'react-native';

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#efebeb',
    padding: 8,
  },
  
  searchBar: {
    marginTop: 30,
    flexDirection: 'row',
    height: 40,
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 20,
    alignItems: 'center',
    paddingHorizontal: 15,
    marginVertical: 10,
    backgroundColor: '#fff',
  },
  
  searchInput: {
    flex: 1,
    height: '100%',
    fontSize: 14,
  },
  
  searchIcon: {
    width: 20,
    height: 20,
    tintColor: '#888',
  },
  
  categoryContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    marginBottom: 15,
    paddingHorizontal: 5,
  },
  
  categoryItem: {
    width: '30%',
    height: 60,
    backgroundColor: '#a9d0f5',
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 10,
  },
  
  categoryIcon: {
    width: 24,
    height: 24,
    marginBottom: 5,
  },
  
  categoryText: {
    fontSize: 12,
    color: 'white',
    fontWeight: 'bold',
  },
  
  productGrid: {
    justifyContent: 'space-between',
  },
  
  productCard: {
    width: '48%',
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#eee',
    borderRadius: 10,
    padding: 8,
    marginBottom: 10,
    height: 190,
    flexDirection: 'column',
  },
  
  productImage: {
    width: '100%',
    height: 100,
    borderRadius: 8,
    marginBottom: 5,
  },
  
  productInfo: {
    flex: 1,
  },
  
  productTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    height: 40,
  },
  
  priceText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1e90ff',
    textAlign: 'left',
    marginTop: 'auto',
    paddingTop: 5,
  },
  
  navBar: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    borderTopWidth: 1,
    borderTopColor: '#eee',
    paddingVertical: 10,
    backgroundColor: 'white',
  },
  
  navItem: {
    alignItems: 'center',
  },
  
  navIcon: {
    width: 24,
    height: 24,
  },
  
  navText: {
    fontSize: 12,
    marginTop: 4,
  },
});

export default styles;