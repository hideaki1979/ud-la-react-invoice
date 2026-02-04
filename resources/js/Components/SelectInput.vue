<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    required: true,
});

const selectInput = ref(null);
defineProps({
    options:null
});

onMounted(() => {
    if (selectInput.value) {
        selectInput.value.focus();
    }
});

defineExpose({ focus: () => selectInput.value.focus() });

</script>

<template>
    <select
        v-model="model"
        ref="selectInput"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    >
        <option value="">選択してください</option>
        <option v-for="op in options" :key="op.id" :value="(op.id)">
            {{ op.name }}
        </option>
    </select>
</template>
