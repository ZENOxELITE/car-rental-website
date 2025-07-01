// Global variables
let cars = [];
let currentTheme = localStorage.getItem('theme') || 'light';

// DOM Elements
const carsGrid = document.getElementById('carsGrid');
const filterButtons = document.querySelectorAll('.filter-btn');
const carModal = document.getElementById('carModal');
const bookingModal = document.getElementById('bookingModal');
const modalContent = document.getElementById('modalContent');
const bookingForm = document.getElementById('bookingForm');
const themeToggle = document.getElementById('themeToggle');

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    loadCarsFromAPI();
    setupEventListeners();
    setMinDate();
    showLoadingState();
});

// Theme Management
function initializeTheme() {
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon();
}

function toggleTheme() {
    currentTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    localStorage.setItem('theme', currentTheme);
    updateThemeIcon();
    
    // Add smooth transition effect
    document.body.style.transition = 'all 0.3s ease';
    setTimeout(() => {
        document.body.style.transition = '';
    }, 300);
}

function updateThemeIcon() {
    const icon = themeToggle.querySelector('i');
    if (currentTheme === 'dark') {
        icon.className = 'fas fa-sun';
        themeToggle.title = 'Switch to Light Mode';
    } else {
        icon.className = 'fas fa-moon';
        themeToggle.title = 'Switch to Dark Mode';
    }
}

// API Functions
async function loadCarsFromAPI() {
    try {
        showLoadingState();
        
        const response = await fetch('cars.php');
        const data = await response.json();
        
        if (data.success) {
            cars = data.data;
            displayCars(cars);
            hideLoadingState();
        } else {
            throw new Error(data.message || 'Failed to load cars');
        }
    } catch (error) {
        console.error('Error loading cars:', error);
        // Fallback to sample data if API fails
        loadFallbackData();
        showMessage('Using offline data. Some features may be limited.', 'info');
    }
}

function loadFallbackData() {
    cars = [
        {
            id: 1,
            name: "BMW 3 Series",
            category: "luxury",
            price_per_day: 89,
            image: "https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "Experience luxury and performance with the BMW 3 Series. Perfect for business trips and special occasions.",
            features: "Automatic Transmission,GPS Navigation,Bluetooth,Premium Sound System,Leather Seats"
        },
        {
            id: 2,
            name: "Mercedes-Benz C-Class",
            category: "luxury",
            price_per_day: 95,
            image: "https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "Elegant and sophisticated, the Mercedes-Benz C-Class offers unmatched comfort and style.",
            features: "Automatic Transmission,Premium Interior,Advanced Safety Features,Climate Control,Sunroof"
        },
        {
            id: 3,
            name: "Toyota Camry",
            category: "sedan",
            price_per_day: 55,
            image: "https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "Reliable and fuel-efficient, the Toyota Camry is perfect for everyday driving and long trips.",
            features: "Fuel Efficient,Spacious Interior,Safety Features,Bluetooth,Backup Camera"
        },
        {
            id: 4,
            name: "Honda Accord",
            category: "sedan",
            price_per_day: 58,
            image: "https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "The Honda Accord combines reliability with modern technology for a comfortable driving experience.",
            features: "Hybrid Option,Advanced Safety,Spacious Cabin,Infotainment System,LED Headlights"
        },
        {
            id: 5,
            name: "Ford Explorer",
            category: "suv",
            price_per_day: 75,
            image: "https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "Spacious and powerful, the Ford Explorer is ideal for family trips and outdoor adventures.",
            features: "7-Seater,4WD Available,Towing Capacity,Advanced Tech,Cargo Space"
        },
        {
            id: 6,
            name: "Chevrolet Tahoe",
            category: "suv",
            price_per_day: 85,
            image: "https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "The Chevrolet Tahoe offers maximum space and capability for large groups and heavy cargo.",
            features: "8-Seater,4WD,Premium Audio,Rear Entertainment,Massive Cargo Space"
        },
        {
            id: 7,
            name: "Porsche 911",
            category: "sports",
            price_per_day: 150,
            image: "https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "Experience pure driving excitement with the iconic Porsche 911. Perfect for special occasions.",
            features: "High Performance,Sport Suspension,Premium Interior,Advanced Electronics,Iconic Design"
        },
        {
            id: 8,
            name: "Ferrari F8",
            category: "sports",
            price_per_day: 300,
            image: "https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            description: "The ultimate supercar experience. The Ferrari F8 delivers unmatched performance and prestige.",
            features: "V8 Engine,Carbon Fiber,Racing Technology,Luxury Interior,Exclusive Experience"
        }
    ];
    
    displayCars(cars);
    hideLoadingState();
}

