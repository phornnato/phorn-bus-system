<?php

namespace App\Services;

class SeatMapService
{
    /**
     * Generates the HTML for the bus seat layout.
     * This is a simplified example. Real bus layouts are much more complex.
     * For a simple 4xN layout (common in express buses/vans), we'll arrange them in rows.
     */
    public static function generateLayout(int $capacity, array $bookedSeats, string $busType): string
    {
        $html = '<div class="bus-layout">';
        $seatNumber = 1;
        
        // Define seats per row based on bus type (simplified)
        $seatsPerRow = ($busType === 'VIP Sleeper') ? 2 : 4; 
        $rows = ceil($capacity / $seatsPerRow);

        // Simple Van/Express Bus Layout (2x2 with center aisle)
        $isVan = ($busType === 'Minivan' || $busType === 'VIP Bus');
        $layoutColumns = $isVan ? 'grid-cols-4' : 'grid-cols-4'; // 4 columns for 2-2 layout

        $html = '<div class="grid ' . $layoutColumns . ' gap-1 p-2 border border-gray-300 rounded-lg bg-white shadow-inner">';

        for ($r = 1; $r <= $rows; $r++) {
            // Front seats next to the driver for Van/Small bus
            if ($r === 1 && $isVan) {
                // Driver seat placeholder (empty space)
                $html .= '<div class="w-8 h-10 m-1"></div>'; 
                // Front passenger seat
                $statusClass = in_array($seatNumber, $bookedSeats) ? 'seat-booked' : 'seat-available';
                $html .= '<div class="seat ' . $statusClass . '" data-seat-number="' . $seatNumber . '">' . $seatNumber . '</div>';
                $seatNumber++;
                // Gap/Aisle
                $html .= '<div class="w-8 h-10 m-1"></div>'; 
                // Right side front seat
                if ($seatNumber <= $capacity) {
                    $statusClass = in_array($seatNumber, $bookedSeats) ? 'seat-booked' : 'seat-available';
                    $html .= '<div class="seat ' . $statusClass . '" data-seat-number="' . $seatNumber . '">' . $seatNumber . '</div>';
                    $seatNumber++;
                } else {
                    $html .= '<div class="w-8 h-10 m-1"></div>'; 
                }
                continue; // Skip the standard row logic for the front
            }
            
            // Standard Rows (Simplified 2-aisle-2)
            for ($c = 1; $c <= $seatsPerRow + 1; $c++) { // +1 for the aisle
                if ($c === 3) { // Aisle (empty column in a 4-seat row)
                    $html .= '<div class="w-8 h-10 m-1"></div>';
                    continue;
                }

                if ($seatNumber <= $capacity) {
                    $statusClass = in_array($seatNumber, $bookedSeats) ? 'seat-booked' : 'seat-available';
                    $seatTypeClass = ($busType === 'VIP Sleeper') ? 'seat-sleeper' : ''; // Example for different types
                    $html .= '<div class="seat ' . $statusClass . ' ' . $seatTypeClass . '" data-seat-number="' . $seatNumber . '">' . $seatNumber . '</div>';
                    $seatNumber++;
                } else {
                    $html .= '<div class="w-8 h-10 m-1"></div>'; // Empty spot
                }
            }
        }

        $html .= '</div>';
        return $html;
    }
}