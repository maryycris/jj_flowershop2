

<?php $__env->startPush('styles'); ?>
<style>
/* Inventory History Files Styling */
.history-files-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}

.history-file-btn {
    background: #e8f5e8;
    border: 1px solid #c3e6c3;
    border-radius: 6px;
    padding: 12px 16px;
    margin: 8px 4px;
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 120px;
    text-align: center;
    font-size: 14px;
    font-weight: 500;
}

.history-file-btn:hover {
    background: #d4edda;
    border-color: #28a745;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.history-file-btn .file-initial {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
    display: block;
    margin-bottom: 4px;
}

.history-file-btn .file-date {
    font-size: 12px;
    color: #666;
}

/* Inventory Update Request Styling */
.update-request-section {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.request-header {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.request-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.btn-accept {
    background: #28a745;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
}

.btn-accept:hover {
    background: #218838;
    color: white;
}

.btn-decline {
    background: transparent;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 500;
}

.btn-decline:hover {
    background: #dc3545;
    color: white;
}

/* Table styling for update request */
.update-request-table {
    margin-top: 20px;
}

.update-request-table .table {
    margin-bottom: 0;
}

.update-request-table .table th {
    background: #e6f4ea;
    border-bottom: 2px solid #28a745;
    font-weight: 600;
    color: #2d5a2d;
}

/* Row border colors */
.row-added {
    border: 2px solid #28a745 !important;
}

.row-edited {
    border: 2px solid #007bff !important;
}

.row-deleted {
    border: 2px solid #dc3545 !important;
}

/* Modal styling */
.history-modal .modal-dialog {
    max-width: 95%;
    width: 1200px;
}

.history-modal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Scrollbar styling */
.history-files-container::-webkit-scrollbar {
    width: 8px;
}

.history-files-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.history-files-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.history-files-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('admin_content'); ?>
<div class="mx-auto" style="max-width: 1400px; padding-top: 24px;">
    <h2>Inventory Reports</h2>
    
    <!-- Inventory History/Reports Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">Inventory History/Reports</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search history reports..." id="historySearch">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                        <input type="date" class="form-control" id="historyDateFilter">
                    </div>
                </div>
            </div>
            
            <div class="history-files-container" id="historyFilesContainer">
                <!-- Sample history files - replace with dynamic data -->
                <div class="history-file-btn" data-date="2025-10-10" data-user="Admin">
                    <span class="file-initial">A</span>
                    <span class="file-date">10/10/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-09" data-user="Clerk">
                    <span class="file-initial">C</span>
                    <span class="file-date">10/09/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-08" data-user="Admin">
                    <span class="file-initial">A</span>
                    <span class="file-date">10/08/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-07" data-user="Clerk">
                    <span class="file-initial">C</span>
                    <span class="file-date">10/07/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-06" data-user="Admin">
                    <span class="file-initial">A</span>
                    <span class="file-date">10/06/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-05" data-user="Clerk">
                    <span class="file-initial">C</span>
                    <span class="file-date">10/05/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-04" data-user="Admin">
                    <span class="file-initial">A</span>
                    <span class="file-date">10/04/2025</span>
                </div>
                <div class="history-file-btn" data-date="2025-10-03" data-user="Clerk">
                    <span class="file-initial">C</span>
                    <span class="file-date">10/03/2025</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Update Request Section -->
    <div class="update-request-section">
        <h4>Inventory Update Request</h4>
        
        <div class="request-header">
            <div class="row">
                <div class="col-md-6">
                    <strong>Clerk's name:</strong> <span id="clerkName">John Doe</span>
                </div>
                <div class="col-md-6">
                    <strong>Date:</strong> <span id="requestDate">2025-10-10</span>
                </div>
            </div>
        </div>
        
        <div class="request-actions">
            <button class="btn btn-accept" id="acceptChangesBtn">
                <i class="bi bi-check-circle me-1"></i> Accept Changes
            </button>
            <button class="btn btn-decline" id="declineChangesBtn">
                <i class="bi bi-x-circle me-1"></i> Decline
            </button>
        </div>
        
        <!-- Category Tabs -->
        <ul class="nav nav-tabs" id="updateRequestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="fresh-flowers-tab" data-bs-toggle="tab" data-bs-target="#fresh-flowers" type="button" role="tab">Fresh Flowers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dried-flowers-tab" data-bs-toggle="tab" data-bs-target="#dried-flowers" type="button" role="tab">Dried Flowers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="artificial-flowers-tab" data-bs-toggle="tab" data-bs-target="#artificial-flowers" type="button" role="tab">Artificial Flowers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="greenery-tab" data-bs-toggle="tab" data-bs-target="#greenery" type="button" role="tab">Greenery</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="floral-supplies-tab" data-bs-toggle="tab" data-bs-target="#floral-supplies" type="button" role="tab">Floral Supplies</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="packaging-materials-tab" data-bs-toggle="tab" data-bs-target="#packaging-materials" type="button" role="tab">Packaging Materials</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="wrappers-tab" data-bs-toggle="tab" data-bs-target="#wrappers" type="button" role="tab">Wrappers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ribbon-tab" data-bs-toggle="tab" data-bs-target="#ribbon" type="button" role="tab">Ribbon</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="other-offers-tab" data-bs-toggle="tab" data-bs-target="#other-offers" type="button" role="tab">Other Offers</button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="updateRequestTabContent">
            <div class="tab-pane fade show active" id="fresh-flowers" role="tabpanel">
                <div class="update-request-table">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Selling Price</th>
                                <th>Acquisition Cost</th>
                                <th>Reordering Rules (Min / Max)</th>
                                <th>Qty On Hand</th>
                                <th>Qty Consumed</th>
                                <th>Qty Damaged</th>
                                <th>Qty Sold</th>
                                <th>Qty to Purchase (Max - On Hand)</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample data with different row borders -->
                            <tr class="row-added">
                                <td>06001</td>
                                <td>Rose</td>
                                <td>Fresh Flower</td>
                                <td>120</td>
                                <td>02/23/2025</td>
                                <td>10 / 60</td>
                                <td>1</td>
                                <td>30</td>
                                <td>15</td>
                                <td>15</td>
                                <td>15</td>
                                <td>02/03/2025</td>
                            </tr>
                            <tr class="row-edited">
                                <td>01002</td>
                                <td>Tulip</td>
                                <td>Fresh Flower</td>
                                <td>70</td>
                                <td>N/A</td>
                                <td>10 / 50</td>
                                <td>0</td>
                                <td>50</td>
                                <td>60</td>
                                <td>60</td>
                                <td>60</td>
                                <td>02/03/2025</td>
                            </tr>
                            <tr class="row-deleted">
                                <td>07003</td>
                                <td>Yellow Tulip</td>
                                <td>Fresh Flower</td>
                                <td>400</td>
                                <td>06/07/2025</td>
                                <td>10 / 50</td>
                                <td>0</td>
                                <td>25</td>
                                <td>40</td>
                                <td>60</td>
                                <td>60</td>
                                <td>02/03/2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Other tab panes would go here -->
        </div>
    </div>
</div>

<!-- History Report Modal -->
<div class="modal fade history-modal" id="historyReportModal" tabindex="-1" aria-labelledby="historyReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyReportModalLabel">Inventory History/Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Clerk's name/Admin:</strong> <span id="modalClerkName">Admin</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Date:</strong> <span id="modalDate">2025-10-10</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search inventory...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Category Tabs for Modal -->
                <ul class="nav nav-tabs" id="modalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="modal-fresh-flowers-tab" data-bs-toggle="tab" data-bs-target="#modal-fresh-flowers" type="button" role="tab">Fresh Flowers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-dried-flowers-tab" data-bs-toggle="tab" data-bs-target="#modal-dried-flowers" type="button" role="tab">Dried Flowers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-artificial-flowers-tab" data-bs-toggle="tab" data-bs-target="#modal-artificial-flowers" type="button" role="tab">Artificial Flowers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-greenery-tab" data-bs-toggle="tab" data-bs-target="#modal-greenery" type="button" role="tab">Greenery</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-floral-supplies-tab" data-bs-toggle="tab" data-bs-target="#modal-floral-supplies" type="button" role="tab">Floral Supplies</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-packaging-materials-tab" data-bs-toggle="tab" data-bs-target="#modal-packaging-materials" type="button" role="tab">Packaging Materials</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-wrappers-tab" data-bs-toggle="tab" data-bs-target="#modal-wrappers" type="button" role="tab">Wrappers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-ribbon-tab" data-bs-toggle="tab" data-bs-target="#modal-ribbon" type="button" role="tab">Ribbon</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="modal-other-offers-tab" data-bs-toggle="tab" data-bs-target="#modal-other-offers" type="button" role="tab">Other Offers</button>
                    </li>
                </ul>
                
                <!-- Modal Tab Content -->
                <div class="tab-content" id="modalTabContent">
                    <div class="tab-pane fade show active" id="modal-fresh-flowers" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Selling Price</th>
                                        <th>Acquisition Cost</th>
                                        <th>Reordering Rules (Min / Max)</th>
                                        <th>Qty On Hand</th>
                                        <th>Qty Consumed</th>
                                        <th>Qty Damaged</th>
                                        <th>Qty Sold</th>
                                        <th>Qty to Purchase (Max - On Hand)</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample historical data -->
                                    <tr>
                                        <td>06001</td>
                                        <td>Rose</td>
                                        <td>Fresh Flower</td>
                                        <td>120</td>
                                        <td>02/23/2025</td>
                                        <td>10 / 60</td>
                                        <td>1</td>
                                        <td>30</td>
                                        <td>15</td>
                                        <td>15</td>
                                        <td>15</td>
                                        <td>02/03/2025</td>
                                    </tr>
                                    <tr>
                                        <td>01002</td>
                                        <td>Tulip</td>
                                        <td>Fresh Flower</td>
                                        <td>70</td>
                                        <td>N/A</td>
                                        <td>10 / 50</td>
                                        <td>0</td>
                                        <td>50</td>
                                        <td>60</td>
                                        <td>60</td>
                                        <td>60</td>
                                        <td>02/03/2025</td>
                                    </tr>
                                    <tr>
                                        <td>07003</td>
                                        <td>Yellow Tulip</td>
                                        <td>Fresh Flower</td>
                                        <td>400</td>
                                        <td>06/07/2025</td>
                                        <td>10 / 50</td>
                                        <td>0</td>
                                        <td>25</td>
                                        <td>40</td>
                                        <td>60</td>
                                        <td>60</td>
                                        <td>02/03/2025</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Other modal tab panes would go here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle history file button clicks
    document.querySelectorAll('.history-file-btn').forEach(button => {
        button.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            const user = this.getAttribute('data-user');
            
            // Update modal content
            document.getElementById('modalClerkName').textContent = user;
            document.getElementById('modalDate').textContent = date;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('historyReportModal'));
            modal.show();
        });
    });
    
    // Handle Accept Changes button
    document.getElementById('acceptChangesBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to accept these inventory changes? This will update the inventory for both admin and clerk.')) {
            // Show loading state
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';
            this.disabled = true;
            
            // Simulate processing
            setTimeout(() => {
                alert('Inventory changes accepted successfully! Changes have been saved to inventory history.');
                
                // Reset button
                this.innerHTML = '<i class="bi bi-check-circle me-1"></i> Accept Changes';
                this.disabled = false;
                
                // Here you would typically:
                // 1. Update inventory for both admin and clerk
                // 2. Save changes to inventory history
                // 3. Remove or mark this request as processed
                // 4. Refresh the page or update the UI
                
            }, 2000);
        }
    });
    
    // Handle Decline button
    document.getElementById('declineChangesBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to decline these inventory changes?')) {
            alert('Inventory changes have been declined.');
            
            // Here you would typically:
            // 1. Mark the request as declined
            // 2. Notify the clerk
            // 3. Remove or hide this request
        }
    });
    
    // Handle history search
    document.getElementById('historySearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const files = document.querySelectorAll('.history-file-btn');
        
        files.forEach(file => {
            const date = file.querySelector('.file-date').textContent.toLowerCase();
            const user = file.getAttribute('data-user').toLowerCase();
            
            if (date.includes(searchTerm) || user.includes(searchTerm)) {
                file.style.display = 'inline-block';
            } else {
                file.style.display = 'none';
            }
        });
    });
    
    // Handle date filter
    document.getElementById('historyDateFilter').addEventListener('change', function() {
        const selectedDate = this.value;
        const files = document.querySelectorAll('.history-file-btn');
        
        files.forEach(file => {
            const fileDate = file.getAttribute('data-date');
            
            if (selectedDate === '' || fileDate === selectedDate) {
                file.style.display = 'inline-block';
            } else {
                file.style.display = 'none';
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/inventory/reports.blade.php ENDPATH**/ ?>