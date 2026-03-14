@extends('layouts.dashboard')

@section('title', 'View Services')
@section('page-title', 'Service Orders')

@section('content')
<div class="service-orders-wrap">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="mb-1 fw-bold">Service Orders</h5>
            <p class="text-muted small mb-0">Repair and service job tracking.</p>
        </div>
        @can('create services')
        <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add service</a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100">
                <div class="small text-muted text-uppercase fw-semibold mb-1">Total</div>
                <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100 border-start border-3 border-warning">
                <div class="small text-muted text-uppercase fw-semibold mb-1">Pending</div>
                <div class="fw-bold fs-5 text-warning">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100 border-start border-3 border-info">
                <div class="small text-muted text-uppercase fw-semibold mb-1">In progress</div>
                <div class="fw-bold fs-5 text-info">{{ $stats['in_progress'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100 border-start border-3 border-success">
                <div class="small text-muted text-uppercase fw-semibold mb-1">Completed</div>
                <div class="fw-bold fs-5 text-success">{{ $stats['completed'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100 border-start border-3 border-danger">
                <div class="small text-muted text-uppercase fw-semibold mb-1">Unpaid</div>
                <div class="fw-bold fs-5 text-danger">{{ $stats['unpaid'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="table-card p-3 text-center h-100 border-start border-3 border-danger">
                <div class="small text-muted text-uppercase fw-semibold mb-1">Total due</div>
                <div class="fw-bold text-danger">৳{{ number_format($stats['total_due'], 0) }}</div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header bg-light border-0 py-3">
            <h6 class="mb-0 fw-semibold text-dark"><i class="fas fa-laptop-medical me-2 text-primary"></i>All service orders</h6>
        </div>
        <div class="p-4">
        <form method="GET" action="{{ route('services.index') }}" id="filterForm">
            <!-- Search Bar -->
            <div class="row filter-row mb-3">
                <div class="col-md-4 col-12">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Search by barcode, phone, service #..." autofocus>
                        @if(request('search'))
                            <a href="{{ route('services.index', request()->except('search')) }}" class="btn btn-outline-secondary" title="Clear search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <small class="text-muted">Scan barcode or enter customer phone to search</small>
                </div>
                
                <!-- Payment Status Filter -->
                <div class="col-md-2 col-6">
                    <select class="form-select" name="payment_status" onchange="document.getElementById('filterForm').submit();">
                        <option value="">Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>
                
                <!-- Date From -->
                <div class="col-md-2 col-6">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date" onchange="document.getElementById('filterForm').submit();">
                </div>
                
                <!-- Date To -->
                <div class="col-md-2 col-6">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date" onchange="document.getElementById('filterForm').submit();">
                </div>
                
                <!-- Clear Filters -->
                <div class="col-md-2 col-6">
                    @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-filter-circle-xmark me-2"></i><span class="d-none d-md-inline">Clear Filters</span><span class="d-md-none">Clear</span>
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Status Filter Buttons -->
            <div class="status-filter-group" role="group">
                <button type="submit" name="status" value="" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    All
                </button>
                <button type="submit" name="status" value="pending" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    Pending
                </button>
                <button type="submit" name="status" value="in_progress" class="btn btn-sm {{ request('status') == 'in_progress' ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    In Progress
                </button>
                <button type="submit" name="status" value="completed" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    Completed
                </button>
                <button type="submit" name="status" value="delivered" class="btn btn-sm {{ request('status') == 'delivered' ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    Delivered
                </button>
                <button type="submit" name="status" value="cancelled" class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-primary' : 'btn-outline-primary' }}" form="filterForm">
                    Cancelled
                </button>
            </div>
        </form>
    </div>
    
    @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
        <div class="alert alert-info mb-3">
            <i class="fas fa-filter me-2"></i>
            <strong>Active Filters:</strong>
            @if(request('status'))
                <span class="badge bg-primary me-1">Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}</span>
            @endif
            @if(request('payment_status'))
                <span class="badge bg-primary me-1">Payment: {{ ucfirst(request('payment_status')) }}</span>
            @endif
            @if(request('date_from'))
                <span class="badge bg-primary me-1">From: {{ request('date_from') }}</span>
            @endif
            @if(request('date_to'))
                <span class="badge bg-primary me-1">To: {{ request('date_to') }}</span>
            @endif
            @if(request('search'))
                <span class="badge bg-primary me-1">Search: "{{ request('search') }}"</span>
            @endif
            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-dark ms-2">
                <i class="fas fa-times me-1"></i>Clear All
            </a>
        </div>
    @endif
    
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0" style="width: 100%;">
            <thead class="table-light">
                <tr>
                    <th>Service #</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Receive Date</th>
                    <th>Delivery Date</th>
                    <th>Cost (BDT)</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>
                            <strong class="text-primary">#{{ $service->service_number }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $service->product_name }}</strong>
                                @if($service->serial_number)
                                    <br><small class="text-muted">SN: {{ $service->serial_number }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $service->customer_name }}</strong>
                                <br><small class="text-muted">{{ $service->customer_phone }}</small>
                            </div>
                        </td>
                        <td>{{ $service->receive_date->format('M d, Y') }}</td>
                        <td>
                            @if($service->delivery_date)
                                {{ $service->delivery_date->format('M d, Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>৳{{ number_format($service->service_cost, 2) }}</strong>
                        </td>
                        <td>
                            @if($service->payment_status === 'fully_paid')
                                <span class="badge bg-success">✓ Paid</span>
                            @elseif($service->payment_status === 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                            <br>
                            <small class="text-muted">
                                Paid: ৳{{ number_format($service->paid_amount, 2) }}<br>
                                @if($service->due_amount > 0)
                                    <span class="text-danger fw-semibold">Due: ৳{{ number_format($service->due_amount, 2) }}</span>
                                @else
                                    Due: ৳0.00
                                @endif
                            </small>
                        </td>
                        <td>
                            @php
                                $statusConfig = [
                                    'pending' => ['label' => 'Pending', 'class' => 'bg-secondary', 'icon' => 'fa-clock'],
                                    'in_progress' => ['label' => 'In Progress', 'class' => 'bg-info', 'icon' => 'fa-spinner'],
                                    'completed' => ['label' => 'Completed', 'class' => 'bg-success', 'icon' => 'fa-check-circle'],
                                    'delivered' => ['label' => 'Delivered', 'class' => 'bg-primary', 'icon' => 'fa-truck'],
                                    'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-danger', 'icon' => 'fa-times-circle']
                                ];
                                $currentStatus = $statusConfig[$service->status] ?? $statusConfig['pending'];
                            @endphp
                            <div class="d-flex flex-column gap-1">
                                @can('update service-status')
                                    <span
                                        role="button"
                                        tabindex="0"
                                        class="badge {{ $currentStatus['class'] }}"
                                        style="cursor: pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#serviceStatusModal"
                                        data-service-id="{{ $service->id }}"
                                        data-service-number="{{ $service->service_number }}"
                                        data-current-status="{{ $service->status }}">
                                        <i class="fas {{ $currentStatus['icon'] }} me-1"></i>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                @else
                                    <span class="badge {{ $currentStatus['class'] }}">
                                        <i class="fas {{ $currentStatus['icon'] }} me-1"></i>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                @endcan
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($service->due_amount > 0)
                                <a href="{{ route('services.show', $service) }}#collectPayment" class="btn btn-sm btn-success" title="Collect Due Payment">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </a>
                                @endif
                                <a href="{{ route('services.print', $service) }}" class="btn btn-sm btn-outline-info" title="Print Memo" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-laptop-medical fa-3x text-muted mb-3 d-block"></i>
                            @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                                <p class="text-muted">No service orders found matching your filters. <a href="{{ route('services.index') }}">Clear filters</a> or <a href="{{ route('services.create') }}">create a new service order</a></p>
                            @else
                                <p class="text-muted">No service orders found. <a href="{{ route('services.create') }}">Create the first service order</a></p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($services->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-3">
            {{ $services->links() }}
        </div>
    @endif
    
    @if($services->count() > 0)
        <div class="mt-3 text-muted">
            <small>Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} service orders</small>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchField = document.getElementById('search');
    
    // Auto-submit search after 500ms of no typing (debounce)
    let searchTimeout;
    if (searchField) {
        searchField.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (searchField.value.length >= 2 || searchField.value.length === 0) {
                    document.getElementById('filterForm').submit();
                }
            }, 500);
        });
        
        // Handle Enter key for immediate search
        searchField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                document.getElementById('filterForm').submit();
            }
        });
        
        // Auto-focus search field if empty (for barcode scanning)
        if (!searchField.value) {
            // Small delay to ensure page is loaded
            setTimeout(function() {
                searchField.focus();
            }, 100);
        }
        
        // Handle barcode scanner input (scanners send Enter after barcode)
        searchField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.value.length > 0) {
                e.preventDefault();
                clearTimeout(searchTimeout);
                // Visual feedback
                this.style.backgroundColor = '#d4edda';
                setTimeout(function() {
                    searchField.style.backgroundColor = '';
                    document.getElementById('filterForm').submit();
                }, 200);
            }
        });
    }

    // Service status modal: populate and handle status selection
    const statusModalEl = document.getElementById('serviceStatusModal');
    if (statusModalEl && typeof bootstrap !== 'undefined') {
        statusModalEl.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;
            const serviceId = trigger.getAttribute('data-service-id');
            const serviceNumber = trigger.getAttribute('data-service-number');
            const currentStatus = trigger.getAttribute('data-current-status') || 'pending';
            const form = statusModalEl.querySelector('form');
            const serviceIdInput = statusModalEl.querySelector('input[name="service_id"]');
            const statusInput = statusModalEl.querySelector('input[name="status"]');
            const titleSpan = statusModalEl.querySelector('#serviceStatusModalService');
            if (form) form.action = '{{ url("services") }}/' + serviceId + '/status';
            if (serviceIdInput) serviceIdInput.value = serviceId;
            if (statusInput) statusInput.value = currentStatus;
            if (titleSpan) titleSpan.textContent = '#' + serviceNumber;
            statusModalEl.querySelectorAll('[data-status-value]').forEach(function(btn) {
                var val = btn.getAttribute('data-status-value');
                btn.classList.toggle('btn-success', val === currentStatus);
                btn.classList.toggle('btn-outline-secondary', val !== currentStatus);
            });
        });
        statusModalEl.querySelectorAll('[data-status-value]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var value = this.getAttribute('data-status-value');
                var statusInput = statusModalEl.querySelector('input[name="status"]');
                if (statusInput) statusInput.value = value;
                statusModalEl.querySelectorAll('[data-status-value]').forEach(function(b) {
                    b.classList.remove('btn-success');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success');
            });
        });
    }
});

