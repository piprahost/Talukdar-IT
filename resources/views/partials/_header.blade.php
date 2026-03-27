<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileMenu()"></div>

<!-- Top Navbar -->
<div class="top-navbar">
    <div class="nav-left">
        <button class="mobile-menu-toggle no-print" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
        <h5>@yield('page-title', 'Dashboard')</h5>
    </div>
    <div class="nav-right">
        @can('create sales')
        <a href="{{ route('quick-sell.index') }}" class="quick-sell-btn no-print" data-bs-toggle="tooltip" title="Quick Sell">
            <i class="fas fa-bolt"></i>
            <span>Quick Sell</span>
        </a>
        @endcan
        <div class="nav-icon no-print" data-bs-toggle="tooltip" title="Calculator" onclick="openCalculator()" style="cursor: pointer;">
            <i class="fas fa-calculator"></i>
        </div>
        <div class="user-profile dropdown">
            <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="px-3 py-2">
                        <div class="fw-bold">{{ auth()->user()->name ?? 'User' }}</div>
                        <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                @can('view settings')
                <li><a class="dropdown-item" href="{{ route('company-info.edit') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                @endcan
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Calculator Modal -->
<div class="modal fade" id="calculatorModal" tabindex="-1" aria-labelledby="calculatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <h5 class="modal-title" id="calculatorModalLabel"><i class="fas fa-calculator me-2"></i>Calculator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: #f8f9fa;">
                <div class="calculator-container">
                    <div class="calculator-display mb-3">
                        <input type="text" id="calcDisplay" class="form-control form-control-lg text-end" value="0" readonly style="font-size: 24px; font-weight: bold; background: white; border: 2px solid #10b981;">
                    </div>
                    <div class="calculator-buttons">
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button class="btn btn-secondary w-100" onclick="calcClear()">C</button></div>
                            <div class="col-3"><button class="btn btn-secondary w-100" onclick="calcClearEntry()">CE</button></div>
                            <div class="col-3"><button class="btn btn-secondary w-100" onclick="calcBackspace()">⌫</button></div>
                            <div class="col-3"><button class="btn btn-warning w-100" onclick="calcOperation('/')">/</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('7')">7</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('8')">8</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('9')">9</button></div>
                            <div class="col-3"><button class="btn btn-warning w-100" onclick="calcOperation('*')">×</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('4')">4</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('5')">5</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('6')">6</button></div>
                            <div class="col-3"><button class="btn btn-warning w-100" onclick="calcOperation('-')">−</button></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('1')">1</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('2')">2</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('3')">3</button></div>
                            <div class="col-3"><button class="btn btn-warning w-100" onclick="calcOperation('+')">+</button></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><button class="btn btn-light w-100" onclick="calcInput('0')">0</button></div>
                            <div class="col-3"><button class="btn btn-light w-100" onclick="calcInput('.')">.</button></div>
                            <div class="col-3"><button class="btn btn-success w-100" onclick="calcCalculate()">=</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let calcValue = '0';
let calcOperator = '';
let calcPreviousValue = '';
let calcKeyboardBound = false;

function openCalculator() {
    const modalEl = document.getElementById('calculatorModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    calcValue = '0';
    calcOperator = '';
    calcPreviousValue = '';
    document.getElementById('calcDisplay').value = '0';
    bindCalculatorKeyboard();
    modal.show();
}

function calcInput(value) {
    if (calcValue === '0') {
        calcValue = value;
    } else {
        calcValue += value;
    }
    document.getElementById('calcDisplay').value = calcValue;
}

function calcOperation(op) {
    if (calcPreviousValue !== '' && calcOperator !== '') {
        calcCalculate();
    }
    calcPreviousValue = calcValue;
    calcValue = '0';
    calcOperator = op;
}

function calcCalculate() {
    if (calcPreviousValue === '' || calcOperator === '') return;
    
    let result;
    const prev = parseFloat(calcPreviousValue);
    const current = parseFloat(calcValue);
    
    switch(calcOperator) {
        case '+':
            result = prev + current;
            break;
        case '-':
            result = prev - current;
            break;
        case '*':
            result = prev * current;
            break;
        case '/':
            if (current === 0) {
                alert('Cannot divide by zero!');
                return;
            }
            result = prev / current;
            break;
        default:
            return;
    }
    
    calcValue = result.toString();
    calcPreviousValue = '';
    calcOperator = '';
    document.getElementById('calcDisplay').value = calcValue;
}

function calcClear() {
    calcValue = '0';
    calcOperator = '';
    calcPreviousValue = '';
    document.getElementById('calcDisplay').value = '0';
}

function calcClearEntry() {
    calcValue = '0';
    document.getElementById('calcDisplay').value = '0';
}

function calcBackspace() {
    if (calcValue.length > 1) {
        calcValue = calcValue.slice(0, -1);
    } else {
        calcValue = '0';
    }
    document.getElementById('calcDisplay').value = calcValue;
}

function isCalculatorOpen() {
    const modal = document.getElementById('calculatorModal');
    return modal && modal.classList.contains('show');
}

function handleCalculatorKeydown(event) {
    if (!isCalculatorOpen()) {
        return;
    }

    const key = event.key;

    if (/[0-9]/.test(key)) {
        event.preventDefault();
        calcInput(key);
        return;
    }

    if (key === '.') {
        event.preventDefault();
        if (!calcValue.includes('.')) {
            calcInput('.');
        }
        return;
    }

    if (['+', '-', '*', '/'].includes(key)) {
        event.preventDefault();
        calcOperation(key);
        return;
    }

    if (key === 'Enter' || key === '=') {
        event.preventDefault();
        calcCalculate();
        return;
    }

    if (key === 'Backspace') {
        event.preventDefault();
        calcBackspace();
        return;
    }

    if (key === 'Delete') {
        event.preventDefault();
        calcClearEntry();
        return;
    }

    if (key.toLowerCase() === 'c') {
        event.preventDefault();
        calcClear();
        return;
    }

    if (key === 'Escape') {
        event.preventDefault();
        const modalEl = document.getElementById('calculatorModal');
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }
}

function bindCalculatorKeyboard() {
    if (calcKeyboardBound) {
        return;
    }

    document.addEventListener('keydown', handleCalculatorKeydown);
    calcKeyboardBound = true;
}
</script>

