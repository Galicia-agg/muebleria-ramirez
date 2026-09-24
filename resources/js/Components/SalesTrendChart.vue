<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    data: { type: Array, required: true }, // [{ date: 'YYYY-MM-DD', total: number }]
    bucket: { type: String, default: 'day' }, // 'day' | 'month'
});

const width = 700;
const height = 220;
const padLeft = 56;
const padRight = 16;
const padTop = 16;
const padBottom = 28;
const plotWidth = width - padLeft - padRight;
const plotHeight = height - padTop - padBottom;

const maxValue = computed(() => {
    const max = Math.max(0, ...props.data.map((d) => d.total));
    if (max === 0) {
        return 100;
    }
    // Round up to a "clean" ceiling (1/2/5 * 10^n)
    const magnitude = 10 ** Math.floor(Math.log10(max));
    const normalized = max / magnitude;
    const niceNormalized = normalized <= 1 ? 1 : normalized <= 2 ? 2 : normalized <= 5 ? 5 : 10;
    return niceNormalized * magnitude;
});

const gridLines = computed(() => {
    const steps = 4;
    return Array.from({ length: steps + 1 }, (_, i) => {
        const value = (maxValue.value / steps) * (steps - i);
        const y = padTop + (plotHeight / steps) * i;
        return { y, value };
    });
});

const points = computed(() =>
    props.data.map((d, i) => ({
        x: padLeft + (props.data.length === 1 ? plotWidth / 2 : (plotWidth / (props.data.length - 1)) * i),
        y: padTop + plotHeight - (d.total / maxValue.value) * plotHeight,
        date: d.date,
        total: d.total,
    })),
);

const linePath = computed(() => points.value.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x.toFixed(1)} ${p.y.toFixed(1)}`).join(' '));

const areaPath = computed(() => {
    if (points.value.length === 0) {
        return '';
    }
    const first = points.value[0];
    const last = points.value[points.value.length - 1];
    const baseline = padTop + plotHeight;
    return `${linePath.value} L ${last.x.toFixed(1)} ${baseline} L ${first.x.toFixed(1)} ${baseline} Z`;
});

const hoverIndex = ref(null);

function onMove(event) {
    const svg = event.currentTarget;
    const rect = svg.getBoundingClientRect();
    const scaleX = width / rect.width;
    const localX = (event.clientX - rect.left) * scaleX;

    if (points.value.length === 0) {
        return;
    }

    const step = points.value.length > 1 ? plotWidth / (points.value.length - 1) : plotWidth;
    let index = Math.round((localX - padLeft) / step);
    index = Math.max(0, Math.min(points.value.length - 1, index));
    hoverIndex.value = index;
}

function onLeave() {
    hoverIndex.value = null;
}

const hovered = computed(() => (hoverIndex.value === null ? null : points.value[hoverIndex.value]));

function formatQ(value) {
    return new Intl.NumberFormat('es-GT', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value ?? 0);
}

function formatDate(dateStr) {
    const [y, m, d] = dateStr.split('-').map(Number);
    if (props.bucket === 'month') {
        return new Date(y, m - 1, d).toLocaleDateString('es-GT', { month: 'short', year: 'numeric' });
    }
    return new Date(y, m - 1, d).toLocaleDateString('es-GT', { day: 'numeric', month: 'short' });
}
</script>

<template>
    <div class="relative">
        <svg
            :viewBox="`0 0 ${width} ${height}`"
            class="w-full touch-none"
            role="img"
            aria-label="Ventas diarias de los últimos 30 días"
            @mousemove="onMove"
            @mouseleave="onLeave"
        >
            <!-- Gridlines -->
            <line
                v-for="line in gridLines"
                :key="line.value"
                :x1="padLeft"
                :x2="width - padRight"
                :y1="line.y"
                :y2="line.y"
                stroke="#e7e5e4"
                stroke-width="1"
            />

            <!-- Area + line -->
            <path :d="areaPath" fill="#8B5E34" fill-opacity="0.1" stroke="none" />
            <path :d="linePath" fill="none" stroke="#6F4A29" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />

            <!-- Crosshair + hover point -->
            <g v-if="hovered">
                <line :x1="hovered.x" :x2="hovered.x" :y1="padTop" :y2="padTop + plotHeight" stroke="#d6d3d1" stroke-width="1" />
                <circle :cx="hovered.x" :cy="hovered.y" r="4" fill="#6F4A29" stroke="#fff" stroke-width="2" />
            </g>

            <!-- Transparent hit layer -->
            <rect :x="padLeft" :y="padTop" :width="plotWidth" :height="plotHeight" fill="transparent" />
        </svg>

        <!-- Y-axis labels (HTML overlay so text size doesn't scale with the SVG viewBox) -->
        <span
            v-for="line in gridLines"
            :key="line.value"
            class="pointer-events-none absolute -translate-y-1/2 whitespace-nowrap text-[11px] text-stone-400"
            :style="{ left: 0, top: `${(line.y / height) * 100}%`, width: `${(padLeft - 8) / width * 100}%`, textAlign: 'right' }"
        >
            Q {{ formatQ(line.value) }}
        </span>

        <!-- X-axis end labels -->
        <span
            v-if="data.length"
            class="pointer-events-none absolute text-[11px] text-stone-400"
            :style="{ left: `${(padLeft / width) * 100}%`, top: `${((height - 14) / height) * 100}%` }"
        >
            {{ formatDate(data[0].date) }}
        </span>
        <span
            v-if="data.length"
            class="pointer-events-none absolute -translate-x-full text-[11px] text-stone-400"
            :style="{ left: `${((width - padRight) / width) * 100}%`, top: `${((height - 14) / height) * 100}%` }"
        >
            {{ formatDate(data[data.length - 1].date) }}
        </span>

        <div
            v-if="hovered"
            class="pointer-events-none absolute rounded-md bg-stone-900 px-2.5 py-1.5 text-xs text-white shadow-lg"
            :style="{
                left: `${(hovered.x / width) * 100}%`,
                top: `${(hovered.y / height) * 100}%`,
                transform: 'translate(-50%, -130%)',
            }"
        >
            <p class="font-semibold">Q {{ formatQ(hovered.total) }}</p>
            <p class="text-stone-300">{{ formatDate(hovered.date) }}</p>
        </div>
    </div>
</template>
