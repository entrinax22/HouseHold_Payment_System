<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

// ====================
// Reactive States
// ====================
const totalContributions = ref(0);
const totalPayments = ref(0);
const paidContributions = ref(0);
const pendingPayments = ref(0);

const chartLabels = ref<string[]>([]);
const chartData = ref<number[]>([]);
const fullChartLabels = ref<string[]>([]);
const fullChartData = ref<number[]>([]);
const contributions = ref<any[]>([]);
const isGeneratingPDF = ref(false);

// ====================
// Fetch Dashboard Data
// ====================
const fetchData = async () => {
    try {
        const response = await axios.get(route('admin.dashboard'));
        if (response.data.result === true) {
            const d = response.data.data;

            // Map the correct fields from API
            totalContributions.value = parseFloat(d.totals.total_contributions) || 0;
            totalPayments.value = parseFloat(d.totals.total_payments) || 0;
            paidContributions.value = parseInt(d.totals.paid_contributions) || 0;
            pendingPayments.value = parseFloat(d.totals.pending_payments) || 0;

            // Store full data
            fullChartLabels.value = d.chart.labels || [];
            fullChartData.value = (d.chart.data || []).map((val) => parseFloat(val) || 0);

            // Apply initial filter
            filterChartData(selectedPeriod.value);

            contributions.value = d.recent || [];
        }
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
};

// Filter chart data based on period
const filterChartData = (period: string) => {
    let monthsToShow = 12;

    if (period === '6months') {
        monthsToShow = 6;
    } else if (period === '3months') {
        monthsToShow = 3;
    }

    // Get the last N months
    chartLabels.value = fullChartLabels.value.slice(-monthsToShow);
    chartData.value = fullChartData.value.slice(-monthsToShow);
};

// Handle period change
const changePeriod = (period: string) => {
    selectedPeriod.value = period;
    filterChartData(period);
};

// Generate PDF Report
const generatePDFReport = () => {
    isGeneratingPDF.value = true;

    // Create iframe
    const iframe = document.createElement('iframe');
    iframe.style.position = 'absolute';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    document.body.appendChild(iframe);

    const iframeDoc = iframe.contentWindow?.document;
    if (!iframeDoc) return;

    // Generate HTML content for PDF
    const htmlContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Contributions Report - Trinidad Smart Homes</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Arial', sans-serif;
                    padding: 40px;
                    color: #1f2937;
                }
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                    padding-bottom: 30px;
                    border-bottom: 3px solid #3b82f6;
                }
                .logo-container {
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 120px;
                    height: auto;
                    margin: 0 auto;
                    display: block;
                }
                .brand-name {
                    font-size: 28px;
                    font-weight: bold;
                    color: #1f2937;
                    margin-bottom: 5px;
                    letter-spacing: 0.5px;
                }
                .header h1 {
                    font-size: 24px;
                    color: #3b82f6;
                    margin-bottom: 10px;
                    font-weight: 600;
                }
                .header p {
                    color: #6b7280;
                    font-size: 14px;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 20px;
                    margin-bottom: 40px;
                }
                .stat-card {
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 20px;
                    background: #f9fafb;
                }
                .stat-card h3 {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 10px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .stat-card .value {
                    font-size: 28px;
                    font-weight: bold;
                    color: #1f2937;
                }
                .chart-section {
                    margin-bottom: 40px;
                }
                .chart-section h2 {
                    font-size: 20px;
                    margin-bottom: 20px;
                    color: #1f2937;
                }
                .chart-container {
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 20px;
                    background: white;
                }
                .chart {
                    display: flex;
                    align-items: flex-end;
                    height: 300px;
                    gap: 10px;
                    padding: 20px 0;
                    border-bottom: 2px solid #e5e7eb;
                    margin: 30px 0; /* Add margin for labels */
                }
                .bar {
                    flex: 1;
                    background: linear-gradient(to top, #3b82f6, #60a5fa);
                    border-radius: 4px 4px 0 0;
                    position: relative;
                    min-height: 1px;
                        transition: all 0.3s ease;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                }
                .bar:hover {
                    background: linear-gradient(to top, #2563eb, #3b82f6);
                    transform: scaleY(1.02);
                }
                .bar-label {
                    position: absolute;
                    bottom: -25px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 11px;
                    color: #6b7280;
                    white-space: nowrap;
                }
                .bar-value {
                    position: absolute;
                    top: -25px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 10px;
                    font-weight: bold;
                    color: #1f2937;
                    white-space: nowrap;
                    background: rgba(255, 255, 255, 0.9);
                    padding: 2px 6px;
                    border-radius: 4px;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                }
                .transactions-section {
                    page-break-inside: avoid;
                }
                .transactions-section h2 {
                    font-size: 20px;
                    margin-bottom: 20px;
                    color: #1f2937;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    overflow: hidden;
                }
                thead {
                    background: #f3f4f6;
                }
                th {
                    padding: 12px;
                    text-align: left;
                    font-size: 12px;
                    font-weight: bold;
                    color: #374151;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                td {
                    padding: 12px;
                    border-top: 1px solid #e5e7eb;
                    font-size: 13px;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-size: 11px;
                    font-weight: bold;
                }
                .status-paid {
                    background: #d1fae5;
                    color: #065f46;
                }
                .status-pending {
                    background: #fef3c7;
                    color: #92400e;
                }
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #e5e7eb;
                    text-align: center;
                    color: #6b7280;
                    font-size: 12px;
                }
                .footer-brand {
                    font-weight: bold;
                    color: #3b82f6;
                }
                    @media print {
                        .bar {
                            background: #3b82f6 !important;
                            background-color: #3b82f6 !important;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                            color-adjust: exact !important;
                        }
                    }
                @media print {
                    body {
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logo-container">
                    <img src="/logo.png" alt="Trinidad Smart Homes Logo" class="logo" />
                </div>
                <div class="brand-name">Trinidad Smart Homes</div>
                <h1>Contributions Report</h1>
                <p>Generated on ${new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                })}</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Contributions</h3>
                    <div class="value">${formatCurrency(totalContributions.value)}</div>
                </div>
                <div class="stat-card">
                    <h3>Total Payments</h3>
                    <div class="value">${formatCurrency(totalPayments.value)}</div>
                </div>
                <div class="stat-card">
                    <h3>Paid Contributions</h3>
                    <div class="value">${paidContributions.value}</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Payments</h3>
                    <div class="value">${formatCurrency(pendingPayments.value)}</div>
                </div>
            </div>
            
            <div class="chart-section">
                <h2>Monthly Contribution Trends</h2>
                <div class="chart-container">
                    <div class="chart">
                        ${fullChartData.value
                            .map((value, idx) => {
                                const maxValue = Math.max(...fullChartData.value, 1);
                                const height = Math.max((value / maxValue) * 100, 1); // Ensure minimum height of 1%
                                return `
                                    <div class="bar" style="height: ${height}%; background: linear-gradient(180deg, #60a5fa 0%, #3b82f6 100%) !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important">
                                    <div class="bar-value">${formatCurrency(value)}</div>
                                    <div class="bar-label">${fullChartLabels.value[idx]}</div>
                                </div>
                            `;
                            })
                            .join('')}
                    </div>
                    <style>
                        @media print {
                            .bar {
                                background-color: #3b82f6 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </div>
            </div>
            
            <div class="transactions-section">
                <h2>Recent Transactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Payer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${contributions.value
                            .map(
                                (c) => `
                            <tr>
                                <td>${c.payer}</td>
                                <td><strong>${formatCurrency(c.amount)}</strong></td>
                                <td>${c.method.charAt(0).toUpperCase() + c.method.slice(1)}</td>
                                <td>${c.date}</td>
                                <td>
                                    <span class="status-badge ${c.status.toLowerCase() === 'paid' ? 'status-paid' : 'status-pending'}">
                                        ${c.status.toLowerCase() === 'paid' ? 'Completed' : 'Pending'}
                                    </span>
                                </td>
                            </tr>
                        `,
                            )
                            .join('')}
                    </tbody>
                </table>
            </div>
            
            <div class="footer">
                <p>This report was automatically generated from the Contributions Dashboard</p>
            </div>
        </body>
        </html>
    `;

    iframeDoc.open();
    iframeDoc.write(htmlContent);
    iframeDoc.close();

    // Wait for content to load then print
    iframe.contentWindow?.addEventListener('load', () => {
        setTimeout(() => {
            iframe.contentWindow?.print();

            // Clean up after printing or if cancelled
            setTimeout(() => {
                document.body.removeChild(iframe);
                isGeneratingPDF.value = false;
            }, 1000);
        }, 250);
    });
};

onMounted(() => {
    fetchData();
});

// ====================
// Computed Properties
// ====================
const selectedPeriod = ref('12months');
const hoveredBar = ref<number | null>(null);

const formatCurrency = (amount: number | string): string => {
    const numericValue = Number(amount);
    if (isNaN(numericValue)) return '₱0.00';
    return `₱${numericValue.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
};

const totalThisMonth = computed(() => {
    if (!chartData.value.length) return 0;
    return chartData.value[chartData.value.length - 1];
});

const averageContribution = computed(() => {
    if (!chartData.value.length) return 0;
    const sum = chartData.value.reduce((a, b) => a + b, 0);
    return Math.round(sum / chartData.value.length);
});

const maxChartValue = computed(() => {
    return Math.max(...chartData.value, 1); // Minimum 1 to avoid division by zero
});

// Calculate growth percentage
const growthPercentage = computed(() => {
    if (chartData.value.length < 2) return 0;
    const currentMonth = chartData.value[chartData.value.length - 1];
    const previousMonth = chartData.value[chartData.value.length - 2];
    if (previousMonth === 0) return currentMonth > 0 ? 100 : 0;
    return Math.round(((currentMonth - previousMonth) / previousMonth) * 100);
});

const ViewAll = () => {
    window.location.href = route('payments.index');
};
</script>

<template>
    <Head title="Contribution Dashboard" />

    <div class="flex min-h-screen">
        <div class="flex-1">
            <AdminLayout>
                <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 p-6 lg:p-8">
                    <!-- Header Section -->
                    <div class="mx-auto mb-8 max-w-7xl">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 lg:text-4xl">Contributions Overview</h1>
                                <p class="mt-1 text-sm text-gray-600">Track and manage all community contributions</p>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    @click="generatePDFReport"
                                    :disabled="isGeneratingPDF"
                                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    {{ isGeneratingPDF ? 'Generating...' : 'Export Report' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="mx-auto mb-8 max-w-7xl">
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Total Contributions -->
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm transition-all hover:shadow-lg"
                            >
                                <div class="absolute top-0 right-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-emerald-500/10"></div>
                                <div class="relative">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="mb-1 text-sm font-medium text-gray-600">Total Contributions</div>
                                    <div class="mb-2 text-2xl font-bold text-gray-900">{{ formatCurrency(totalContributions) }}</div>
                                    <div
                                        class="flex items-center gap-1 text-xs font-semibold"
                                        :class="growthPercentage >= 0 ? 'text-emerald-600' : 'text-red-600'"
                                    >
                                        <svg
                                            v-if="growthPercentage >= 0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        {{ growthPercentage >= 0 ? '+' : '' }}{{ growthPercentage }}% from last month
                                    </div>
                                </div>
                            </div>

                            <!-- Total Payments -->
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-blue-100 bg-white p-6 shadow-sm transition-all hover:shadow-lg"
                            >
                                <div class="absolute top-0 right-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-blue-500/10"></div>
                                <div class="relative">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="mb-1 text-sm font-medium text-gray-600">Total Payments</div>
                                    <div class="mb-2 text-2xl font-bold text-gray-900">{{ formatCurrency(totalPayments) }}</div>
                                    <div class="flex items-center gap-1 text-xs font-semibold text-blue-600">
                                        <span>Successfully processed</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Paid Contributions Count -->
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-purple-100 bg-white p-6 shadow-sm transition-all hover:shadow-lg"
                            >
                                <div class="absolute top-0 right-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-purple-500/10"></div>
                                <div class="relative">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="mb-1 text-sm font-medium text-gray-600">Paid Contributions</div>
                                    <div class="mb-2 text-2xl font-bold text-gray-900">{{ paidContributions }}</div>
                                    <div class="flex items-center gap-1 text-xs font-semibold text-purple-600">
                                        <span>Completed transactions</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Payments -->
                            <div
                                class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-sm transition-all hover:shadow-lg"
                            >
                                <div class="absolute top-0 right-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full bg-amber-500/10"></div>
                                <div class="relative">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="mb-1 text-sm font-medium text-gray-600">Pending Payments</div>
                                    <div class="mb-2 text-2xl font-bold text-gray-900">{{ formatCurrency(pendingPayments) }}</div>
                                    <div class="flex items-center gap-1 text-xs font-semibold text-amber-600">
                                        <span>{{ pendingPayments > 0 ? 'Awaiting verification' : 'All payments processed' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="mx-auto max-w-7xl">
                        <div class="grid gap-6 lg:grid-cols-3">
                            <!-- Chart Section -->
                            <div class="lg:col-span-2">
                                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Contribution Trends</h3>
                                            <p class="mt-1 text-sm text-gray-600">Monthly breakdown for the past year</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                @click="changePeriod('12months')"
                                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                                :class="
                                                    selectedPeriod === '12months'
                                                        ? 'bg-blue-50 text-blue-700 hover:bg-blue-100'
                                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                "
                                            >
                                                12M
                                            </button>
                                            <button
                                                @click="changePeriod('6months')"
                                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                                :class="
                                                    selectedPeriod === '6months'
                                                        ? 'bg-blue-50 text-blue-700 hover:bg-blue-100'
                                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                "
                                            >
                                                6M
                                            </button>
                                            <button
                                                @click="changePeriod('3months')"
                                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                                :class="
                                                    selectedPeriod === '3months'
                                                        ? 'bg-blue-50 text-blue-700 hover:bg-blue-100'
                                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                                "
                                            >
                                                3M
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Chart -->
                                    <div v-if="chartData.length" class="relative mt-6 flex h-80 w-full">
                                        <!-- Y-axis -->
                                        <div class="mr-4 flex h-full flex-col justify-between py-1 text-xs font-medium text-gray-500">
                                            <span class="w-24 text-right">{{ formatCurrency(maxChartValue) }}</span>
                                            <span class="w-24 text-right">{{ formatCurrency(Math.round(maxChartValue * 0.75)) }}</span>
                                            <span class="w-24 text-right">{{ formatCurrency(Math.round(maxChartValue * 0.5)) }}</span>
                                            <span class="w-24 text-right">{{ formatCurrency(Math.round(maxChartValue * 0.25)) }}</span>
                                            <span class="w-24 text-right">₱0.00</span>
                                        </div>

                                        <!-- Chart Area -->
                                        <div class="relative flex h-full w-full flex-1 flex-col">
                                            <!-- Grid lines -->
                                            <div class="absolute inset-0 flex flex-col justify-between">
                                                <div class="h-px bg-gray-100"></div>
                                                <div class="h-px bg-gray-100"></div>
                                                <div class="h-px bg-gray-100"></div>
                                                <div class="h-px bg-gray-100"></div>
                                                <div class="h-px bg-gray-200"></div>
                                            </div>

                                            <!-- Bars -->
                                            <div class="relative flex h-full w-full items-end gap-2 px-1 pb-8">
                                                <div
                                                    v-for="(value, idx) in chartData"
                                                    :key="idx"
                                                    class="group relative h-full flex-1 cursor-pointer"
                                                    @mouseenter="hoveredBar = idx"
                                                    @mouseleave="hoveredBar = null"
                                                >
                                                    <div
                                                        class="absolute right-0 bottom-0 left-0 rounded-t-lg transition-all duration-300"
                                                        :class="hoveredBar === idx ? 'shadow-lg' : ''"
                                                        :style="{
                                                            height: value > 0 ? Math.max((value / maxChartValue) * 100, 4) + '%' : '4px',
                                                            backgroundColor: value > 0 ? '#3b82f6' : '#e5e7eb',
                                                            opacity: value > 0 ? 1 : 0.2,
                                                        }"
                                                    ></div>

                                                    <!-- Value label -->
                                                    <div
                                                        class="absolute -top-8 left-1/2 z-10 -translate-x-1/2 rounded-md px-2 py-1 text-xs font-semibold shadow-sm transition-all"
                                                        :class="hoveredBar === idx ? 'scale-110 bg-gray-900 text-white' : 'bg-white text-gray-900'"
                                                    >
                                                        <div class="whitespace-nowrap">{{ formatCurrency(value) }}</div>
                                                        <div
                                                            class="absolute -bottom-1 left-1/2 h-2 w-2 -translate-x-1/2 rotate-45"
                                                            :class="hoveredBar === idx ? 'bg-gray-900' : 'bg-white'"
                                                        ></div>
                                                    </div>

                                                    <!-- Month label -->
                                                    <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-xs font-medium text-gray-600">
                                                        {{ chartLabels[idx] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty state -->
                                    <div v-else class="flex h-80 items-center justify-center text-gray-400">
                                        <div class="text-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mx-auto mb-2 h-12 w-12"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                />
                                            </svg>
                                            <p>No chart data available</p>
                                        </div>
                                    </div>

                                    <!-- Quick Stats Below Chart -->
                                    <div class="mt-6 grid grid-cols-3 gap-4 border-t border-gray-100 pt-6">
                                        <div class="text-center">
                                            <div class="text-sm text-gray-600">This Month</div>
                                            <div class="mt-1 text-xl font-bold text-gray-900">{{ formatCurrency(totalThisMonth) }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm text-gray-600">Average</div>
                                            <div class="mt-1 text-xl font-bold text-gray-900">{{ formatCurrency(averageContribution) }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm text-gray-600">Growth</div>
                                            <div class="mt-1 text-xl font-bold" :class="growthPercentage >= 0 ? 'text-emerald-600' : 'text-red-600'">
                                                {{ growthPercentage >= 0 ? '+' : '' }}{{ growthPercentage }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Contributions -->
                            <div class="lg:col-span-1">
                                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <div class="mb-6 flex items-center justify-between">
                                        <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
                                        <button @click="ViewAll" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700">
                                            View All
                                        </button>
                                    </div>

                                    <div v-if="contributions.length" class="space-y-4">
                                        <div
                                            v-for="(c, idx) in contributions"
                                            :key="idx"
                                            class="group rounded-xl border border-gray-100 bg-gray-50/50 p-4 transition-all hover:border-blue-200 hover:bg-white hover:shadow-md"
                                        >
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                                                        >
                                                            {{
                                                                c.payer
                                                                    .split(' ')
                                                                    .map((n: string) => n[0])
                                                                    .join('')
                                                                    .toUpperCase()
                                                            }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-gray-900">{{ c.payer }}</div>
                                                            <div class="mt-0.5 flex items-center gap-2 text-xs text-gray-600">
                                                                <span>{{ c.date }}</span>
                                                                <span>•</span>
                                                                <span
                                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                                                    :class="{
                                                                        'bg-green-100 text-green-700': c.method.toLowerCase() === 'cash',
                                                                        'bg-blue-100 text-blue-700': c.method.toLowerCase() === 'paypal',
                                                                        'bg-purple-100 text-purple-700': c.method.toLowerCase() === 'gcash',
                                                                    }"
                                                                >
                                                                    {{ c.method.charAt(0).toUpperCase() + c.method.slice(1) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-bold text-gray-900">{{ formatCurrency(c.amount) }}</div>
                                                    <div
                                                        class="mt-1 inline-flex items-center gap-1 text-xs font-medium"
                                                        :class="c.status.toLowerCase() === 'paid' ? 'text-emerald-600' : 'text-amber-600'"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-3 w-3"
                                                            viewBox="0 0 20 20"
                                                            fill="currentColor"
                                                        >
                                                            <path
                                                                v-if="c.status.toLowerCase() === 'paid'"
                                                                fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd"
                                                            />
                                                            <path
                                                                v-else
                                                                fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                clip-rule="evenodd"
                                                            />
                                                        </svg>
                                                        {{ c.status.toLowerCase() === 'paid' ? 'Completed' : 'Pending' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty state -->
                                    <div v-else class="py-8 text-center text-gray-400">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="mx-auto mb-2 h-12 w-12"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                            />
                                        </svg>
                                        <p>No recent activity</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AdminLayout>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