// Professional Status Dropdown Functions
let activeDropdown = null;

function toggleStatusDropdown(serviceId) {
    const dropdown = document.getElementById(`statusDropdown${serviceId}`);
    const button = document.querySelector(`.status-select-btn-${serviceId}`);
    const wrapper = document.querySelector(`.status-select-wrapper[data-service-id="${serviceId}"]`);
    
    // Close all other dropdowns
    document.querySelectorAll('.status-dropdown').forEach(dd => {
        if (dd.id !== `statusDropdown${serviceId}`) {
            dd.classList.remove('show');
            dd.classList.remove('dropdown-up');
            const btn = dd.closest('.status-select-wrapper')?.querySelector('.status-select-btn');
            if (btn) btn.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
    if (dropdown && button && wrapper) {
        const isOpen = dropdown.classList.contains('show');
        
        if (!isOpen) {
            // Show dropdown temporarily to measure it
            dropdown.style.visibility = 'hidden';
            dropdown.style.display = 'block';
            dropdown.classList.remove('dropdown-up');
            
            // Calculate position using fixed positioning
            const buttonRect = button.getBoundingClientRect();
            const dropdownHeight = dropdown.offsetHeight || 200;
            const spaceBelow = window.innerHeight - buttonRect.bottom;
            const spaceAbove = buttonRect.top;
            const scrollY = window.scrollY || window.pageYOffset;
            const scrollX = window.scrollX || window.pageXOffset;
            
            // Use fixed positioning to escape table boundaries
            dropdown.style.position = 'fixed';
            
            // Calculate vertical position
            let topPosition;
            if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                // Open upward
                topPosition = buttonRect.top + scrollY - dropdownHeight - 5;
                dropdown.classList.add('dropdown-up');
            } else {
                // Open downward
                topPosition = buttonRect.bottom + scrollY + 5;
                dropdown.classList.remove('dropdown-up');
            }
            
            dropdown.style.top = topPosition + 'px';
            
            // Calculate horizontal position
            const spaceRight = window.innerWidth - buttonRect.right;
            const spaceLeft = buttonRect.left;
            
            if (spaceRight < 180 && spaceLeft > spaceRight) {
                dropdown.style.left = 'auto';
                dropdown.style.right = (window.innerWidth - buttonRect.right) + 'px';
            } else {
                dropdown.style.left = buttonRect.left + scrollX + 'px';
                dropdown.style.right = 'auto';
            }
            
            // Make visible and add show class
            dropdown.style.visibility = 'visible';
            dropdown.classList.add('show');
            button.classList.add('active');
            activeDropdown = serviceId;
        } else {
            dropdown.classList.remove('show');
            dropdown.classList.remove('dropdown-up');
            dropdown.style.visibility = '';
            dropdown.style.display = '';
            dropdown.style.position = '';
            dropdown.style.top = '';
            dropdown.style.left = '';
            dropdown.style.right = '';
            button.classList.remove('active');
            activeDropdown = null;
        }
    }
    
    // Setup/remove click outside listener
    if (activeDropdown) {
        // Use capture phase to ensure we catch the event early
        setTimeout(() => {
            document.addEventListener('click', closeDropdownOnOutsideClick, true);
        }, 10);
    } else {
        document.removeEventListener('click', closeDropdownOnOutsideClick, true);
    }
}

// Global click outside handler for status dropdowns
function closeDropdownOnOutsideClick(event) {
    if (!activeDropdown) return;
    
    // Check if click is inside any status dropdown wrapper
    const clickedElement = event.target;
    const wrapper = clickedElement.closest('.status-select-wrapper');
    const isDropdownOption = clickedElement.closest('.status-option');
    const isDropdownButton = clickedElement.closest('.status-select-btn');
    
    // Check if the clicked wrapper belongs to the active dropdown
    const isActiveDropdown = wrapper && wrapper.getAttribute('data-service-id') == activeDropdown;
    
    // Don't close if clicking inside the active dropdown
    if (isActiveDropdown || isDropdownOption || isDropdownButton) {
        // If clicking on the button of active dropdown, let toggleStatusDropdown handle it
        if (isDropdownButton && wrapper && wrapper.getAttribute('data-service-id') == activeDropdown) {
            return;
        }
        // If clicking on option inside active dropdown, don't close
        if (isDropdownOption) {
            return;
        }
        return;
    }
    
    // Close the active dropdown
    const dropdown = document.getElementById(`statusDropdown${activeDropdown}`);
    const button = document.querySelector(`.status-select-btn-${activeDropdown}`);
    
    if (dropdown) {
        dropdown.classList.remove('show');
        dropdown.classList.remove('dropdown-up');
        dropdown.style.visibility = '';
        dropdown.style.display = '';
        dropdown.style.position = '';
        dropdown.style.top = '';
        dropdown.style.left = '';
        dropdown.style.right = '';
    }
    
    if (button) {
        button.classList.remove('active');
    }
    
    activeDropdown = null;
    document.removeEventListener('click', closeDropdownOnOutsideClick, true);
}

// Initialize global click listener on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set up a global click listener that always checks for dropdown closures
    document.addEventListener('click', function(event) {
        if (activeDropdown) {
            closeDropdownOnOutsideClick(event);
        }
    }, true);
});

