import api from './index'

export const getBookings = () => api.get('/bookings')
export const createBooking = (bookingData) => api.post('/bookings', bookingData)
