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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header calculator-modal-header">
                <h5 class="modal-title" id="calculatorModalLabel"><i class="fas fa-calculator me-2"></i>Calculator</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body calculator-modal-body">
                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="calculator-panel">
                            <div class="calculator-expression" id="calcExpression">&nbsp;</div>
                            <div class="calculator-display mb-2">
                                <input type="text" id="calcDisplay" class="form-control form-control-lg text-end" value="0" readonly>
                            </div>
                            <div class="calculator-shortcuts mb-3">Shortcuts: Enter/=, Del=CE, C=Clear, Esc=Close</div>
                            <div class="calculator-buttons">
                                <div class="row g-2 mb-2">
                                    <div class="col-3"><button class="btn calc-btn calc-btn-muted w-100" onclick="calcClear()">C</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-muted w-100" onclick="calcClearEntry()">CE</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-muted w-100" onclick="calcBackspace()">⌫</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-op w-100" onclick="calcOperation('/')">/</button></div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('7')">7</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('8')">8</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('9')">9</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-op w-100" onclick="calcOperation('*')">×</button></div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('4')">4</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('5')">5</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('6')">6</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-op w-100" onclick="calcOperation('-')">−</button></div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('1')">1</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('2')">2</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('3')">3</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-op w-100" onclick="calcOperation('+')">+</button></div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('0')">0</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-number w-100" onclick="calcInput('.')">.</button></div>
                                    <div class="col-3"><button class="btn calc-btn calc-btn-equals w-100" onclick="calcCalculate()">=</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="calculator-history-panel">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-semibold"><i class="fas fa-history me-2"></i>History</div>
                                <button class="btn btn-sm btn-outline-danger" onclick="calcClearHistory()">Clear</button>
                            </div>
                            <div class="calculator-history-list" id="calcHistoryList">
                                <div class="calculator-history-empty">No calculations yet</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calculator-modal-header {
    background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
    color: #fff;
}
.calculator-modal-body {
    background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
}
.calculator-panel,
.calculator-history-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}
.calculator-expression {
    min-height: 20px;
    color: #64748b;
    font-size: 0.9rem;
    text-align: right;
    margin-bottom: 0.35rem;
}
#calcDisplay {
    font-size: 2rem;
    font-weight: 700;
    border: 2px solid #14b8a6;
    color: #0f172a;
}
.calculator-shortcuts {
    color: #64748b;
    font-size: 0.78rem;
}
.calc-btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.55rem 0;
    border: none;
}
.calc-btn-number {
    background: #f8fafc;
    color: #0f172a;
}
.calc-btn-number:hover {
    background: #e2e8f0;
}
.calc-btn-muted {
    background: #334155;
    color: #fff;
}
.calc-btn-muted:hover {
    background: #1e293b;
    color: #fff;
}
.calc-btn-op {
    background: #f59e0b;
    color: #fff;
}
.calc-btn-op:hover {
    background: #d97706;
    color: #fff;
}
.calc-btn-equals {
    background: #10b981;
    color: #fff;
}
.calc-btn-equals:hover {
    background: #059669;
    color: #fff;
}
.calculator-history-list {
    max-height: 318px;
    overflow-y: auto;
}
.calculator-history-item {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.5rem 0.6rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    background: #f8fafc;
}
.calculator-history-item:hover {
    background: #eef2ff;
}
.calculator-history-expression {
    font-size: 0.85rem;
    color: #475569;
}
.calculator-history-result {
    font-weight: 700;
    color: #0f172a;
}
.calculator-history-empty {
    color: #94a3b8;
    text-align: center;
    padding: 2rem 0.5rem;
}
</style>

<script>
let calcValue = '0';
let calcOperator = '';
let calcPreviousValue = '';
let calcKeyboardBound = false;
let calcHistory = [];
const CALC_HISTORY_KEY = 'talukdar_calc_history';

