@extends('layouts.customer_app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Loyalty Card Display -->
            <div class="card shadow-lg mb-4" style="border: none; border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <div class="card-body p-4">
                    <!-- Card Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success mb-2">Loyalty Card</h2>
                        <p class="text-muted mb-0">J'J Flower Shop</p>
                    </div>

                    <!-- Stamps Display -->
                    <div class="row justify-content-center mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <div class="col-auto">
                                <div class="stamp-circle {{ $i <= $status['stamps_count'] ? 'filled' : 'empty' }}">
                                    @if($i <= $status['stamps_count'])
                                        <i class="fas fa-check text-white"></i>
                                    @elseif($i == 5 && $status['can_redeem'])
                                        <span class="discount-text">50%<br><small>OFF</small></span>
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Progress</span>
                            <span class="text-success fw-bold">{{ $status['stamps_count'] }}/{{ $status['required_stamps'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $status['progress_percentage'] }}%" 
                                 aria-valuenow="{{ $status['stamps_count'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $status['required_stamps'] }}">
                            </div>
                        </div>
                    </div>

                    <!-- Status Message -->
                    @if($status['can_redeem'])
                        <div class="alert alert-success text-center mb-4">
                            <i class="fas fa-gift me-2"></i>
                            <strong>Congratulations!</strong> You can now redeem your 50% discount on any bouquet!
                        </div>
                    @else
                        <div class="alert alert-info text-center mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Collect {{ $status['required_stamps'] - $status['stamps_count'] }} more stamps to get your 50% discount!
                        </div>
                    @endif

                    <!-- Customer Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-muted">Name:</label>
                                <div class="form-control-plaintext fw-bold">{{ Auth::user()->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-muted">Phone Number:</label>
                                <div class="form-control-plaintext fw-bold">{{ Auth::user()->contact_number ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mechanics Card -->
            <div class="card shadow mb-4" style="border: none; border-radius: 15px;">
                <div class="card-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Loyalty Card Mechanics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">How to Earn Stamps:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>1 stamp per bouquet purchase</li>
                                <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>Mini bouquets excluded</li>
                                <li class="mb-2"><i class="fas fa-info text-info me-2"></i>Multiple bouquets = 1 stamp only</li>
                                <li class="mb-2"><i class="fas fa-calendar text-primary me-2"></i>Started March 24, 2024</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">How to Redeem:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-gift text-success me-2"></i>Collect 5 stamps</li>
                                <li class="mb-2"><i class="fas fa-percentage text-success me-2"></i>Get 50% off bouquet in package</li>
                                <li class="mb-2"><i class="fas fa-shopping-cart text-info me-2"></i>Present card during purchase</li>
                                <li class="mb-2"><i class="fas fa-redo text-warning me-2"></i>Card resets after redemption</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Card -->
            @if($history['stamps']->count() > 0 || $history['redemptions']->count() > 0)
            <div class="card shadow mb-4" style="border: none; border-radius: 15px;">
                <div class="card-header bg-light" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Loyalty History</h5>
                </div>
                <div class="card-body">
                    <!-- Stamps History -->
                    @if($history['stamps']->count() > 0)
                    <h6 class="text-success mb-3">Stamps Earned:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date Earned</th>
                                    <th>Order Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history['stamps'] as $stamp)
                                <tr>
                                    <td>#{{ $stamp['order_id'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stamp['earned_at'])->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($stamp['order_total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Redemptions History -->
                    @if($history['redemptions']->count() > 0)
                    <h6 class="text-success mb-3 mt-4">Discounts Redeemed:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date Redeemed</th>
                                    <th>Discount Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history['redemptions'] as $redemption)
                                <tr>
                                    <td>#{{ $redemption['order_id'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($redemption['redeemed_at'])->format('M d, Y') }}</td>
                                    <td class="text-success fw-bold">₱{{ number_format($redemption['discount_amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.stamp-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 0 5px;
    transition: all 0.3s ease;
}

.stamp-circle.filled {
    background: #28a745;
    color: white;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.stamp-circle.empty {
    background: #e9ecef;
    color: #6c757d;
    border: 2px dashed #dee2e6;
}

.stamp-circle .discount-text {
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    line-height: 1.2;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    background: linear-gradient(45deg, #28a745, #20c997);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
