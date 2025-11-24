<div class="trip-info">
    <p><strong>{{ $tripSchedule->available_seats }}</strong> Seats Available</p>
    <p><strong>${{ number_format($tripSchedule->trip->price, 2) }}</strong></p>
</div>
