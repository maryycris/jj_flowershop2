<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid" style="background: #F6FBF4; min-height: 100vh;">
    <div class="row">
        <!-- Main Dashboard Content (no duplicate nav) -->
        <div class="col pt-4">
            <div class="mx-auto" style="max-width: 1200px;">
            <div class="row g-4 mb-3 align-items-stretch">
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-pink text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-hourglass-split me-1"></i> Pending Orders</div>
                        <div class="dashboard-count"><?php echo e($pendingOrdersCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'approved'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-blue text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-check2-circle me-1"></i> Approved Orders</div>
                        <div class="dashboard-count"><?php echo e($approvedOrdersCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'on_delivery'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-red text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-truck me-1"></i> On Delivery</div>
                        <div class="dashboard-count"><?php echo e($onDeliveryCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'completed', 'today' => 1])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-yellow text-center p-3" style="min-width:180px;">
                        <div class="fw-semibold mb-1" style="font-size:1.1rem;"><i class="bi bi-star-fill me-1"></i> Complete Order Today</div>
                        <div class="dashboard-count"><?php echo e($completedTodayCount ?? 0); ?></div>
                    </div>
                    </a>
                </div>
            </div>
            <!-- Added Analytics: Totals and Popular Products -->
            <div class="row g-4 mb-3 align-items-stretch">
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card stat-card stat-green p-3 w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Total Customers</div>
                                <div class="stat-value"><?php echo e(number_format($totalCustomers ?? 0)); ?></div>
                            </div>
                            <div class="icon-circle bg-success-subtle text-success"><i class="bi bi-people"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card stat-card stat-blue p-3 w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Total Products</div>
                                <div class="stat-value"><?php echo e(number_format($totalProducts ?? 0)); ?></div>
                            </div>
                            <div class="icon-circle bg-primary-subtle text-primary"><i class="bi bi-box"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card stat-card stat-yellow p-3 w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Total Orders</div>
                                <div class="stat-value"><?php echo e(number_format($totalOrders ?? 0)); ?></div>
                            </div>
                            <div class="icon-circle bg-warning-subtle text-warning"><i class="bi bi-receipt"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 d-flex">
                    <div class="card stat-card stat-white p-3 w-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="stat-label">Most Popular Products</div>
                            <div class="icon-circle bg-secondary-subtle text-secondary"><i class="bi bi-bar-chart"></i></div>
                        </div>
                        <div id="popularProductsList" style="max-height: 180px; overflow-y: auto;">
                            <?php if(!empty($popularProducts) && count($popularProducts)): ?>
                                <?php $__currentLoopData = $popularProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex justify-content-between small"><span><?php echo e($p->name); ?></span><span class="text-muted">x<?php echo e($p->total_quantity); ?></span></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="text-muted">No data yet.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Restock Card -->
            <div class="row mt-2 align-items-stretch">
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo e(route('admin.inventory.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="card dashboard-card dashboard-orange text-start p-3" style="min-width:260px;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold me-2" style="color: #6c757d; letter-spacing: 2px;">RESTOCK</span>
                                <i class="bi bi-exclamation-triangle" style="color: #FFD600;"></i>
                            </div>
                            <div class="restock-list">
                                <?php if(isset($restockProducts) && count($restockProducts)): ?>
                                    <?php $__currentLoopData = $restockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex justify-content-between"><span><?php echo e($product->name); ?></span> <span><?php echo e($product->stock); ?></span></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-muted">No products need restocking.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.dashboard-card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.18s cubic-bezier(.4,2,.6,1), box-shadow 0.18s; }
.dashboard-card:hover { transform: translateY(-6px) scale(1.04); box-shadow: 0 8px 24px rgba(0,0,0,0.10); cursor: pointer; }
.dashboard-pink { background: #F8D6F8; }
.dashboard-blue { background: #D6E6F8; }
.dashboard-red { background: #F8D6D6; }
.dashboard-yellow { background: #FFF8D6; }
.dashboard-orange { background: #F8D6C1; }
.dashboard-count { font-size: 2.2rem; font-weight: bold; margin-top: 0.5rem; }
.restock-list div { font-size: 1.1rem; margin-bottom: 0.2rem; }
.stat-card { background:#ffffff; border:none; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.stat-card{ transition: transform .18s ease, box-shadow .18s ease; }
.stat-card:hover{ transform: translateY(-4px); box-shadow:0 10px 24px rgba(0,0,0,0.12); }
.stat-green{ background:linear-gradient(135deg,#eafbe7,#d7f3d3); }
.stat-blue{ background:linear-gradient(135deg,#e7f0fb,#d6e6f8); }
.stat-yellow{ background:linear-gradient(135deg,#fff8d6,#fbeec4); }
.stat-white{ background:#fff; }
.stat-value { font-size:1.8rem; font-weight:700; color:#385E42; }
.stat-label { color:#6c757d; letter-spacing: .5px; text-transform: uppercase; font-size:.8rem; }
.icon-circle{ display:flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:50%; font-size:20px; }

/* Scrollbar styling for popular products list */
#popularProductsList::-webkit-scrollbar{ width: 6px; }
#popularProductsList::-webkit-scrollbar-track{ background:#f1f1f1; border-radius:3px; }
#popularProductsList::-webkit-scrollbar-thumb{ background:#7bb47b; border-radius:3px; }
#popularProductsList::-webkit-scrollbar-thumb:hover{ background:#5aa65a; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Small inline analytics: Daily and Monthly Sales from AnalyticsController endpoints are not used here;
// We'll fetch lightweight aggregates via a tiny endpoint if available; fallback to render empty charts.
(function(){
  const daily = document.createElement('canvas');
  daily.id = 'dashDailySales';
  const monthly = document.createElement('canvas');
  monthly.id = 'dashMonthlySales';
  const row = document.querySelector('.container-fluid .row');
  if(row){
    const col = document.createElement('div');
    col.className = 'col-12 mt-4';
    col.innerHTML = '<div class="card shadow-sm"><div class="card-header py-2"><strong>Sales Overview</strong></div><div class="card-body"><div class="row"><div class="col-md-6 mb-3" id="dailyWrap"></div><div class="col-md-6 mb-3" id="monthlyWrap"></div></div></div></div>';
    row.parentNode.insertBefore(col, row.nextSibling);
    document.getElementById('dailyWrap').appendChild(daily);
    document.getElementById('monthlyWrap').appendChild(monthly);
  }

  // Try to load compact analytics data if backend exposes it
  fetch('/api/analytics/compact').then(r=>r.ok?r.json():null).then(data=>{
    const dailyLabels = data?.daily?.map(d=>d.day) || [];
    const dailyValues = data?.daily?.map(d=>d.revenue) || [];
    const monthlyLabels = data?.monthly?.map(m=>m.month) || [];
    const monthlyValues = data?.monthly?.map(m=>m.revenue) || [];

    new Chart(document.getElementById('dashDailySales').getContext('2d'),{
      type:'line',data:{labels:dailyLabels,datasets:[{label:'Daily Revenue (₱)',data:dailyValues,borderColor:'#5E8458',backgroundColor:'rgba(94,132,88,0.15)',tension:0.2}]},
      options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
    new Chart(document.getElementById('dashMonthlySales').getContext('2d'),{
      type:'bar',data:{labels:monthlyLabels,datasets:[{label:'Monthly Revenue (₱)',data:monthlyValues,backgroundColor:'rgba(123,180,123,0.9)'}]},
      options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });
  }).catch(()=>{});
})();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.stat-card { background:#ffffff; border:none; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.stat-value { font-size:1.8rem; font-weight:700; color:#385E42; }
.stat-label { color:#6c757d; letter-spacing: .5px; text-transform: uppercase; font-size:.8rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Inject extra analytics cards without touching existing content
(function(){
  const wrap = document.querySelector('.container-fluid .row .col.pt-4');
  if(!wrap) return;
  const row = document.createElement('div');
  row.className = 'row g-4 mt-1';
  row.innerHTML = `
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card p-3 text-center">
        <div class="stat-label">Total Customers</div>
        <div class="stat-value">${Number(${JSON.stringify(isset($totalCustomers) ? $totalCustomers : 0)}).toLocaleString()}</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card p-3 text-center">
        <div class="stat-label">Total Products</div>
        <div class="stat-value">${Number(${JSON.stringify(isset($totalProducts) ? $totalProducts : 0)}).toLocaleString()}</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card p-3 text-center">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">${Number(${JSON.stringify(isset($totalOrders) ? $totalOrders : 0)}).toLocaleString()}</div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card p-3" id="popularProductsCard">
        <div class="stat-label mb-2">Most Popular Products</div>
        <div id="popularProductsList" style="min-height:54px"></div>
      </div>
    </div>`;
  wrap.prepend(row);

  // Render popular products list from server data embedded as JSON
  const popular = ${JSON.stringify(isset($popularProducts) ? $popularProducts : [])};
  const list = document.getElementById('popularProductsList');
  if(Array.isArray(popular) && popular.length){
    list.innerHTML = popular.map(p => `<div class="d-flex justify-content-between small"><span>${p.name}</span><span class="text-muted">x${p.total_quantity}</span></div>`).join('');
  } else {
    list.innerHTML = '<span class="text-muted">No data yet.</span>';
  }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>