// Display functions
function showLoadingState() {
    carsGrid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
            <div class="loading"></div>
            <p style="margin-top: 1rem; color: var(--text-secondary);">Loading amazing cars...</p>
        </div>
    `;
}

function hideLoadingState() {
    // Loading state will be replaced by displayCars
}

function displayCars(carsToShow) {
    if (!carsToShow || carsToShow.length === 0) {
        carsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <i class="fas fa-car" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <p style="color: var(--text-secondary);">No cars available at the moment.</p>
            </div>
        `;
        return;
    }
    
    carsGrid.innerHTML = '';
    
    carsToShow.forEach((car, index) => {
        const carCard = createCarCard(car);
        carCard.style.animationDelay = `${index * 0.1}s`;
        carCard.classList.add('fade-in');
        carsGrid.appendChild(carCard);
    });
}

function createCarCard(car) {
    const carCard = document.createElement('div');
    carCard.className = 'car-card';
    carCard.dataset.category = car.category;
    
    // Handle features array or string
    let features = [];
    if (Array.isArray(car.features)) {
        features = car.features;
    } else if (typeof car.features === 'string') {
        features = car.features.split(',');
    }
    
    // Use price_per_day or price for compatibility
    const price = car.price_per_day || car.price || 0;
    
    carCard.innerHTML = `
        <img src="${car.image}" alt="${car.name}" class="car-image" 
             onerror="this.src='https://via.placeholder.com/400x250/3498db/ffffff?text=Car+Image'"
             loading="lazy">
        <div class="car-details">
            <h3>${car.name}</h3>
            <p class="car-price">$${price}/day</p>
            <p>${car.description.substring(0, 100)}...</p>
            <div class="car-actions">
                <button class="book-btn view-details" data-car-id="${car.id}">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="book-btn book-now" data-car-id="${car.id}">
                    <i class="fas fa-calendar-check"></i> Book Now
                </button>
            </div>
        </div>
    `;
    
    return carCard;
}

// Event Listeners Setup
function setupEventListeners() {
    // Theme toggle
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // Filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter cars with animation
            filterCars(filter);
        });
    });
    
    // Car actions (view details and book now)
    carsGrid.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-details') || e.target.closest('.view-details')) {
            const button = e.target.classList.contains('view-details') ? e.target : e.target.closest('.view-details');
            const carId = parseInt(button.dataset.carId);
            showCarDetails(carId);
        } else if (e.target.classList.contains('book-now') || e.target.closest('.book-now')) {
            const button = e.target.classList.contains('book-now') ? e.target : e.target.closest('.book-now');
            const carId = parseInt(button.dataset.carId);
            showBookingForm(carId);
        }
    });
    
    // Modal close buttons
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            closeModal(carModal);
            closeModal(bookingModal);
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === carModal) {
            closeModal(carModal);
        }
        if (e.target === bookingModal) {
            closeModal(bookingModal);
        }
    });
    
    // Booking form submission
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleBookingSubmission);
    }
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal(carModal);
            closeModal(bookingModal);
        }
    });
}

// Filter functionality with animation
function filterCars(filter) {
    const filteredCars = filter === 'all' ? cars : cars.filter(car => car.category === filter);
    
    // Add fade out animation
    const carCards = document.querySelectorAll('.car-card');
    carCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });
    
    // Display filtered cars after animation
    setTimeout(() => {
        displayCars(filteredCars);
    }, 300);
}

// Modal functions
function openModal(modal) {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    modal.setAttribute('aria-hidden', 'false');
}

function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
    modal.setAttribute('aria-hidden', 'true');
}

// Show car details in modal
function showCarDetails(carId) {
    const car = cars.find(c => c.id === carId);
    if (!car) {
        showMessage('Car not found', 'error');
        return;
    }
    
    // Handle features
    let features = [];
    if (Array.isArray(car.features)) {
        features = car.features;
    } else if (typeof car.features === 'string') {
        features = car.features.split(',');
    }
    
    const price = car.price_per_day || car.price || 0;
    
    modalContent.innerHTML = `
        <h2>${car.name}</h2>
        <img src="${car.image}" alt="${car.name}" 
             style="width: 100%; height: 300px; object-fit: cover; border-radius: 15px; margin: 1rem 0;"
             onerror="this.src='https://via.placeholder.com/600x300/3498db/ffffff?text=Car+Image'">
        <p class="car-price" style="font-size: 1.8rem; color: var(--primary-color); font-weight: bold; margin: 1rem 0;">
            $${price}/day
        </p>
        <p style="margin: 1rem 0; line-height: 1.6;">${car.description}</p>
        <h3 style="margin: 1.5rem 0 1rem 0; color: var(--text-color);">Features:</h3>
        <ul style="margin: 1rem 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
            ${features.map(feature => `<li style="padding: 0.5rem; background: var(--surface-color); border-radius: 5px; border-left: 3px solid var(--primary-color);">${feature.trim()}</li>`).join('')}
        </ul>
        <button class="book-btn" onclick="showBookingForm(${car.id})" 
                style="margin-top: 2rem; width: 100%; padding: 1rem; font-size: 1.1rem;">
            <i class="fas fa-calendar-check"></i> Book This Car
        </button>
    `;
    
    openModal(carModal);
}