function openCalculator() {
    const modalEl = document.getElementById('calculatorModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    calcValue = '0';
    calcOperator = '';
    calcPreviousValue = '';
    updateCalculatorDisplay();
    loadCalculatorHistory();
    renderCalculatorHistory();
    bindCalculatorKeyboard();
    modal.show();
}

function calcInput(value) {
    if (value === '.' && calcValue.includes('.')) {
        return;
    }

    if (calcValue === '0' && value !== '.') {
        calcValue = value;
    } else {
        calcValue += value;
    }
    updateCalculatorDisplay();
}

function calcOperation(op) {
    if (calcPreviousValue !== '' && calcOperator !== '' && calcValue !== '0') {
        const result = getCalcResult();
        if (result === null) {
            return;
        }
        calcValue = result.toString();
        calcPreviousValue = '';
        calcOperator = '';
    }

    calcPreviousValue = calcValue;
    calcValue = '0';
    calcOperator = op;
    updateCalculatorDisplay();
}

function getCalcResult() {
    const prev = parseFloat(calcPreviousValue);
    const current = parseFloat(calcValue);

    switch(calcOperator) {
        case '+':
            return prev + current;
        case '-':
            return prev - current;
        case '*':
            return prev * current;
        case '/':
            if (current === 0) {
                alert('Cannot divide by zero!');
                return null;
            }
            return prev / current;
        default:
            return null;
    }
}

function calcCalculate() {
    if (calcPreviousValue === '' || calcOperator === '') return;

    const expression = `${calcPreviousValue}${calcOperator}${calcValue}`;
    const result = getCalcResult();
    if (result === null) {
        return;
    }

    addCalculationToHistory(expression, result);
    calcValue = result.toString();
    calcPreviousValue = '';
    calcOperator = '';
    updateCalculatorDisplay();
}

function calcClear() {
    calcValue = '0';
    calcOperator = '';
    calcPreviousValue = '';
    updateCalculatorDisplay();
}

function calcClearEntry() {
    calcValue = '0';
    updateCalculatorDisplay();
}

function calcBackspace() {
    if (calcValue.length > 1) {
        calcValue = calcValue.slice(0, -1);
    } else {
        calcValue = '0';
    }
    updateCalculatorDisplay();
}

function updateCalculatorDisplay() {
    const displayEl = document.getElementById('calcDisplay');
    const expressionEl = document.getElementById('calcExpression');

    if (displayEl) {
        displayEl.value = calcValue;
    }

    if (!expressionEl) {
        return;
    }

    if (calcPreviousValue !== '' && calcOperator !== '') {
        expressionEl.textContent = `${calcPreviousValue}${calcOperator}${calcValue}`;
    } else {
        expressionEl.innerHTML = '&nbsp;';
    }
}

function loadCalculatorHistory() {
    try {
        const raw = localStorage.getItem(CALC_HISTORY_KEY);
        calcHistory = raw ? JSON.parse(raw) : [];
        if (!Array.isArray(calcHistory)) {
            calcHistory = [];
        }
    } catch (error) {
        calcHistory = [];
    }
}

function saveCalculatorHistory() {
    localStorage.setItem(CALC_HISTORY_KEY, JSON.stringify(calcHistory));
}

function addCalculationToHistory(expression, result) {
    calcHistory.unshift({
        expression,
        result: result.toString()
    });

    calcHistory = calcHistory.slice(0, 20);
    saveCalculatorHistory();
    renderCalculatorHistory();
}

function useHistoryResult(result) {
    calcValue = result.toString();
    calcOperator = '';
    calcPreviousValue = '';
    updateCalculatorDisplay();
}

function calcClearHistory() {
    calcHistory = [];
    saveCalculatorHistory();
    renderCalculatorHistory();
}

function renderCalculatorHistory() {
    const list = document.getElementById('calcHistoryList');
    if (!list) {
        return;
    }

    if (calcHistory.length === 0) {
        list.innerHTML = '<div class="calculator-history-empty">No calculations yet</div>';
        return;
    }

    list.innerHTML = calcHistory.map((item) => `
        <div class="calculator-history-item" onclick="useHistoryResult('${item.result.replace(/'/g, "\\'")}')">
            <div class="calculator-history-expression">${item.expression}</div>
            <div class="calculator-history-result">= ${item.result}</div>
        </div>
    `).join('');
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
        calcInput('.');
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