// Handle window scroll to close dropdowns
window.addEventListener('scroll', function() {
    if (activeDropdown) {
        const dropdown = document.getElementById(`statusDropdown${activeDropdown}`);
        const button = document.querySelector(`.status-select-btn-${activeDropdown}`);
        if (dropdown) {
            dropdown.classList.remove('show');
            dropdown.classList.remove('dropdown-up');
            dropdown.style.visibility = '';
            dropdown.style.display = '';
            dropdown.style.position = '';
            dropdown.style.top = '';
            dropdown.style.left = '';
            dropdown.style.right = '';
        }
        if (button) button.classList.remove('active');
        document.removeEventListener('click', closeDropdownOnOutsideClick, true);
        activeDropdown = null;
    }
}, true);

// Handle window resize to close dropdowns
window.addEventListener('resize', function() {
    if (activeDropdown) {
        const dropdown = document.getElementById(`statusDropdown${activeDropdown}`);
        const button = document.querySelector(`.status-select-btn-${activeDropdown}`);
        if (dropdown) {
            dropdown.classList.remove('show');
            dropdown.classList.remove('dropdown-up');
            dropdown.style.visibility = '';
            dropdown.style.display = '';
            dropdown.style.position = '';
            dropdown.style.top = '';
            dropdown.style.left = '';
            dropdown.style.right = '';
        }
        if (button) button.classList.remove('active');
        document.removeEventListener('click', closeDropdownOnOutsideClick, true);
        activeDropdown = null;
    }
});

