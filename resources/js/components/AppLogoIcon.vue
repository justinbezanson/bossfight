<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        src="/images/bossfight-icon.png"
        alt="Boss Fight"
        :class="['object-cover', className]"
        v-bind="$attrs"
    />
</template>
