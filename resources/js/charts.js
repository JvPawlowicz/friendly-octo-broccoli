/**
 * Integração Chart.js
 * Usado nos relatórios e dashboard
 */

import Chart from 'chart.js/auto';

let chartInstances = new Map();

export function initChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    
    if (!canvas) {
        return null;
    }

    // Destruir instância anterior se existir
    if (chartInstances.has(canvasId)) {
        chartInstances.get(canvasId).destroy();
    }

    const chart = new Chart(canvas, {
        ...config,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: config.options?.plugins?.legend?.display !== false,
                    position: config.options?.plugins?.legend?.position || 'top',
                },
            },
            ...config.options,
        },
    });

    chartInstances.set(canvasId, chart);
    return chart;
}

// Helpers para tipos comuns de gráficos
export const chartHelpers = {
    // Gráfico de barras
    bar: (canvasId, labels, data, label = 'Dados', color = 'rgb(59, 130, 246)') => {
        return initChart(canvasId, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: color,
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    },

    // Gráfico de linha
    line: (canvasId, labels, data, label = 'Dados', color = 'rgb(59, 130, 246)') => {
        return initChart(canvasId, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: color.replace('rgb', 'rgba').replace(')', ', 0.1)'),
                    tension: 0.4,
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    },

    // Gráfico de pizza
    pie: (canvasId, labels, data, colors = null) => {
        const defaultColors = [
            'rgb(59, 130, 246)',   // blue
            'rgb(16, 185, 129)',   // green
            'rgb(239, 68, 68)',     // red
            'rgb(245, 158, 11)',    // yellow
            'rgb(139, 92, 246)',    // purple
            'rgb(236, 72, 153)',    // pink
        ];

        return initChart(canvasId, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors || defaultColors.slice(0, labels.length),
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    },
};

// Exportar para uso global
window.initChart = initChart;
window.chartHelpers = chartHelpers;

