<template>
    <div class="h-full w-full flex flex-col space-y-4">
        <!-- Header with Filter Controls -->
        <div class="flex items-center justify-end px-2 py-3">
            <!-- Filter Controls -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-1.5">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Time Range:</span>
                    <select 
                        id="timeRange"
                        v-model="selectedRange"
                        class="block bg-transparent border-0 py-0.5 pl-1 pr-6 text-sm font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-50 dark:focus:ring-offset-gray-800 rounded-md transition-colors"
                    >
                        <option v-for="range in timeRanges" :key="range.value" :value="range.value" class="bg-white dark:bg-gray-800">
                            {{ range.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="flex-1 min-h-0 w-full overflow-hidden rounded-lg bg-white dark:bg-gray-800/50 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="h-full w-full overflow-x-auto overflow-y-hidden touch-pan-x p-1 scrollbar-thin">
                <div class="min-w-[800px] w-full h-full min-h-[400px] p-4">
                    <LineChart :chart-data="bgChartData" :chart-options="chartOptions" />
                </div>
            </div>
        </div>

        <!-- Legend/Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mt-2">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-900/30">
                <p class="text-xs font-medium text-blue-800 dark:text-blue-200">Current</p>
                <p class="text-xl font-semibold text-blue-900 dark:text-white">
                    {{ latestReading || '--' }} <span class="text-sm font-normal text-blue-700 dark:text-blue-300">mg/dL</span>
                </p>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-100 dark:border-emerald-900/30">
                <p class="text-xs font-medium text-emerald-800 dark:text-emerald-200">Average</p>
                <p class="text-xl font-semibold text-emerald-900 dark:text-white">
                    {{ averageReading || '--' }} <span class="text-sm font-normal text-emerald-700 dark:text-emerald-300">mg/dL</span>
                </p>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-100 dark:border-amber-900/30">
                <p class="text-xs font-medium text-amber-800 dark:text-amber-200">High</p>
                <p class="text-xl font-semibold text-amber-900 dark:text-white">
                    {{ highReading || '--' }} <span class="text-sm font-normal text-amber-700 dark:text-amber-300">mg/dL</span>
                </p>
            </div>
            <div class="bg-rose-50 dark:bg-rose-900/20 p-3 rounded-lg border border-rose-100 dark:border-rose-900/30">
                <p class="text-xs font-medium text-rose-800 dark:text-rose-200">Low</p>
                <p class="text-xl font-semibold text-rose-900 dark:text-white">
                    {{ lowReading || '--' }} <span class="text-sm font-normal text-rose-700 dark:text-rose-300">mg/dL</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import LineChart from '@/Components/LineChart.vue';

// Time range options
const timeRanges = [
    { value: 'day', label: 'Today' },
    { value: 'week', label: 'This Week' },
    { value: 'month', label: 'This Month' },
];

const selectedRange = ref('day');

// Generate time labels based on selected range
const generateTimeLabels = (range) => {
    const now = new Date();
    const labels = [];
    
    if (range === 'day') {
        // Generate hourly labels for a day
        for (let i = 0; i < 24; i++) {
            const hour = i % 12 || 12;
            const period = i >= 12 ? 'PM' : 'AM';
            labels.push(`${hour} ${period}`);
        }
    } else if (range === 'week') {
        // Generate daily labels for a week
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const today = now.getDay();
        
        for (let i = 0; i < 7; i++) {
            const dayIndex = (today + i) % 7;
            labels.push(days[dayIndex]);
            
            // Add a second label for weekend days for better spacing
            if (dayIndex === 0 || dayIndex === 6) {
                labels.push('');
            }
        }
    } else if (range === 'month') {
        // Generate weekly labels for a month
        const weeksInMonth = 4;
        for (let i = 1; i <= weeksInMonth; i++) {
            labels.push(`Week ${i}`);
        }
    }
    
    return labels;
};

// Generate sample data based on the selected range
const generateSampleData = (range) => {
    const now = new Date();
    const dataPoints = range === 'day' ? 24 : range === 'week' ? 7 : 4;
    
    // Generate sample data points
    const preMeal = [];
    const postMeal = [];
    const randomChecks = [];
    
    // Base values that vary by time of day
    const getBaseValue = (i, totalPoints) => {
        if (range === 'day') {
            // Simulate daily patterns - higher during the day, lower at night
            const hour = i;
            if (hour < 6) return 85 + Math.sin(hour * 0.5) * 5; // Night time (low)
            if (hour < 12) return 90 + Math.sin(hour * 0.5) * 10; // Morning rise
            if (hour < 18) return 95 + Math.sin(hour * 0.5) * 15; // Afternoon
            return 90 + Math.sin(hour * 0.5) * 8; // Evening
        } else if (range === 'week') {
            // Weekly pattern - slightly higher on weekends
            const dayOfWeek = i % 7;
            const isWeekend = dayOfWeek >= 5; // Saturday or Sunday
            return isWeekend ? 95 + Math.random() * 10 : 90 + Math.random() * 8;
        } else {
            // Monthly pattern - more stable with slight variations
            return 92 + Math.sin(i * 0.5) * 5 + (Math.random() * 6 - 3);
        }
    };
    
    // Generate data points
    for (let i = 0; i < dataPoints; i++) {
        const baseValue = getBaseValue(i, dataPoints);
        const randomFactor = Math.random() * 10 - 5; // -5 to 5
        const mealSpike = 40 + Math.random() * 30; // 40-70 point increase after meals
        
        // Pre-meal readings (typically lower)
        preMeal.push({
            x: i,
            y: Math.round(baseValue + randomFactor * 0.5)
        });
        
        // Post-meal readings (higher, with more variation)
        postMeal.push({
            x: i + 0.3, // Offset slightly from pre-meal
            y: Math.round(baseValue + mealSpike + randomFactor)
        });
        
        // Random checks (occasional readings between meals)
        if (Math.random() > 0.7) { // 30% chance of a random check
            const randomTimeOffset = 0.1 + Math.random() * 0.8; // Between 0.1 and 0.9
            randomChecks.push({
                x: i + randomTimeOffset,
                y: Math.round(baseValue + (mealSpike * randomTimeOffset) + (randomFactor * 0.7))
            });
        }
    }
    
    return { preMeal, postMeal, randomChecks };
};

// Function to update chart options based on time range
const updateChartOptions = (range) => {
    // Create a new options object to maintain reactivity
    const newOptions = {
        ...chartOptions.value,
        scales: {
            ...chartOptions.value.scales,
            x: {
                ...chartOptions.value.scales.x,
                ticks: {
                    ...(chartOptions.value.scales.x.ticks || {}),
                    maxRotation: range === 'day' ? 45 : 0,
                    autoSkip: true,
                    maxTicksLimit: range === 'day' ? 12 : range === 'week' ? 7 : 4
                }
            }
        },
        plugins: {
            ...chartOptions.value.plugins,
            tooltip: {
                ...chartOptions.value.plugins.tooltip,
                callbacks: {
                    ...(chartOptions.value.plugins.tooltip.callbacks || {}),
                    label: (context) => {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed?.y !== null && context.parsed?.y !== undefined) {
                            label += `${context.parsed.y} mg/dL`;
                        }
                        return label;
                    }
                }
            }
        }
    };
    
    // Update the ref value
    chartOptions.value = newOptions;
};

// Function to calculate min/max values from data
const calculateYAxisRange = (data) => {
  // Flatten all data points from all datasets
  const allDataPoints = data.datasets.flatMap(dataset => 
    dataset.data?.filter(Boolean).map(point => point.y ?? point) ?? []
  );

  if (allDataPoints.length === 0) return { min: 0, max: 200 };

  // Calculate min and max values
  const min = Math.max(0, Math.floor(Math.min(...allDataPoints) / 10) * 10 - 20);
  const max = Math.ceil((Math.max(...allDataPoints) + 20) / 10) * 10;
  
  // Ensure we have a reasonable range
  return {
    min: Math.max(0, min),
    max: Math.max(200, max) // Always show at least up to 200
  };
};

// Initialize with default 'day' data
const sampleData = ref(generateSampleData('day'));
const timeLabels = ref(generateTimeLabels('day'));

// Computed properties for stats cards
const latestReading = computed(() => {
    if (!sampleData.value?.preMeal?.length) return null;
    const allReadings = [
        ...(sampleData.value.preMeal || []),
        ...(sampleData.value.postMeal || []),
        ...(sampleData.value.randomChecks || [])
    ];
    
    // Get the most recent reading by x value
    const latest = allReadings.reduce((latest, current) => {
        return (!latest || current.x > latest.x) ? current : latest;
    }, null);
    
    return latest?.y || null;
});

const averageReading = computed(() => {
    if (!sampleData.value?.preMeal?.length) return null;
    const allReadings = [
        ...(sampleData.value.preMeal || []),
        ...(sampleData.value.postMeal || []),
        ...(sampleData.value.randomChecks || [])
    ];
    
    if (allReadings.length === 0) return null;
    const sum = allReadings.reduce((acc, curr) => acc + curr.y, 0);
    return Math.round(sum / allReadings.length);
});

const highReading = computed(() => {
    if (!sampleData.value?.preMeal?.length) return null;
    const allReadings = [
        ...(sampleData.value.preMeal || []),
        ...(sampleData.value.postMeal || []),
        ...(sampleData.value.randomChecks || [])
    ];
    
    if (allReadings.length === 0) return null;
    return Math.max(...allReadings.map(r => r.y));
});

const lowReading = computed(() => {
    if (!sampleData.value?.preMeal?.length) return null;
    const allReadings = [
        ...(sampleData.value.preMeal || []),
        ...(sampleData.value.postMeal || []),
        ...(sampleData.value.randomChecks || [])
    ];
    
    if (allReadings.length === 0) return null;
    return Math.min(...allReadings.map(r => r.y));
});

// Update chart data when time range changes
watch(selectedRange, (newRange) => {
    timeLabels.value = generateTimeLabels(newRange);
    sampleData.value = generateSampleData(newRange);
    
    // Update chart options for the new range
    updateChartOptions(newRange);
});

// Initialize chart with day view
onMounted(() => {
    updateChartOptions('day');
});

// Create a computed property that combines background zones with blood glucose data
const bgChartData = computed(() => {
  const dataPoints = timeLabels.value?.length || 24;
  
  // Background zones
  const backgroundZones = [
    // Danger Zone - High (>400)
    {
      label: '',
      backgroundColor: 'rgba(220, 38, 38, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 500 })),
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Critical Zone - High (250-400)
    {
      label: '',
      backgroundColor: 'rgba(249, 115, 22, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 400 })),
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Warning Zone - High (180-250)
    {
      label: '',
      backgroundColor: 'rgba(250, 204, 21, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 250 })),
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Safe Zone (70-180)
    {
      label: '',
      backgroundColor: 'rgba(34, 197, 94, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 180 })),
      yAxisID: 'y',
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 70 })),
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Critical Zone - Low (50-60)
    {
      label: '',
      backgroundColor: 'rgba(249, 115, 22, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 60 })),
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Danger Zone - Low (<50)
    {
      label: '',
      backgroundColor: 'rgba(220, 38, 38, 0.15)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointRadius: 0,
      fill: true,
      data: Array.from({ length: dataPoints }, (_, i) => ({ x: i, y: 50 })),
      yAxisID: 'y',
      xAxisID: 'x'
    }
  ];

  // Blood glucose data points
  const glucoseData = [
    // Pre-meal data points (BG Checks - blue-600)
    {
      label: 'Pre-meal',
      backgroundColor: 'transparent',
      borderColor: 'transparent',
      borderWidth: 0,
      pointBackgroundColor: '#2563eb',
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: '#2563eb',
      pointHoverBorderColor: 'transparent',
      pointRadius: 4,
      pointHoverRadius: 6,
      pointHitRadius: 10,
      pointBorderWidth: 1,
      fill: false,
      data: sampleData.value.preMeal || [],
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Post-meal data points (BG Checks - red-600)
    {
      label: 'Post-meal',
      backgroundColor: 'transparent',
      borderColor: 'transparent',
      borderWidth: 0,
      pointBackgroundColor: '#dc2626',
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: '#dc2626',
      pointHoverBorderColor: 'transparent',
      pointRadius: 4,
      pointHoverRadius: 6,
      pointHitRadius: 10,
      pointBorderWidth: 1,
      fill: false,
      data: sampleData.value.postMeal || [],
      yAxisID: 'y',
      xAxisID: 'x'
    },
    // Random checks (BG Checks - purple-600)
    {
      label: 'Random Check',
      backgroundColor: 'transparent',
      borderColor: 'transparent',
      borderWidth: 0,
      pointBackgroundColor: '#9333ea',
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: '#9333ea',
      pointHoverBorderColor: 'transparent',
      pointRadius: 4,
      pointHoverRadius: 6,
      pointHitRadius: 10,
      pointBorderWidth: 1,
      fill: false,
      data: sampleData.value.randomChecks || [],
      yAxisID: 'y',
      xAxisID: 'x'
    }
  ];

  // Bottom of chart (invisible, just for filling)
  const bottomZone = {
    label: 'Bottom',
    backgroundColor: 'transparent',
    borderColor: 'transparent',
    borderWidth: 0,
    pointRadius: 0,
    fill: false,
    data: Array.from({ length: timeLabels.value?.length || 24 }, (_, i) => ({ x: i, y: 0 })),
    yAxisID: 'y',
    xAxisID: 'x'
  };

  return {
    labels: timeLabels.value || [],
    datasets: [...backgroundZones, ...glucoseData, bottomZone]
  };
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  layout: {
    padding: {
      top: 20,
      right: 15,
      bottom: 15,
      left: 10
    },
    autoPadding: true
  },
  aspectRatio: 2,
  interaction: {
    intersect: false,
    mode: 'index',
  },
  // Base font settings
  font: {
    family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
    size: 12,
    weight: 400
  },
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      align: 'center',
      labels: {
        usePointStyle: true,
        boxWidth: 8,
        boxHeight: 8,
        padding: 15,
        color: 'rgb(75, 85, 99)', // gray-600
        font: {
          family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
          size: 13,
          weight: '500',
          lineHeight: 1.5
        },
        filter: (legendItem) => {
          // Only show legend items for actual data series (pre-meal, post-meal, random)
          return ['Pre-meal', 'Post-meal', 'Random Check'].includes(legendItem.text);
        }
      }
    },
    tooltip: {
      enabled: true,
      mode: 'nearest',
      intersect: false,
      backgroundColor: 'rgba(15, 23, 42, 0.95)', // slate-900 with high opacity
      titleFont: {
        family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        size: 12,
        weight: '600',
        lineHeight: 1.4
      },
      bodyFont: {
        family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        size: 12,
        weight: '500',
        lineHeight: 1.4
      },
      padding: 10,
      cornerRadius: 8,
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      boxPadding: 6,
      displayColors: true,
      usePointStyle: true,
      boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
      callbacks: {
        label: function(context) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            label += context.parsed.y + ' mg/dL';
          }
          return label;
        },
        title: function(context) {
          return 'Time: ' + context[0].label;
        }
      }
    },
    annotation: {
      annotations: {
        dangerHigh: {
          type: 'box',
          yMin: 400,
          yMax: 500,
          backgroundColor: 'rgba(220, 38, 38, 0.1)',
          borderColor: 'rgba(220, 38, 38, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(220, 38, 38, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        criticalHigh: {
          type: 'box',
          yMin: 250,
          yMax: 400,
          backgroundColor: 'rgba(249, 115, 22, 0.1)',
          borderColor: 'rgba(249, 115, 22, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(249, 115, 22, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        warningHigh: {
          type: 'box',
          yMin: 180,
          yMax: 250,
          backgroundColor: 'rgba(250, 204, 21, 0.1)',
          borderColor: 'rgba(250, 204, 21, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(250, 204, 21, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        safe: {
          type: 'box',
          yMin: 70,
          yMax: 180,
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          borderColor: 'rgba(34, 197, 94, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(34, 197, 94, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        warningLow: {
          type: 'box',
          yMin: 60,
          yMax: 70,
          backgroundColor: 'rgba(250, 204, 21, 0.1)',
          borderColor: 'rgba(250, 204, 21, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(250, 204, 21, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        criticalLow: {
          type: 'box',
          yMin: 50,
          yMax: 60,
          backgroundColor: 'rgba(249, 115, 22, 0.1)',
          borderColor: 'rgba(249, 115, 22, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(249, 115, 22, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        dangerLow: {
          type: 'box',
          yMin: 0,
          yMax: 50,
          backgroundColor: 'rgba(220, 38, 38, 0.1)',
          borderColor: 'rgba(220, 38, 38, 0.2)',
          borderWidth: 1,
          drawTime: 'beforeDatasetsDraw',
          label: {
            display: true,
            position: 'left',
            xAdjust: 10,
            backgroundColor: 'rgba(220, 38, 38, 0.7)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        preMealTarget: {
          type: 'line',
          yMin: 80,
          yMax: 80,
          borderColor: '#3b82f6',
          borderWidth: 1,
          borderDash: [4, 4],
          label: {
            display: true,
            position: 'left',
            backgroundColor: 'rgba(59, 130, 246, 0.9)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        },
        postMealTarget: {
          type: 'line',
          yMin: 180,
          yMax: 180,
          borderColor: '#f97316',
          borderWidth: 1,
          borderDash: [4, 4],
          label: {
            display: true,
            position: 'left',
            backgroundColor: 'rgba(249, 115, 22, 0.9)',
            color: 'white',
            font: {
              size: 10,
              weight: 'bold'
            },
            padding: 4,
            borderRadius: 4
          }
        }
      }
    }
  },
  scales: {
    x: {
      offset: true,
      type: 'category',
      grid: {
        display: false,
        drawBorder: false,
        color: 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        color: 'rgb(107, 114, 128)', // text-gray-500
        font: {
          family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
          size: 14, // text-sm (0.875rem)
          weight: '500', // font-medium
          lineHeight: 1.5
        },
        maxRotation: 0,
        autoSkip: true,
        maxTicksLimit: 12,
        padding: 6
      },
      title: {
        display: true,
        color: 'rgb(107, 114, 128)', // text-gray-500
        padding: {top: 8},
        font: {
          size: 14, // text-sm (0.875rem)
          weight: '500' // font-medium
        }
      }
    },
    y: {
      grid: {
        display: true,
        color: 'rgba(0, 0, 0, 0.05)',
        drawBorder: false
      },
      title: {
        display: true,
        color: 'rgb(107, 114, 128)', // text-gray-500
        padding: {bottom: 8},
        font: {
          family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
          size: 14, // text-sm (0.875rem)
          weight: '500', // font-medium
          lineHeight: 1.5
        }
      },
      ticks: {
        color: 'rgb(107, 114, 128)', // text-gray-500
        font: {
          family: '"Figtree", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
          size: 14, // text-sm (0.875rem)
          weight: '500', // font-medium
          lineHeight: 1.5
        },
        padding: 8,
        callback: function(value) {
          return value + ' mg/dL';
        },
        // Auto calculate step size based on range
        autoSkip: true,
        maxTicksLimit: 8
      },
      // Dynamic min/max based on data
      ...calculateYAxisRange({
        datasets: [
          { data: sampleData.preMeal },
          { data: sampleData.postMeal },
          { data: sampleData.randomChecks }
        ]
      })
    }
  },
  elements: {
    point: {
      radius: 0
    },
    line: {
      borderWidth: 2,
      tension: 0.4
    }
  }
});
</script>
