<?php
// File-based database implementation for car rental system

class FileDB {
    private $dataDir;
    
    public function __construct() {
        $this->dataDir = __DIR__ . '/../data/';
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function getCars() {
        $carsFile = $this->dataDir . 'cars.json';
        if (!file_exists($carsFile)) {
            $this->initializeCars();
        }
        
        $data = file_get_contents($carsFile);
        return json_decode($data, true);
    }
    
    public function getCarById($id) {
        $cars = $this->getCars();
        foreach ($cars as $car) {
            if ($car['id'] == $id) {
                return $car;
            }
        }
        return null;
    }
    
    public function addBooking($booking) {
        $bookingsFile = $this->dataDir . 'bookings.json';
        $bookings = [];
        
        if (file_exists($bookingsFile)) {
            $data = file_get_contents($bookingsFile);
            $bookings = json_decode($data, true) ?: [];
        }
        
        $booking['id'] = count($bookings) + 1;
        $booking['created_at'] = date('Y-m-d H:i:s');
        $booking['status'] = 'pending';
        
        $bookings[] = $booking;
        
        file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
        return $booking;
    }
    
    public function getBookings() {
        $bookingsFile = $this->dataDir . 'bookings.json';
        if (!file_exists($bookingsFile)) {
            return [];
        }
        
        $data = file_get_contents($bookingsFile);
        return json_decode($data, true) ?: [];
    }
    
    private function initializeCars() {
        $cars = [
            [
                'id' => 1,
                'name' => 'BMW 3 Series',
                'category' => 'luxury',
                'price_per_day' => 89.00,
                'image' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'Experience luxury and performance with the BMW 3 Series. Perfect for business trips and special occasions.',
                'features' => 'Automatic Transmission,GPS Navigation,Bluetooth,Premium Sound System,Leather Seats'
            ],
            [
                'id' => 2,
                'name' => 'Mercedes-Benz C-Class',
                'category' => 'luxury',
                'price_per_day' => 95.00,
                'image' => 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'Elegant and sophisticated, the Mercedes-Benz C-Class offers unmatched comfort and style.',
                'features' => 'Automatic Transmission,Premium Interior,Advanced Safety Features,Climate Control,Sunroof'
            ],
            [
                'id' => 3,
                'name' => 'Toyota Camry',
                'category' => 'sedan',
                'price_per_day' => 55.00,
                'image' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'Reliable and fuel-efficient, the Toyota Camry is perfect for everyday driving and long trips.',
                'features' => 'Fuel Efficient,Spacious Interior,Safety Features,Bluetooth,Backup Camera'
            ],
            [
                'id' => 4,
                'name' => 'Honda Accord',
                'category' => 'sedan',
                'price_per_day' => 58.00,
                'image' => 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'The Honda Accord combines reliability with modern technology for a comfortable driving experience.',
                'features' => 'Hybrid Option,Advanced Safety,Spacious Cabin,Infotainment System,LED Headlights'
            ],
            [
                'id' => 5,
                'name' => 'Ford Explorer',
                'category' => 'suv',
                'price_per_day' => 75.00,
                'image' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'Spacious and powerful, the Ford Explorer is ideal for family trips and outdoor adventures.',
                'features' => '7-Seater,4WD Available,Towing Capacity,Advanced Tech,Cargo Space'
            ],
            [
                'id' => 6,
                'name' => 'Chevrolet Tahoe',
                'category' => 'suv',
                'price_per_day' => 85.00,
                'image' => 'https://images.unsplash.com/photo-1606016159991-8b5d5f8e7e8e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'The Chevrolet Tahoe offers maximum space and capability for large groups and heavy cargo.',
                'features' => '8-Seater,4WD,Premium Audio,Rear Entertainment,Massive Cargo Space'
            ],
            [
                'id' => 7,
                'name' => 'Porsche 911',
                'category' => 'sports',
                'price_per_day' => 150.00,
                'image' => 'https://images.unsplash.com/photo-1544829099-b9a0c5303bea?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'Experience pure driving excitement with the iconic Porsche 911. Perfect for special occasions.',
                'features' => 'High Performance,Sport Suspension,Premium Interior,Advanced Electronics,Iconic Design'
            ],
            [
                'id' => 8,
                'name' => 'Ferrari F8',
                'category' => 'sports',
                'price_per_day' => 300.00,
                'image' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'description' => 'The ultimate supercar experience. The Ferrari F8 delivers unmatched performance and prestige.',
                'features' => 'V8 Engine,Carbon Fiber,Racing Technology,Luxury Interior,Exclusive Experience'
            ]
        ];
        
        $carsFile = $this->dataDir . 'cars.json';
        file_put_contents($carsFile, json_encode($cars, JSON_PRETTY_PRINT));
    }
}
?>
