import api from './index'

export const getBookings = () => api.get('/bookings')
export const createBooking = (bookingData) => api.post('/bookings', bookingData)
export const updateBookingStatus = (bookingId, data) => api.put(`/admin/bookings/${bookingId}`, data)