// Status configuration
const statusConfig = {
    'pending': { label: 'Pending', icon: 'fa-clock', color: '#6c757d' },
    'in_progress': { label: 'In Progress', icon: 'fa-spinner', color: '#0dcaf0' },
    'completed': { label: 'Completed', icon: 'fa-check-circle', color: '#198754' },
    'delivered': { label: 'Delivered', icon: 'fa-truck', color: '#0d6efd' },
    'cancelled': { label: 'Cancelled', icon: 'fa-times-circle', color: '#dc3545' }
};

function selectStatus(serviceId, newStatus, label, icon, color) {
    const button = document.querySelector(`.status-select-btn-${serviceId}`);
    const dropdown = document.getElementById(`statusDropdown${serviceId}`);
    const originalStatus = button ? button.getAttribute('data-current-status') : null;
    
    // Close dropdown immediately and completely when status is selected
    if (dropdown) {
        dropdown.classList.remove('show');
        dropdown.classList.remove('dropdown-up');
        dropdown.style.visibility = 'hidden';
        dropdown.style.display = 'none';
        dropdown.style.position = '';
        dropdown.style.top = '';
        dropdown.style.left = '';
        dropdown.style.right = '';
    }
    if (button) {
        button.classList.remove('active');
    }
    
    // Clear active dropdown and remove listeners
    activeDropdown = null;
    document.removeEventListener('click', closeDropdownOnOutsideClick, true);
    
    // Add loading state
    if (button) {
        button.classList.add('loading');
        button.disabled = true;
    }
    
    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                  document.querySelector('input[name="_token"]')?.value;
    
    // Send AJAX request
    fetch(`/services/${serviceId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && button) {
            // Update button appearance
            button.setAttribute('data-current-status', newStatus);
            button.style.setProperty('--status-color', color);
            
            // Update button content
            const statusText = button.querySelector('.status-text');
            const statusIcon = button.querySelector('.fa:not(.status-arrow)');
            
            if (statusText) statusText.textContent = label;
            if (statusIcon) {
                statusIcon.className = `fas ${icon} me-2`;
            }
            
            // Show success animation
            button.style.borderColor = '#10b981';
            button.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
            setTimeout(function() {
                button.style.borderColor = '';
                button.style.boxShadow = '';
            }, 1000);
            
            showNotification('Status updated successfully!', 'success');
        } else {
            showNotification('Failed to update status. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Remove loading state
        if (button) {
            button.classList.remove('loading');
            button.disabled = false;
        }
    });
}

// Simple notification function
function showNotification(message, type) {
    // Remove existing notifications
    const existing = document.querySelector('.status-update-notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `status-update-notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        notification.classList.remove('show');
        setTimeout(function() {
            notification.remove();
        }, 150);
    }, 3000);
}
</script>
@endpush
@endsection

@section('modals')
<!-- Service Status Change Modal -->
<div class="modal fade" id="serviceStatusModal" tabindex="-1" aria-labelledby="serviceStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceStatusModalLabel">
                    <i class="fas fa-laptop-medical me-2"></i>Update Service Status <small class="text-muted" id="serviceStatusModalService"></small>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="#">
                @csrf
                @method('PATCH')
                <input type="hidden" name="service_id">
                <input type="hidden" name="status">
                <div class="modal-body">
                    <p class="text-muted mb-3">Choose a new status for this service order:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-status-value="pending">Pending</button>
                        <button type="button" class="btn btn-outline-secondary" data-status-value="in_progress">In Progress</button>
                        <button type="button" class="btn btn-outline-secondary" data-status-value="completed">Completed</button>
                        <button type="button" class="btn btn-outline-secondary" data-status-value="delivered">Delivered</button>
                        <button type="button" class="btn btn-outline-secondary" data-status-value="cancelled">Cancelled</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

