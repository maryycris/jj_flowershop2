@extends('layouts.customer_app')

@section('content')
<div class="container-fluid py-4" style="background: #f4faf4; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h2 class="text-center mb-4" style="color: #385E42; font-weight: 600;">Custom Flower Design</h2>
                
                <!-- Design Options Tabs -->
                <ul class="nav nav-tabs mb-4" id="designTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">
                            <i class="fas fa-upload me-2"></i>Upload Your Design
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="automation-tab" data-bs-toggle="tab" data-bs-target="#automation" type="button" role="tab">
                            <i class="fas fa-magic me-2"></i>Design Automation
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="designTabsContent">
                    <!-- Upload Design Tab -->
                    <div class="tab-pane fade show active" id="upload" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Upload Your Flower Design</h5>
                                <form action="#" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="design_image" class="form-label">Upload Design Image</label>
                                        <input type="file" class="form-control" id="design_image" name="design_image" accept="image/*" required>
                                        <div class="form-text">Upload a photo of your desired flower arrangement</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="design_description" class="form-label">Design Description</label>
                                        <textarea class="form-control" id="design_description" name="design_description" rows="3" placeholder="Describe your design preferences, colors, style, etc."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="occasion" class="form-label">Occasion</label>
                                        <select class="form-select" id="occasion" name="occasion">
                                            <option value="">Select Occasion</option>
                                            <option value="birthday">Birthday</option>
                                            <option value="anniversary">Anniversary</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="funeral">Funeral</option>
                                            <option value="graduation">Graduation</option>
                                            <option value="valentines">Valentine's Day</option>
                                            <option value="mothers_day">Mother's Day</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="budget_range" class="form-label">Budget Range</label>
                                        <select class="form-select" id="budget_range" name="budget_range">
                                            <option value="">Select Budget</option>
                                            <option value="500-1000">₱500 - ₱1,000</option>
                                            <option value="1000-2000">₱1,000 - ₱2,000</option>
                                            <option value="2000-5000">₱2,000 - ₱5,000</option>
                                            <option value="5000+">Above ₱5,000</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Design Request
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3">Design Preview</h5>
                                <div class="border rounded p-3 text-center" style="min-height: 300px; background: #f8f9fa;">
                                    <img id="design_preview" src="" alt="Design Preview" class="img-fluid" style="display: none; max-height: 250px;">
                                    <div id="upload_placeholder">
                                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Upload an image to see preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Design Automation Tab -->
                    <div class="tab-pane fade" id="automation" role="tabpanel">
                        <h5 class="mb-3">Automated Flower Design</h5>
                        <p class="text-muted mb-4">Create your custom bouquet using our available flowers and components</p>
                        
                        <form action="#" method="POST">
                            @csrf
                            <div class="row">
                                <!-- Flower Selection -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Select Flowers</h6>
                                    <div class="flower-selection">
                                        <div class="mb-3">
                                            <label class="form-label">Red Roses</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="flowers[red_roses]" min="0" value="0">
                                                <span class="input-group-text">stems</span>
                                                <span class="input-group-text">₱50 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">White Roses</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="flowers[white_roses]" min="0" value="0">
                                                <span class="input-group-text">stems</span>
                                                <span class="input-group-text">₱45 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Sunflowers</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="flowers[sunflowers]" min="0" value="0">
                                                <span class="input-group-text">stems</span>
                                                <span class="input-group-text">₱60 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tulips</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="flowers[tulips]" min="0" value="0">
                                                <span class="input-group-text">stems</span>
                                                <span class="input-group-text">₱40 each</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Filler & Accessories -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-3">Filler & Accessories</h6>
                                    <div class="filler-selection">
                                        <div class="mb-3">
                                            <label class="form-label">Baby's Breath</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="fillers[babys_breath]" min="0" value="0">
                                                <span class="input-group-text">bunches</span>
                                                <span class="input-group-text">₱30 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Eucalyptus</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="fillers[eucalyptus]" min="0" value="0">
                                                <span class="input-group-text">sprigs</span>
                                                <span class="input-group-text">₱25 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Ribbon</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="accessories[ribbon]" min="0" value="0">
                                                <span class="input-group-text">meters</span>
                                                <span class="input-group-text">₱20 each</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Vase</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="accessories[vase]" min="0" value="0">
                                                <span class="input-group-text">pieces</span>
                                                <span class="input-group-text">₱150 each</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Design Summary -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Design Summary</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Total Flowers:</strong> <span id="total_flowers">0</span></p>
                                                    <p><strong>Total Fillers:</strong> <span id="total_fillers">0</span></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Subtotal:</strong> ₱<span id="design_subtotal">0.00</span></p>
                                                    <p><strong>Assembly Fee:</strong> ₱<span id="assembly_fee">200.00</span></p>
                                                    <p><strong>Total:</strong> ₱<span id="design_total">200.00</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-magic me-2"></i>Create Custom Design
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for design upload
    const designImageInput = document.getElementById('design_image');
    const designPreview = document.getElementById('design_preview');
    const uploadPlaceholder = document.getElementById('upload_placeholder');

    if (designImageInput) {
        designImageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    designPreview.src = e.target.result;
                    designPreview.style.display = 'block';
                    uploadPlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(event.target.files[0]);
            } else {
                designPreview.style.display = 'none';
                uploadPlaceholder.style.display = 'block';
            }
        });
    }

    // Design automation calculation
    const flowerInputs = document.querySelectorAll('input[name^="flowers"]');
    const fillerInputs = document.querySelectorAll('input[name^="fillers"]');
    const accessoryInputs = document.querySelectorAll('input[name^="accessories"]');
    
    const totalFlowersSpan = document.getElementById('total_flowers');
    const totalFillersSpan = document.getElementById('total_fillers');
    const designSubtotalSpan = document.getElementById('design_subtotal');
    const designTotalSpan = document.getElementById('design_total');
    
    const assemblyFee = 200;
    
    function calculateDesignTotal() {
        let totalFlowers = 0;
        let totalFillers = 0;
        let subtotal = 0;
        
        // Calculate flowers
        flowerInputs.forEach(input => {
            const quantity = parseInt(input.value) || 0;
            totalFlowers += quantity;
            
            // Calculate price based on flower type
            const flowerType = input.name.match(/\[(.*?)\]/)[1];
            let price = 0;
            switch(flowerType) {
                case 'red_roses': price = 50; break;
                case 'white_roses': price = 45; break;
                case 'sunflowers': price = 60; break;
                case 'tulips': price = 40; break;
            }
            subtotal += quantity * price;
        });
        
        // Calculate fillers
        fillerInputs.forEach(input => {
            const quantity = parseInt(input.value) || 0;
            totalFillers += quantity;
            
            const fillerType = input.name.match(/\[(.*?)\]/)[1];
            let price = 0;
            switch(fillerType) {
                case 'babys_breath': price = 30; break;
                case 'eucalyptus': price = 25; break;
            }
            subtotal += quantity * price;
        });
        
        // Calculate accessories
        accessoryInputs.forEach(input => {
            const quantity = parseInt(input.value) || 0;
            const accessoryType = input.name.match(/\[(.*?)\]/)[1];
            let price = 0;
            switch(accessoryType) {
                case 'ribbon': price = 20; break;
                case 'vase': price = 150; break;
            }
            subtotal += quantity * price;
        });
        
        const total = subtotal + assemblyFee;
        
        // Update display
        totalFlowersSpan.textContent = totalFlowers;
        totalFillersSpan.textContent = totalFillers;
        designSubtotalSpan.textContent = subtotal.toFixed(2);
        designTotalSpan.textContent = total.toFixed(2);
    }
    
    // Add event listeners to all quantity inputs
    [...flowerInputs, ...fillerInputs, ...accessoryInputs].forEach(input => {
        input.addEventListener('input', calculateDesignTotal);
    });
    
    // Initialize calculation
    calculateDesignTotal();
});
</script>
@endpush 