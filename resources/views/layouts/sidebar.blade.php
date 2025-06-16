<!-- Sidebar -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="p-4">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">DAILY LAUNDRY</h2>
        <ul class="space-y-3">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">🏠 Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customers.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">🧍 Customers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-types.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">🛎️ Service Types</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">🔄 Transactions</span>
                </a>
            </li>
            <li>
                <a href="{{ route('payments.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">💰 Payments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('order.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">📦 Orders</span>
                </a>
            </li>
            <li>
                <a href="{{ route('kurir.pengambilan.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">📥 Pengambilan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('kurir.pengantaran.index') }}"
                   class="flex items-center p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg no-underline">
                    <span class="flex-1">🚚 Pengantaran</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Overlay backdrop -->
<div id="backdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 md:hidden"></div>

<!-- Hamburger Toggle -->
<button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg">
    <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<!-- Main Content Wrapper -->
<div id="mainContent" class="transition-transform duration-300 ease-in-out md:ml-64">
    <!-- Masukkan konten halaman Anda di sini -->
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('backdrop');
    const content = document.getElementById('mainContent');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        content.classList.add('ml-64');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        content.classList.remove('ml-64');
    }

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    backdrop.addEventListener('click', closeSidebar);
</script>