// Show booking form
function showBookingForm(carId) {
    const car = cars.find(c => c.id === carId);
    if (!car) {
        showMessage('Car not found', 'error');
        return;
    }
    
    document.getElementById('carId').value = carId;
    closeModal(carModal);
    openModal(bookingModal);
    
    // Update form title
    const formTitle = document.querySelector('#bookingContent h2');
    if (formTitle) {
        formTitle.innerHTML = `<i class="fas fa-car"></i> Book ${car.name}`;
    }
}

// Handle booking form submission
async function handleBookingSubmission(e) {
    e.preventDefault();
    
    const formData = new FormData(bookingForm);
    const bookingData = Object.fromEntries(formData);
    
    // Validate dates
    const startDate = new Date(bookingData.start_date);
    const endDate = new Date(bookingData.end_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (startDate < today) {
        showMessage('Start date cannot be in the past', 'error');
        return;
    }
    
    if (endDate <= startDate) {
        showMessage('End date must be after start date', 'error');
        return;
    }
    
    // Calculate total days and cost
    const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
    const car = cars.find(c => c.id === parseInt(bookingData.car_id));
    const price = car.price_per_day || car.price || 0;
    const totalCost = days * price;
    
    // Show confirmation
    const confirmation = confirm(`
        🚗 Booking Summary:
        
        Car: ${car.name}
        Duration: ${days} day(s)
        Total Cost: $${totalCost}
        
        Confirm booking?
    `);
    
    if (confirmation) {
        await submitBooking(bookingData, car, days, totalCost);
    }
}

// Submit booking to backend
async function submitBooking(bookingData, car, days, totalCost) {
    const submitBtn = document.querySelector('.submit-btn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<div class="loading"></div> Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('process_booking_new.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(bookingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Success
            closeModal(bookingModal);
            showMessage(`
                🎉 Booking Confirmed!
                
                Thank you ${bookingData.customer_name} for choosing Elite Car Rentals!
                
                Booking Details:
                • Car: ${car.name}
                • Duration: ${days} day(s)
                • Total Cost: $${totalCost}
                • Booking ID: #${result.data.booking_id}
                
                ${result.data.email_sent ? '📧 A confirmation email has been sent to ' + bookingData.email : '⚠️ Confirmation email could not be sent, but your booking is confirmed.'}
                
                We'll contact you soon with pickup details!
            `, 'success');
            
            // Reset form
            bookingForm.reset();
        } else {
            throw new Error(result.message || 'Booking failed');
        }
    } catch (error) {
        console.error('Booking error:', error);
        showMessage(`❌ Booking failed: ${error.message}. Please try again or contact us directly.`, 'error');
    } finally {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Utility functions
function setMinDate() {
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    
    if (startDateInput) {
        startDateInput.min = today;
        startDateInput.addEventListener('change', function() {
            if (endDateInput) {
                endDateInput.min = this.value;
            }
        });
    }
    
    if (endDateInput) {
        endDateInput.min = today;
    }
}

function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="white-space: pre-line;">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" 
                    style="background: none; border: none; color: inherit; font-size: 1.2rem; cursor: pointer; margin-left: 1rem;">
                ×
            </button>
        </div>
    `;
    
    // Insert at top of page
    document.body.insertBefore(messageDiv, document.body.firstChild);
    
    // Auto remove after 10 seconds for success messages, 15 seconds for errors
    const timeout = type === 'error' ? 15000 : 10000;
    setTimeout(() => {
        if (messageDiv.parentElement) {
            messageDiv.remove();
        }
    }, timeout);
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Image loading optimization
function optimizeImages() {
    const images = document.querySelectorAll('.car-image');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Performance monitoring
function trackPerformance() {
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`Page loaded in ${loadTime}ms`);
        });
    }
}

// Initialize performance tracking
trackPerformance();

// Add CSS animation classes
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .car-card {
        transition: all 0.3s ease;
    }
    
    .loaded {
        opacity: 1;
        transition: opacity 0.3s ease;
    }
`;
document.head.appendChild(style);
